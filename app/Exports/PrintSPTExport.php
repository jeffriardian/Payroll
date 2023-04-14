<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;

class PrintSPTExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $no;

    function __construct($id) {
        $this->no = $id;
    }

    use Exportable;

    public function collection()
    {
        $spt = DB::select("SELECT c.id, c.nik, c.nama, c.ptkp_code, c.periode_bulan, c.periode_tahun,

(SELECT sum(jumlah) FROM pph_detail a INNER JOIN parameter_pph b ON a.parameter_id=b.id WHERE
        (b.parameter_code = 'jumlahgapok' OR b.parameter_code = 'premihadir') AND pph_id = c.id) AS salary,

        (SELECT sum(jumlah) FROM pph_detail a INNER JOIN parameter_pph b ON a.parameter_id=b.id WHERE
        (b.parameter_code = 'totallembur') AND pph_id = c.id) AS allowance,

        (SELECT sum(jumlah) FROM pph_detail a INNER JOIN parameter_pph b ON a.parameter_id=b.id WHERE
        (b.parameter_code = 'absensi') AND pph_id = c.id) AS deduction,

        (SELECT sum(jumlah) FROM pph_detail a INNER JOIN parameter_pph b ON a.parameter_id=b.id WHERE
        (b.parameter_code = 'jkk') AND pph_id = c.id) AS jkk,

        (SELECT sum(jumlah) FROM pph_detail a INNER JOIN parameter_pph b ON a.parameter_id=b.id WHERE
        (b.parameter_code = 'jkm') AND pph_id = c.id) AS jkm,

        (SELECT sum(jumlah) FROM pph_detail a INNER JOIN parameter_pph b ON a.parameter_id=b.id WHERE
        (b.parameter_code = 'bpjskesper') AND pph_id = c.id) AS bpjsk_company,

        (SELECT sum(jumlah) FROM pph_detail a INNER JOIN parameter_pph b ON a.parameter_id=b.id WHERE
        (b.parameter_code = 'tunjanganpph') AND pph_id = c.id) AS tax_allowance,

        (SELECT gross FROM pph WHERE id = c.id) AS gross_monthly,

        (SELECT gross_yearly FROM pph WHERE id = c.id) AS gross_yearly,

        (SELECT position_cost_yearly FROM pph WHERE id = c.id) AS position_cost_yearly,

        (SELECT SUM(potjhtkar) FROM payroll WHERE nik = c.nik AND YEAR(date_created) = c.periode_tahun) AS jht_employee_yearly,

        (SELECT SUM(potpensiunkar) FROM payroll WHERE nik = c.nik AND YEAR(date_created) = c.periode_tahun) AS jp_employee_yearly,

        (SELECT netto_yearly FROM pph WHERE id = c.id) AS netto_yearly,

        (SELECT ptkp FROM pph WHERE id = c.id) AS ptkp,

        (SELECT pkp FROM pph WHERE id = c.id) AS pkp,

        (SELECT pph21_terutang_setahun FROM pph WHERE id = c.id) AS pph_yearly,

        (SELECT pph21_terutang_sebulan FROM pph WHERE id = c.id) AS pph_monthly,

        (SELECT sum(jumlah) FROM pph_detail a INNER JOIN parameter_pph b ON a.parameter_id=b.id WHERE
        (b.parameter_code = 'bonus') AND pph_id = c.id) AS bonus,

        (SELECT sum(jumlah) FROM pph_detail a INNER JOIN parameter_pph b ON a.parameter_id=b.id WHERE
        (b.parameter_code = 'thr') AND pph_id = c.id) AS thr

        FROM pph c WHERE c.nik = :id AND c.periode_tahun = 2020",
        array('id'=>$this->no));

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
