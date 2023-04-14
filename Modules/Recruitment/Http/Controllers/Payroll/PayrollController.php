<?php

namespace Modules\Recruitment\Http\Controllers\Payroll;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Recruitment\Entities\Payroll;
use Modules\Recruitment\Entities\PeriodePayroll;
use Modules\Recruitment\Entities\pph;
use Modules\Recruitment\Entities\pphdetail;
use Modules\Recruitment\Entities\PphSalary;
use Modules\Recruitment\Entities\PphBonus;
use Modules\Recruitment\Entities\PphResign;
use Modules\Recruitment\Entities\PphMasa;
use Modules\Recruitment\Entities\AbsensiPayrollStaff;
use Maatwebsite\Excel\Facades\Excel;
// use Barryvdh\DomPDF\PDF;
use Barryvdh\DomPDF\Facade as PDF;
use App\Imports\AbsensiPayrollStaffImport;

use App\Exports\RekapanExportSmm;
use App\Exports\RekapanExportAtm;

use App\Exports\PermataExportSmm;
use App\Exports\PermataExportAtm;

use App\Exports\ExcellExportSmm;
use App\Exports\ExcellExportAtm;

use App\Exports\FullExportSmm;
use App\Exports\FullExportAtm;

use App\Exports\PphExportSmm;
use App\Exports\PphExportAtm;
use Modules\Recruitment\Entities\PphThrBonus;

