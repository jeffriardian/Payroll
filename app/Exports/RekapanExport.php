<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;

class RekapanExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;

    public function collection()
    {
        $rekapan = DB::select('select a.id, a.nik, a.nama, a.netto as gaji, b.pph21_terutang_sebulan as pph,
        (a.netto+b.pph21_terutang_sebulan) as gaji_pph from payroll a inner join pph b on a.nik=b.nik order by a.id');

        $data = [];

        foreach ($rekapan as $rekapan){
            $data[] = [
                'id' => $rekapan->id,
                'nik' => $rekapan->nik,
                'nama' => $rekapan->nama,
                'gaji' => number_format($rekapan->gaji),
                'tunjangan_pph' => number_format($rekapan->pph),
                'gaji_pph' => number_format($rekapan->gaji_pph)
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
