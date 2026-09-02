<?php

namespace App\Http\Controllers\Admin;

use App\Models\ApiClient;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ApiClientController extends Controller
{
    public function index()
    {
        return view('admin.api-client.index');
    }

    public function data(Request $request)
    {
        $search = request('search.value');
        $data = ApiClient::select('*')->orderBy('id', 'desc');

        return DataTables::of($data)
            ->filter(function ($query) use ($search) {
                if (!$search) {
                    return;
                }
                $query->where(function ($q) use ($search) {
                    $q->orWhere('name', 'LIKE', "%$search%");
                    $q->orWhere('api_key', 'LIKE', "%$search%");
                    $q->orWhere('description', 'LIKE', "%$search%");
                });
            })
            ->editColumn('is_active', function ($row) {
                if ($row->is_active) {
                    return '<span class="badge bg-label-success">Active</span>';
                }
                return '<span class="badge bg-label-danger">Inactive</span>';
            })
            ->editColumn('api_key', function ($row) {
                return '<div class="d-flex align-items-center">
                            <code class="me-2">' . e($row->api_key) . '</code>
                            <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill btn-copy" data-clipboard-text="' . e($row->api_key) . '" title="Copy API Key">
                                <i class="ti ti-copy ti-xs"></i>
                            </button>
                        </div>';
            })
            ->editColumn('secret_key', function ($row) {
                return '<div class="d-flex align-items-center">
                            <code class="me-2 secret-text" data-secret="' . e($row->secret_key) . '">••••••••••••••••••••••••••••••••</code>
                            <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill btn-toggle-secret" title="Toggle Secret Key">
                                <i class="ti ti-eye ti-xs"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill btn-copy ms-1" data-clipboard-text="' . e($row->secret_key) . '" title="Copy Secret Key">
                                <i class="ti ti-copy ti-xs"></i>
                            </button>
                        </div>';
            })
            ->editColumn('last_used_at', function ($row) {
                return $row->last_used_at ? $row->last_used_at->format('d/m/Y H:i:s') : '<span class="text-muted">-</span>';
            })
            ->addColumn('action', function ($row) {
                $actionButtons = '
                    <div class="d-inline-block">
                        <a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="ti ti-dots-vertical ti-md"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end m-0">
                            <li>
                                <button class="dropdown-item edit-record-button"
                                    data-id="' . $row->id . '"
                                    data-name="' . e($row->name) . '"
                                    data-description="' . e($row->description) . '"
                                    data-is_active="' . ($row->is_active ? '1' : '0') . '"
                                    >Edit</button>
                            </li>
                            <div class="dropdown-divider"></div>
                            <li>
                                <form class="form-delete-record">
                                ' . method_field('DELETE') . csrf_field() . '
                                    <input type="hidden" name="id" value="' . $row->id . '">
                                    <input type="hidden" name="name" value="' . e($row->name) . '">
                                    <button type="submit" class="dropdown-item text-danger">
                                        Delete
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>';
                return $actionButtons;
            })
            ->rawColumns(['is_active', 'api_key', 'secret_key', 'last_used_at', 'action'])
            ->toJson();
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $credentials = ApiClient::generateCredentials();

            $client = new ApiClient();
            $client->name = $request->name;
            $client->api_key = $credentials['api_key'];
            $client->secret_key = $credentials['secret_key'];
            $client->description = $request->description;
            $client->is_active = $request->has('is_active') || $request->is_active == '1';
            $client->save();

            DB::commit();
            return [
                'status' => true,
                'type' => 'success',
                'message' => 'API Client berhasil dibuat. Kredensial telah digenerate otomatis.'
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'type' => 'error',
                'message' => implode('<br><br>', array_map('implode', $e->errors())),
                'req' => $request->all()
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return [
                'status' => false,
                'type' => 'error',
                'message' => $th->getMessage()
            ];
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $client = ApiClient::findOrFail($request->id);

            $request->validate([
                'id' => 'required|exists:api_clients,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $client->name = $request->name;
            $client->description = $request->description;
            $client->is_active = $request->has('is_active') && $request->is_active == '1';

            if ($request->has('regenerate_secret') && $request->regenerate_secret == '1') {
                $credentials = ApiClient::generateCredentials();
                $client->secret_key = $credentials['secret_key'];
            }

            $client->save();

            DB::commit();
            return [
                'status' => true,
                'type' => 'success',
                'message' => 'API Client berhasil diperbarui.'
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'type' => 'error',
                'message' => implode('<br><br>', array_map('implode', $e->errors())),
                'req' => $request->all()
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return [
                'status' => false,
                'type' => 'error',
                'message' => $th->getMessage()
            ];
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'id' => 'required|exists:api_clients,id',
            ]);

            $client = ApiClient::findOrFail($request->id);
            $client->delete();

            DB::commit();
            return [
                'status' => true,
                'type' => 'success',
                'message' => 'API Client berhasil dihapus.',
            ];
        } catch (\Throwable $th) {
            DB::rollback();
            return [
                'status' => false,
                'type' => 'error',
                'message' => $th->getMessage(),
            ];
        }
    }
}
