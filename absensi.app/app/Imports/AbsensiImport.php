<?php
namespace App\Imports;

use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class AbsensiImport implements ToCollection
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
        $initIndex  = $collection->search(function ($row) use ($initUserId) { // for detect first looping
            return $row[3] == $initUserId;
        });

        $max      = 0;
        $error    = 0;
        $success  = 0;
        $dataTest = [];

        $dateAttendace = $this->getDateAttendance($collection);

        $i          = $initIndex;
        $dataInsert = [];
        do {
            $userIdCollection = $collection->get($i);
            $dateCollection   = $collection->get($i + 2);

            for ($j = $dateAttendace['start']->day; $j <= $dateAttendace['end']->day; $j++) {
                $userId         = $userIdCollection[3];
                $timeAttendance = $dateCollection[$j];
                $timeAttendance = explode("\n", trim($timeAttendance))[0];
                if ($timeAttendance != null) {
                    $checkAttendance = Absensi::where([
                        ['users_id', '=', $userId],
                        ['tgl_absen', '=', $dateAttendace['start']->year . '-' . $dateAttendace['start']->month . '-' . $j],
                    ])->first();

                    if (! $checkAttendance) {
                        $dataInsert[] = [
                            'users_id'   => $userId,
                            'tgl_absen'  => $dateAttendace['start']->year . '-' . $dateAttendace['start']->month . '-' . $j,
                            'pagi'       => $timeAttendance,
                            'latitude'   => 'imported',
                            'longitude'  => 'imported',
                            'keterangan' => 'imported',
                        ];
                        $success++;
                        $max++;
                    } else {
                        $error++;
                        $max++;
                    }
                }
            }
            $i += 3;
        } while ($i < $collection->count());

        Absensi::insert($dataInsert);

        $this->max     = $max;
        $this->error   = $error;
        $this->success = $success;
    }

    public function getDateAttendance(Collection $collection)
    {
        $keyDateCollection = $collection->search(function ($row) {
            if ($row instanceof Collection) {
                foreach ($row as $cell) {
                    if (is_string($cell) && strpos($cell, 'Attendance date:') === 0) {
                        return true;
                    }
                }
            }
            return false;
        });

        $row = $collection[$keyDateCollection];

        $keyDateDetailCollection = $row->search(function ($cell) {
            return is_string($cell) && strpos($cell, 'Attendance date:') !== false;
        });

        $attendanceDate = $collection[$keyDateCollection][$keyDateDetailCollection];
        $parts          = explode(':', $attendanceDate);
        $range          = explode('~', $parts[1]);

        return [
            'start' => Carbon::parse($range[0]),
            'end'   => Carbon::parse($range[1]),
        ];
    }
}
