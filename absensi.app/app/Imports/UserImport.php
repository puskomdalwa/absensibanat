<?php

namespace App\Imports;

use App\Models\Departemen;
use App\Models\Role;
use App\Models\Type;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;

class UserImport implements ToCollection
{
    private $request;
    public $error = 0;
    public $success = 0;
    public $createdCount = 0;
    public $updatedCount = 0;
    public $max = 0;
    public $newRolesList = [];
    public $newDepartemenList = [];
    public $errorsDetail = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection(Collection $collection)
    {
        // 1. Filter out completely empty rows
        $rows = $collection->filter(function ($row) {
            if (!$row instanceof Collection && !is_array($row)) {
                return false;
            }
            foreach ($row as $cell) {
                if ($cell !== null && trim((string)$cell) !== '') {
                    return true;
                }
            }
            return false;
        })->values();

        if ($rows->isEmpty()) {
            $this->max = 0;
            return;
        }

        // 2. Identify header row and map column positions
        $headerIndex = -1;
        $columnMap = [];

        foreach ($rows->take(15) as $idx => $row) {
            $detected = $this->detectHeaderColumns($row);
            if ($detected !== false) {
                $headerIndex = $idx;
                $columnMap = $detected;
                break;
            }
        }

        // If no header row was detected, fallback to default positional mapping
        if ($headerIndex === -1) {
            $firstRow = $rows->first();
            $colCount = count($firstRow);

            if ($colCount >= 9) {
                // New multi-column format (NO, KODE, NAMA DOSEN, L/P, TTL, E-MAIL, HP, STATUS, ROLE, DEPARTEMEN, KODE-DEPARTEMEN)
                $columnMap = [
                    'no'              => 0,
                    'kode'            => 1,
                    'nama'            => 2,
                    'lp'              => 3,
                    'ttl'             => 4,
                    'email'           => 5,
                    'hp'              => 6,
                    'status'          => 7,
                    'role'            => 8,
                    'departemen'      => 9,
                    'kode_departemen' => 10,
                ];
                $dataRows = $rows;
            } else {
                // Legacy 3-column format (id/kode, name, departemen_kode)
                $columnMap = [
                    'kode'            => 0,
                    'nama'            => 1,
                    'kode_departemen' => 2,
                ];
                $dataRows = $rows;
            }
        } else {
            // Rows after header row are the data rows
            $dataRows = $rows->slice($headerIndex + 1)->values();
        }

        // Handle optional init_user_id filter if specified
        $initUserId = trim((string)$this->request->input('init_user_id', ''));
        if ($initUserId !== '') {
            $initIndex = $dataRows->search(function ($row) use ($initUserId, $columnMap) {
                $kodeIdx = $columnMap['kode'] ?? 1;
                $noIdx   = $columnMap['no'] ?? 0;
                $cellVal = trim((string)($row[$kodeIdx] ?? ''));
                $noVal   = trim((string)($row[$noIdx] ?? ''));
                return $cellVal === $initUserId || $noVal === $initUserId;
            });

            if ($initIndex !== false) {
                $dataRows = $dataRows->slice($initIndex)->values();
            }
        }

        $this->max = $dataRows->count();

        // 3. Process data rows
        foreach ($dataRows as $rowIdx => $row) {
            try {
                $getVal = function ($key) use ($row, $columnMap) {
                    if (!isset($columnMap[$key])) {
                        return null;
                    }
                    $idx = $columnMap[$key];
                    $val = $row[$idx] ?? null;
                    return ($val !== null) ? trim((string)$val) : null;
                };

                $kode         = $getVal('kode');
                $nama         = $getVal('nama');
                $lp           = $getVal('lp');
                $ttl          = $getVal('ttl');
                $email        = $getVal('email');
                $hp           = $getVal('hp');
                $status       = $getVal('status');
                $roleStr      = $getVal('role');
                $deptStr      = $getVal('departemen');
                $deptKodeStr  = $getVal('kode_departemen');

                // If both kode and nama are empty, skip row
                if (empty($kode) && empty($nama)) {
                    $this->error++;
                    continue;
                }

                // If kode is empty, but we have row index or name
                if (empty($kode)) {
                    $this->error++;
                    $this->errorsDetail[] = "Baris " . ($rowIdx + 1) . ": KODE kosong.";
                    continue;
                }

                if (empty($nama)) {
                    $nama = 'User ' . $kode;
                }

                // 3.1. Auto-create or resolve Role
                $roleId = $this->resolveRole($roleStr);

                // 3.2. Auto-create or resolve Departemen
                $departemenId = $this->resolveDepartemen($deptKodeStr, $deptStr);

                // 3.3. Gender mapping
                $jenisKelamin = $this->resolveGender($lp);

                // 3.4. Resolve Type (e.g. santri)
                $typeId = $this->resolveType($roleStr, $deptStr);

                // 3.5. Resolve Email
                $cleanEmail = $this->resolveEmail($email, $kode);

                // 3.6. Upsert User
                $this->upsertUser([
                    'kode'          => (string)$kode,
                    'name'          => $nama,
                    'email'         => $cleanEmail,
                    'jenis_kelamin' => $jenisKelamin,
                    'role_id'       => $roleId,
                    'departemen_id' => $departemenId,
                    'type_id'       => $typeId,
                ]);

                $this->success++;
            } catch (\Throwable $th) {
                $this->error++;
                $this->errorsDetail[] = "Baris " . ($rowIdx + 1) . " (Kode: {$kode}): " . $th->getMessage();
                Log::error("[UserImport] Row Error: " . $th->getMessage(), [
                    'row' => $row,
                    'exception' => $th
                ]);
            }
        }
    }

