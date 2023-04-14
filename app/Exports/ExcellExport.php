<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;

class ExcellExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;

    public function collection()
    {
        $excell = DB::select('select a.id, a.nik, a.nama, a.bagian, a.stapajak, a.gapok, a.premihadir, a.premiprod, a.uangjabatan, a.bonus,
        a.gaji, a.h, a.s, a.i, a.a, a.c, a.off, a.st, a.telat_kurang, a.telat_lebih, a.setengah_hari, a.nilai_s, a.nilai_i, a.nilai_a,
        a.nilai_c, a.nilai_off, a.nilai_st, a.nilai_telat_kurang, a.nilai_telat_lebih, a.nilai_setengah_hari, a.totalpotonganabsensi,
        a.jumlahlembur1, a.tariflembur1, a.uanglembur1, a.jumlahlembur2, a.tariflembur2, a.uanglembur2, a.jumlahlembur2minggu,
        a.tariflembur2minggu, a.uanglembur2minggu, a.jumlahlembur3minggu, a.tariflembur3minggu, a.uanglembur3minggu, a.totallembur,
        a.bruto, a.uangbpjstkper, a.uangbpjskesper, a.uangpensiunper, a.potkoperasi, a.potbpjstkkar, a.potbpjskeskar, a.potpensiunkar,
        a.potlain, a.totalpotongan, a.netto, a.periode_awal, a.periode_akhir from payroll a inner join pph b on a.nik = b.nik
        where MONTH(a.date_created) = MONTH(now()) AND YEAR(a.date_created) = YEAR(now()) group by a.id');

        $data = [];

        foreach ($excell as $excell){
            $data[] = [
                'id' => $excell->id,
                'nik' => $excell->nik,
                'nama' => $excell->nama,
                'bagian' => $excell->bagian,
                'stapajak' => $excell->stapajak,
                'gapok' => number_format($excell->gapok),
                'premihadir' => number_format($excell->premihadir),
                'premiprod' => number_format($excell->premiprod),
                'uangjabatan' => number_format($excell->uangjabatan),
                'bonus' => number_format($excell->bonus),
                'gaji' => number_format($excell->gaji),
                'h' => $excell->h,
                's' => $excell->s,
                'i' => $excell->i,
                'a' => $excell->a,
                'c' => $excell->c,
                'off' => $excell->off,
                'st' => $excell->st,
                'telat_kurang' => $excell->telat_kurang,
                'telat_lebih' => $excell->telat_lebih,
                'setengah_hari' => $excell->setengah_hari,
                'nilai_s' => number_format($excell->nilai_s),
                'nilai_i' => number_format($excell->nilai_i),
                'nilai_a' => number_format($excell->nilai_a),
                'nilai_c' => number_format($excell->nilai_c),
                'nilai_off' => number_format($excell->nilai_off),
                'nilai_st' => number_format($excell->nilai_st),
                'nilai_telat_kurang' => number_format($excell->nilai_telat_kurang),
                'nilai_telat_lebih' => number_format($excell->nilai_telat_lebih),
                'nilai_setengah_hari' => number_format($excell->nilai_setengah_hari),
                'totalpotonganabsensi' => number_format($excell->totalpotonganabsensi),
                'jumlahlembur1' => $excell->jumlahlembur1,
                'tariflembur1' => number_format($excell->tariflembur1),
                'uanglembur1' => number_format($excell->uanglembur1),
                'jumlahlembur2' => $excell->jumlahlembur2,
                'tariflembur2' => number_format($excell->tariflembur2),
                'uanglembur2' => number_format($excell->uanglembur2),
                'jumlahlembur2minggu' => $excell->jumlahlembur2minggu,
                'tariflembur2minggu' => number_format($excell->tariflembur2minggu),
                'uanglembur2minggu' => number_format($excell->uanglembur2minggu),
                'jumlahlembur3minggu' => $excell->jumlahlembur3minggu,
                'tariflembur3minggu' => number_format($excell->tariflembur3minggu),
                'uanglembur3minggu' => number_format($excell->uanglembur3minggu),
                'totallembur' => number_format($excell->totallembur),
                'bruto' => number_format($excell->bruto),
                'uangbpjstkper' => number_format($excell->uangbpjstkper),
                'uangbpjskesper' => number_format($excell->uangbpjskesper),
                'uangpensiunper' => number_format($excell->uangpensiunper),
                'potkoperasi' => number_format($excell->potkoperasi),
                'potbpjstkkar' => number_format($excell->potbpjstkkar),
                'potbpjskeskar' => number_format($excell->potbpjskeskar),
                'potpensiunkar' => number_format($excell->potpensiunkar),
                'potlain' => number_format($excell->potlain),
                'totalpotongan' => number_format($excell->totalpotongan),
                'netto' => number_format($excell->netto),
                'nama1' => $excell->nama,
                // 'periode_awal' => $excell->periode_awal,
                // 'periode_akhir' => $excell->periode_akhir,
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
            'Bagian',
            'Kode PTKP',
            'Gaji Pokok',
            'Premi Hadir',
            'Premi',
            'Uang Jabatan',
            'Bonus',
            'Gaji',
            'H',
            'S',
            'I',
            'A',
            'C',
            'OFF',
            'ST',
            'Telat <= 2 Jam',
            'Telat > 2 Jam',
            'Setengah Hari',
            'Nilai S',
            'Nilai I',
            'Nilai A',
            'Nilai C',
            'Nilai OFF',
            'Nilai ST',
            'Nilai Telat <= 2 Jam',
            'Nilai Telat > 2 Jam',
            'Nilai Setengah Hari',
            'Potongan Absensi',
            'Jumlah L1',
            'Tarif L1',
            'Uang L1',
            'Jumlah L2',
            'Tarif L2',
            'Uang L2',
            'Jumlah L2minggu',
            'Tarif L2minggu',
            'Uang L2minggu',
            'Jumlah L3minggu',
            'Tarif L3minggu',
            'Uang L3minggu',
            'Total Lembur',
            'Bruto',
            'BPJS TK (Perusahaan)',
            'BPJS Kes (Perusahaan)',
            'Pesiun (Perusahaan)',
            'Koperasi',
            'BPJS TK (Karyawan)',
            'BPJS Kes (Karyawan)',
            'Pesiun (Karyawan)',
            'Potongan Lain',
            'Total Potongan',
            'Netto',
            'Nama1',
            // 'Periode Awal',
            // 'Periode Akhir',
        ];
    }
}
