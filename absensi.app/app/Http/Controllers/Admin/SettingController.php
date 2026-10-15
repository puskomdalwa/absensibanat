<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Tampilkan halaman pengaturan sistem (khusus Superadmin).
     */
    public function index()
    {
        $mode = Setting::getNominalDisplayMode();
        $isVisible = Setting::isNominalVisible();

        return view('admin.setting.index', compact('mode', 'isVisible'));
    }

    /**
     * Perbarui konfigurasi tampilan nominal honorarium.
     */
    public function updateNominal(Request $request)
    {
        $request->validate([
            'mode' => 'required|in:show,hide,admin_only',
        ]);

        $mode = $request->input('mode');
        Setting::setNominalDisplayMode($mode);

        $messages = [
            'show' => 'Nominal honorarium berhasil diaktifkan untuk SEMUA pengguna di semua halaman.',
            'hide' => 'Nominal honorarium berhasil DISEMBUNYIKAN untuk SEMUA pengguna di semua halaman.',
            'admin_only' => 'Nominal honorarium berhasil disembunyikan untuk Civitas (Hanya Superadmin & Admin yang dapat melihat).',
        ];

        $message = $messages[$mode] ?? 'Pengaturan tampilan nominal berhasil diperbarui.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'mode' => $mode,
                'is_visible' => Setting::isNominalVisible(),
                'message' => $message,
            ]);
        }

        return redirect()->route('admin.setting.index')->with('success', $message);
    }

    /**
     * Saklar cepat (Quick toggle) 1-klik untuk Tampilkan / Sembunyikan Nominal.
     */
    public function toggleNominal(Request $request)
    {
        $currentMode = Setting::getNominalDisplayMode();
        // Jika sedang hide -> ubah ke show. Jika show/admin_only -> ubah ke hide.
        $newMode = ($currentMode === 'hide') ? 'show' : 'hide';

        Setting::setNominalDisplayMode($newMode);

        $message = ($newMode === 'show')
            ? 'Nominal honorarium berhasil DITAMPILKAN untuk seluruh pengguna.'
            : 'Nominal honorarium berhasil DISEMBUNYIKAN untuk seluruh pengguna dan semua halaman.';

        return response()->json([
            'success' => true,
            'mode' => $newMode,
            'is_visible' => Setting::isNominalVisible(),
            'message' => $message,
        ]);
    }
}