    /**
     * Detect header row by inspecting column titles.
     */
    protected function detectHeaderColumns($row)
    {
        $normalizedCells = [];
        foreach ($row as $colIdx => $cell) {
            $str = strtolower(trim((string)$cell));
            // strip out unwanted punctuation
            $clean = preg_replace('/[^a-z0-9]/', '', $str);
            $normalizedCells[$colIdx] = [
                'raw'   => $str,
                'clean' => $clean,
            ];
        }

        $map = [];
        $matchedFields = 0;

        foreach ($normalizedCells as $idx => $cell) {
            $c = $cell['clean'];
            $raw = $cell['raw'];

            // Match KODE-DEPARTEMEN first before DEPARTEMEN
            if (str_contains($c, 'kodedep') || str_contains($raw, 'kode-dep') || str_contains($raw, 'kode dep')) {
                $map['kode_departemen'] = $idx;
                $matchedFields++;
            } elseif (str_contains($c, 'departemen') || str_contains($c, 'department') || $c === 'dept') {
                if (!isset($map['departemen'])) {
                    $map['departemen'] = $idx;
                    $matchedFields++;
                }
            } elseif ($c === 'kode' || $c === 'pin' || $c === 'nip' || $c === 'nik' || $c === 'userid' || $c === 'iduser') {
                $map['kode'] = $idx;
                $matchedFields++;
            } elseif (str_contains($c, 'nama') || $c === 'name' || str_contains($c, 'namadosen')) {
                $map['nama'] = $idx;
                $matchedFields++;
            } elseif ($c === 'lp' || $raw === 'l/p' || $c === 'jk' || str_contains($c, 'kelamin') || $c === 'gender') {
                $map['lp'] = $idx;
                $matchedFields++;
            } elseif ($c === 'ttl' || str_contains($c, 'tanggallahir') || str_contains($c, 'tempatlahir')) {
                $map['ttl'] = $idx;
                $matchedFields++;
            } elseif (str_contains($c, 'mail') || $c === 'email') {
                $map['email'] = $idx;
                $matchedFields++;
            } elseif ($c === 'hp' || $c === 'nohp' || str_contains($c, 'telepon') || str_contains($c, 'telp') || $c === 'phone' || $c === 'wa') {
                $map['hp'] = $idx;
                $matchedFields++;
            } elseif (str_contains($c, 'statu')) { // matches 'statu' and 'status'
                $map['status'] = $idx;
                $matchedFields++;
            } elseif ($c === 'role' || $c === 'jabatan' || $c === 'akses' || $c === 'level') {
                $map['role'] = $idx;
                $matchedFields++;
            } elseif ($c === 'no' || $c === 'nomor') {
                $map['no'] = $idx;
                $matchedFields++;
            }
        }

        // Must match at least 2 distinct recognizable columns to be considered a header row
        if ($matchedFields >= 2 && (isset($map['kode']) || isset($map['nama']))) {
            return $map;
        }

        return false;
    }

    /**
     * Resolve or auto-create Role.
     */
    protected function resolveRole($roleStr): int
    {
        $roleName = strtolower(trim((string)$roleStr));
        if ($roleName === '') {
            $roleName = 'user';
        }

        $role = Role::where('akses', $roleName)->first();
        if (!$role) {
            $role = Role::create(['akses' => $roleName]);
            $this->newRolesList[] = $roleName;
        }

        return $role->id;
    }

