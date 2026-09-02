<?php

namespace App\Imports;

use App\Models\Departemen;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;

class UserImport implements ToCollection
{
    /**
     * @param Collection $collection
     * @param Request $request
     */

    private $request;
    public $error;
    public $success;
    public $max;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection(Collection $collection)
    {
        $initUserId = (int) $this->request->init_user_id;
        $initIndex = $collection->search(function ($row) use ($initUserId) { // for detect first looping
            return $row[0] == $initUserId;
        });

        $max = $collection->count() - $initIndex;

        $roleId = Role::where('akses', 'user')->value('id');

        $error = 0;
        $success = 0;
        $dataTest = [];

        foreach ($collection->skip($initIndex) as $key => $row) {
            $userId = $row[0];
            $name = $row[1];
            $departemenKode = $row[2];

            if ($userId == null || $name == null || $departemenKode == null) {
                $error++;
                continue;
            }

            $departemen = Departemen::firstOrCreate([
                'kode' => $departemenKode,
            ], [
                'nama' => $departemenKode,
            ]);


            User::updateOrCreate(
                [
                    'id' => $userId
                ],
                [
                    'id' => $userId,
                    'username' => $userId,
                    'email' => $userId . '@gmail.com',
                    'name' => trim($name),
                    'role_id' => $roleId,
                    'departemen_id' => $departemen->id,
                    'password' => Hash::make('dalwa123')
                ]
            );

            $success++;
        }

        $this->max = $max;
        $this->error = $error;
        $this->success = $success;
    }
}
