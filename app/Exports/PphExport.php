<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;

class PphExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;

    public function collection()
    {
        $pph = DB::select('select a.id, a.nik, a.nama,
        (select b.jumlah from pph_detail b where a.id=b.pph_id and b.parameter_id = (select c.id from parameter_pph c where c.parameter_code = "jumlahgapok")) as jumlahgapok,
        (select b.jumlah from pph_detail b where a.id=b.pph_id and b.parameter_id = (select c.id from parameter_pph c where c.parameter_code = "premihadir")) as premihadir,
        (select b.jumlah from pph_detail b where a.id=b.pph_id and b.parameter_id = (select c.id from parameter_pph c where c.parameter_code = "premiprod")) as premiprod,
        (select b.jumlah from pph_detail b where a.id=b.pph_id and b.parameter_id = (select c.id from parameter_pph c where c.parameter_code = "uangjabatan")) as uangjabatan,
        (select b.jumlah from pph_detail b where a.id=b.pph_id and b.parameter_id = (select c.id from parameter_pph c where c.parameter_code = "totallembur")) as totallembur,
        (select b.jumlah from pph_detail b where a.id=b.pph_id and b.parameter_id = (select c.id from parameter_pph c where c.parameter_code = "tlain")) as tlain,
        (select b.jumlah from pph_detail b where a.id=b.pph_id and b.parameter_id = (select c.id from parameter_pph c where c.parameter_code = "bonus")) as bonus,
        (select b.jumlah from pph_detail b where a.id=b.pph_id and b.parameter_id = (select c.id from parameter_pph c where c.parameter_code = "bpjstkper")) as bpjstkper,
        (select b.jumlah from pph_detail b where a.id=b.pph_id and b.parameter_id = (select c.id from parameter_pph c where c.parameter_code = "pensiunper")) as pensiunper,
        (select b.jumlah from pph_detail b where a.id=b.pph_id and b.parameter_id = (select c.id from parameter_pph c where c.parameter_code = "bpjskesper")) as bpjskesper,
        (select b.jumlah from pph_detail b where a.id=b.pph_id and b.parameter_id = (select c.id from parameter_pph c where c.parameter_code = "bpjstkkar")) as bpjstkkar,
        (select b.jumlah from pph_detail b where a.id=b.pph_id and b.parameter_id = (select c.id from parameter_pph c where c.parameter_code = "pensiunkar")) as pensiunkar,
        (select b.jumlah from pph_detail b where a.id=b.pph_id and b.parameter_id = (select c.id from parameter_pph c where c.parameter_code = "bpjskeskar")) as bpjskeskar,
        (select b.jumlah from pph_detail b where a.id=b.pph_id and b.parameter_id = (select c.id from parameter_pph c where c.parameter_code = "biayajabatan")) as biayajabatan,
        (select b.jumlah from pph_detail b where a.id=b.pph_id and b.parameter_id = (select c.id from parameter_pph c where c.parameter_code = "tunjanganpph")) as tunjanganpph
        from pph a');

        $data = [];

        foreach ($pph as $pph){
            $data[] = [
                'id' => $pph->id,
                'nik' => $pph->nik,
                'nama' => $pph->nama,
                'jumlahgapok' => number_format($pph->jumlahgapok),
                'premihadir' => number_format($pph->premihadir),
                'premiprod' => number_format($pph->premiprod),
                'uangjabatan' => number_format($pph->uangjabatan),
                'totallembur' => number_format($pph->totallembur),
                'tlain' => number_format($pph->tlain),
                'bonus' => number_format($pph->bonus),
                'bpjstkper' => number_format($pph->bpjstkper),
                'pensiunper' => number_format($pph->pensiunper),
                'bpjskesper' => number_format($pph->bpjskesper),
                'bpjstkkar' => number_format($pph->bpjstkkar),
                'pensiunkar' => number_format($pph->pensiunkar),
                'bpjskeskar' => number_format($pph->bpjskeskar),
                'biayajabatan' => number_format($pph->biayajabatan),
                'tunjanganpph' => number_format($pph->tunjanganpph)

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
            'Gaji Pokok',
            'Premi Hadir',
            'Uang Prestasi',
            'Uang Jabatan',
            'Total Lembur',
            'Tunjangan Lain',
            'Bonus',
            'BPJS TK (Perusahaan)',
            'Pensiun (Perusahaan)',
            'BPJS Kes (Perusahaan)',
            'BPJS TK (Karyawan)',
            'Pensiun (Karyawan)',
            'BPJS Kes (Karyawan)',
            'Biaya Jabatan',
            'Tunjangan PPH',
        ];
    }
}
