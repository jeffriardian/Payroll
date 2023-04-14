<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;

class RekapanExportAtm implements FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;

    public function collection()
    {
        $rekapan = DB::select("SELECT @no:=@no+1 nomor, a.id, a.nik, a.nama, a.netto as gaji, b.pph21_terutang_sebulan as pph,
        (a.netto+b.pph21_terutang_sebulan) as gaji_pph from payroll a inner join pph b on a.nik=b.nik,(SELECT @no:= 0) AS no
		  WHERE a.nrp IN (SELECT nrp FROM absensi_payroll_staff WHERE kantor = 'ATM')");

        $data = [];

        foreach ($rekapan as $rekapan){
            $data[] = [
                'id' => $rekapan->nomor,
                'nik' => $rekapan->nik,
                'nama' => $rekapan->nama,
                'gaji' => number_format($rekapan->gaji, 2, ",", "."),
                'tunjangan_pph' => number_format($rekapan->pph, 2, ",", "."),
                'gaji_pph' => number_format($rekapan->gaji_pph, 2, ",", ".")
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'No. ',
            'NIK',
            'Nama',
            'Gaji',
            'Tunjangan PPH',
            'Gaji dan Tunjangan PPH',
        ];
    }
}
