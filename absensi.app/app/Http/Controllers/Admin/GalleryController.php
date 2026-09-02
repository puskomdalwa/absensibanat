<?php

namespace App\Http\Controllers\Admin;

use App\Models\Gallery;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;

class GalleryController extends Controller
{
    /**
     * Display gallery management page
     */
    public function index()
    {
        return view('admin.gallery.index');
    }

    /**
     * Get gallery data for DataTables
     */
    public function data(Request $request)
    {
        $data = Gallery::orderBy('created_at', 'desc');

        return DataTables::of($data)
            ->addColumn('image_preview', function ($row) {
                return '<img src="' . asset('uploads/gallery/' . $row->filename) . '" class="img-thumbnail" style="max-width: 100px; max-height: 60px;">';
            })
            ->addColumn('action', function ($row) {
                return '
                    <button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                ';
            })
            ->rawColumns(['image_preview', 'action'])
            ->make(true);
    }

    /**
     * Store newly uploaded images
     */
    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120'
        ]);

        $uploadPath = public_path('uploads/gallery');

        // Create directory if not exists
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        $uploaded = 0;

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $originalName = $image->getClientOriginalName();
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                $image->move($uploadPath, $filename);

                Gallery::create([
                    'filename' => $filename,
                    'original_name' => $originalName,
                    'caption' => $request->caption ?? null,
                ]);

                $uploaded++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => $uploaded . ' foto berhasil diupload!'
        ]);
    }

    /**
     * Delete a gallery image
     */
    public function destroy(Request $request)
    {
        $gallery = Gallery::find($request->id);

        if (!$gallery) {
            return response()->json([
                'success' => false,
                'message' => 'Foto tidak ditemukan!'
            ], 404);
        }

        // Delete file from storage
        $filePath = public_path('uploads/gallery/' . $gallery->filename);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        $gallery->delete();

        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil dihapus!'
        ]);
    }

    /**
     * Get latest images for marquee (public API)
     */
    public function getLatest(Request $request)
    {
        $limit = $request->get('limit', 15);

        $images = Gallery::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'url' => asset('uploads/gallery/' . $item->filename),
                    'caption' => $item->caption,
                ];
            });

        return response()->json($images);
    }
}
