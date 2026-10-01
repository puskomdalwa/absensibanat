<?php

namespace App\Imports;

use App\Imports\UserImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MainUserImport implements WithMultipleSheets
{
    protected $request;
    public $error;
    public $success;
    public $max;

    protected $userImport;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function sheets(): array
    {
        $this->userImport = new UserImport($this->request);

        return [
            0 => $this->userImport,
        ];
    }

    public function getResult()
    {
        return [
            'error'          => $this->userImport->error ?? 0,
            'success'        => $this->userImport->success ?? 0,
            'created'        => $this->userImport->createdCount ?? 0,
            'updated'        => $this->userImport->updatedCount ?? 0,
            'max'            => $this->userImport->max ?? 0,
            'new_roles'      => $this->userImport->newRolesList ?? [],
            'new_departemen' => $this->userImport->newDepartemenList ?? [],
            'errors_detail'  => $this->userImport->errorsDetail ?? [],
        ];
    }
}
