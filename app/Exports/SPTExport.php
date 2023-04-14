<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;

class SPTExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;

    public function collection()
    {
        $spt = DB::select('select a.id, a.nik, a.nama, a.periode_tahun, a.pph21_terutang_setahun, a.pph21_dibayar,
        (select min(CONVERT(b.periode_bulan, unsigned)) from pph b where b.periode_tahun = a.periode_tahun and a.nik = b.nik) as awal,
        (select max(CONVERT(b.periode_bulan, unsigned)) from pph b where b.periode_tahun = a.periode_tahun and a.nik = b.nik) as akhir,
        (a.pph21_terutang_setahun-(select sum(b.pph21_terutang_sebulan) from pph b where b.periode_tahun = a.periode_tahun and b.nik = a.nik)) as selisih,
        (select b.pph21_terutang_sebulan from pph b where b.periode_bulan = "1" and b.periode_tahun = a.periode_tahun and a.nik = b.nik) as januari,
        (select b.pph21_terutang_sebulan from pph b where b.periode_bulan = "2" and b.periode_tahun = a.periode_tahun and a.nik = b.nik) as februari,
        (select b.pph21_terutang_sebulan from pph b where b.periode_bulan = "3" and b.periode_tahun = a.periode_tahun and a.nik = b.nik) as maret,
        (select b.pph21_terutang_sebulan from pph b where b.periode_bulan = "4" and b.periode_tahun = a.periode_tahun and a.nik = b.nik) as april,
        (select b.pph21_terutang_sebulan from pph b where b.periode_bulan = "5" and b.periode_tahun = a.periode_tahun and a.nik = b.nik) as mei,
        (select b.pph21_terutang_sebulan from pph b where b.periode_bulan = "6" and b.periode_tahun = a.periode_tahun and a.nik = b.nik) as juni,
        (select b.pph21_terutang_sebulan from pph b where b.periode_bulan = "7" and b.periode_tahun = a.periode_tahun and a.nik = b.nik) as juli,
        (select b.pph21_terutang_sebulan from pph b where b.periode_bulan = "8" and b.periode_tahun = a.periode_tahun and a.nik = b.nik) as agustus,
        (select b.pph21_terutang_sebulan from pph b where b.periode_bulan = "9" and b.periode_tahun = a.periode_tahun and a.nik = b.nik) as september,
        (select b.pph21_terutang_sebulan from pph b where b.periode_bulan = "10" and b.periode_tahun = a.periode_tahun and a.nik = b.nik) as oktober,
        (select b.pph21_terutang_sebulan from pph b where b.periode_bulan = "11" and b.periode_tahun = a.periode_tahun and a.nik = b.nik) as november,
        (select b.pph21_terutang_sebulan from pph b where b.periode_bulan = "12" and b.periode_tahun = a.periode_tahun and a.nik = b.nik) as desember
        from spt a');

        $data = [];

        foreach ($spt as $spt){
            $data[] = [
                'id' => $spt->id,
                'nik' => $spt->nik,
                'nama' => $spt->nama,
                'awal' => $spt->awal,
                'akhir' => $spt->akhir,
                '1721a1' => number_format($spt->pph21_terutang_setahun),
                '1721bulanan' => number_format($spt->pph21_dibayar),
                'selisih' => $spt->selisih,
                'januari' => number_format($spt->januari),
                'februari' => number_format($spt->februari),
                'maret' => number_format($spt->maret),
                'april' => number_format($spt->april),
                'mei' => number_format($spt->mei),
                'juni' => number_format($spt->juni),
                'juli' => number_format($spt->juli),
                'agustus' => number_format($spt->agustus),
                'september' => number_format($spt->september),
                'oktober' => number_format($spt->oktober),
                'november' => number_format($spt->november),
                'desember' => number_format($spt->desember)

            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'No. ',
            'NIK',
            'Nama Pegawai',
            'Awal',
            'Akhir',
            '1721-A1',
            '1721-I Bulanan',
            'Selisih',
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        ];
    }
}
