<?php

namespace App\Http\Controllers\Webhook;

use Carbon\Carbon;
use App\Models\Verify;
use App\Models\Absensi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\RealtimeAbsensi;
use App\Models\Device;

class FingerspotController extends Controller
{
    public function index(Request $request)
    {
        $original_data  = file_get_contents('php://input');
        $decodedData   = json_decode($original_data, true);
        $encodedData   = json_encode($decodedData);

        if ($decodedData['type'] == 'attlog') {
            return response()->json($this->realtimeAttLog($decodedData));
        }
    }

    public function realtimeAttLog($decodedData)
    {
        try {
            $data = $decodedData['data'];

            $scanTime = Carbon::parse($data['scan']);
            $tanggal  = $scanTime->format('Y-m-d');
            $waktu    = $scanTime->format('H:i');

            $device = Device::where('cloud_id', $decodedData['cloud_id'])->firstOrFail();
            $verify = Verify::findOrFail($data['verify']);

            $absensi = Absensi::where('users_id', $data['pin'])
                ->where('tgl_absen', $tanggal)
                ->first();

            // =====================================
            // SCAN PERTAMA (BELUM ADA DATA)
            // =====================================
            if (!$absensi) {

                $absensi = new Absensi();
                $absensi->users_id  = $data['pin'];
                $absensi->tgl_absen = $tanggal;
                $absensi->latitude  = $device->name;
                $absensi->longitude = $device->name;
                $absensi->verify_id = $verify->id;
                $absensi->device_id = $device->id;

                // Scan datang normal
                $absensi->pagi = $waktu;
                RealtimeAbsensi::isiKategori($absensi, $scanTime);
                $absensi->save();

                return [
                    'status'  => true,
                    'message' => 'Berhasil absensi datang',
                    'data'    => $absensi
                ];
            }

            // =====================================
            // DATA SUDAH ADA
            // =====================================

            $jamDatang = $absensi->getRawOriginal('pagi');

            // Pulihkan data lama yang belum mempunyai jam datang.
            if (!$jamDatang) {
                $absensi->pagi = $waktu;
                $absensi->sore = null;
                RealtimeAbsensi::isiKategori($absensi, $scanTime);
                $absensi->save();

                return [
                    'status' => true,
                    'message' => 'Berhasil absensi datang',
                    'data' => $absensi,
                ];
            }

            if (!RealtimeAbsensi::memenuhiDurasiMinimumPulang($tanggal, $jamDatang, $waktu)) {
                return [
                    'status' => false,
                    'message' => 'Absensi pulang belum dicatat. Jarak dari absensi datang minimal 2 jam.',
                    'data' => $absensi,
                ];
            }

            // =====================================
            // SCAN PULANG (SELALU UPDATE)
            // =====================================
            $absensi->sore = $waktu;
            RealtimeAbsensi::isiKategori($absensi, $scanTime);
            $absensi->save();

            return [
                'status'  => true,
                'message' => 'Berhasil memperbarui absensi pulang',
                'data'    => $absensi
            ];
        } catch (\Throwable $th) {
            return [
                'status'  => false,
                'message' => $th->getMessage()
            ];
        }
    }
}
