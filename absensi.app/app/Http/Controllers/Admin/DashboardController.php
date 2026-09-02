<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DashboardController extends Controller
{
    public function index()
    {
        $user = \Auth::user();

        $departemen = Departemen::orderBy('nama')->get();
        return view('admin/dashboard/index', compact(
            'user', 'departemen'
        ));
    }

    public function dataAbsensi(Request $request)
    {
        $search = request('search.value');

        $data = User::select(
            'users.id',
            'users.name',
            'users.email',
            'users.username',
            'users.jenis_kelamin',
            'users.photo',
            'users.departemen_id',
            'users.role_id',
            'users.created_at',
            'users.updated_at'
        )
        ->selectRaw('COUNT(absensi.id) as total_kehadiran')
        ->leftJoin('absensi', 'absensi.users_id', '=', 'users.id')
        ->groupBy(
            'users.id',
            'users.name',
            'users.email',
            'users.username',
            'users.jenis_kelamin',
            'users.photo',
            'users.departemen_id',
            'users.role_id',
            'users.created_at',
            'users.updated_at'
        );


        return DataTables::of($data)
            ->filter(function ($query) use ($search, $request) {
                $query->where(function ($query) use ($search) {
                    $query->orWhere('users.username', 'LIKE', "%$search%");
                    $query->orWhere('users.name', 'LIKE', "%$search%");
                    $query->orWhere('users.email', 'LIKE', "%$search%");
                    $query->orWhere('users.jenis_kelamin', 'LIKE', "%$search%");
                });
            })
            ->editColumn('name', function ($row) {
                $assetsPath = asset('photo') . '/';
                if ($row->photo) {
                    // Untuk photo gambar
                    $output = '<img src="' . $assetsPath . $row->photo . '" alt="Avatar" class="rounded-circle">';
                } else {
                    // Untuk photo badge dengan inisial
                    $stateNum = rand(0, 5);
                    $states   = ['success', 'danger', 'warning', 'info', 'primary', 'secondary'];
                    $state    = $states[$stateNum];

                    // Ambil inisial dari nama lengkap
                    preg_match_all('/\b\w/', $row->name, $matches);
                    $initials = isset($matches[0]) ? strtoupper($matches[0][0] . end($matches[0])) : '';

                    $output = '<span class="avatar-initial rounded-circle bg-label-' . $state . '">' . $initials . '</span>';
                }

                return '
                    <div class="d-flex justify-content-start align-items-center user-name">
                        <div class="avatar-wrapper">
                            <div class="avatar me-2">
                                ' . $output . '
                            </div>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="emp_name text-truncate">' . htmlspecialchars($row->name) . '</span>
                            <small class="emp_post text-truncate text-muted">' . htmlspecialchars($row->email ?? 'Unknown') . '</small>
                            <small class="emp_post text-truncate text-muted">' . htmlspecialchars($row->role->akses . ' - ' . $row->departemen->nama) . '</small>
                        </div>
                    </div>
                ';
            })
            ->rawColumns(['action', 'name'])
            ->toJson();
    }
}
