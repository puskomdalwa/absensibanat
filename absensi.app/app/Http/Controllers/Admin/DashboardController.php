<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

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
                    $output = '<img src="' . $assetsPath . $row->photo . '" alt="Avatar" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #fb7185; box-shadow: 0 2px 8px rgba(225, 29, 72, 0.2);">';
                } else {
                    // Ambil inisial dari nama lengkap
                    preg_match_all('/\b\w/', $row->name, $matches);
                    $initials = isset($matches[0]) ? strtoupper($matches[0][0] . end($matches[0])) : 'U';

                    $output = '<span class="avatar-initial rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 0.88rem; background: linear-gradient(135deg, rgba(225, 29, 72, 0.12) 0%, rgba(251, 113, 133, 0.22) 100%); color: #e11d48; border: 1.5px solid rgba(225, 29, 72, 0.25);">' . $initials . '</span>';
                }

                $roleAkses = $row->role ? $row->role->akses : '-';
                $deptNama = $row->departemen ? $row->departemen->nama : '-';

                return '
                    <div class="d-flex justify-content-start align-items-center user-name py-1">
                        <div class="avatar-wrapper me-3">
                            <div class="avatar">
                                ' . $output . '
                            </div>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-heading" style="font-size: 0.92rem; letter-spacing: -0.01em;">' . htmlspecialchars($row->name) . '</span>
                            <small class="text-muted" style="font-size: 0.76rem;">' . htmlspecialchars($row->email ?? '-') . '</small>
                            <small class="mt-1">
                                <span class="badge" style="background: rgba(225, 29, 72, 0.08); color: #e11d48; border: 1px solid rgba(225, 29, 72, 0.2); border-radius: 4px; font-size: 0.72rem; padding: 2px 8px;">
                                    ' . htmlspecialchars($roleAkses . ' • ' . $deptNama) . '
                                </span>
                            </small>
                        </div>
                    </div>
                ';
            })
            ->rawColumns(['action', 'name'])
            ->toJson();
    }
}
