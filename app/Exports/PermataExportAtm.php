<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;

class PermataExportAtm implements FromCollection, ShouldAutoSize
{
    use Exportable;

    public function collection()
    {
        $permata = DB::select('select "PERMATA" as bank, "" as column2, "" as column3, "" as column4, a.nama,
        (SELECT DISTINCT(norek) FROM absensi_payroll_staff WHERE nrp=a.nrp) as norek, "IDR" as idr,
        a.netto, concat("GAJI", " ", DATE_FORMAT(a.date_created, "%d-%m-%Y")) as tgl, "" as column10, "" as column11, "OVB" as column12,
        "0" as column13, "0" as column14 from payroll a WHERE a.nrp IN (SELECT nrp FROM absensi_payroll_staff WHERE kantor = "ATM")');

        $data = [];

        foreach ($permata as $permata){
            $data[] = [
                'bank' => $permata->bank,
                'column2' => $permata->column2,
                'column3' => $permata->column3,
                'column4' => $permata->column4,
                'nama' => $permata->nama,
                'norek' => $permata->norek,
                'idr' => $permata->idr,
                'netto' => $permata->netto,
                'tgl' => $permata->tgl,
                'column10' => $permata->column10,
                'column11' => $permata->column11,
                'column12' => $permata->column12,
                'column13' => $permata->column13,
                'column14' => $permata->column14
            ];
        }

        return collect($data);
    }
}