// use Maatwebsite\Excel\Excel as ExcelExcel;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('recruitment::payroll.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('recruitment::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'date_created' => 'required',
            'periode_awal' => 'required',
            'periode_akhir' => 'required',
            'company' => 'required',
        ]);

        $data_periode = [
            'periode_awal' => $request->input('periode_awal'),
            'periode_akhir' => $request->input('periode_akhir'),
            'date_created' => $request->input('date_created'),
            'kodeprs' => $request->input('company'),
        ];

        DB::beginTransaction();
        try {
            $save = PeriodePayroll::create($data_periode);
            DB::commit();

            return response()->json(['message' => __('Proses Payroll Berhasil.')]);
        }catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function import(Request $request)
    {
        // return ['st'=>'err'];

        Excel::import(new AbsensiPayrollStaffImport, $request->file('file'));
        // return response()->json(['message' => __('Berhasil import payroll gaji.')]);

        // $tanggal1 = $request->input('date_created');
        // $tanggal2 = $request->input('date_created');
        $bulan1 = $request->input('bulan');
        $bulan2 = $request->input('bulan');
        $bulan3 = $request->input('bulan');
        $tahun1 = $request->input('tahun');
        $tahun2 = $request->input('tahun');
        $tahun3 = $request->input('tahun');
        $tahun4 = $request->input('tahun');
        $tahun5 = $request->input('tahun');
        $periode = $this->getPeriode($bulan1, $bulan2, $bulan3, $tahun1, $tahun2, $tahun3, $tahun4, $tahun5);

        if (!empty($periode)) {
            foreach ($periode as $periode) {

                $data_periode = [
                    'date_created' =>$periode->date_created,
                    'periode_awal' => $periode->awal,
                    'periode_akhir' => $periode->akhir,
                ];

                DB::beginTransaction();
                try {
                    $save = PeriodePayroll::create($data_periode);

                    //start payroll process
                    $gaji = $this->getGaji();

                    if (!empty($gaji)) {
                        foreach ($gaji as $gaji) {
                            $idemployee = $gaji->nrp;
                            $startdate = $periode->awal;
                            $enddate = $periode->akhir;
                            $createdate = $periode->date_created;

                            $totalgaji              = ($gaji->gapok)+($gaji->uang_hadir)+($gaji->uang_prestasi);

                            $nilaisakit             = ($gaji->uang_hadir/26)*($gaji->sakit);
                            $nilaiijin              = (($gaji->gapok+$gaji->uang_hadir)/26)*$gaji->ijin;
                            $nilaialpa              = (($gaji->gapok+$gaji->uang_hadir)/26)*$gaji->alpa;
                            $nilaioff               = (($gaji->gapok+$gaji->uang_hadir)/26)*$gaji->off;
                            $nilaist                = (($gaji->gapok+$gaji->uang_hadir)/26)*$gaji->st;
                            $nilaitelatlebih2jam    = ($gaji->gapok/26/2)*$gaji->telat_lebih;
                            $nilaisetengahhari      = ($gaji->gapok/26/2)*$gaji->setengah_hari;
                            $totalpotonganabsensi   = $nilaisakit+$nilaiijin+$nilaialpa+$nilaioff+$nilaist+$nilaitelatlebih2jam+$nilaisetengahhari;

                            $l1                     = $gaji->l1;
                            $l2                     = $gaji->l2;
                            $l2minggu               = $gaji->l2minggu;
                            $l3minggu               = $gaji->l3minggu;

                            if ($gaji->jenis_gaji   = "Lemburan") {
                                $tarifl1            = (($gaji->gapok)*1.5)/173;
                                $tarifl2            = (($gaji->gapok)*2)/173;
                                $tarifl2minggu      = (($gaji->gapok)*2)/173;
                                $tarifl3minggu      = (($gaji->gapok)*3)/173;
                            }else{
                                $tarifl1            = 0;
                                $tarifl2            = 0;
                                $tarifl2minggu      = 0;
                                $tarifl3minggu      = 0;
                            }

                            $nilail1                = $l1*$tarifl1;
                            $nilail2                = $l2*$tarifl2;
                            $nilail2minggu          = $l2minggu*$tarifl2minggu;
                            $nilail3minggu          = $l3minggu*$tarifl3minggu;

                            $totallembur            = $nilail1+$nilail2+$nilail2minggu+$nilail3minggu;

                            $bruto                  = ($totalgaji) + ($totallembur);
                            $totalpotongan          = ($totalpotonganabsensi)+($gaji->koperasi)+($gaji->jht)+($gaji->pensiun)+($gaji->bpjskeskar);
                            $netto                  = $bruto - $totalpotongan;

                            $payrollcheck = $this->checkPayroll( $idemployee,  $startdate, $enddate, $createdate);

                            if (empty($payrollcheck)) {
                                    $data_gaji = [
                                        'notrans'               => $save->id,
                                        'kantor'                => $gaji->kantor,
                                        'bagian'                => $gaji->bagian,
                                        'nrp'                   => $gaji->nrp,
                                        'nik'                   => $gaji->nik,
                                        'nama'                  => $gaji->nama,
                                        'stapajak'              => $gaji->stapajak,
                                        'npwp'                  => $gaji->npwp,
                                        'jumlahhari'            => $gaji->hari,
                                        'gapok'                 => $gaji->gapok,
                                        'premihadir'            => $gaji->uang_hadir,
                                        'premiprod'             => $gaji->uang_prestasi,
                                        'uangjabatan'           => '0',
                                        'bonus'                 => '0',
                                        'gaji'                  => $totalgaji,
                                        'jkm'                   => $gaji->jkm,
                                        'jkk'                   => $gaji->jkk,
                                        'bpjskes'               => $gaji->bpjskes,
                                        'bpjskeskar'            => $gaji->bpjskeskar,
                                        'h'                     => $gaji->hari,
                                        's'                     => $gaji->sakit,
                                        'i'                     => $gaji->ijin,
                                        'a'                     => $gaji->alpa,
                                        'c'                     => $gaji->cuti,
                                        'off'                   => $gaji->off,
                                        'st'                    => $gaji->st,
                                        'telat_kurang'          => $gaji->telat_kurang,
                                        'telat_lebih'           => $gaji->telat_lebih,
                                        'setengah_hari'         => $gaji->setengah_hari,
                                        'nilai_s'               => $nilaisakit,
                                        'nilai_i'               => $nilaiijin,
                                        'nilai_a'               => $nilaialpa,
                                        'nilai_c'               => '0',
                                        'nilai_off'             => $nilaioff,
                                        'nilai_st'              => $nilaist,
                                        'nilai_telat_kurang'    => '0',
                                        'nilai_telat_lebih'     => $nilaitelatlebih2jam,
                                        'nilai_setengah_hari'   => $nilaisetengahhari,
                                        'totalpotonganabsensi'  => $totalpotonganabsensi,
                                        'jumlahlembur1'         => $l1,
                                        'tariflembur1'          => $tarifl1,
                                        'uanglembur1'           => $nilail1,
                                        'jumlahlembur2'         => $l2,
                                        'tariflembur2'          => $tarifl2,
                                        'uanglembur2'           => $nilail2,
                                        'jumlahlembur2minggu'   => $l2minggu,
                                        'tariflembur2minggu'    => $tarifl2minggu,
                                        'uanglembur2minggu'     => $nilail2minggu,
                                        'jumlahlembur3minggu'   => $l3minggu,
                                        'tariflembur3minggu'    => $tarifl3minggu,
                                        'uanglembur3minggu'     => $nilail3minggu,
                                        'totallembur'           => $totallembur,
                                        'bruto'                 => $bruto,
                                        'potkoperasi'           => $gaji->koperasi,
                                        'potjhtkar'             => $gaji->jht,
                                        'potpensiunkar'         => $gaji->pensiun,
                                        'potlain'               => '0',
                                        'totalpotongan'         => $totalpotongan,
                                        'netto'                 => $netto,
                                        'norek'                 => $gaji->norek,
                                        'periode_awal'          => $periode->awal,
                                        'periode_akhir'         => $periode->akhir,
                                        'date_created'          => $periode->date_created,
                                    ];

                                    Payroll::create($data_gaji);
                            }else
                            if (!empty($payrollcheck)) {
                                foreach ($payrollcheck as $payrollcheck) {
                                    $data_gaji = [
                                        'notrans'               => $save->id,
                                        'kantor'                => $gaji->kantor,
                                        'bagian'                => $gaji->bagian,
                                        'nrp'                   => $gaji->nrp,
                                        'nik'                   => $gaji->nik,
                                        'nama'                  => $gaji->nama,
                                        'stapajak'              => $gaji->stapajak,
                                        'npwp'                  => $gaji->npwp,
                                        'jumlahhari'            => $gaji->hari,
                                        'gapok'                 => $gaji->gapok,
                                        'premihadir'            => $gaji->uang_hadir,
                                        'premiprod'             => $gaji->uang_prestasi,
                                        'uangjabatan'           => '0',
                                        'bonus'                 => '0',
                                        'gaji'                  => $totalgaji,
                                        'jkm'                   => $gaji->jkm,
                                        'jkk'                   => $gaji->jkk,
                                        'bpjskes'               => $gaji->bpjskes,
                                        'bpjskeskar'            => $gaji->bpjskeskar,
                                        'h'                     => $gaji->hari,
                                        's'                     => $gaji->sakit,
                                        'i'                     => $gaji->ijin,
                                        'a'                     => $gaji->alpa,
                                        'c'                     => $gaji->cuti,
                                        'off'                   => $gaji->off,
                                        'st'                    => $gaji->st,
                                        'telat_kurang'          => $gaji->telat_kurang,
                                        'telat_lebih'           => $gaji->telat_lebih,
                                        'setengah_hari'         => $gaji->setengah_hari,
                                        'nilai_s'               => $nilaisakit,
                                        'nilai_i'               => $nilaiijin,
                                        'nilai_a'               => $nilaialpa,
                                        'nilai_c'               => '0',
                                        'nilai_off'             => $nilaioff,
                                        'nilai_st'              => $nilaist,
                                        'nilai_telat_kurang'    => '0',
                                        'nilai_telat_lebih'     => $nilaitelatlebih2jam,
                                        'nilai_setengah_hari'   => $nilaisetengahhari,
                                        'totalpotonganabsensi'  => $totalpotonganabsensi,
                                        'jumlahlembur1'         => $l1,
                                        'tariflembur1'          => $tarifl1,
                                        'uanglembur1'           => $nilail1,
                                        'jumlahlembur2'         => $l2,
                                        'tariflembur2'          => $tarifl2,
                                        'uanglembur2'           => $nilail2,
                                        'jumlahlembur2minggu'   => $l2minggu,
                                        'tariflembur2minggu'    => $tarifl2minggu,
                                        'uanglembur2minggu'     => $nilail2minggu,
                                        'jumlahlembur3minggu'   => $l3minggu,
                                        'tariflembur3minggu'    => $tarifl3minggu,
                                        'uanglembur3minggu'     => $nilail3minggu,
                                        'totallembur'           => $totallembur,
                                        'bruto'                 => $bruto,
                                        'potkoperasi'           => $gaji->koperasi,
                                        'potjhtkar'             => $gaji->jht,
                                        'potpensiunkar'         => $gaji->pensiun,
                                        'potlain'               => '0',
                                        'totalpotongan'         => $totalpotongan,
                                        'netto'                 => $netto,
                                        'norek'                 => $gaji->norek,
                                        'periode_awal'          => $periode->awal,
                                        'periode_akhir'         => $periode->akhir,
                                        'date_created'          => $periode->date_created,
                                    ];

                                    Payroll::whereId($payrollcheck->id)->update($data_gaji);
                                }
                            }

                        }
                    }

                    DB::table('absensi_payroll_staff')->update(['status_payroll_staff' => '1']);
                    //end payroll process

                    // start pph process
                    $tglpph = $periode->date_created;

                    //perhitungan pph di akhir tahun
                    if ($bulan1 == '12') {
                        $gajistaff = $this->getGajiStaffTahunan($bulan1, $tahun1);

                        if (!empty($gajistaff)) {
                            foreach ($gajistaff as $gajistaff) {
                                $idemployee = $gajistaff->nik;

                                $pphcheck = $this->checkPph($idemployee,  $bulan1, $tahun1);

                                //Jika belum ada data PPH
                                if (empty($pphcheck)) {

                                    $data_pph = [
                                        'nrp'                       => $gajistaff->nrp,
                                        'nik'                       => $gajistaff->nik,
                                        'nama'                      => $gajistaff->nama,
                                        'ptkp_code'                 => $gajistaff->stapajak,
                                        'periode_bulan'             => $bulan1,
                                        'periode_tahun'             => $tahun1,
                                    ];

                                    $save = pph::create($data_pph);

                                    $parameterpph = $this->getParameterPph();

                                    if (!empty($parameterpph)) {
                                        foreach ($parameterpph as $parameterpph) {
                                            $code = $parameterpph->parameter_code;

                                            $data_detail_pph = [
                                                'pph_id'        => $save->id,
                                                'parameter_id'  => $parameterpph->id,
                                                'jumlah'        => $gajistaff->$code,
                                            ];

                                            pphdetail::create($data_detail_pph);
                                        }
                                    }

                                    $pphid = $save->id;

                                    //Perhitungan PPH Salary
                                    //Tahap 1 Salary
                                    $datasalary = $this->getDataSalary($pphid);

                                    if (!empty($datasalary)) {
                                        foreach ($datasalary as $datasalary) {
                                            $ptkp                           = $datasalary->ptkp;
                                            $salary                         = $datasalary->salary;
                                            $salary_deduction               = $datasalary->salary_deduction;
                                            $premi                          = $datasalary->premi;
                                            $jht                            = $datasalary->jht;

                                            $gross_yearly = (12*($salary+$premi-$salary_deduction));

                                            if ((0.05)*($gross_yearly) >= 6000000){
                                                $position_cost_yearly = 6000000;
                                            }else if ((0.05)*($gross_yearly) < 6000000){
                                                $position_cost_yearly = (0.05)*($gross_yearly);
                                            }

                                            $netto_yearly = ($gross_yearly) - ($position_cost_yearly) - (12*$jht);

                                            $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                            if ($pkp <= 0){
                                                $tax_allowance_yearly = 0;
                                            }else if ($pkp > 405000000){
                                                $tax_allowance_yearly = ROUND((($pkp - 405000000)*(30/70)+95000000));
                                            }else if ($pkp > 217500000 && $pkp < 405000000){
                                                $tax_allowance_yearly = ROUND((($pkp - 217500000)*(25/75)+32500000));
                                            }else if ($pkp > 47500000 && $pkp < 217500000){
                                                $tax_allowance_yearly = ROUND((($pkp - 47500000)*(15/85)+2500000));
                                            }else if ($pkp <= 47500000){
                                                $tax_allowance_yearly = ROUND(($pkp)*(5/95));
                                            }

                                            $pphsalaryid = $this->GetPphSalaryId($pphid);

                                            if (!empty($pphsalaryid)) {
                                                foreach ($pphsalaryid as $pphsalaryid) {
                                                    $detailid = $pphsalaryid->id;

                                                    $data_detail_pph = [
                                                        'pph_id'                => $pphid,
                                                        'tax_allowance_yearly'  => $tax_allowance_yearly,
                                                    ];

                                                    PphSalary::whereId($detailid)->update($data_detail_pph);
                                                }
                                            }else{

                                                $data_detail_pph = [
                                                    'pph_id'                => $pphid,
                                                    'tax_allowance_yearly'  => $tax_allowance_yearly,
                                                ];

                                                PphSalary::create($data_detail_pph);

                                            }
                                        }
                                    }

                                    //Tahap 2 Salary
                                    $datasalary = $this->getDataSalary($pphid);

                                    if (!empty($datasalary)) {
                                        foreach ($datasalary as $datasalary) {
                                            $ptkp                           = $datasalary->ptkp;
                                            $salary                         = $datasalary->salary;
                                            $salary_deduction               = $datasalary->salary_deduction;
                                            $premi                          = $datasalary->premi;
                                            $jht                            = $datasalary->jht;
                                            $tax_allowance_salary_yearly    = $datasalary->tax_allowance_salary_yearly;

                                            $gross_yearly       = (12*($salary+$premi-$salary_deduction));

                                            $gross_yearly_new   = ((12*($salary+$premi-$salary_deduction))+$tax_allowance_salary_yearly);

                                            if ((0.05)*($gross_yearly_new) >= 6000000){
                                                $position_cost_yearly = 6000000;
                                            }else if ((0.05)*($gross_yearly_new) < 6000000){
                                                $position_cost_yearly = (0.05)*($gross_yearly_new);
                                            }

                                            $netto_yearly = ($gross_yearly) - ($position_cost_yearly) - (12*$jht);

                                            $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                            if ($pkp <= 0){
                                                $tax_allowance_yearly = 0;
                                            }else if ($pkp > 405000000){
                                                $tax_allowance_yearly = ROUND((($pkp - 405000000)*(30/70)+95000000));
                                            }else if ($pkp > 217500000 && $pkp < 405000000){
                                                $tax_allowance_yearly = ROUND((($pkp - 217500000)*(25/75)+32500000));
                                            }else if ($pkp > 47500000 && $pkp < 217500000){
                                                $tax_allowance_yearly = ROUND((($pkp - 47500000)*(15/85)+2500000));
                                            }else if ($pkp <= 47500000){
                                                $tax_allowance_yearly = ROUND(($pkp)*(5/95));
                                            }

                                            $pphsalaryid = $this->GetPphSalaryId($pphid);

                                            if (!empty($pphsalaryid)) {
                                                foreach ($pphsalaryid as $pphsalaryid) {
                                                    $detailid = $pphsalaryid->id;

                                                    $data_detail_pph = [
                                                        'pph_id'                => $pphid,
                                                        'tax_allowance_yearly'  => $tax_allowance_yearly,
                                                    ];

                                                    PphSalary::whereId($detailid)->update($data_detail_pph);
                                                }
                                            }
                                        }
                                    }

                                    //Tahap 3 Salary
                                    $datasalary = $this->getDataSalary($pphid);

                                    if (!empty($datasalary)) {
                                        foreach ($datasalary as $datasalary) {
                                            $ptkp                           = $datasalary->ptkp;
                                            $salary                         = $datasalary->salary;
                                            $salary_deduction               = $datasalary->salary_deduction;
                                            $premi                          = $datasalary->premi;
                                            $jht_employee                   = $datasalary->jht_employee;
                                            $jp_employee                    = $datasalary->jp_employee;
                                            $jht                            = $datasalary->jht;
                                            $tax_allowance_salary_yearly    = $datasalary->tax_allowance_salary_yearly;
                                            $ptkp                           = $ptkp;

                                            $gross_yearly       = (12*($salary+$premi-$salary_deduction));

                                            $gross_yearly_new   = ((12*($salary+$premi-$salary_deduction))+$tax_allowance_salary_yearly);

                                            if ((0.05)*($gross_yearly_new) >= 6000000){
                                                $position_cost_yearly = 6000000;
                                            }else if ((0.05)*($gross_yearly_new) < 6000000){
                                                $position_cost_yearly = (0.05)*($gross_yearly_new);
                                            }

                                            $netto_yearly = ($gross_yearly_new) - ($position_cost_yearly) - (12*$jht);

                                            $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                            if ($pkp <= 0){
                                                $pph_yearly = 0;
                                            }else if ($pkp > 500000000){
                                                $pph_yearly = ROUND((0.30*($pkp-500000000))+95000000);
                                            }else if ($pkp > 250000000 && $pkp < 500000000){
                                                $pph_yearly = ROUND((0.25*($pkp-250000000))+32500000);
                                            }else if ($pkp > 50000000 && $pkp < 250000000){
                                                $pph_yearly = ROUND((0.15*($pkp-50000000))+2500000);
                                            }else if ($pkp <= 50000000){
                                                $pph_yearly = ROUND(0.05*$pkp);
                                            }

                                            $pphsalaryid = $this->GetPphSalaryId($pphid);

                                            if (!empty($pphsalaryid)) {
                                                foreach ($pphsalaryid as $pphsalaryid) {
                                                    $detailid = $pphsalaryid->id;

                                                    $data_detail_pph = [
                                                        'pph_id'                => $pphid,
                                                        'tax_allowance_yearly'  => $pph_yearly,
                                                        'tax_allowance'         => ($pph_yearly/12),
                                                        'gross_monthly'         => ($gross_yearly_new/12),
                                                        'gross_yearly'          => $gross_yearly_new,
                                                        'position_cost_yearly'  => $position_cost_yearly,
                                                        'position_cost_monthly' => ($position_cost_yearly/12),
                                                        'jht_employee_yearly'   => (12*$jht_employee),
                                                        'jp_employee_yearly'    => (12*$jp_employee),
                                                        'netto_yearly'          => $netto_yearly,
                                                        'ptkp'                  => $ptkp,
                                                        'pkp'                   => $pkp,
                                                        'pph_yearly'            => $pph_yearly,
                                                        'pph_salary'            => ($pph_yearly/12),
                                                    ];

                                                    PphSalary::whereId($detailid)->update($data_detail_pph);
                                                }
                                            }
                                        }
                                    }

                                    //Perhitungan PPH THR
                                    //Tahap 1 THR
                                    $datasalary = $this->getDataSalary($pphid);

                                    if (!empty($datasalary)) {
                                        foreach ($datasalary as $datasalary) {
                                            $thr = $datasalary->thr;

                                            if ($thr <= 0){

                                                $pphsalaryid = $this->GetPphSalaryId($pphid);

                                                if (!empty($pphsalaryid)) {
                                                    foreach ($pphsalaryid as $pphsalaryid) {
                                                        $detailid = $pphsalaryid->id;

                                                        $data_detail_pph = [
                                                            'pph_id'                    => $pphid,
                                                            'tax_allowance_thr_yearly'  => 0,
                                                            'tax_allowance_thr'         => 0,
                                                            'pph_thr_yearly'            => 0,
                                                            'pph_thr'                   => 0,
                                                        ];

                                                        PphBonus::whereId($detailid)->update($data_detail_pph);
                                                    }
                                                }else{

                                                    $data_detail_pph = [
                                                        'pph_id'                    => $pphid,
                                                        'tax_allowance_thr_yearly'  => 0,
                                                        'tax_allowance_thr'         => 0,
                                                        'pph_thr_yearly'            => 0,
                                                        'pph_thr'                   => 0,
                                                    ];

                                                    PphBonus::create($data_detail_pph);
                                                }
                                            }else if ($thr > 0){
                                                //Tahap 1 THR
                                                $datasalary = $this->getDataSalary($pphid);

                                                if (!empty($datasalary)) {
                                                    foreach ($datasalary as $datasalary) {
                                                        $ptkp                           = $datasalary->ptkp;
                                                        $salary                         = $datasalary->salary;
                                                        $salary_deduction               = $datasalary->salary_deduction;
                                                        $premi                          = $datasalary->premi;
                                                        $jht                            = $datasalary->jht;
                                                        $thr                            = $datasalary->thr;

                                                        $gross_yearly = ((12*($salary+$premi-$salary_deduction))+$thr);

                                                        if ((0.05)*($gross_yearly) >= 6000000){
                                                            $position_cost_yearly = 6000000;
                                                        }else if ((0.05)*($gross_yearly) < 6000000){
                                                            $position_cost_yearly = (0.05)*($gross_yearly);
                                                        }

                                                        $netto_yearly = ($gross_yearly) - ($position_cost_yearly) - (12*$jht);

                                                        $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                                        if ($pkp <= 0){
                                                            $tax_allowance_yearly = 0;
                                                        }else if ($pkp > 405000000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 405000000)*(30/70)+95000000));
                                                        }else if ($pkp > 217500000 && $pkp < 405000000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 217500000)*(25/75)+32500000));
                                                        }else if ($pkp > 47500000 && $pkp < 217500000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 47500000)*(15/85)+2500000));
                                                        }else if ($pkp <= 47500000){
                                                            $tax_allowance_yearly = ROUND(($pkp)*(5/95));
                                                        }

                                                        $pphbonusid = $this->GetPphBonusId($pphid);

                                                        if (!empty($pphbonusid)) {
                                                            foreach ($pphbonusid as $pphbonusid) {
                                                                $detailid = $pphbonusid->id;

                                                                $data_detail_pph = [
                                                                    'pph_id'                    => $pphid,
                                                                    'tax_allowance_thr_yearly'  => $tax_allowance_yearly,
                                                                ];

                                                                PphBonus::whereId($detailid)->update($data_detail_pph);
                                                            }
                                                        }else{

                                                            $data_detail_pph = [
                                                                'pph_id'                    => $pphid,
                                                                'tax_allowance_thr_yearly'  => $tax_allowance_yearly,
                                                            ];

                                                            PphBonus::create($data_detail_pph);

                                                        }
                                                    }
                                                }

                                                //Tahap 2 THR
                                                $datasalary = $this->getDataSalary($pphid);

                                                if (!empty($datasalary)) {
                                                    foreach ($datasalary as $datasalary) {
                                                        $ptkp                           = $datasalary->ptkp;
                                                        $salary                         = $datasalary->salary;
                                                        $salary_deduction               = $datasalary->salary_deduction;
                                                        $premi                          = $datasalary->premi;
                                                        $jht                            = $datasalary->jht;
                                                        $thr                            = $datasalary->thr;
                                                        $tax_allowance_thr_yearly       = $datasalary->tax_allowance_thr_yearly;

                                                        $gross_yearly       = ((12*($salary+$premi-$salary_deduction))+$thr);

                                                        $gross_yearly_new   = ((12*($salary+$premi-$salary_deduction))+$tax_allowance_thr_yearly+$thr);

                                                        if ((0.05)*($gross_yearly_new) >= 6000000){
                                                            $position_cost_yearly = 6000000;
                                                        }else if ((0.05)*($gross_yearly_new) < 6000000){
                                                            $position_cost_yearly = (0.05)*($gross_yearly_new);
                                                        }

                                                        $netto_yearly = ($gross_yearly) - ($position_cost_yearly) - (12*$jht);

                                                        $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                                        if ($pkp <= 0){
                                                            $tax_allowance_yearly = 0;
                                                        }else if ($pkp > 405000000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 405000000)*(30/70)+95000000));
                                                        }else if ($pkp > 217500000 && $pkp < 405000000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 217500000)*(25/75)+32500000));
                                                        }else if ($pkp > 47500000 && $pkp < 217500000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 47500000)*(15/85)+2500000));
                                                        }else if ($pkp <= 47500000){
                                                            $tax_allowance_yearly = ROUND(($pkp)*(5/95));
                                                        }

                                                        $pphsalaryid = $this->GetPphSalaryId($pphid);

                                                        if (!empty($pphsalaryid)) {
                                                            foreach ($pphsalaryid as $pphsalaryid) {
                                                                $detailid = $pphsalaryid->id;

                                                                $data_detail_pph = [
                                                                    'pph_id'                    => $pphid,
                                                                    'tax_allowance_thr_yearly'  => $tax_allowance_yearly,
                                                                ];

                                                                PphBonus::whereId($detailid)->update($data_detail_pph);
                                                            }
                                                        }
                                                    }
                                                }

                                                //Tahap 3 THR
                                                $datasalary = $this->getDataSalary($pphid);

                                                if (!empty($datasalary)) {
                                                    foreach ($datasalary as $datasalary) {
                                                        $ptkp                           = $datasalary->ptkp;
                                                        $salary                         = $datasalary->salary;
                                                        $salary_deduction               = $datasalary->salary_deduction;
                                                        $premi                          = $datasalary->premi;
                                                        $jht_employee                   = $datasalary->jht_employee;
                                                        $jp_employee                    = $datasalary->jp_employee;
                                                        $jht                            = $datasalary->jht;
                                                        $thr                            = $datasalary->thr;
                                                        $tax_allowance_thr_yearly       = $datasalary->tax_allowance_thr_yearly;
                                                        $tax_allowance_salary_yearly    = $datasalary->tax_allowance_salary_yearly;
                                                        $tax_allowance_salary           = $datasalary->tax_allowance_salary;
                                                        $ptkp                           = $ptkp;

                                                        $gross_yearly       = ((12*($salary+$premi-$salary_deduction))+$thr);

                                                        $gross_yearly_new   = ((12*($salary+$premi-$salary_deduction))+$tax_allowance_thr_yearly+$thr);

                                                        if ((0.05)*($gross_yearly_new) >= 6000000){
                                                            $position_cost_yearly = 6000000;
                                                        }else if ((0.05)*($gross_yearly_new) < 6000000){
                                                            $position_cost_yearly = (0.05)*($gross_yearly_new);
                                                        }

                                                        $netto_yearly = ($gross_yearly_new) - ($position_cost_yearly) - (12*$jht);

                                                        $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                                        if ($pkp <= 0){
                                                            $pph_yearly = 0;
                                                            $pph_thr = (($pph_yearly-$tax_allowance_salary_yearly));
                                                        }else if ($pkp > 500000000){
                                                            $pph_yearly = ROUND((0.30*($pkp-500000000))+95000000);
                                                            $pph_thr = (($pph_yearly-$tax_allowance_salary_yearly));
                                                        }else if ($pkp > 250000000 && $pkp < 500000000){
                                                            $pph_yearly = ROUND((0.25*($pkp-250000000))+32500000);
                                                            $pph_thr = (($pph_yearly-$tax_allowance_salary_yearly));
                                                        }else if ($pkp > 50000000 && $pkp < 250000000){
                                                            $pph_yearly = ROUND((0.15*($pkp-50000000))+2500000);
                                                            $pph_thr = (($pph_yearly-$tax_allowance_salary_yearly));
                                                        }else if ($pkp <= 50000000){
                                                            $pph_yearly = ROUND(0.05*$pkp);
                                                            $pph_thr = (($pph_yearly-$tax_allowance_salary_yearly));
                                                        }

                                                        $pphsalaryid = $this->GetPphSalaryId($pphid);

                                                        if (!empty($pphsalaryid)) {
                                                            foreach ($pphsalaryid as $pphsalaryid) {
                                                                $detailid = $pphsalaryid->id;

                                                                $data_detail_pph = [
                                                                    'pph_id'                    => $pphid,
                                                                    'tax_allowance_thr_yearly'  => $pph_yearly,
                                                                    'tax_allowance_thr'         => $pph_thr,
                                                                    'pph_thr_yearly'            => $pph_yearly,
                                                                    'pph_thr'                   => $pph_thr,
                                                                ];

                                                                PphBonus::whereId($detailid)->update($data_detail_pph);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    //Perhitungan PPH Bonus
                                    $datasalary = $this->getDataSalary($pphid);

                                    if (!empty($datasalary)) {
                                        foreach ($datasalary as $datasalary) {
                                            $bonus = $datasalary->bonus;

                                            if ($bonus <= 0){

                                                $pphsalaryid = $this->GetPphSalaryId($pphid);

                                                if (!empty($pphsalaryid)) {
                                                    foreach ($pphsalaryid as $pphsalaryid) {
                                                        $detailid = $pphsalaryid->id;

                                                        $data_detail_pph = [
                                                            'pph_id'                      => $pphid,
                                                            'gross_yearly_bonus'          => 0,
                                                            'position_cost_yearly_bonus'  => 0,
                                                            'position_cost_monthly_bonus' => 0,
                                                            'netto_yearly_bonus'          => 0,
                                                            'tax_allowance_bonus_yearly'  => 0,
                                                            'tax_allowance_bonus'         => 0,
                                                            'pph_bonus_yearly'            => 0,
                                                            'pph_bonus'                   => 0,
                                                        ];

                                                        PphBonus::whereId($detailid)->update($data_detail_pph);
                                                    }
                                                }else{

                                                    $data_detail_pph = [
                                                        'pph_id'                      => $pphid,
                                                        'gross_yearly_bonus'          => 0,
                                                        'position_cost_yearly_bonus'  => 0,
                                                        'position_cost_monthly_bonus' => 0,
                                                        'netto_yearly_bonus'          => 0,
                                                        'tax_allowance_bonus_yearly'  => 0,
                                                        'tax_allowance_bonus'         => 0,
                                                        'pph_bonus_yearly'            => 0,
                                                        'pph_bonus'                   => 0,
                                                    ];

                                                    PphBonus::create($data_detail_pph);

                                                }
                                            }else if ($bonus > 0){

                                                //Tahap 1 Bonus
                                                $datasalary = $this->getDataSalary($pphid);

                                                if (!empty($datasalary)) {
                                                    foreach ($datasalary as $datasalary) {
                                                        $ptkp                           = $datasalary->ptkp;
                                                        $salary                         = $datasalary->salary;
                                                        $salary_deduction               = $datasalary->salary_deduction;
                                                        $premi                          = $datasalary->premi;
                                                        $jht                            = $datasalary->jht;
                                                        $bonus                          = $datasalary->bonus;

                                                        $gross_yearly = ((12*($salary+$premi-$salary_deduction))+$bonus);

                                                        if ((0.05)*($gross_yearly) >= 6000000){
                                                            $position_cost_yearly = 6000000;
                                                        }else if ((0.05)*($gross_yearly) < 6000000){
                                                            $position_cost_yearly = (0.05)*($gross_yearly);
                                                        }

                                                        $netto_yearly = ($gross_yearly) - ($position_cost_yearly) - (12*$jht);

                                                        $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                                        if ($pkp <= 0){
                                                            $tax_allowance_yearly = 0;
                                                        }else if ($pkp > 405000000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 405000000)*(30/70)+95000000));
                                                        }else if ($pkp > 217500000 && $pkp < 405000000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 217500000)*(25/75)+32500000));
                                                        }else if ($pkp > 47500000 && $pkp < 217500000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 47500000)*(15/85)+2500000));
                                                        }else if ($pkp <= 47500000){
                                                            $tax_allowance_yearly = ROUND(($pkp)*(5/95));
                                                        }

                                                        $pphbonusid = $this->GetPphBonusId($pphid);

                                                        if (!empty($pphbonusid)) {
                                                            foreach ($pphbonusid as $pphbonusid) {
                                                                $detailid = $pphbonusid->id;

                                                                $data_detail_pph = [
                                                                    'pph_id'                    => $pphid,
                                                                    'tax_allowance_bonus_yearly'  => $tax_allowance_yearly,
                                                                ];

                                                                PphBonus::whereId($detailid)->update($data_detail_pph);
                                                            }
                                                        }else{

                                                            $data_detail_pph = [
                                                                'pph_id'                    => $pphid,
                                                                'tax_allowance_bonus_yearly'  => $tax_allowance_yearly,
                                                            ];

                                                            PphBonus::create($data_detail_pph);

                                                        }
                                                    }
                                                }

                                                //Tahap 2 Bonus
                                                $datasalary = $this->getDataSalary($pphid);

                                                if (!empty($datasalary)) {
                                                    foreach ($datasalary as $datasalary) {
                                                        $ptkp                           = $datasalary->ptkp;
                                                        $salary                         = $datasalary->salary;
                                                        $salary_deduction               = $datasalary->salary_deduction;
                                                        $premi                          = $datasalary->premi;
                                                        $jht                            = $datasalary->jht;
                                                        $bonus                          = $datasalary->bonus;
                                                        $tax_allowance_bonus_yearly     = $datasalary->tax_allowance_bonus_yearly;

                                                        $gross_yearly       = ((12*($salary+$premi-$salary_deduction))+$bonus);

                                                        $gross_yearly_new   = ((12*($salary+$premi-$salary_deduction))+$tax_allowance_bonus_yearly+$bonus);

                                                        if ((0.05)*($gross_yearly_new) >= 6000000){
                                                            $position_cost_yearly = 6000000;
                                                        }else if ((0.05)*($gross_yearly_new) < 6000000){
                                                            $position_cost_yearly = (0.05)*($gross_yearly_new);
                                                        }

                                                        $netto_yearly = ($gross_yearly) - ($position_cost_yearly) - (12*$jht);

                                                        $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                                        if ($pkp <= 0){
                                                            $tax_allowance_yearly = 0;
                                                        }else if ($pkp > 405000000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 405000000)*(30/70)+95000000));
                                                        }else if ($pkp > 217500000 && $pkp < 405000000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 217500000)*(25/75)+32500000));
                                                        }else if ($pkp > 47500000 && $pkp < 217500000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 47500000)*(15/85)+2500000));
                                                        }else if ($pkp <= 47500000){
                                                            $tax_allowance_yearly = ROUND(($pkp)*(5/95));
                                                        }

                                                        $pphsalaryid = $this->GetPphSalaryId($pphid);

                                                        if (!empty($pphsalaryid)) {
                                                            foreach ($pphsalaryid as $pphsalaryid) {
                                                                $detailid = $pphsalaryid->id;

                                                                $data_detail_pph = [
                                                                    'pph_id'                        => $pphid,
                                                                    'tax_allowance_bonus_yearly'    => $tax_allowance_yearly,
                                                                ];

                                                                PphBonus::whereId($detailid)->update($data_detail_pph);
                                                            }
                                                        }
                                                    }
                                                }

                                                //Tahap 3 Bonus
                                                $datasalary = $this->getDataSalary($pphid);

                                                if (!empty($datasalary)) {
                                                    foreach ($datasalary as $datasalary) {
                                                        $ptkp                           = $datasalary->ptkp;
                                                        $salary                         = $datasalary->salary;
                                                        $salary_deduction               = $datasalary->salary_deduction;
                                                        $premi                          = $datasalary->premi;
                                                        $jht_employee                   = $datasalary->jht_employee;
                                                        $jp_employee                    = $datasalary->jp_employee;
                                                        $jht                            = $datasalary->jht;
                                                        $bonus                          = $datasalary->bonus;
                                                        $tax_allowance_bonus_yearly     = $datasalary->tax_allowance_bonus_yearly;
                                                        $tax_allowance_salary_yearly    = $datasalary->tax_allowance_salary_yearly;
                                                        $tax_allowance_salary           = $datasalary->tax_allowance_salary;
                                                        $ptkp                           = $ptkp;

                                                        $gross_yearly       = ((12*($salary+$premi-$salary_deduction))+$bonus);

                                                        $gross_yearly_new   = ((12*($salary+$premi-$salary_deduction))+$tax_allowance_bonus_yearly+$bonus);

                                                        if ((0.05)*($gross_yearly_new) >= 6000000){
                                                            $position_cost_yearly = 6000000;
                                                        }else if ((0.05)*($gross_yearly_new) < 6000000){
                                                            $position_cost_yearly = (0.05)*($gross_yearly_new);
                                                        }

                                                        $netto_yearly = ($gross_yearly_new) - ($position_cost_yearly) - (12*$jht);

                                                        $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                                        if ($pkp <= 0){
                                                            $pph_yearly = 0;
                                                            $pph_bonus = (($pph_yearly-$tax_allowance_salary_yearly));
                                                        }else if ($pkp > 500000000){
                                                            $pph_yearly = ROUND((0.30*($pkp-500000000))+95000000);
                                                            $pph_bonus = (($pph_yearly-$tax_allowance_salary_yearly));
                                                        }else if ($pkp > 250000000 && $pkp < 500000000){
                                                            $pph_yearly = ROUND((0.25*($pkp-250000000))+32500000);
                                                            $pph_bonus = (($pph_yearly-$tax_allowance_salary_yearly));
                                                        }else if ($pkp > 50000000 && $pkp < 250000000){
                                                            $pph_yearly = ROUND((0.15*($pkp-50000000))+2500000);
                                                            $pph_bonus = (($pph_yearly-$tax_allowance_salary_yearly));
                                                        }else if ($pkp <= 50000000){
                                                            $pph_yearly = ROUND(0.05*$pkp);
                                                            $pph_bonus = (($pph_yearly-$tax_allowance_salary_yearly));
                                                        }

                                                        $pphsalaryid = $this->GetPphSalaryId($pphid);

                                                        if (!empty($pphsalaryid)) {
                                                            foreach ($pphsalaryid as $pphsalaryid) {
                                                                $detailid = $pphsalaryid->id;

                                                                $data_detail_pph = [
                                                                    'pph_id'                      => $pphid,
                                                                    'gross_yearly_bonus'          => $gross_yearly_new,
                                                                    'position_cost_yearly_bonus'  => $position_cost_yearly,
                                                                    'position_cost_monthly_bonus' => ($position_cost_yearly/12),
                                                                    'netto_yearly_bonus'          => $netto_yearly,
                                                                    'tax_allowance_bonus_yearly'  => $pph_yearly,
                                                                    'tax_allowance_bonus'         => $pph_bonus,
                                                                    'pph_bonus_yearly'            => $pph_yearly,
                                                                    'pph_bonus'                   => $pph_bonus,
                                                                ];

                                                                PphBonus::whereId($detailid)->update($data_detail_pph);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    //Perhitungan Pesangon
                                    $datasalary = $this->getDataSalary($pphid);

                                    if (!empty($datasalary)) {
                                        foreach ($datasalary as $datasalary) {
                                            $ptkp                           = $datasalary->ptkp;
                                            $salary                         = $datasalary->salary;
                                            $salary_deduction               = $datasalary->salary_deduction;
                                            $premi                          = $datasalary->premi;
                                            $jht_employee                   = $datasalary->jht_employee;
                                            $jp_employee                    = $datasalary->jp_employee;
                                            $jht                            = $datasalary->jht;
                                            $pesangon                       = $datasalary->pesangon;
                                            $tax_allowance_bonus_yearly     = $datasalary->tax_allowance_bonus_yearly;
                                            $tax_allowance_salary_yearly    = $datasalary->tax_allowance_salary_yearly;
                                            $tax_allowance_salary           = $datasalary->tax_allowance_salary;
                                            $ptkp                           = $ptkp;

                                            if ($pesangon > 437500000){
                                                $tax_allowance_resign = (($pesangon - 437500000)*(1/3))+62500000;
                                            }else if ($pesangon > 100000000 && $pesangon < 437500000){
                                                $tax_allowance_resign = (($pesangon -97500000)*(3/17))+2500000;
                                            }else if ($pesangon > 50000000 && $pesangon < 97500000){
                                                $tax_allowance_resign = ($pesangon - 50000000)*(1/19);
                                            }else if ($pesangon <= 50000000){
                                                $tax_allowance_resign = 0;
                                            }

                                            $pkp = $pesangon+$tax_allowance_resign;

                                            if ($pesangon > 500000000){
                                                $pph_resign = (($pkp-500000000)*0.25)+62500000;
                                            }else if ($pesangon > 100000000 && $pesangon < 500000000){
                                                $pph_resign = (($pkp-100000000)*0.15)+2500000;
                                            }else if ($pesangon > 50000000 && $pesangon < 100000000){
                                                $pph_resign = ($pkp-50000000)*0.05;
                                            }else if ($pesangon <= 50000000){
                                                $pph_resign = 0;
                                            }

                                            $pphresignid = $this->GetPphResignId($pphid);

                                            if (!empty($pphresignid)) {
                                                foreach ($pphresignid as $pphresignid) {
                                                    $detailid = $pphresignid->id;

                                                    $data_detail_pph = [
                                                        'pph_id'                => $pphid,
                                                        'tax_allowance_resign'  => $tax_allowance_resign,
                                                        'pph_resign'            => $pph_resign,
                                                    ];

                                                    PphResign::whereId($detailid)->update($data_detail_pph);
                                                }
                                            }else{

                                                $data_detail_pph = [
                                                    'pph_id'                => $pphid,
                                                    'tax_allowance_resign'  => $tax_allowance_resign,
                                                    'pph_resign'            => $pph_resign,
                                                ];

                                                PphResign::create($data_detail_pph);
                                            }

                                        }
                                    }

                                    //Perhitungan PPH Total
                                    $datapph = $this->getDataPph($pphid);

                                    if (!empty($datapph)) {
                                        foreach ($datapph as $datapph) {

                                            $data_detail_pph = [
                                                'tax_allowance_total'   => $datapph->tax_allowance_total,
                                                'pph_due'               => $datapph->pph_due,
                                                'pph_paid'              => $datapph->pph_due,
                                            ];

                                            Pph::whereId($pphid)->update($data_detail_pph);

                                        }
                                    }

                                    //Perhitungan PPH Setahun
                                    //Tahap 1
                                    $pphsalaryyearly = $this->getDataSalaryYearly($pphid, $bulan1, $tahun1);

                                    if (!empty($pphsalaryyearly)) {
                                        foreach ($pphsalaryyearly as $pphsalaryyearly) {
                                            $ptkp                           = $pphsalaryyearly->ptkp;
                                            $salary                         = $pphsalaryyearly->salary;
                                            $salary_deduction               = $pphsalaryyearly->salary_deduction;
                                            $premi                          = $pphsalaryyearly->premi;
                                            $jht_employee                   = $pphsalaryyearly->jht_employee;
                                            $jp_employee                    = $pphsalaryyearly->jp_employee;
                                            $jht                            = $pphsalaryyearly->jht;
                                            $thr                            = $pphsalaryyearly->thr;
                                            $bonus                          = $pphsalaryyearly->bonus;
                                            $pesangon                       = $pphsalaryyearly->pesangon;

                                            $gross_yearly = $salary - $salary_deduction + $premi;

                                            if ((0.05)*($gross_yearly) >= 6000000){
                                                $position_cost_yearly = 6000000;
                                            }else if ((0.05)*($gross_yearly) < 6000000){
                                                $position_cost_yearly = (0.05)*($gross_yearly);
                                            }

                                            $netto_yearly = ($gross_yearly) - ($position_cost_yearly) - ($jht);

                                            $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                            if ($pkp <= 0){
                                                $tax_allowance_yearly = 0;
                                            }else if ($pkp > 405000000){
                                                $tax_allowance_yearly = ROUND((($pkp - 405000000)*(30/70)+95000000));
                                            }else if ($pkp > 217500000 && $pkp < 405000000){
                                                $tax_allowance_yearly = ROUND((($pkp - 217500000)*(25/75)+32500000));
                                            }else if ($pkp > 47500000 && $pkp < 217500000){
                                                $tax_allowance_yearly = ROUND((($pkp - 47500000)*(15/85)+2500000));
                                            }else if ($pkp <= 47500000){
                                                $tax_allowance_yearly = ROUND(($pkp)*(5/95));
                                            }

                                            $pphmasaid = $this->GetPphMasaId($pphid);

                                            if (!empty($pphmasaid)) {
                                                foreach ($pphmasaid as $pphmasaid) {
                                                    $detailid = $pphmasaid->id;

                                                    $data_detail_pph = [
                                                        'pph_id'                => $pphid,
                                                        'tax_allowance_yearly'  => $tax_allowance_yearly,
                                                    ];

                                                    PphMasa::whereId($detailid)->update($data_detail_pph);
                                                }
                                            }else{

                                                $data_detail_pph = [
                                                    'pph_id'                => $pphid,
                                                    'tax_allowance_yearly'  => $tax_allowance_yearly,
                                                ];

                                                PphMasa::create($data_detail_pph);

                                            }

                                        }
                                    }

                                    //Tahap 2
                                    $pphsalaryyearly = $this->getDataSalaryYearly($pphid, $bulan1, $tahun1);

                                    if (!empty($pphsalaryyearly)) {
                                        foreach ($pphsalaryyearly as $pphsalaryyearly) {
                                            $ptkp                           = $pphsalaryyearly->ptkp;
                                            $salary                         = $pphsalaryyearly->salary;
                                            $salary_deduction               = $pphsalaryyearly->salary_deduction;
                                            $premi                          = $pphsalaryyearly->premi;
                                            $jht_employee                   = $pphsalaryyearly->jht_employee;
                                            $jp_employee                    = $pphsalaryyearly->jp_employee;
                                            $jht                            = $pphsalaryyearly->jht;
                                            $thr                            = $pphsalaryyearly->thr;
                                            $bonus                          = $pphsalaryyearly->bonus;
                                            $pesangon                       = $pphsalaryyearly->pesangon;
                                            $tax_allowance_masa_yearly      = $pphsalaryyearly->tax_allowance_masa_yearly;

                                            $gross_yearly = $salary - $salary_deduction + $premi;

                                            $gross_yearly_new = $salary - $salary_deduction + $premi + $tax_allowance_masa_yearly;

                                            if ((0.05)*($gross_yearly_new) >= 6000000){
                                                $position_cost_yearly = 6000000;
                                            }else if ((0.05)*($gross_yearly_new) < 6000000){
                                                $position_cost_yearly = (0.05)*($gross_yearly_new);
                                            }

                                            $netto_yearly = ($gross_yearly) - ($position_cost_yearly) - ($jht);

                                            $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                            if ($pkp <= 0){
                                                $tax_allowance_yearly = 0;
                                            }else if ($pkp > 405000000){
                                                $tax_allowance_yearly = ROUND((($pkp - 405000000)*(30/70)+95000000));
                                            }else if ($pkp > 217500000 && $pkp < 405000000){
                                                $tax_allowance_yearly = ROUND((($pkp - 217500000)*(25/75)+32500000));
                                            }else if ($pkp > 47500000 && $pkp < 217500000){
                                                $tax_allowance_yearly = ROUND((($pkp - 47500000)*(15/85)+2500000));
                                            }else if ($pkp <= 47500000){
                                                $tax_allowance_yearly = ROUND(($pkp)*(5/95));
                                            }

                                            $pphmasaid = $this->GetPphMasaId($pphid);

                                            if (!empty($pphmasaid)) {
                                                foreach ($pphmasaid as $pphmasaid) {
                                                    $detailid = $pphmasaid->id;

                                                    $data_detail_pph = [
                                                        'pph_id'                => $pphid,
                                                        'tax_allowance_yearly'  => $tax_allowance_yearly,
                                                    ];

                                                    PphMasa::whereId($detailid)->update($data_detail_pph);
                                                }
                                            }
                                        }
                                    }

                                    //Tahap 3
                                    $pphsalaryyearly = $this->getDataSalaryYearly($pphid, $bulan1, $tahun1);

                                    if (!empty($pphsalaryyearly)) {
                                        foreach ($pphsalaryyearly as $pphsalaryyearly) {
                                            $ptkp                           = $pphsalaryyearly->ptkp;
                                            $salary                         = $pphsalaryyearly->salary;
                                            $salary_deduction               = $pphsalaryyearly->salary_deduction;
                                            $premi                          = $pphsalaryyearly->premi;
                                            $jht_employee                   = $pphsalaryyearly->jht_employee;
                                            $jp_employee                    = $pphsalaryyearly->jp_employee;
                                            $jht                            = $pphsalaryyearly->jht;
                                            $thr                            = $pphsalaryyearly->thr;
                                            $bonus                          = $pphsalaryyearly->bonus;
                                            $pesangon                       = $pphsalaryyearly->pesangon;
                                            $tax_allowance_masa_yearly      = $pphsalaryyearly->tax_allowance_masa_yearly;
                                            $pph_paid                       = $pphsalaryyearly->pph_paid;

                                            $gross_yearly = $salary - $salary_deduction + $premi + $tax_allowance_masa_yearly;

                                            if ((0.05)*($gross_yearly) >= 6000000){
                                                $position_cost_yearly = 6000000;
                                            }else if ((0.05)*($gross_yearly) < 6000000){
                                                $position_cost_yearly = (0.05)*($gross_yearly);
                                            }

                                            $netto_yearly = ($gross_yearly) - ($position_cost_yearly) - ($jht);

                                            $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                            if ($pkp <= 0){
                                                $pph_masa_yearly = 0;
                                            }else if ($pkp > 500000000){
                                                $pph_masa_yearly = ROUND((0.30*($pkp-500000000))+95000000);
                                            }else if ($pkp > 250000000 && $pkp < 500000000){
                                                $pph_masa_yearly = ROUND((0.25*($pkp-250000000))+32500000);
                                            }else if ($pkp > 50000000 && $pkp < 250000000){
                                                $pph_masa_yearly = ROUND((0.15*($pkp-50000000))+2500000);
                                            }else if ($pkp <= 50000000){
                                                $pph_masa_yearly = ROUND(0.05*$pkp);
                                            }

                                            $pphmasaid = $this->GetPphMasaId($pphid);

                                            if (!empty($pphmasaid)) {
                                                foreach ($pphmasaid as $pphmasaid) {
                                                    $detailid = $pphmasaid->id;

                                                    $data_detail_pph = [
                                                        'pph_id'                => $pphid,
                                                        'tax_allowance_yearly'  => $pph_masa_yearly,
                                                        'tax_allowance'         => ($pph_masa_yearly/12),
                                                        'gross_yearly'          => $gross_yearly,
                                                        'gross_monthly'         => ($gross_yearly/12),
                                                        'position_cost_yearly'  => $position_cost_yearly,
                                                        'position_cost_monthly' => ($position_cost_yearly/12),
                                                        'jht_employee_yearly'   => $jht_employee,
                                                        'jp_employee_yearly'    => $jp_employee,
                                                        'netto_yearly'          => $netto_yearly,
                                                        'pkp'                   => $pkp,
                                                        'ptkp'                  => $ptkp,
                                                        'pph_masa_yearly'       => $pph_masa_yearly,
                                                    ];

                                                    PphMasa::whereId($detailid)->update($data_detail_pph);
                                                }
                                            }

                                            $pphtotal = $pph_masa_yearly - $pph_paid;

                                            //Perhitungan PPH Total

                                            $data_detail_pph = [
                                                'tax_allowance_total'   => $pphtotal,
                                                'pph_due'               => $pphtotal,
                                                'pph_paid'              => $pphtotal,
                                            ];

                                            Pph::whereId($pphid)->update($data_detail_pph);
                                        }
                                    }

                                }
                                //Jika sudah ada data PPH
                                else if (!empty($pphcheck)) {
                                    foreach($pphcheck as $pphcheck){

                                        $pphid = $pphcheck->id;

                                        $datasalary = $this->getDataSalary($pphid);

                                        if (!empty($datasalary)) {
                                            foreach ($datasalary as $datasalary) {

                                                //Perhitungan PPH Salary

                                                //Perhitungan PPH Bonus

                                                //Perhitungan PPH THR

                                                //Perhitungan Pesangon

                                                //Perhitungan PPH Total

                                            }
                                        }

                                    }
                                }
                            }
                        }

                    //perhitungan pph di bulan januari sampai november
                    }else{
                        $gajistaff = $this->getGajiStaff($tglpph, $bulan1, $tahun1);

                        if (!empty($gajistaff)) {
                            foreach ($gajistaff as $gajistaff) {
                                $idemployee = $gajistaff->nik;

                                $pphcheck = $this->checkPph($idemployee,  $bulan1, $tahun1);

                                //Jika belum ada data PPH
                                if (empty($pphcheck)) {

                                    $data_pph = [
                                        'nrp'           => $gajistaff->nrp,
                                        'nik'           => $gajistaff->nik,
                                        'nama'          => $gajistaff->nama,
                                        'ptkp_code'     => $gajistaff->stapajak,
                                        'periode_bulan' => $gajistaff->bulan,
                                        'periode_tahun' => $gajistaff->tahun,
                                    ];

                                    $save = pph::create($data_pph);

                                    $parameterpph = $this->getParameterPph();

                                    if (!empty($parameterpph)) {
                                        foreach ($parameterpph as $parameterpph) {
                                            $code = $parameterpph->parameter_code;

                                            $data_detail_pph = [
                                                'pph_id'        => $save->id,
                                                'parameter_id'  => $parameterpph->id,
                                                'jumlah'        => $gajistaff->$code,
                                            ];

                                            pphdetail::create($data_detail_pph);
                                        }
                                    }

                                    $pphid = $save->id;

                                    //Perhitungan PPH Salary
                                    //Tahap 1 Salary
                                    $datasalary = $this->getDataSalary($pphid);

                                    if (!empty($datasalary)) {
                                        foreach ($datasalary as $datasalary) {
                                            $ptkp                           = $datasalary->ptkp;
                                            $salary                         = $datasalary->salary;
                                            $salary_deduction               = $datasalary->salary_deduction;
                                            $premi                          = $datasalary->premi;
                                            $jht                            = $datasalary->jht;

                                            $gross_yearly = (12*($salary+$premi-$salary_deduction));

                                            if ((0.05)*($gross_yearly) >= 6000000){
                                                $position_cost_yearly = 6000000;
                                            }else if ((0.05)*($gross_yearly) < 6000000){
                                                $position_cost_yearly = (0.05)*($gross_yearly);
                                            }

                                            $netto_yearly = ($gross_yearly) - ($position_cost_yearly) - (12*$jht);

                                            $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                            if ($pkp <= 0){
                                                $tax_allowance_yearly = 0;
                                            }else if ($pkp > 405000000){
                                                $tax_allowance_yearly = ROUND((($pkp - 405000000)*(30/70)+95000000));
                                            }else if ($pkp > 217500000 && $pkp < 405000000){
                                                $tax_allowance_yearly = ROUND((($pkp - 217500000)*(25/75)+32500000));
                                            }else if ($pkp > 47500000 && $pkp < 217500000){
                                                $tax_allowance_yearly = ROUND((($pkp - 47500000)*(15/85)+2500000));
                                            }else if ($pkp <= 47500000){
                                                $tax_allowance_yearly = ROUND(($pkp)*(5/95));
                                            }

                                            $pphsalaryid = $this->GetPphSalaryId($pphid);

                                            if (!empty($pphsalaryid)) {
                                                foreach ($pphsalaryid as $pphsalaryid) {
                                                    $detailid = $pphsalaryid->id;

                                                    $data_detail_pph = [
                                                        'pph_id'                => $pphid,
                                                        'tax_allowance_yearly'  => $tax_allowance_yearly,
                                                    ];

                                                    PphSalary::whereId($detailid)->update($data_detail_pph);
                                                }
                                            }else{

                                                $data_detail_pph = [
                                                    'pph_id'                => $pphid,
                                                    'tax_allowance_yearly'  => $tax_allowance_yearly,
                                                ];

                                                PphSalary::create($data_detail_pph);

                                            }
                                        }
                                    }

                                    //Tahap 2 Salary
                                    $datasalary = $this->getDataSalary($pphid);

                                    if (!empty($datasalary)) {
                                        foreach ($datasalary as $datasalary) {
                                            $ptkp                           = $datasalary->ptkp;
                                            $salary                         = $datasalary->salary;
                                            $salary_deduction               = $datasalary->salary_deduction;
                                            $premi                          = $datasalary->premi;
                                            $jht                            = $datasalary->jht;
                                            $tax_allowance_salary_yearly    = $datasalary->tax_allowance_salary_yearly;

                                            $gross_yearly       = (12*($salary+$premi-$salary_deduction));

                                            $gross_yearly_new   = ((12*($salary+$premi-$salary_deduction))+$tax_allowance_salary_yearly);

                                            if ((0.05)*($gross_yearly_new) >= 6000000){
                                                $position_cost_yearly = 6000000;
                                            }else if ((0.05)*($gross_yearly_new) < 6000000){
                                                $position_cost_yearly = (0.05)*($gross_yearly_new);
                                            }

                                            $netto_yearly = ($gross_yearly) - ($position_cost_yearly) - (12*$jht);

                                            $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                            if ($pkp <= 0){
                                                $tax_allowance_yearly = 0;
                                            }else if ($pkp > 405000000){
                                                $tax_allowance_yearly = ROUND((($pkp - 405000000)*(30/70)+95000000));
                                            }else if ($pkp > 217500000 && $pkp < 405000000){
                                                $tax_allowance_yearly = ROUND((($pkp - 217500000)*(25/75)+32500000));
                                            }else if ($pkp > 47500000 && $pkp < 217500000){
                                                $tax_allowance_yearly = ROUND((($pkp - 47500000)*(15/85)+2500000));
                                            }else if ($pkp <= 47500000){
                                                $tax_allowance_yearly = ROUND(($pkp)*(5/95));
                                            }

                                            $pphsalaryid = $this->GetPphSalaryId($pphid);

                                            if (!empty($pphsalaryid)) {
                                                foreach ($pphsalaryid as $pphsalaryid) {
                                                    $detailid = $pphsalaryid->id;

                                                    $data_detail_pph = [
                                                        'pph_id'                => $pphid,
                                                        'tax_allowance_yearly'  => $tax_allowance_yearly,
                                                    ];

                                                    PphSalary::whereId($detailid)->update($data_detail_pph);
                                                }
                                            }
                                        }
                                    }

                                    //Tahap 3 Salary
                                    $datasalary = $this->getDataSalary($pphid);

                                    if (!empty($datasalary)) {
                                        foreach ($datasalary as $datasalary) {
                                            $ptkp                           = $datasalary->ptkp;
                                            $salary                         = $datasalary->salary;
                                            $salary_deduction               = $datasalary->salary_deduction;
                                            $premi                          = $datasalary->premi;
                                            $jht_employee                   = $datasalary->jht_employee;
                                            $jp_employee                    = $datasalary->jp_employee;
                                            $jht                            = $datasalary->jht;
                                            $tax_allowance_salary_yearly    = $datasalary->tax_allowance_salary_yearly;
                                            $ptkp                           = $ptkp;

                                            $gross_yearly       = (12*($salary+$premi-$salary_deduction));

                                            $gross_yearly_new   = ((12*($salary+$premi-$salary_deduction))+$tax_allowance_salary_yearly);

                                            if ((0.05)*($gross_yearly_new) >= 6000000){
                                                $position_cost_yearly = 6000000;
                                            }else if ((0.05)*($gross_yearly_new) < 6000000){
                                                $position_cost_yearly = (0.05)*($gross_yearly_new);
                                            }

                                            $netto_yearly = ($gross_yearly_new) - ($position_cost_yearly) - (12*$jht);

                                            $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                            if ($pkp <= 0){
                                                $pph_yearly = 0;
                                            }else if ($pkp > 500000000){
                                                $pph_yearly = ROUND((0.30*($pkp-500000000))+95000000);
                                            }else if ($pkp > 250000000 && $pkp < 500000000){
                                                $pph_yearly = ROUND((0.25*($pkp-250000000))+32500000);
                                            }else if ($pkp > 50000000 && $pkp < 250000000){
                                                $pph_yearly = ROUND((0.15*($pkp-50000000))+2500000);
                                            }else if ($pkp <= 50000000){
                                                $pph_yearly = ROUND(0.05*$pkp);
                                            }

                                            $pphsalaryid = $this->GetPphSalaryId($pphid);

                                            if (!empty($pphsalaryid)) {
                                                foreach ($pphsalaryid as $pphsalaryid) {
                                                    $detailid = $pphsalaryid->id;

                                                    $data_detail_pph = [
                                                        'pph_id'                => $pphid,
                                                        'tax_allowance_yearly'  => $pph_yearly,
                                                        'tax_allowance'         => ($pph_yearly/12),
                                                        'gross_monthly'         => ($gross_yearly_new/12),
                                                        'gross_yearly'          => $gross_yearly_new,
                                                        'position_cost_yearly'  => $position_cost_yearly,
                                                        'position_cost_monthly' => ($position_cost_yearly/12),
                                                        'jht_employee_yearly'   => (12*$jht_employee),
                                                        'jp_employee_yearly'    => (12*$jp_employee),
                                                        'netto_yearly'          => $netto_yearly,
                                                        'ptkp'                  => $ptkp,
                                                        'pkp'                   => $pkp,
                                                        'pph_yearly'            => $pph_yearly,
                                                        'pph_salary'            => ($pph_yearly/12),
                                                    ];

                                                    PphSalary::whereId($detailid)->update($data_detail_pph);
                                                }
                                            }
                                        }
                                    }

                                    //Perhitungan PPH THR
                                    //Tahap 1 THR
                                    $datasalary = $this->getDataSalary($pphid);

                                    if (!empty($datasalary)) {
                                        foreach ($datasalary as $datasalary) {
                                            $thr = $datasalary->thr;

                                            if ($thr <= 0){

                                                $pphsalaryid = $this->GetPphSalaryId($pphid);

                                                if (!empty($pphsalaryid)) {
                                                    foreach ($pphsalaryid as $pphsalaryid) {
                                                        $detailid = $pphsalaryid->id;

                                                        $data_detail_pph = [
                                                            'pph_id'                    => $pphid,
                                                            'tax_allowance_thr_yearly'  => 0,
                                                            'tax_allowance_thr'         => 0,
                                                            'pph_thr_yearly'            => 0,
                                                            'pph_thr'                   => 0,
                                                        ];

                                                        PphBonus::whereId($detailid)->update($data_detail_pph);
                                                    }
                                                }else{

                                                    $data_detail_pph = [
                                                        'pph_id'                    => $pphid,
                                                        'tax_allowance_thr_yearly'  => 0,
                                                        'tax_allowance_thr'         => 0,
                                                        'pph_thr_yearly'            => 0,
                                                        'pph_thr'                   => 0,
                                                    ];

                                                    PphBonus::create($data_detail_pph);
                                                }
                                            }else if ($thr > 0){
                                                //Tahap 1 THR
                                                $datasalary = $this->getDataSalary($pphid);

                                                if (!empty($datasalary)) {
                                                    foreach ($datasalary as $datasalary) {
                                                        $ptkp                           = $datasalary->ptkp;
                                                        $salary                         = $datasalary->salary;
                                                        $salary_deduction               = $datasalary->salary_deduction;
                                                        $premi                          = $datasalary->premi;
                                                        $jht                            = $datasalary->jht;
                                                        $thr                            = $datasalary->thr;

                                                        $gross_yearly = ((12*($salary+$premi-$salary_deduction))+$thr);

                                                        if ((0.05)*($gross_yearly) >= 6000000){
                                                            $position_cost_yearly = 6000000;
                                                        }else if ((0.05)*($gross_yearly) < 6000000){
                                                            $position_cost_yearly = (0.05)*($gross_yearly);
                                                        }

                                                        $netto_yearly = ($gross_yearly) - ($position_cost_yearly) - (12*$jht);

                                                        $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                                        if ($pkp <= 0){
                                                            $tax_allowance_yearly = 0;
                                                        }else if ($pkp > 405000000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 405000000)*(30/70)+95000000));
                                                        }else if ($pkp > 217500000 && $pkp < 405000000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 217500000)*(25/75)+32500000));
                                                        }else if ($pkp > 47500000 && $pkp < 217500000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 47500000)*(15/85)+2500000));
                                                        }else if ($pkp <= 47500000){
                                                            $tax_allowance_yearly = ROUND(($pkp)*(5/95));
                                                        }

                                                        $pphbonusid = $this->GetPphBonusId($pphid);

                                                        if (!empty($pphbonusid)) {
                                                            foreach ($pphbonusid as $pphbonusid) {
                                                                $detailid = $pphbonusid->id;

                                                                $data_detail_pph = [
                                                                    'pph_id'                    => $pphid,
                                                                    'tax_allowance_thr_yearly'  => $tax_allowance_yearly,
                                                                ];

                                                                PphBonus::whereId($detailid)->update($data_detail_pph);
                                                            }
                                                        }else{

                                                            $data_detail_pph = [
                                                                'pph_id'                    => $pphid,
                                                                'tax_allowance_thr_yearly'  => $tax_allowance_yearly,
                                                            ];

                                                            PphBonus::create($data_detail_pph);

                                                        }
                                                    }
                                                }

                                                //Tahap 2 THR
                                                $datasalary = $this->getDataSalary($pphid);

                                                if (!empty($datasalary)) {
                                                    foreach ($datasalary as $datasalary) {
                                                        $ptkp                           = $datasalary->ptkp;
                                                        $salary                         = $datasalary->salary;
                                                        $salary_deduction               = $datasalary->salary_deduction;
                                                        $premi                          = $datasalary->premi;
                                                        $jht                            = $datasalary->jht;
                                                        $thr                            = $datasalary->thr;
                                                        $tax_allowance_thr_yearly       = $datasalary->tax_allowance_thr_yearly;

                                                        $gross_yearly       = ((12*($salary+$premi-$salary_deduction))+$thr);

                                                        $gross_yearly_new   = ((12*($salary+$premi-$salary_deduction))+$tax_allowance_thr_yearly+$thr);

                                                        if ((0.05)*($gross_yearly_new) >= 6000000){
                                                            $position_cost_yearly = 6000000;
                                                        }else if ((0.05)*($gross_yearly_new) < 6000000){
                                                            $position_cost_yearly = (0.05)*($gross_yearly_new);
                                                        }

                                                        $netto_yearly = ($gross_yearly) - ($position_cost_yearly) - (12*$jht);

                                                        $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                                        if ($pkp <= 0){
                                                            $tax_allowance_yearly = 0;
                                                        }else if ($pkp > 405000000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 405000000)*(30/70)+95000000));
                                                        }else if ($pkp > 217500000 && $pkp < 405000000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 217500000)*(25/75)+32500000));
                                                        }else if ($pkp > 47500000 && $pkp < 217500000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 47500000)*(15/85)+2500000));
                                                        }else if ($pkp <= 47500000){
                                                            $tax_allowance_yearly = ROUND(($pkp)*(5/95));
                                                        }

                                                        $pphsalaryid = $this->GetPphSalaryId($pphid);

                                                        if (!empty($pphsalaryid)) {
                                                            foreach ($pphsalaryid as $pphsalaryid) {
                                                                $detailid = $pphsalaryid->id;

                                                                $data_detail_pph = [
                                                                    'pph_id'                    => $pphid,
                                                                    'tax_allowance_thr_yearly'  => $tax_allowance_yearly,
                                                                ];

                                                                PphBonus::whereId($detailid)->update($data_detail_pph);
                                                            }
                                                        }
                                                    }
                                                }

                                                //Tahap 3 THR
                                                $datasalary = $this->getDataSalary($pphid);

                                                if (!empty($datasalary)) {
                                                    foreach ($datasalary as $datasalary) {
                                                        $ptkp                           = $datasalary->ptkp;
                                                        $salary                         = $datasalary->salary;
                                                        $salary_deduction               = $datasalary->salary_deduction;
                                                        $premi                          = $datasalary->premi;
                                                        $jht_employee                   = $datasalary->jht_employee;
                                                        $jp_employee                    = $datasalary->jp_employee;
                                                        $jht                            = $datasalary->jht;
                                                        $thr                            = $datasalary->thr;
                                                        $tax_allowance_thr_yearly       = $datasalary->tax_allowance_thr_yearly;
                                                        $tax_allowance_salary_yearly    = $datasalary->tax_allowance_salary_yearly;
                                                        $tax_allowance_salary           = $datasalary->tax_allowance_salary;
                                                        $ptkp                           = $ptkp;

                                                        $gross_yearly       = ((12*($salary+$premi-$salary_deduction))+$thr);

                                                        $gross_yearly_new   = ((12*($salary+$premi-$salary_deduction))+$tax_allowance_thr_yearly+$thr);

                                                        if ((0.05)*($gross_yearly_new) >= 6000000){
                                                            $position_cost_yearly = 6000000;
                                                        }else if ((0.05)*($gross_yearly_new) < 6000000){
                                                            $position_cost_yearly = (0.05)*($gross_yearly_new);
                                                        }

                                                        $netto_yearly = ($gross_yearly_new) - ($position_cost_yearly) - (12*$jht);

                                                        $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                                        if ($pkp <= 0){
                                                            $pph_yearly = 0;
                                                            $pph_thr = (($pph_yearly-$tax_allowance_salary_yearly));
                                                        }else if ($pkp > 500000000){
                                                            $pph_yearly = ROUND((0.30*($pkp-500000000))+95000000);
                                                            $pph_thr = (($pph_yearly-$tax_allowance_salary_yearly));
                                                        }else if ($pkp > 250000000 && $pkp < 500000000){
                                                            $pph_yearly = ROUND((0.25*($pkp-250000000))+32500000);
                                                            $pph_thr = (($pph_yearly-$tax_allowance_salary_yearly));
                                                        }else if ($pkp > 50000000 && $pkp < 250000000){
                                                            $pph_yearly = ROUND((0.15*($pkp-50000000))+2500000);
                                                            $pph_thr = (($pph_yearly-$tax_allowance_salary_yearly));
                                                        }else if ($pkp <= 50000000){
                                                            $pph_yearly = ROUND(0.05*$pkp);
                                                            $pph_thr = (($pph_yearly-$tax_allowance_salary_yearly));
                                                        }

                                                        $pphsalaryid = $this->GetPphSalaryId($pphid);

                                                        if (!empty($pphsalaryid)) {
                                                            foreach ($pphsalaryid as $pphsalaryid) {
                                                                $detailid = $pphsalaryid->id;

                                                                $data_detail_pph = [
                                                                    'pph_id'                    => $pphid,
                                                                    'tax_allowance_thr_yearly'  => $pph_yearly,
                                                                    'tax_allowance_thr'         => $pph_thr,
                                                                    'pph_thr_yearly'            => $pph_yearly,
                                                                    'pph_thr'                   => $pph_thr,
                                                                ];

                                                                PphBonus::whereId($detailid)->update($data_detail_pph);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    //Perhitungan PPH Bonus
                                    $datasalary = $this->getDataSalary($pphid);

                                    if (!empty($datasalary)) {
                                        foreach ($datasalary as $datasalary) {
                                            $bonus = $datasalary->bonus;

                                            if ($bonus <= 0){

                                                $pphsalaryid = $this->GetPphSalaryId($pphid);

                                                if (!empty($pphsalaryid)) {
                                                    foreach ($pphsalaryid as $pphsalaryid) {
                                                        $detailid = $pphsalaryid->id;

                                                        $data_detail_pph = [
                                                            'pph_id'                      => $pphid,
                                                            'gross_yearly_bonus'          => 0,
                                                            'position_cost_yearly_bonus'  => 0,
                                                            'position_cost_monthly_bonus' => 0,
                                                            'netto_yearly_bonus'          => 0,
                                                            'tax_allowance_bonus_yearly'  => 0,
                                                            'tax_allowance_bonus'         => 0,
                                                            'pph_bonus_yearly'            => 0,
                                                            'pph_bonus'                   => 0,
                                                        ];

                                                        PphBonus::whereId($detailid)->update($data_detail_pph);
                                                    }
                                                }else{

                                                    $data_detail_pph = [
                                                        'pph_id'                      => $pphid,
                                                        'gross_yearly_bonus'          => 0,
                                                        'position_cost_yearly_bonus'  => 0,
                                                        'position_cost_monthly_bonus' => 0,
                                                        'netto_yearly_bonus'          => 0,
                                                        'tax_allowance_bonus_yearly'  => 0,
                                                        'tax_allowance_bonus'         => 0,
                                                        'pph_bonus_yearly'            => 0,
                                                        'pph_bonus'                   => 0,
                                                    ];

                                                    PphBonus::create($data_detail_pph);

                                                }
                                            }else if ($bonus > 0){

                                                //Tahap 1 Bonus
                                                $datasalary = $this->getDataSalary($pphid);

                                                if (!empty($datasalary)) {
                                                    foreach ($datasalary as $datasalary) {
                                                        $ptkp                           = $datasalary->ptkp;
                                                        $salary                         = $datasalary->salary;
                                                        $salary_deduction               = $datasalary->salary_deduction;
                                                        $premi                          = $datasalary->premi;
                                                        $jht                            = $datasalary->jht;
                                                        $bonus                          = $datasalary->bonus;

                                                        $gross_yearly = ((12*($salary+$premi-$salary_deduction))+$bonus);

                                                        if ((0.05)*($gross_yearly) >= 6000000){
                                                            $position_cost_yearly = 6000000;
                                                        }else if ((0.05)*($gross_yearly) < 6000000){
                                                            $position_cost_yearly = (0.05)*($gross_yearly);
                                                        }

                                                        $netto_yearly = ($gross_yearly) - ($position_cost_yearly) - (12*$jht);

                                                        $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                                        if ($pkp <= 0){
                                                            $tax_allowance_yearly = 0;
                                                        }else if ($pkp > 405000000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 405000000)*(30/70)+95000000));
                                                        }else if ($pkp > 217500000 && $pkp < 405000000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 217500000)*(25/75)+32500000));
                                                        }else if ($pkp > 47500000 && $pkp < 217500000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 47500000)*(15/85)+2500000));
                                                        }else if ($pkp <= 47500000){
                                                            $tax_allowance_yearly = ROUND(($pkp)*(5/95));
                                                        }

                                                        $pphbonusid = $this->GetPphBonusId($pphid);

                                                        if (!empty($pphbonusid)) {
                                                            foreach ($pphbonusid as $pphbonusid) {
                                                                $detailid = $pphbonusid->id;

                                                                $data_detail_pph = [
                                                                    'pph_id'                    => $pphid,
                                                                    'tax_allowance_bonus_yearly'  => $tax_allowance_yearly,
                                                                ];

                                                                PphBonus::whereId($detailid)->update($data_detail_pph);
                                                            }
                                                        }else{

                                                            $data_detail_pph = [
                                                                'pph_id'                    => $pphid,
                                                                'tax_allowance_bonus_yearly'  => $tax_allowance_yearly,
                                                            ];

                                                            PphBonus::create($data_detail_pph);

                                                        }
                                                    }
                                                }

                                                //Tahap 2 Bonus
                                                $datasalary = $this->getDataSalary($pphid);

                                                if (!empty($datasalary)) {
                                                    foreach ($datasalary as $datasalary) {
                                                        $ptkp                           = $datasalary->ptkp;
                                                        $salary                         = $datasalary->salary;
                                                        $salary_deduction               = $datasalary->salary_deduction;
                                                        $premi                          = $datasalary->premi;
                                                        $jht                            = $datasalary->jht;
                                                        $bonus                          = $datasalary->bonus;
                                                        $tax_allowance_bonus_yearly     = $datasalary->tax_allowance_bonus_yearly;

                                                        $gross_yearly       = ((12*($salary+$premi-$salary_deduction))+$bonus);

                                                        $gross_yearly_new   = ((12*($salary+$premi-$salary_deduction))+$tax_allowance_bonus_yearly+$bonus);

                                                        if ((0.05)*($gross_yearly_new) >= 6000000){
                                                            $position_cost_yearly = 6000000;
                                                        }else if ((0.05)*($gross_yearly_new) < 6000000){
                                                            $position_cost_yearly = (0.05)*($gross_yearly_new);
                                                        }

                                                        $netto_yearly = ($gross_yearly) - ($position_cost_yearly) - (12*$jht);

                                                        $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                                        if ($pkp <= 0){
                                                            $tax_allowance_yearly = 0;
                                                        }else if ($pkp > 405000000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 405000000)*(30/70)+95000000));
                                                        }else if ($pkp > 217500000 && $pkp < 405000000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 217500000)*(25/75)+32500000));
                                                        }else if ($pkp > 47500000 && $pkp < 217500000){
                                                            $tax_allowance_yearly = ROUND((($pkp - 47500000)*(15/85)+2500000));
                                                        }else if ($pkp <= 47500000){
                                                            $tax_allowance_yearly = ROUND(($pkp)*(5/95));
                                                        }

                                                        $pphsalaryid = $this->GetPphSalaryId($pphid);

                                                        if (!empty($pphsalaryid)) {
                                                            foreach ($pphsalaryid as $pphsalaryid) {
                                                                $detailid = $pphsalaryid->id;

                                                                $data_detail_pph = [
                                                                    'pph_id'                        => $pphid,
                                                                    'tax_allowance_bonus_yearly'    => $tax_allowance_yearly,
                                                                ];

                                                                PphBonus::whereId($detailid)->update($data_detail_pph);
                                                            }
                                                        }
                                                    }
                                                }

                                                //Tahap 3 Bonus
                                                $datasalary = $this->getDataSalary($pphid);

                                                if (!empty($datasalary)) {
                                                    foreach ($datasalary as $datasalary) {
                                                        $ptkp                           = $datasalary->ptkp;
                                                        $salary                         = $datasalary->salary;
                                                        $salary_deduction               = $datasalary->salary_deduction;
                                                        $premi                          = $datasalary->premi;
                                                        $jht_employee                   = $datasalary->jht_employee;
                                                        $jp_employee                    = $datasalary->jp_employee;
                                                        $jht                            = $datasalary->jht;
                                                        $bonus                          = $datasalary->bonus;
                                                        $tax_allowance_bonus_yearly     = $datasalary->tax_allowance_bonus_yearly;
                                                        $tax_allowance_salary_yearly    = $datasalary->tax_allowance_salary_yearly;
                                                        $tax_allowance_salary           = $datasalary->tax_allowance_salary;
                                                        $ptkp                           = $ptkp;

                                                        $gross_yearly       = ((12*($salary+$premi-$salary_deduction))+$bonus);

                                                        $gross_yearly_new   = ((12*($salary+$premi-$salary_deduction))+$tax_allowance_bonus_yearly+$bonus);

                                                        if ((0.05)*($gross_yearly_new) >= 6000000){
                                                            $position_cost_yearly = 6000000;
                                                        }else if ((0.05)*($gross_yearly_new) < 6000000){
                                                            $position_cost_yearly = (0.05)*($gross_yearly_new);
                                                        }

                                                        $netto_yearly = ($gross_yearly_new) - ($position_cost_yearly) - (12*$jht);

                                                        $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                                        if ($pkp <= 0){
                                                            $pph_yearly = 0;
                                                            $pph_bonus = (($pph_yearly-$tax_allowance_salary_yearly));
                                                        }else if ($pkp > 500000000){
                                                            $pph_yearly = ROUND((0.30*($pkp-500000000))+95000000);
                                                            $pph_bonus = (($pph_yearly-$tax_allowance_salary_yearly));
                                                        }else if ($pkp > 250000000 && $pkp < 500000000){
                                                            $pph_yearly = ROUND((0.25*($pkp-250000000))+32500000);
                                                            $pph_bonus = (($pph_yearly-$tax_allowance_salary_yearly));
                                                        }else if ($pkp > 50000000 && $pkp < 250000000){
                                                            $pph_yearly = ROUND((0.15*($pkp-50000000))+2500000);
                                                            $pph_bonus = (($pph_yearly-$tax_allowance_salary_yearly));
                                                        }else if ($pkp <= 50000000){
                                                            $pph_yearly = ROUND(0.05*$pkp);
                                                            $pph_bonus = (($pph_yearly-$tax_allowance_salary_yearly));
                                                        }

                                                        $pphsalaryid = $this->GetPphSalaryId($pphid);

                                                        if (!empty($pphsalaryid)) {
                                                            foreach ($pphsalaryid as $pphsalaryid) {
                                                                $detailid = $pphsalaryid->id;

                                                                $data_detail_pph = [
                                                                    'pph_id'                      => $pphid,
                                                                    'gross_yearly_bonus'          => $gross_yearly_new,
                                                                    'position_cost_yearly_bonus'  => $position_cost_yearly,
                                                                    'position_cost_monthly_bonus' => ($position_cost_yearly/12),
                                                                    'netto_yearly_bonus'          => $netto_yearly,
                                                                    'tax_allowance_bonus_yearly'  => $pph_yearly,
                                                                    'tax_allowance_bonus'         => $pph_bonus,
                                                                    'pph_bonus_yearly'            => $pph_yearly,
                                                                    'pph_bonus'                   => $pph_bonus,
                                                                ];

                                                                PphBonus::whereId($detailid)->update($data_detail_pph);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    //Perhitungan Pesangon
                                    $datasalary = $this->getDataSalary($pphid);

                                    if (!empty($datasalary)) {
                                        foreach ($datasalary as $datasalary) {
                                            $ptkp                           = $datasalary->ptkp;
                                            $salary                         = $datasalary->salary;
                                            $salary_deduction               = $datasalary->salary_deduction;
                                            $premi                          = $datasalary->premi;
                                            $jht_employee                   = $datasalary->jht_employee;
                                            $jp_employee                    = $datasalary->jp_employee;
                                            $jht                            = $datasalary->jht;
                                            $pesangon                       = $datasalary->pesangon;
                                            $tax_allowance_bonus_yearly     = $datasalary->tax_allowance_bonus_yearly;
                                            $tax_allowance_salary_yearly    = $datasalary->tax_allowance_salary_yearly;
                                            $tax_allowance_salary           = $datasalary->tax_allowance_salary;
                                            $ptkp                           = $ptkp;

                                            if ($pesangon > 437500000){
                                                $tax_allowance_resign = (($pesangon - 437500000)*(1/3))+62500000;
                                            }else if ($pesangon > 100000000 && $pesangon < 437500000){
                                                $tax_allowance_resign = (($pesangon -97500000)*(3/17))+2500000;
                                            }else if ($pesangon > 50000000 && $pesangon < 97500000){
                                                $tax_allowance_resign = ($pesangon - 50000000)*(1/19);
                                            }else if ($pesangon <= 50000000){
                                                $tax_allowance_resign = 0;
                                            }

                                            $pkp = $pesangon+$tax_allowance_resign;

                                            if ($pesangon > 500000000){
                                                $pph_resign = (($pkp-500000000)*0.25)+62500000;
                                            }else if ($pesangon > 100000000 && $pesangon < 500000000){
                                                $pph_resign = (($pkp-100000000)*0.15)+2500000;
                                            }else if ($pesangon > 50000000 && $pesangon < 100000000){
                                                $pph_resign = ($pkp-50000000)*0.05;
                                            }else if ($pesangon <= 50000000){
                                                $pph_resign = 0;
                                            }

                                            $pphresignid = $this->GetPphResignId($pphid);

                                            if (!empty($pphresignid)) {
                                                foreach ($pphresignid as $pphresignid) {
                                                    $detailid = $pphresignid->id;

                                                    $data_detail_pph = [
                                                        'pph_id'                => $pphid,
                                                        'tax_allowance_resign'  => $tax_allowance_resign,
                                                        'pph_resign'            => $pph_resign,
                                                    ];

                                                    PphResign::whereId($detailid)->update($data_detail_pph);
                                                }
                                            }else{

                                                $data_detail_pph = [
                                                    'pph_id'                => $pphid,
                                                    'tax_allowance_resign'  => $tax_allowance_resign,
                                                    'pph_resign'            => $pph_resign,
                                                ];

                                                PphResign::create($data_detail_pph);
                                            }

                                        }
                                    }

                                    //Perhitungan PPH Total
                                    $datapph = $this->getDataPph($pphid);

                                    if (!empty($datapph)) {
                                        foreach ($datapph as $datapph) {

                                            $data_detail_pph = [
                                                'tax_allowance_total'   => $datapph->tax_allowance_total,
                                                'pph_due'               => $datapph->pph_due,
                                                'pph_paid'              => $datapph->pph_due,
                                            ];

                                            Pph::whereId($pphid)->update($data_detail_pph);

                                        }
                                    }

                                }
                                //Jika sudah ada data PPH
                                else {
                                    foreach($pphcheck as $pphcheck){

                                        $pphid = $pphcheck->id;

                                        $parameterpph = $this->getParameterPph();

                                        if (!empty($parameterpph)) {
                                            foreach ($parameterpph as $parameterpph) {
                                                $parameterid = $parameterpph->id;
                                                $code = $parameterpph->parameter_code;

                                                $pphdetail = $this->getPphDetail($pphid, $parameterid);

                                                if (!empty($pphdetail)) {
                                                    foreach ($pphdetail as $pphdetail) {
                                                        $pphdetailid = $pphdetail->id;

                                                        $data_detail_pph = [
                                                            'pph_id'        => $pphid,
                                                            'parameter_id'  => $parameterpph->id,
                                                            'jumlah'        => $gajistaff->$code,
                                                        ];

                                                        pphdetail::whereId($pphdetailid)->update($data_detail_pph);
                                                    }
                                                }
                                            }
                                        }

                                        //Perhitungan PPH Salary
                                        //Tahap 1 Salary
                                        $datasalary = $this->getDataSalary($pphid);

                                        if (!empty($datasalary)) {
                                            foreach ($datasalary as $datasalary) {
                                                $ptkp                           = $datasalary->ptkp;
                                                $salary                         = $datasalary->salary;
                                                $salary_deduction               = $datasalary->salary_deduction;
                                                $premi                          = $datasalary->premi;
                                                $jht                            = $datasalary->jht;

                                                $gross_yearly = (12*($salary+$premi-$salary_deduction));

                                                if ((0.05)*($gross_yearly) >= 6000000){
                                                    $position_cost_yearly = 6000000;
                                                }else if ((0.05)*($gross_yearly) < 6000000){
                                                    $position_cost_yearly = (0.05)*($gross_yearly);
                                                }

                                                $netto_yearly = ($gross_yearly) - ($position_cost_yearly) - (12*$jht);

                                                $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                                if ($pkp <= 0){
                                                    $tax_allowance_yearly = 0;
                                                }else if ($pkp > 405000000){
                                                    $tax_allowance_yearly = ROUND((($pkp - 405000000)*(30/70)+95000000));
                                                }else if ($pkp > 217500000 && $pkp < 405000000){
                                                    $tax_allowance_yearly = ROUND((($pkp - 217500000)*(25/75)+32500000));
                                                }else if ($pkp > 47500000 && $pkp < 217500000){
                                                    $tax_allowance_yearly = ROUND((($pkp - 47500000)*(15/85)+2500000));
                                                }else if ($pkp <= 47500000){
                                                    $tax_allowance_yearly = ROUND(($pkp)*(5/95));
                                                }

                                                $pphsalaryid = $this->GetPphSalaryId($pphid);

                                                if (!empty($pphsalaryid)) {
                                                    foreach ($pphsalaryid as $pphsalaryid) {
                                                        $detailid = $pphsalaryid->id;

                                                        $data_detail_pph = [
                                                            'pph_id'                => $pphid,
                                                            'tax_allowance_yearly'  => $tax_allowance_yearly,
                                                        ];

                                                        PphSalary::whereId($detailid)->update($data_detail_pph);
                                                    }
                                                }else{

                                                    $data_detail_pph = [
                                                        'pph_id'                => $pphid,
                                                        'tax_allowance_yearly'  => $tax_allowance_yearly,
                                                    ];

                                                    PphSalary::create($data_detail_pph);

                                                }
                                            }
                                        }

                                        //Tahap 2 Salary
                                        $datasalary = $this->getDataSalary($pphid);

                                        if (!empty($datasalary)) {
                                            foreach ($datasalary as $datasalary) {
                                                $ptkp                           = $datasalary->ptkp;
                                                $salary                         = $datasalary->salary;
                                                $salary_deduction               = $datasalary->salary_deduction;
                                                $premi                          = $datasalary->premi;
                                                $jht                            = $datasalary->jht;
                                                $tax_allowance_salary_yearly    = $datasalary->tax_allowance_salary_yearly;

                                                $gross_yearly       = (12*($salary+$premi-$salary_deduction));

                                                $gross_yearly_new   = ((12*($salary+$premi-$salary_deduction))+$tax_allowance_salary_yearly);

                                                if ((0.05)*($gross_yearly_new) >= 6000000){
                                                    $position_cost_yearly = 6000000;
                                                }else if ((0.05)*($gross_yearly_new) < 6000000){
                                                    $position_cost_yearly = (0.05)*($gross_yearly_new);
                                                }

                                                $netto_yearly = ($gross_yearly) - ($position_cost_yearly) - (12*$jht);

                                                $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                                if ($pkp <= 0){
                                                    $tax_allowance_yearly = 0;
                                                }else if ($pkp > 405000000){
                                                    $tax_allowance_yearly = ROUND((($pkp - 405000000)*(30/70)+95000000));
                                                }else if ($pkp > 217500000 && $pkp < 405000000){
                                                    $tax_allowance_yearly = ROUND((($pkp - 217500000)*(25/75)+32500000));
                                                }else if ($pkp > 47500000 && $pkp < 217500000){
                                                    $tax_allowance_yearly = ROUND((($pkp - 47500000)*(15/85)+2500000));
                                                }else if ($pkp <= 47500000){
                                                    $tax_allowance_yearly = ROUND(($pkp)*(5/95));
                                                }

                                                $pphsalaryid = $this->GetPphSalaryId($pphid);

                                                if (!empty($pphsalaryid)) {
                                                    foreach ($pphsalaryid as $pphsalaryid) {
                                                        $detailid = $pphsalaryid->id;

                                                        $data_detail_pph = [
                                                            'pph_id'                => $pphid,
                                                            'tax_allowance_yearly'  => $tax_allowance_yearly,
                                                        ];

                                                        PphSalary::whereId($detailid)->update($data_detail_pph);
                                                    }
                                                }
                                            }
                                        }

                                        //Tahap 3 Salary
                                        $datasalary = $this->getDataSalary($pphid);

                                        if (!empty($datasalary)) {
                                            foreach ($datasalary as $datasalary) {
                                                $ptkp                           = $datasalary->ptkp;
                                                $salary                         = $datasalary->salary;
                                                $salary_deduction               = $datasalary->salary_deduction;
                                                $premi                          = $datasalary->premi;
                                                $jht_employee                   = $datasalary->jht_employee;
                                                $jp_employee                    = $datasalary->jp_employee;
                                                $jht                            = $datasalary->jht;
                                                $tax_allowance_salary_yearly    = $datasalary->tax_allowance_salary_yearly;
                                                $ptkp                           = $ptkp;

                                                $gross_yearly       = (12*($salary+$premi-$salary_deduction));

                                                $gross_yearly_new   = ((12*($salary+$premi-$salary_deduction))+$tax_allowance_salary_yearly);

                                                if ((0.05)*($gross_yearly_new) >= 6000000){
                                                    $position_cost_yearly = 6000000;
                                                }else if ((0.05)*($gross_yearly_new) < 6000000){
                                                    $position_cost_yearly = (0.05)*($gross_yearly_new);
                                                }

                                                $netto_yearly = ($gross_yearly_new) - ($position_cost_yearly) - (12*$jht);

                                                $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                                if ($pkp <= 0){
                                                    $pph_yearly = 0;
                                                }else if ($pkp > 500000000){
                                                    $pph_yearly = ROUND((0.30*($pkp-500000000))+95000000);
                                                }else if ($pkp > 250000000 && $pkp < 500000000){
                                                    $pph_yearly = ROUND((0.25*($pkp-250000000))+32500000);
                                                }else if ($pkp > 50000000 && $pkp < 250000000){
                                                    $pph_yearly = ROUND((0.15*($pkp-50000000))+2500000);
                                                }else if ($pkp <= 50000000){
                                                    $pph_yearly = ROUND(0.05*$pkp);
                                                }

                                                $pphsalaryid = $this->GetPphSalaryId($pphid);

                                                if (!empty($pphsalaryid)) {
                                                    foreach ($pphsalaryid as $pphsalaryid) {
                                                        $detailid = $pphsalaryid->id;

                                                        $data_detail_pph = [
                                                            'pph_id'                => $pphid,
                                                            'tax_allowance_yearly'  => $pph_yearly,
                                                            'tax_allowance'         => ($pph_yearly/12),
                                                            'gross_monthly'         => ($gross_yearly_new/12),
                                                            'gross_yearly'          => $gross_yearly_new,
                                                            'position_cost_yearly'  => $position_cost_yearly,
                                                            'position_cost_monthly' => ($position_cost_yearly/12),
                                                            'jht_employee_yearly'   => (12*$jht_employee),
                                                            'jp_employee_yearly'    => (12*$jp_employee),
                                                            'netto_yearly'          => $netto_yearly,
                                                            'ptkp'                  => $ptkp,
                                                            'pkp'                   => $pkp,
                                                            'pph_yearly'            => $pph_yearly,
                                                            'pph_salary'            => ($pph_yearly/12),
                                                        ];

                                                        PphSalary::whereId($detailid)->update($data_detail_pph);
                                                    }
                                                }
                                            }
                                        }

                                        //Perhitungan PPH THR
                                        $datasalary = $this->getDataSalary($pphid);

                                        if (!empty($datasalary)) {
                                            foreach ($datasalary as $datasalary) {
                                                $thr = $datasalary->thr;

                                                if ($thr <= 0){

                                                    $pphsalaryid = $this->GetPphSalaryId($pphid);

                                                    if (!empty($pphsalaryid)) {
                                                        foreach ($pphsalaryid as $pphsalaryid) {
                                                            $detailid = $pphsalaryid->id;

                                                            $data_detail_pph = [
                                                                'pph_id'                    => $pphid,
                                                                'tax_allowance_thr_yearly'  => 0,
                                                                'tax_allowance_thr'         => 0,
                                                                'pph_thr_yearly'            => 0,
                                                                'pph_thr'                   => 0,
                                                            ];

                                                            PphBonus::whereId($detailid)->update($data_detail_pph);
                                                        }
                                                    }else{

                                                        $data_detail_pph = [
                                                            'pph_id'                    => $pphid,
                                                            'tax_allowance_thr_yearly'  => 0,
                                                            'tax_allowance_thr'         => 0,
                                                            'pph_thr_yearly'            => 0,
                                                            'pph_thr'                   => 0,
                                                        ];

                                                        PphBonus::create($data_detail_pph);
                                                    }

                                                }else if ($thr > 0){
                                                    //Tahap 1 THR
                                                    $datasalary = $this->getDataSalary($pphid);

                                                    if (!empty($datasalary)) {
                                                        foreach ($datasalary as $datasalary) {
                                                            $ptkp                           = $datasalary->ptkp;
                                                            $salary                         = $datasalary->salary;
                                                            $salary_deduction               = $datasalary->salary_deduction;
                                                            $premi                          = $datasalary->premi;
                                                            $jht                            = $datasalary->jht;
                                                            $thr                            = $datasalary->thr;

                                                            $gross_yearly = ((12*($salary+$premi-$salary_deduction))+$thr);

                                                            if ((0.05)*($gross_yearly) >= 6000000){
                                                                $position_cost_yearly = 6000000;
                                                            }else if ((0.05)*($gross_yearly) < 6000000){
                                                                $position_cost_yearly = (0.05)*($gross_yearly);
                                                            }

                                                            $netto_yearly = ($gross_yearly) - ($position_cost_yearly) - (12*$jht);

                                                            $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                                            if ($pkp <= 0){
                                                                $tax_allowance_yearly = 0;
                                                            }else if ($pkp > 405000000){
                                                                $tax_allowance_yearly = ROUND((($pkp - 405000000)*(30/70)+95000000));
                                                            }else if ($pkp > 217500000 && $pkp < 405000000){
                                                                $tax_allowance_yearly = ROUND((($pkp - 217500000)*(25/75)+32500000));
                                                            }else if ($pkp > 47500000 && $pkp < 217500000){
                                                                $tax_allowance_yearly = ROUND((($pkp - 47500000)*(15/85)+2500000));
                                                            }else if ($pkp <= 47500000){
                                                                $tax_allowance_yearly = ROUND(($pkp)*(5/95));
                                                            }

                                                            $pphbonusid = $this->GetPphBonusId($pphid);

                                                            if (!empty($pphbonusid)) {
                                                                foreach ($pphbonusid as $pphbonusid) {
                                                                    $detailid = $pphbonusid->id;

                                                                    $data_detail_pph = [
                                                                        'pph_id'                    => $pphid,
                                                                        'tax_allowance_thr_yearly'  => $tax_allowance_yearly,
                                                                    ];

                                                                    PphBonus::whereId($detailid)->update($data_detail_pph);
                                                                }
                                                            }else{

                                                                $data_detail_pph = [
                                                                    'pph_id'                    => $pphid,
                                                                    'tax_allowance_thr_yearly'  => $tax_allowance_yearly,
                                                                ];

                                                                PphBonus::create($data_detail_pph);

                                                            }
                                                        }
                                                    }

                                                    //Tahap 2 THR
                                                    $datasalary = $this->getDataSalary($pphid);

                                                    if (!empty($datasalary)) {
                                                        foreach ($datasalary as $datasalary) {
                                                            $ptkp                           = $datasalary->ptkp;
                                                            $salary                         = $datasalary->salary;
                                                            $salary_deduction               = $datasalary->salary_deduction;
                                                            $premi                          = $datasalary->premi;
                                                            $jht                            = $datasalary->jht;
                                                            $thr                            = $datasalary->thr;
                                                            $tax_allowance_thr_yearly       = $datasalary->tax_allowance_thr_yearly;

                                                            $gross_yearly       = ((12*($salary+$premi-$salary_deduction))+$thr);

                                                            $gross_yearly_new   = ((12*($salary+$premi-$salary_deduction))+$tax_allowance_thr_yearly+$thr);

                                                            if ((0.05)*($gross_yearly_new) >= 6000000){
                                                                $position_cost_yearly = 6000000;
                                                            }else if ((0.05)*($gross_yearly_new) < 6000000){
                                                                $position_cost_yearly = (0.05)*($gross_yearly_new);
                                                            }

                                                            $netto_yearly = ($gross_yearly) - ($position_cost_yearly) - (12*$jht);

                                                            $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                                            if ($pkp <= 0){
                                                                $tax_allowance_yearly = 0;
                                                            }else if ($pkp > 405000000){
                                                                $tax_allowance_yearly = ROUND((($pkp - 405000000)*(30/70)+95000000));
                                                            }else if ($pkp > 217500000 && $pkp < 405000000){
                                                                $tax_allowance_yearly = ROUND((($pkp - 217500000)*(25/75)+32500000));
                                                            }else if ($pkp > 47500000 && $pkp < 217500000){
                                                                $tax_allowance_yearly = ROUND((($pkp - 47500000)*(15/85)+2500000));
                                                            }else if ($pkp <= 47500000){
                                                                $tax_allowance_yearly = ROUND(($pkp)*(5/95));
                                                            }

                                                            $pphsalaryid = $this->GetPphSalaryId($pphid);

                                                            if (!empty($pphsalaryid)) {
                                                                foreach ($pphsalaryid as $pphsalaryid) {
                                                                    $detailid = $pphsalaryid->id;

                                                                    $data_detail_pph = [
                                                                        'pph_id'                    => $pphid,
                                                                        'tax_allowance_thr_yearly'  => $tax_allowance_yearly,
                                                                    ];

                                                                    PphBonus::whereId($detailid)->update($data_detail_pph);
                                                                }
                                                            }
                                                        }
                                                    }

                                                    //Tahap 3 THR
                                                    $datasalary = $this->getDataSalary($pphid);

                                                    if (!empty($datasalary)) {
                                                        foreach ($datasalary as $datasalary) {
                                                            $ptkp                           = $datasalary->ptkp;
                                                            $salary                         = $datasalary->salary;
                                                            $salary_deduction               = $datasalary->salary_deduction;
                                                            $premi                          = $datasalary->premi;
                                                            $jht_employee                   = $datasalary->jht_employee;
                                                            $jp_employee                    = $datasalary->jp_employee;
                                                            $jht                            = $datasalary->jht;
                                                            $thr                            = $datasalary->thr;
                                                            $tax_allowance_thr_yearly       = $datasalary->tax_allowance_thr_yearly;
                                                            $tax_allowance_salary_yearly    = $datasalary->tax_allowance_salary_yearly;
                                                            $tax_allowance_salary           = $datasalary->tax_allowance_salary;
                                                            $ptkp                           = $ptkp;

                                                            $gross_yearly       = ((12*($salary+$premi-$salary_deduction))+$thr);

                                                            $gross_yearly_new   = ((12*($salary+$premi-$salary_deduction))+$tax_allowance_thr_yearly+$thr);

                                                            if ((0.05)*($gross_yearly_new) >= 6000000){
                                                                $position_cost_yearly = 6000000;
                                                            }else if ((0.05)*($gross_yearly_new) < 6000000){
                                                                $position_cost_yearly = (0.05)*($gross_yearly_new);
                                                            }

                                                            $netto_yearly = ($gross_yearly_new) - ($position_cost_yearly) - (12*$jht);

                                                            $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                                            if ($pkp <= 0){
                                                                $pph_yearly = 0;
                                                                $pph_thr = (($pph_yearly-$tax_allowance_salary_yearly));
                                                            }else if ($pkp > 500000000){
                                                                $pph_yearly = ROUND((0.30*($pkp-500000000))+95000000);
                                                                $pph_thr = (($pph_yearly-$tax_allowance_salary_yearly));
                                                            }else if ($pkp > 250000000 && $pkp < 500000000){
                                                                $pph_yearly = ROUND((0.25*($pkp-250000000))+32500000);
                                                                $pph_thr = (($pph_yearly-$tax_allowance_salary_yearly));
                                                            }else if ($pkp > 50000000 && $pkp < 250000000){
                                                                $pph_yearly = ROUND((0.15*($pkp-50000000))+2500000);
                                                                $pph_thr = (($pph_yearly-$tax_allowance_salary_yearly));
                                                            }else if ($pkp <= 50000000){
                                                                $pph_yearly = ROUND(0.05*$pkp);
                                                                $pph_thr = (($pph_yearly-$tax_allowance_salary_yearly));
                                                            }

                                                            $pphsalaryid = $this->GetPphSalaryId($pphid);

                                                            if (!empty($pphsalaryid)) {
                                                                foreach ($pphsalaryid as $pphsalaryid) {
                                                                    $detailid = $pphsalaryid->id;

                                                                    $data_detail_pph = [
                                                                        'pph_id'                    => $pphid,
                                                                        'tax_allowance_thr_yearly'  => $pph_yearly,
                                                                        'tax_allowance_thr'         => $pph_thr,
                                                                        'pph_thr_yearly'            => $pph_yearly,
                                                                        'pph_thr'                   => $pph_thr,
                                                                    ];

                                                                    PphBonus::whereId($detailid)->update($data_detail_pph);
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        //Perhitungan PPH Bonus
                                        $datasalary = $this->getDataSalary($pphid);

                                        if (!empty($datasalary)) {
                                            foreach ($datasalary as $datasalary) {
                                                $bonus = $datasalary->bonus;

                                                if ($bonus <= 0){

                                                    $pphsalaryid = $this->GetPphSalaryId($pphid);

                                                    if (!empty($pphsalaryid)) {
                                                        foreach ($pphsalaryid as $pphsalaryid) {
                                                            $detailid = $pphsalaryid->id;

                                                            $data_detail_pph = [
                                                                'pph_id'                      => $pphid,
                                                                'gross_yearly_bonus'          => 0,
                                                                'position_cost_yearly_bonus'  => 0,
                                                                'position_cost_monthly_bonus' => 0,
                                                                'netto_yearly_bonus'          => 0,
                                                                'tax_allowance_bonus_yearly'  => 0,
                                                                'tax_allowance_bonus'         => 0,
                                                                'pph_bonus_yearly'            => 0,
                                                                'pph_bonus'                   => 0,
                                                            ];

                                                            PphBonus::whereId($detailid)->update($data_detail_pph);
                                                        }
                                                    }else{

                                                        $data_detail_pph = [
                                                            'pph_id'                      => $pphid,
                                                            'gross_yearly_bonus'          => 0,
                                                            'position_cost_yearly_bonus'  => 0,
                                                            'position_cost_monthly_bonus' => 0,
                                                            'netto_yearly_bonus'          => 0,
                                                            'tax_allowance_bonus_yearly'  => 0,
                                                            'tax_allowance_bonus'         => 0,
                                                            'pph_bonus_yearly'            => 0,
                                                            'pph_bonus'                   => 0,
                                                        ];

                                                        PphBonus::create($data_detail_pph);

                                                    }
                                                }else if ($bonus > 0){

                                                    //Tahap 1 Bonus
                                                    $datasalary = $this->getDataSalary($pphid);

                                                    if (!empty($datasalary)) {
                                                        foreach ($datasalary as $datasalary) {
                                                            $ptkp                           = $datasalary->ptkp;
                                                            $salary                         = $datasalary->salary;
                                                            $salary_deduction               = $datasalary->salary_deduction;
                                                            $premi                          = $datasalary->premi;
                                                            $jht                            = $datasalary->jht;
                                                            $bonus                          = $datasalary->bonus;

                                                            $gross_yearly = ((12*($salary+$premi-$salary_deduction))+$bonus);

                                                            if ((0.05)*($gross_yearly) >= 6000000){
                                                                $position_cost_yearly = 6000000;
                                                            }else if ((0.05)*($gross_yearly) < 6000000){
                                                                $position_cost_yearly = (0.05)*($gross_yearly);
                                                            }

                                                            $netto_yearly = ($gross_yearly) - ($position_cost_yearly) - (12*$jht);

                                                            $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                                            if ($pkp <= 0){
                                                                $tax_allowance_yearly = 0;
                                                            }else if ($pkp > 405000000){
                                                                $tax_allowance_yearly = ROUND((($pkp - 405000000)*(30/70)+95000000));
                                                            }else if ($pkp > 217500000 && $pkp < 405000000){
                                                                $tax_allowance_yearly = ROUND((($pkp - 217500000)*(25/75)+32500000));
                                                            }else if ($pkp > 47500000 && $pkp < 217500000){
                                                                $tax_allowance_yearly = ROUND((($pkp - 47500000)*(15/85)+2500000));
                                                            }else if ($pkp <= 47500000){
                                                                $tax_allowance_yearly = ROUND(($pkp)*(5/95));
                                                            }

                                                            $pphbonusid = $this->GetPphBonusId($pphid);

                                                            if (!empty($pphbonusid)) {
                                                                foreach ($pphbonusid as $pphbonusid) {
                                                                    $detailid = $pphbonusid->id;

                                                                    $data_detail_pph = [
                                                                        'pph_id'                    => $pphid,
                                                                        'tax_allowance_bonus_yearly'  => $tax_allowance_yearly,
                                                                    ];

                                                                    PphBonus::whereId($detailid)->update($data_detail_pph);
                                                                }
                                                            }else{

                                                                $data_detail_pph = [
                                                                    'pph_id'                    => $pphid,
                                                                    'tax_allowance_bonus_yearly'  => $tax_allowance_yearly,
                                                                ];

                                                                PphBonus::create($data_detail_pph);

                                                            }
                                                        }
                                                    }

                                                    //Tahap 2 Bonus
                                                    $datasalary = $this->getDataSalary($pphid);

                                                    if (!empty($datasalary)) {
                                                        foreach ($datasalary as $datasalary) {
                                                            $ptkp                           = $datasalary->ptkp;
                                                            $salary                         = $datasalary->salary;
                                                            $salary_deduction               = $datasalary->salary_deduction;
                                                            $premi                          = $datasalary->premi;
                                                            $jht                            = $datasalary->jht;
                                                            $bonus                          = $datasalary->bonus;
                                                            $tax_allowance_bonus_yearly     = $datasalary->tax_allowance_bonus_yearly;

                                                            $gross_yearly       = ((12*($salary+$premi-$salary_deduction))+$bonus);

                                                            $gross_yearly_new   = ((12*($salary+$premi-$salary_deduction))+$tax_allowance_bonus_yearly+$bonus);

                                                            if ((0.05)*($gross_yearly_new) >= 6000000){
                                                                $position_cost_yearly = 6000000;
                                                            }else if ((0.05)*($gross_yearly_new) < 6000000){
                                                                $position_cost_yearly = (0.05)*($gross_yearly_new);
                                                            }

                                                            $netto_yearly = ($gross_yearly) - ($position_cost_yearly) - (12*$jht);

                                                            $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                                            if ($pkp <= 0){
                                                                $tax_allowance_yearly = 0;
                                                            }else if ($pkp > 405000000){
                                                                $tax_allowance_yearly = ROUND((($pkp - 405000000)*(30/70)+95000000));
                                                            }else if ($pkp > 217500000 && $pkp < 405000000){
                                                                $tax_allowance_yearly = ROUND((($pkp - 217500000)*(25/75)+32500000));
                                                            }else if ($pkp > 47500000 && $pkp < 217500000){
                                                                $tax_allowance_yearly = ROUND((($pkp - 47500000)*(15/85)+2500000));
                                                            }else if ($pkp <= 47500000){
                                                                $tax_allowance_yearly = ROUND(($pkp)*(5/95));
                                                            }

                                                            $pphsalaryid = $this->GetPphSalaryId($pphid);

                                                            if (!empty($pphsalaryid)) {
                                                                foreach ($pphsalaryid as $pphsalaryid) {
                                                                    $detailid = $pphsalaryid->id;

                                                                    $data_detail_pph = [
                                                                        'pph_id'                        => $pphid,
                                                                        'tax_allowance_bonus_yearly'    => $tax_allowance_yearly,
                                                                    ];

                                                                    PphBonus::whereId($detailid)->update($data_detail_pph);
                                                                }
                                                            }
                                                        }
                                                    }

                                                    //Tahap 3 Bonus
                                                    $datasalary = $this->getDataSalary($pphid);

                                                    if (!empty($datasalary)) {
                                                        foreach ($datasalary as $datasalary) {
                                                            $ptkp                           = $datasalary->ptkp;
                                                            $salary                         = $datasalary->salary;
                                                            $salary_deduction               = $datasalary->salary_deduction;
                                                            $premi                          = $datasalary->premi;
                                                            $jht_employee                   = $datasalary->jht_employee;
                                                            $jp_employee                    = $datasalary->jp_employee;
                                                            $jht                            = $datasalary->jht;
                                                            $bonus                          = $datasalary->bonus;
                                                            $tax_allowance_bonus_yearly     = $datasalary->tax_allowance_bonus_yearly;
                                                            $tax_allowance_salary_yearly    = $datasalary->tax_allowance_salary_yearly;
                                                            $tax_allowance_salary           = $datasalary->tax_allowance_salary;
                                                            $ptkp                           = $ptkp;

                                                            $gross_yearly       = ((12*($salary+$premi-$salary_deduction))+$bonus);

                                                            $gross_yearly_new   = ((12*($salary+$premi-$salary_deduction))+$tax_allowance_bonus_yearly+$bonus);

                                                            if ((0.05)*($gross_yearly_new) >= 6000000){
                                                                $position_cost_yearly = 6000000;
                                                            }else if ((0.05)*($gross_yearly_new) < 6000000){
                                                                $position_cost_yearly = (0.05)*($gross_yearly_new);
                                                            }

                                                            $netto_yearly = ($gross_yearly_new) - ($position_cost_yearly) - (12*$jht);

                                                            $pkp = FLOOR(($netto_yearly-$ptkp)/1000)*1000;

                                                            if ($pkp <= 0){
                                                                $pph_yearly = 0;
                                                                $pph_bonus = (($pph_yearly-$tax_allowance_salary_yearly));
                                                            }else if ($pkp > 500000000){
                                                                $pph_yearly = ROUND((0.30*($pkp-500000000))+95000000);
                                                                $pph_bonus = (($pph_yearly-$tax_allowance_salary_yearly));
                                                            }else if ($pkp > 250000000 && $pkp < 500000000){
                                                                $pph_yearly = ROUND((0.25*($pkp-250000000))+32500000);
                                                                $pph_bonus = (($pph_yearly-$tax_allowance_salary_yearly));
                                                            }else if ($pkp > 50000000 && $pkp < 250000000){
                                                                $pph_yearly = ROUND((0.15*($pkp-50000000))+2500000);
                                                                $pph_bonus = (($pph_yearly-$tax_allowance_salary_yearly));
                                                            }else if ($pkp <= 50000000){
                                                                $pph_yearly = ROUND(0.05*$pkp);
                                                                $pph_bonus = (($pph_yearly-$tax_allowance_salary_yearly));
                                                            }

                                                            $pphsalaryid = $this->GetPphSalaryId($pphid);

                                                            if (!empty($pphsalaryid)) {
                                                                foreach ($pphsalaryid as $pphsalaryid) {
                                                                    $detailid = $pphsalaryid->id;

                                                                    $data_detail_pph = [
                                                                        'pph_id'                      => $pphid,
                                                                        'gross_yearly_bonus'          => $gross_yearly_new,
                                                                        'position_cost_yearly_bonus'  => $position_cost_yearly,
                                                                        'position_cost_monthly_bonus' => ($position_cost_yearly/12),
                                                                        'netto_yearly_bonus'          => $netto_yearly,
                                                                        'tax_allowance_bonus_yearly'  => $pph_yearly,
                                                                        'tax_allowance_bonus'         => $pph_bonus,
                                                                        'pph_bonus_yearly'            => $pph_yearly,
                                                                        'pph_bonus'                   => $pph_bonus,
                                                                    ];

                                                                    PphBonus::whereId($detailid)->update($data_detail_pph);
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        //Perhitungan Pesangon
                                        $datasalary = $this->getDataSalary($pphid);

                                        if (!empty($datasalary)) {
                                            foreach ($datasalary as $datasalary) {
                                                $ptkp                           = $datasalary->ptkp;
                                                $salary                         = $datasalary->salary;
                                                $salary_deduction               = $datasalary->salary_deduction;
                                                $premi                          = $datasalary->premi;
                                                $jht_employee                   = $datasalary->jht_employee;
                                                $jp_employee                    = $datasalary->jp_employee;
                                                $jht                            = $datasalary->jht;
                                                $pesangon                       = $datasalary->pesangon;
                                                $tax_allowance_bonus_yearly     = $datasalary->tax_allowance_bonus_yearly;
                                                $tax_allowance_salary_yearly    = $datasalary->tax_allowance_salary_yearly;
                                                $tax_allowance_salary           = $datasalary->tax_allowance_salary;
                                                $ptkp                           = $ptkp;

                                                if ($pesangon > 437500000){
                                                    $tax_allowance_resign = (($pesangon - 437500000)*(1/3))+62500000;
                                                }else if ($pesangon > 100000000 && $pesangon < 437500000){
                                                    $tax_allowance_resign = (($pesangon -97500000)*(3/17))+2500000;
                                                }else if ($pesangon > 50000000 && $pesangon < 97500000){
                                                    $tax_allowance_resign = ($pesangon - 50000000)*(1/19);
                                                }else if ($pesangon <= 50000000){
                                                    $tax_allowance_resign = 0;
                                                }

                                                $pkp = $pesangon+$tax_allowance_resign;

                                                if ($pesangon > 500000000){
                                                    $pph_resign = (($pkp-500000000)*0.25)+62500000;
                                                }else if ($pesangon > 100000000 && $pesangon < 500000000){
                                                    $pph_resign = (($pkp-100000000)*0.15)+2500000;
                                                }else if ($pesangon > 50000000 && $pesangon < 100000000){
                                                    $pph_resign = ($pkp-50000000)*0.05;
                                                }else if ($pesangon <= 50000000){
                                                    $pph_resign = 0;
                                                }

                                                $pphresignid = $this->GetPphResignId($pphid);

                                                if (!empty($pphresignid)) {
                                                    foreach ($pphresignid as $pphresignid) {
                                                        $detailid = $pphresignid->id;

                                                        $data_detail_pph = [
                                                            'pph_id'                => $pphid,
                                                            'tax_allowance_resign'  => $tax_allowance_resign,
                                                            'pph_resign'            => $pph_resign,
                                                        ];

                                                        PphResign::whereId($detailid)->update($data_detail_pph);
                                                    }
                                                }else{

                                                    $data_detail_pph = [
                                                        'pph_id'                => $pphid,
                                                        'tax_allowance_resign'  => $tax_allowance_resign,
                                                        'pph_resign'            => $pph_resign,
                                                    ];

                                                    PphResign::create($data_detail_pph);
                                                }

                                            }
                                        }

                                        //Perhitungan PPH Total
                                        $datapph = $this->getDataPph($pphid);

                                        if (!empty($datapph)) {
                                            foreach ($datapph as $datapph) {

                                                $data_detail_pph = [
                                                    'tax_allowance_total'   => $datapph->tax_allowance_total,
                                                    'pph_due'               => $datapph->pph_due,
                                                    'pph_paid'              => $datapph->pph_due,
                                                ];

                                                Pph::whereId($pphid)->update($data_detail_pph);

                                            }
                                        }

                                    }
                                }
                            }
                        }
                    }
                    // end pph process

                    DB::commit();

                    return response()->json(['message' => __('Berhasil Proses Payroll dan PPH 21.')]);

                }catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['message' => $e->getMessage()], 400);
                }
            }
        }
    }

    public function printSlip()
    {
        $payroll = Payroll::all();

        $pdf = PDF::loadview('payroll_pdf',['payroll'=>$payroll]);
        return $pdf->download('slip-gaji.pdf');
    }

    public function deletePayroll()
    {
        // Payroll::truncate();
        // pph::truncate();
        // pphdetail::truncate();
        // PeriodePayroll::truncate();
        // AbsensiPayrollStaff::truncate();
    }

    public function downloadRekapanSmm()
    {
        return Excel::download(new RekapanExportSmm, 'Rekapan (SMM).xlsx');
        // return ['st'=>'err'];
    }

    public function downloadRekapanAtm($company)
    {
         return Excel::download(new RekapanExportAtm, 'Rekapan ($company).xlsx');
    }

    public function downloadPermataSmm()
    {
        return Excel::download(new PermataExportSmm, 'Permata (SMM).xlsx');
    }

    public function downloadPermataAtm()
    {
        return Excel::download(new PermataExportAtm, 'Permata (ATM).xlsx');
    }

    public function downloadExcellSmm()
    {
        return Excel::download(new ExcellExportSmm, 'Excell (SMM).xlsx');
    }

    public function downloadExcellAtm()
    {
        return Excel::download(new ExcellExportAtm, 'Excell (ATM).xlsx');
    }

    public function downloadFullSmm()
    {
        return Excel::download(new FullExportSmm, 'Full (SMM).xlsx');
    }

    public function downloadFullAtm()
    {
        return Excel::download(new FullExportAtm, 'Full (ATM).xlsx');
    }

    public function downloadPPHSmm()
    {
        return Excel::download(new PphExportSmm, 'PPH (SMM).xlsx');
        return ['st'=>'err'];
    }

    public function downloadPPHAtm()
    {
        return Excel::download(new PphExportAtm, 'PPH (ATM).xlsx');
        return ['st'=>'err'];
    }

    public function getPeriode($bulan1, $bulan2, $bulan3, $tahun1, $tahun2, $tahun3, $tahun4, $tahun5)
    {
        $periode = DB::select('select (concat(:thn1, "-" ,:bln1, "-" ,"01")) as date_created, concat(:thn2, "-" ,
        date_format(date_add(concat(:thn3, "-" ,:bln2, "-" ,"01"), interval -2 month), "%m"),  "-" ,"21") as awal,
        concat(:thn4, "-" ,date_format(date_add(concat(:thn5, "-" ,:bln3, "-" ,"01"), interval -1 month), "%m"),
         "-" ,"20") as akhir',
        array('bln1' => $bulan1, 'bln2' => $bulan2, 'bln3' => $bulan3,
        'thn1' => $tahun1, 'thn2' => $tahun2, 'thn3' => $tahun3, 'thn4' => $tahun4, 'thn5' => $tahun5));

        return $periode;
    }

    public function getGaji()
    {
        $gaji = DB::select('SELECT kantor, nrp, bagian, nik, nama, npwp, stapajak, jenis_gaji,
        coalesce(jhari, 0) as jhari, coalesce(gapok, 0) as gapok, coalesce(uang_hadir, 0) as uang_hadir,
        coalesce(uang_prestasi, 0) as uang_prestasi, coalesce(bruto_gaji, 0) as bruto_gaji,
        coalesce(jkm, 0) as jkm, coalesce(jkk, 0) as jkk, coalesce(bpjskes, 0) as bpjskes, coalesce(bpjskeskar, 0) as bpjskeskar,
        coalesce(hari, 0) as hari, coalesce(sakit, 0) as sakit, coalesce(ijin, 0) as ijin, coalesce(alpa, 0) as alpa,
        coalesce(cuti, 0) as cuti, coalesce(off, 0) as off, coalesce(st, 0) as st, coalesce(telat_kurang, 0) as telat_kurang,
        coalesce(telat_lebih, 0) as telat_lebih, coalesce(setengah_hari, 0) as setengah_hari,
        coalesce(nilai_s, 0) as nilai_s, coalesce(nilai_i, 0) as nilai_i, coalesce(nilai_a, 0) as nilai_a, coalesce(nilai_c, 0) as nilai_c,
        coalesce(nilai_off, 0) as nilai_off, coalesce(nilai_st, 0) as nilai_st, coalesce(nilaitelatkurang, 0) as nilaitelatkurang,
        coalesce(nilaitelatlebih, 0) as nilaitelatlebih, coalesce(nilaisetengahhari, 0) as nilaisetengahhari,
        coalesce(totalpotonganabsensi, 0) as totalpotonganabsensi, lembur,
        coalesce(l1, 0) as l1, coalesce(tarif_l1, 0) as tarif_l1, coalesce(nilai_l1, 0) as nilai_l1,
        coalesce(l2, 0) as l2, coalesce(tarif_l2, 0) as tarif_l2, coalesce(nilai_l2, 0) as nilai_l2,
        coalesce(l2minggu, 0) as l2minggu, coalesce(tarifl2minggu, 0) as tarifl2minggu, coalesce(nilail2minggu, 0) as nilail2minggu,
        coalesce(l3minggu, 0) as l3minggu, coalesce(tarifl3minggu, 0) as tarifl3minggu, coalesce(nilail3minggu, 0) as nilail3minggu,
        coalesce(total_lembur, 0) as total_lembur, coalesce(koperasi, 0) as koperasi, coalesce(jht, 0) as jht, coalesce(pensiun, 0) as pensiun,
        coalesce(total_potongan, 0) as total_potongan, coalesce(total_gaji, 0) as total_gaji,
        nama_1, norek, status_payroll_staff
        FROM absensi_payroll_staff WHERE status_payroll_staff = 0');

        return $gaji;
    }

    public function getGajiStaff($tglpph, $bulan1, $tahun1)
    {
        $gajistaff = DB::select('SELECT a.kantor, a.bagian, a.nrp, a.nik, a.nama, a.stapajak, a.npwp,
            COALESCE((SELECT thr FROM thr WHERE nik = a.nik AND periode_bulan = :bulan1 AND periode_tahun = :tahun1),0) AS thr,
            COALESCE((SELECT bonus FROM bonus WHERE nik = a.nik AND periode_bulan = :bulan2 AND periode_tahun = :tahun2),0) AS bonus,
            COALESCE((SELECT pesangon FROM pesangon WHERE nik = a.nik AND periode_bulan = :bulan3 AND periode_tahun = :tahun3),0) AS pesangon,
            SUM(COALESCE(a.gapok,0)) as jumlahgapok,
            SUM(COALESCE(a.premihadir,0)) as premihadir,
            SUM(COALESCE(a.premiprod,0)) as premiprod,
            SUM(COALESCE(a.uangjabatan,0)) as uangjabatan,
            SUM(COALESCE(a.totallembur,0)) as totallembur,
            0 as tlain,
            SUM(COALESCE(a.jkm,0)) as jkm,
            SUM(COALESCE(a.jkk,0)) as jkk,
            (SUM(COALESCE(a.bpjskes,0))+SUM(COALESCE(a.bpjskeskar,0))) as bpjskesper,

            0 as biayajabatan,

            sum(COALESCE(a.potjhtkar,0)) as jht,
            sum(COALESCE(a.potpensiunkar,0)) as pensiunkar,
            sum(COALESCE(a.totalpotonganabsensi,0)) as absensi,
            MONTH(a.date_created) as bulan,
            YEAR(a.date_created) as tahun
            from payroll a where a.stapajak is not null and a.date_created = :tanggal
            group by a.nrp, a.nik, a.nama, a.stapajak, a.bagian, a.date_created, a.kantor, a.npwp',
            array('tanggal' => $tglpph, 'bulan1' => $bulan1, 'tahun1' => $tahun1, 'bulan2' => $bulan1, 'tahun2' => $tahun1, 'bulan3' => $bulan1, 'tahun3' => $tahun1)
        );

        return $gajistaff;
    }

    public function getGajiStaffTahunan($bulan1, $tahun1)
    {
        $gajistaff = DB::select("SELECT a.kantor, a.bagian, a.nrp, a.nik, a.nama, a.stapajak, a.npwp, year(a.date_created) AS tahun,

            (SUM(COALESCE(a.gapok,0))-(SELECT SUM(COALESCE(jumlah,0)) FROM pph_detail WHERE pph_id in (SELECT DISTINCT(id) FROM pph WHERE nik = a.nik AND periode_tahun = tahun) AND
            parameter_id = (SELECT id FROM parameter_pph WHERE parameter_code = 'jumlahgapok'))) as jumlahgapok,

            (SUM(COALESCE(a.premihadir,0))-(SELECT SUM(COALESCE(jumlah,0)) FROM pph_detail WHERE pph_id in (SELECT DISTINCT(id) FROM pph WHERE nik = a.nik AND periode_tahun = tahun) AND
            parameter_id = (SELECT id FROM parameter_pph WHERE parameter_code = 'premihadir'))) as premihadir,

            (SUM(COALESCE(a.premiprod,0))-(SELECT SUM(COALESCE(jumlah,0)) FROM pph_detail WHERE pph_id in (SELECT DISTINCT(id) FROM pph WHERE nik = a.nik AND periode_tahun = tahun) AND
            parameter_id = (SELECT id FROM parameter_pph WHERE parameter_code = 'premiprod'))) as premiprod,

            (SUM(COALESCE(a.uangjabatan,0))-(SELECT SUM(COALESCE(jumlah,0)) FROM pph_detail WHERE pph_id in (SELECT DISTINCT(id) FROM pph WHERE nik = a.nik AND periode_tahun = tahun) AND
            parameter_id = (SELECT id FROM parameter_pph WHERE parameter_code = 'uangjabatan'))) as uangjabatan,

            (SUM(COALESCE(a.totallembur,0))-(SELECT SUM(COALESCE(jumlah,0)) FROM pph_detail WHERE pph_id in (SELECT DISTINCT(id) FROM pph WHERE nik = a.nik AND periode_tahun = tahun) AND
            parameter_id = (SELECT id FROM parameter_pph WHERE parameter_code = 'totallembur'))) as totallembur,

            0 as tlain,

            -- (SUM(COALESCE(a.bonus,0))-(SELECT SUM(COALESCE(jumlah,0)) FROM pph_detail WHERE pph_id in (SELECT DISTINCT(id) FROM pph WHERE nik = a.nik AND periode_tahun = tahun) AND
            -- parameter_id = (SELECT id FROM parameter_pph WHERE parameter_code = 'bonus'))) as bonus,

            0 as tunjanganpph,

            (SUM(COALESCE(a.jkm,0))-(SELECT SUM(COALESCE(jumlah,0)) FROM pph_detail WHERE pph_id in (SELECT DISTINCT(id) FROM pph WHERE nik = a.nik AND periode_tahun = tahun) AND
            parameter_id = (SELECT id FROM parameter_pph WHERE parameter_code = 'jkm'))) as jkm,

            (SUM(COALESCE(a.jkk,0))-(SELECT SUM(COALESCE(jumlah,0)) FROM pph_detail WHERE pph_id in (SELECT DISTINCT(id) FROM pph WHERE nik = a.nik AND periode_tahun = tahun) AND
            parameter_id = (SELECT id FROM parameter_pph WHERE parameter_code = 'jkk'))) as jkk,

            (SUM(COALESCE(a.bpjskes,0))+SUM(COALESCE(a.bpjskeskar,0))-(SELECT SUM(COALESCE(jumlah,0)) FROM pph_detail WHERE pph_id in (SELECT DISTINCT(id) FROM pph WHERE nik = a.nik AND periode_tahun = tahun) AND
            parameter_id = (SELECT id FROM parameter_pph WHERE parameter_code = 'bpjskesper'))) as bpjskesper,

            (SUM(COALESCE(a.potjhtkar,0))-(SELECT SUM(COALESCE(jumlah,0)) FROM pph_detail WHERE pph_id in (SELECT DISTINCT(id) FROM pph WHERE nik = a.nik AND periode_tahun = tahun) AND
            parameter_id = (SELECT id FROM parameter_pph WHERE parameter_code = 'jht'))) as jht,

            (SUM(COALESCE(a.potpensiunkar,0))-(SELECT SUM(COALESCE(jumlah,0)) FROM pph_detail WHERE pph_id in (SELECT DISTINCT(id) FROM pph WHERE nik = a.nik AND periode_tahun = tahun) AND
            parameter_id = (SELECT id FROM parameter_pph WHERE parameter_code = 'pensiunkar'))) as pensiunkar,

            (SUM(COALESCE(a.totalpotonganabsensi,0))-(SELECT SUM(COALESCE(jumlah,0)) FROM pph_detail WHERE pph_id in (SELECT DISTINCT(id) FROM pph WHERE nik = a.nik AND periode_tahun = tahun) AND
            parameter_id = (SELECT id FROM parameter_pph WHERE parameter_code = 'absensi'))) as absensi,

            COALESCE((SELECT thr FROM thr WHERE nik = a.nik AND periode_bulan = :bulan1 AND periode_tahun = tahun),0) AS thr,

            COALESCE((SELECT bonus FROM bonus WHERE nik = a.nik AND periode_bulan = :bulan2 AND periode_tahun = tahun),0) AS bonus,

            COALESCE((SELECT pesangon FROM pesangon WHERE nik = a.nik AND periode_bulan = :bulan3 AND periode_tahun = tahun),0) AS pesangon,

            0 as biayajabatan

            from payroll a WHERE a.stapajak is not null and year(a.date_created) = :tahun1
            group BY a.kantor, a.npwp, a.nrp, a.nik, a.nama, a.stapajak, a.bagian, year(a.date_created)",
            array('bulan1' => $bulan1, 'bulan2' => $bulan1, 'bulan3' => $bulan1, 'tahun1' => $tahun1
        ));

        return $gajistaff;
    }

    public function getParameterPph()
    {
        $parameterpph = DB::table('parameter_pph')
                     ->select('*')
                     ->groupBy('id')
                     ->get();

        return $parameterpph;
    }

    public function GetBiayaJabatanId($pphid)
    {
        $pphdetailid = DB::select('select id from pph_detail where pph_id = :pphid and parameter_id = (select id from parameter_pph where parameter_code = :tunjanganpph)',
         array('pphid' => $pphid, 'tunjanganpph' => 'biayajabatan'));

        return $pphdetailid;
    }

    public function GetTunjanganPphId($pphid)
    {
        $pphdetailid = DB::select('select id from pph_detail where pph_id = :pphid and parameter_id = (select id from parameter_pph where parameter_code = :tunjanganpph)',
         array('pphid' => $pphid, 'tunjanganpph' => 'tunjanganpph'));

        return $pphdetailid;
    }

    public function GetPphSalaryId($pphid)
    {
        $pphdetailid = DB::select('select id from pph_salary where pph_id = :pphid',
         array('pphid' => $pphid));

        return $pphdetailid;
    }

    public function GetPphMasaId($pphid)
    {
        $pphdetailid = DB::select('select id from pph_masa where pph_id = :pphid',
         array('pphid' => $pphid));

        return $pphdetailid;
    }

    public function getPphDetailId($pphid, $parameterpphid)
    {
        $pphdetailid = DB::select('select id from pph_detail where pph_id = :pphid and parameter_id = :parameterpphid',
         array('pphid' => $pphid, 'parameterpphid' => $parameterpphid));

        return $pphdetailid;
    }

    public function GetPphBonusId($pphid)
    {
        $pphdetailid = DB::select('select id from pph_bonus where pph_id = :pphid',
         array('pphid' => $pphid));

        return $pphdetailid;
    }

    public function GetPphResignId($pphid)
    {
        $pphdetailid = DB::select('select id from pph_resign where pph_id = :pphid',
         array('pphid' => $pphid));

        return $pphdetailid;
    }

    public function checkPayroll( $idemployee,  $startdate, $enddate, $createdate)
    {
        $payrollcheck = DB::select('SELECT id FROM payroll WHERE nrp = :idemployee AND periode_awal = :startdate
        AND periode_akhir = :enddate AND date_created = :createdate',
        array('idemployee' => $idemployee, 'startdate' => $startdate, 'enddate' => $enddate, 'createdate' => $createdate));

        return $payrollcheck;
    }

    public function checkPph($idemployee,  $bulan1, $tahun1)
    {
        $pphcheck = DB::select('SELECT id FROM pph WHERE nik = :idemployee AND periode_bulan = :bulan1 AND periode_tahun = :tahun1',
        array('idemployee' => $idemployee, 'bulan1' => $bulan1, 'tahun1' => $tahun1));

        return $pphcheck;
    }

    public function checkThr($idemployee,  $bulan1, $tahun1)
    {
        $thrcheck = DB::select('SELECT * FROM thr WHERE nik = :idemployee AND periode_bulan = :bulan1 AND periode_tahun = :tahun1',
        array('idemployee' => $idemployee, 'bulan1' => $bulan1, 'tahun1' => $tahun1));

        return $thrcheck;
    }

    public function checkBonus($idemployee,  $bulan1, $tahun1)
    {
        $bonuscheck = DB::select('SELECT * FROM bonus WHERE nik = :idemployee AND periode_bulan = :bulan1 AND periode_tahun = :tahun1',
        array('idemployee' => $idemployee, 'bulan1' => $bulan1, 'tahun1' => $tahun1));

        return $bonuscheck;
    }

    public function checkPesangon($idemployee,  $bulan1, $tahun1)
    {
        $pesangoncheck = DB::select('SELECT * FROM pesangon WHERE nik = :idemployee AND periode_bulan = :bulan1 AND periode_tahun = :tahun1',
        array('idemployee' => $idemployee, 'bulan1' => $bulan1, 'tahun1' => $tahun1));

        return $pesangoncheck;
    }

    public function getDataSalary($pphid)
    {
        $pphdetail = DB::select("SELECT a.id,

        -- PTKP --
        (SELECT jumlah_ptkp FROM ptkp WHERE CODE = a.ptkp_code) AS ptkp,
        -- PTKP --

        -- SALARY --
        (SELECT SUM(jumlah) FROM pph_detail b INNER JOIN parameter_pph c ON b.parameter_id=c.id WHERE b.pph_id = a.id AND c.status_parameter_pph = 'gaji') AS salary,
        -- SALARY --

        -- SALARY DEDUCTION --
        (SELECT SUM(jumlah) FROM pph_detail b INNER JOIN parameter_pph c ON b.parameter_id=c.id WHERE b.pph_id = a.id AND c.status_parameter_pph = 'potongan gaji')
        AS salary_deduction,
        -- SALARY DEDUCTION --

        -- PREMI --
        (SELECT SUM(jumlah) FROM pph_detail b INNER JOIN parameter_pph c ON b.parameter_id=c.id WHERE b.pph_id = a.id AND c.status_parameter_pph = 'jaminan') AS premi,
        -- PREMI --

        -- JHT EMPLOYEE --
        (SELECT SUM(jumlah) FROM pph_detail b INNER JOIN parameter_pph c ON b.parameter_id=c.id WHERE b.pph_id = a.id AND c.parameter_code = 'jht') AS jht_employee,
        -- JHT EMPLOYEE --

        -- JP EMPLOYEE --
        (SELECT SUM(jumlah) FROM pph_detail b INNER JOIN parameter_pph c ON b.parameter_id=c.id WHERE b.pph_id = a.id AND c.parameter_code = 'pensiunkar') AS jp_employee,
        -- JP EMPLOYEE --

        -- JHT --
        (SELECT SUM(jumlah) FROM pph_detail b INNER JOIN parameter_pph c ON b.parameter_id=c.id WHERE b.pph_id = a.id AND c.status_parameter_pph = 'potongan') AS jht,
        -- JHT --

        -- THR --
        (SELECT SUM(jumlah) FROM pph_detail b INNER JOIN parameter_pph c ON b.parameter_id=c.id WHERE b.pph_id = a.id AND c.status_parameter_pph = 'thr') AS thr,
        -- THR --

        -- BONUS --
        (SELECT SUM(jumlah) FROM pph_detail b INNER JOIN parameter_pph c ON b.parameter_id=c.id WHERE b.pph_id = a.id AND c.status_parameter_pph = 'bonus') AS bonus,
        -- BONUS --

        -- PESANGON --
        (SELECT SUM(jumlah) FROM pph_detail b INNER JOIN parameter_pph c ON b.parameter_id=c.id WHERE b.pph_id = a.id AND c.status_parameter_pph = 'pesangon')
        AS pesangon,
        -- PESANGON --

        -- TAX ALLOWANCE SALARY YEARLY --
        COALESCE((SELECT tax_allowance_yearly FROM pph_salary WHERE pph_id = a.id), 0) AS tax_allowance_salary_yearly,
        -- TAX ALLOWANCE SALARY YEARLY --

        -- TAX ALLOWANCE THR YEARLY --
        COALESCE((SELECT tax_allowance_thr_yearly FROM pph_bonus WHERE pph_id = a.id), 0) AS tax_allowance_thr_yearly,
        -- TAX ALLOWANCE THR YEARLY --

        -- TAX ALLOWANCE BONUS YEARLY --
        COALESCE((SELECT tax_allowance_bonus_yearly FROM pph_bonus WHERE pph_id = a.id), 0) AS tax_allowance_bonus_yearly,
        -- TAX ALLOWANCE BONUS YEARLY --

        -- TAX ALLOWANCE SALARY --
        COALESCE((SELECT tax_allowance FROM pph_salary WHERE pph_id = a.id), 0) AS tax_allowance_salary,
        -- TAX ALLOWANCE SALARY --

        -- TAX ALLOWANCE THR --
        COALESCE((SELECT tax_allowance_thr FROM pph_bonus WHERE pph_id = a.id), 0) AS tax_allowance_thr,
        -- TAX ALLOWANCE THR --

        -- TAX ALLOWANCE BONUS --
        COALESCE((SELECT tax_allowance_bonus FROM pph_bonus WHERE pph_id = a.id), 0) AS tax_allowance_bonus
        -- TAX ALLOWANCE BONUS --

        FROM pph a WHERE a.id = :pphid",
        array('pphid' => $pphid));

        return $pphdetail;
    }

    public function getDataSalaryYearly($pphid, $bulan1, $tahun1)
    {
        $pphdetail = DB::select("SELECT a.id,

        -- PTKP --
        (SELECT jumlah_ptkp FROM ptkp WHERE CODE = a.ptkp_code) AS ptkp,
        -- PTKP --

        -- SALARY --
        (SELECT SUM(jumlah) FROM pph_detail b INNER JOIN parameter_pph c ON b.parameter_id=c.id
		  WHERE b.pph_id IN (SELECT id FROM pph WHERE nik = a.nik AND periode_tahun = a.periode_tahun) AND c.status_parameter_pph = 'gaji') AS salary,
        -- SALARY --

        -- SALARY DEDUCTION --
        (SELECT SUM(jumlah) FROM pph_detail b INNER JOIN parameter_pph c ON b.parameter_id=c.id
		  WHERE b.pph_id IN (SELECT id FROM pph WHERE nik = a.nik AND periode_tahun = a.periode_tahun) AND c.status_parameter_pph = 'potongan gaji')
        AS salary_deduction,
        -- SALARY DEDUCTION --

        -- PREMI --
        (SELECT SUM(jumlah) FROM pph_detail b INNER JOIN parameter_pph c ON b.parameter_id=c.id
		  WHERE b.pph_id IN (SELECT id FROM pph WHERE nik = a.nik AND periode_tahun = a.periode_tahun) AND c.status_parameter_pph = 'jaminan') AS premi,
        -- PREMI --

        -- JHT EMPLOYEE --
        (SELECT SUM(jumlah) FROM pph_detail b INNER JOIN parameter_pph c ON b.parameter_id=c.id
		  WHERE b.pph_id IN (SELECT id FROM pph WHERE nik = a.nik AND periode_tahun = a.periode_tahun) AND c.parameter_code = 'jht') AS jht_employee,
        -- JHT EMPLOYEE --

        -- JP EMPLOYEE --
        (SELECT SUM(jumlah) FROM pph_detail b INNER JOIN parameter_pph c ON b.parameter_id=c.id
		  WHERE b.pph_id IN (SELECT id FROM pph WHERE nik = a.nik AND periode_tahun = a.periode_tahun) AND c.parameter_code = 'pensiunkar') AS jp_employee,
        -- JP EMPLOYEE --

        -- JHT --
        (SELECT SUM(jumlah) FROM pph_detail b INNER JOIN parameter_pph c ON b.parameter_id=c.id
		  WHERE b.pph_id IN (SELECT id FROM pph WHERE nik = a.nik AND periode_tahun = a.periode_tahun) AND c.status_parameter_pph = 'potongan') AS jht,
        -- JHT --

        -- THR --
        (SELECT SUM(jumlah) FROM pph_detail b INNER JOIN parameter_pph c ON b.parameter_id=c.id
		  WHERE b.pph_id IN (SELECT id FROM pph WHERE nik = a.nik AND periode_tahun = a.periode_tahun) AND c.status_parameter_pph = 'thr') AS thr,
        -- THR --

        -- BONUS --
        (SELECT SUM(jumlah) FROM pph_detail b INNER JOIN parameter_pph c ON b.parameter_id=c.id
		  WHERE b.pph_id IN (SELECT id FROM pph WHERE nik = a.nik AND periode_tahun = a.periode_tahun) AND c.status_parameter_pph = 'bonus') AS bonus,
        -- BONUS --

        -- PESANGON --
        (SELECT SUM(jumlah) FROM pph_detail b INNER JOIN parameter_pph c ON b.parameter_id=c.id
		  WHERE b.pph_id IN (SELECT id FROM pph WHERE nik = a.nik AND periode_tahun = a.periode_tahun) AND c.status_parameter_pph = 'pesangon')
        AS pesangon,
        -- PESANGON --

        -- TAX ALLOWANCE THR YEARLY --
        COALESCE((SELECT tax_allowance_yearly FROM pph_masa WHERE pph_id = a.id), 0) AS tax_allowance_masa_yearly,
        -- TAX ALLOWANCE THR YEARLY --

        -- PPH PAID --
        COALESCE((SELECT SUM(pph_paid) FROM pph WHERE nik = a.nik AND periode_tahun = a.periode_tahun), 0) AS pph_paid
        -- PPH PAID --

        FROM pph a WHERE a.id = :pphid AND periode_bulan = :bulan AND periode_tahun = :tahun",
        array('pphid' => $pphid, 'bulan' => $bulan1, 'tahun' => $tahun1));

        return $pphdetail;
    }

    public function getDataPph($pphid)
    {
        $pphdetail = DB::select("SELECT a.id,

        (COALESCE((SELECT tax_allowance FROM pph_salary WHERE pph_id = a.id),0)+
        COALESCE((SELECT tax_allowance_thr FROM pph_bonus WHERE pph_id = a.id),0)+
        COALESCE((SELECT tax_allowance_bonus FROM pph_bonus WHERE pph_id = a.id),0)+
        COALESCE((SELECT tax_allowance_resign FROM pph_resign WHERE pph_id = a.id),0)) AS tax_allowance_total,

        (COALESCE((SELECT pph_salary FROM pph_salary WHERE pph_id = a.id),0)+
        COALESCE((SELECT pph_thr FROM pph_bonus WHERE pph_id = a.id),0)+
        COALESCE((SELECT pph_bonus FROM pph_bonus WHERE pph_id = a.id),0)+
        COALESCE((SELECT pph_resign FROM pph_resign WHERE pph_id = a.id),0)) AS pph_due

        FROM pph a WHERE a.id = :pphid",
        array('pphid' => $pphid));

        return $pphdetail;
    }

    public function getPphDetail($pphid, $parameterid)
    {
        $pphdetail = DB::select('SELECT * FROM pph_detail where pph_id = :pphid and parameter_id = :parameterid',
        array('pphid' => $pphid, 'parameterid' => $parameterid));

        return $pphdetail;
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('recruitment::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('recruitment::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
