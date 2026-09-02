<?php

namespace App\Http\Controllers\Api;

use App\Models\Absensi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AbsensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'kode' => 'nullable',
            'search' => 'nullable',
            'sort_key' => 'nullable',
            'sort_order' => 'nullable',
            'limit' => 'nullable',
        ]);

        $query = Absensi::query();
        $query->select('absensi.*', 'users.name as users_name', 'users.username as users_username', 'users.kode as users_kode', 'users.kode as kode_user', 'users.kode', 'device.name as device_name', 'verify.name as verify_name');
        
        $query->join('users', 'users.id', '=', 'absensi.users_id');
        $query->join('verify', 'verify.id', '=', 'absensi.verify_id');
        $query->join('device', 'device.id', '=', 'absensi.device_id');

        if ($request->filled('kode')) {
            $query->where('users.kode', $request->input('kode'));
        }

        // SEARCH
        if ($request->filled('search')) {
            $term = trim((string) $request->input('search'));
            $query->where(function ($q) use ($term) {
                $q->orWhere('users.name', 'LIKE', "%{$term}%");
                $q->orWhere('users.kode', 'LIKE', "%{$term}%");
                $q->orWhere('users.username', 'LIKE', "%{$term}%");
                $q->orWhere('device.name', 'LIKE', "%{$term}%");
                $q->orWhere('verify.name', 'LIKE', "%{$term}%");
                $q->orWhere('absensi.tgl_absen', 'LIKE', "%{$term}%");
            });
        }

        $sortKey = $request->input('sort_key', 'id');
        $sortOrder = $request->input('sort_order', 'asc');

        $query->orderBy($sortKey, $sortOrder);
        $data = $query->paginate((int) $request->get('limit', 10));

        return response()->json([
            'status'  => true,
            'data'    => $data,
            'message' => 'Absensi retrieved successfully',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
