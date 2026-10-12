<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GeofenceLocation;
use Illuminate\Http\Request;

class GeofenceApiController extends Controller
{
    /**
     * List lokasi aktif untuk aplikasi mobile (semua user)
     */
    public function getActiveLocations()
    {
        $locations = GeofenceLocation::where('is_active', true)->get();
        return response()->json([
            'status' => true,
            'message' => 'Berhasil memuat daftar lokasi absensi aktif',
            'data' => $locations
        ]);
    }

    /**
     * List semua lokasi (untuk Admin / Manajemen)
     */
    public function index()
    {
        $locations = GeofenceLocation::latest()->get();
        return response()->json([
            'status' => true,
            'data' => $locations
        ]);
    }

    /**
     * Simpan lokasi baru oleh Admin
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:191',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meter' => 'required|numeric|min:10',
            'is_active' => 'nullable|boolean',
            'keterangan' => 'nullable|string',
        ]);

        $loc = GeofenceLocation::create([
            'nama_lokasi' => $request->nama_lokasi,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius_meter' => $request->radius_meter ?? 100,
            'is_active' => $request->input('is_active', true),
            'keterangan' => $request->keterangan,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Titik lokasi absensi berhasil ditambahkan!',
            'data' => $loc
        ]);
    }

    /**
     * Update lokasi oleh Admin
     */
    public function update(Request $request, $id)
    {
        $loc = GeofenceLocation::findOrFail($id);

        $request->validate([
            'nama_lokasi' => 'required|string|max:191',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meter' => 'required|numeric|min:10',
            'is_active' => 'nullable|boolean',
            'keterangan' => 'nullable|string',
        ]);

        $loc->update([
            'nama_lokasi' => $request->nama_lokasi,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius_meter' => $request->radius_meter,
            'is_active' => $request->input('is_active', $loc->is_active),
            'keterangan' => $request->keterangan,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Titik lokasi absensi berhasil diperbarui!',
            'data' => $loc
        ]);
    }

    /**
     * Hapus lokasi oleh Admin
     */
    public function destroy($id)
    {
        $loc = GeofenceLocation::findOrFail($id);
        $loc->delete();

        return response()->json([
            'status' => true,
            'message' => 'Titik lokasi absensi berhasil dihapus!'
        ]);
    }
}