    /**
     * Resolve or auto-create Departemen.
     */
    protected function resolveDepartemen($deptKodeStr, $deptNamaStr): ?int
    {
        $cleanKode = trim((string)$deptKodeStr);
        $cleanNama = trim((string)$deptNamaStr);

        if ($cleanKode === '' && $cleanNama === '') {
            return null;
        }

        $dept = null;

        // 1. Try finding by kode if given
        if ($cleanKode !== '') {
            $dept = Departemen::where('kode', $cleanKode)->first();
        }

        // 2. Try finding by nama if not found by kode
        if (!$dept && $cleanNama !== '') {
            $dept = Departemen::where('nama', $cleanNama)->first();
        }

        // 3. Auto-create if neither matched
        if (!$dept) {
            if ($cleanKode === '') {
                // Auto-generate numeric kode
                $maxNum = Departemen::whereRaw('kode REGEXP "^[0-9]+$"')->max(DB::raw('CAST(kode AS UNSIGNED)'));
                $next = $maxNum ? ($maxNum + 1) : (Departemen::count() + 1);
                $cleanKode = sprintf('%03d', $next);
            }

            if ($cleanNama === '') {
                $cleanNama = 'Departemen ' . $cleanKode;
            }

            $dept = Departemen::create([
                'kode' => $cleanKode,
                'nama' => $cleanNama,
            ]);

            $this->newDepartemenList[] = "{$cleanKode} - {$cleanNama}";
        } else {
            // Update nama if previously generic or identical to kode
            if ($cleanNama !== '' && ($dept->nama === $dept->kode || empty($dept->nama))) {
                $dept->nama = $cleanNama;
                $dept->save();
            }
        }

        return $dept->id;
    }

    /**
     * Map gender string to enum: 'Laki-laki', 'Perempuan', '*'.
     */
    protected function resolveGender($lp): string
    {
        $clean = strtoupper(trim((string)$lp));

        if (in_array($clean, ['P', 'PR', 'PEREMPUAN', 'FEMALE', 'WANITA'])) {
            return 'Perempuan';
        }

        if (in_array($clean, ['L', 'LK', 'LAKI-LAKI', 'LAKI', 'MALE', 'PRIA'])) {
            return 'Laki-laki';
        }

        return '*';
    }

    /**
     * Resolve type ID if applicable (e.g. santri).
     */
    protected function resolveType($roleStr, $deptStr): ?int
    {
        $str = strtolower(trim((string)$roleStr . ' ' . (string)$deptStr));
        if (str_contains($str, 'santri')) {
            $type = Type::where('nama', 'santri')->first();
            return $type ? $type->id : null;
        }
        return null;
    }

    /**
     * Ensure valid and safe unique email.
     */
    protected function resolveEmail($email, $kode): string
    {
        $clean = trim((string)$email);
        if ($clean === '' || !filter_var($clean, FILTER_VALIDATE_EMAIL)) {
            return $kode . '@gmail.com';
        }
        return $clean;
    }

    /**
     * Update or create User.
     */
    protected function upsertUser(array $data)
    {
        $kode = $data['kode'];

        // Find existing user by kode, username, or id
        $user = User::where('kode', $kode)
            ->orWhere('username', $kode)
            ->when(is_numeric($kode), function ($q) use ($kode) {
                $q->orWhere('id', (int)$kode);
            })
            ->first();

        if ($user) {
            // Update existing user
            $user->name          = $data['name'];
            $user->jenis_kelamin = $data['jenis_kelamin'];
            $user->role_id       = $data['role_id'];
            $user->departemen_id = $data['departemen_id'];
            if ($data['type_id']) {
                $user->type_id   = $data['type_id'];
            }
            if (empty($user->kode)) {
                $user->kode      = $kode;
            }
            if (empty($user->username)) {
                $user->username  = $kode;
            }

            // Check email uniqueness before setting
            $emailTaken = User::where('email', $data['email'])->where('id', '!=', $user->id)->exists();
            if (!$emailTaken) {
                $user->email = $data['email'];
            }

            $user->save();
            $this->updatedCount++;
        } else {
            // New user: ensure email is unique
            $cleanEmail = $data['email'];
            if (User::where('email', $cleanEmail)->exists()) {
                $cleanEmail = $kode . '_' . $cleanEmail;
            }

            $insertData = [
                'kode'          => $kode,
                'username'      => $kode,
                'name'          => $data['name'],
                'email'         => $cleanEmail,
                'jenis_kelamin' => $data['jenis_kelamin'],
                'role_id'       => $data['role_id'],
                'departemen_id' => $data['departemen_id'],
                'type_id'       => $data['type_id'],
                'password'      => Hash::make('dalwa123'),
            ];

            // If kode is numeric and not yet taken as an ID, assign ID = kode
            if (is_numeric($kode) && (int)$kode > 0 && !User::find((int)$kode)) {
                $insertData['id'] = (int)$kode;
            }

            User::create($insertData);
            $this->createdCount++;
        }
    }

    /**
     * Get import summary results.
     */
    public function getResult(): array
    {
        return [
            'error'          => $this->error ?? 0,
            'success'        => $this->success ?? 0,
            'created'        => $this->createdCount ?? 0,
            'updated'        => $this->updatedCount ?? 0,
            'max'            => $this->max ?? 0,
            'new_roles'      => $this->newRolesList ?? [],
            'new_departemen' => $this->newDepartemenList ?? [],
            'errors_detail'  => $this->errorsDetail ?? [],
        ];
    }
}
