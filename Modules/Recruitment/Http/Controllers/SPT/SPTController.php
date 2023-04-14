<?php

namespace Modules\Recruitment\Http\Controllers\SPT;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Recruitment\Entities\Spt;
use Modules\Recruitment\Entities\SptDetail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SPTExport;
use App\Exports\PrintSPTExport;

class SPTController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('recruitment::spt.index');
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
            'tahun' => 'required',
        ]);

        // proses spt start
        $tahun = $request->input('tahun');
        $gajistaff = $this->getGajiStaff($tahun);

        if (!empty($gajistaff)) {
            foreach ($gajistaff as $gajistaff) {
                $data_pph = [
                    'nrp'                       => $gajistaff->nrp,
                    'nik'                       => $gajistaff->nik,
                    'nama'                      => $gajistaff->nama,
                    'ptkp_code'                 => $gajistaff->stapajak,
                    'salary'                    => '0',
                    'salary_deduction'          => '0',
                    'allowance'                 => '0',
                    'gross_yearly'              => '0',
                    'position_cost_yearly'      => '0',
                    'jht_yearly'                => '0',
                    'deduction_yearly'          => '0',
                    'netto_yearly'              => '0',
                    'ptkp'                      => '0',
                    'pkp'                       => '0',
                    'pph21_terutang_setahun'    => '0',
                    'pph21_dibayar'             => '0',
                    'periode_tahun'             => $gajistaff->tahun,
                ];


                $save = Spt::create($data_pph);

                $parameterpph = $this->getParameterPph();

                if (!empty($parameterpph)) {
                    foreach ($parameterpph as $parameterpph) {
                        $code = $parameterpph->parameter_code;

                        $data_detail_pph = [
                            'spt_id'        => $save->id,
                            'parameter_id'  => $parameterpph->id,
                            'jumlah'        => $gajistaff->$code,
                        ];

                        SptDetail::create($data_detail_pph);
                    }
                }

                $pphid = $save->id;

                //PERHITUNGAN PPH SALARY

                // $step1salary = $this->getDataStep1Salary($pphid, $tahun);
                // if (!empty($step1salary)) {
                //     foreach ($step1salary as $step1salary) {

                //         $data_detail_pph = [
                //             'tax_allowance_salary'        => $step1salary->tax_allowance_salary
                //         ];

                //         Spt::whereId($pphid)->update($data_detail_pph);
                //     }
                // }

                // $step2salary = $this->getDataStep2Salary($pphid, $tahun);
                // if (!empty($step2salary)) {
                //     foreach ($step2salary as $step2salary) {

                //         $data_detail_pph = [
                //             'tax_allowance_salary'        => $step2salary->tax_allowance_salary
                //         ];

                //         Spt::whereId($pphid)->update($data_detail_pph);
                //     }
                // }

                // $step3salary = $this->getDataStep3Salary($pphid, $tahun);
                // if (!empty($step3salary)) {
                //     foreach ($step3salary as $step3salary) {

                //         $data_detail_pph = [
                //             'pph_salary'        => $step3salary->pph_salary
                //         ];

                //         Spt::whereId($pphid)->update($data_detail_pph);
                //     }
                // }

                //PERHITUNGAN PPH THR

                // $step1thr = $this->getDataStep1Thr($pphid, $tahun);
                // if (!empty($step1thr)) {
                //     foreach ($step1thr as $step1thr) {

                //         $data_detail_pph = [
                //             'tax_allowance_thr'        => $step1thr->tax_allowance_thr
                //         ];

                //         Spt::whereId($pphid)->update($data_detail_pph);
                //     }
                // }

                // $step2thr = $this->getDataStep2Thr($pphid, $tahun);
                // if (!empty($step2thr)) {
                //     foreach ($step2thr as $step2thr) {

                //         $data_detail_pph = [
                //             'tax_allowance_thr'        => $step2thr->tax_allowance_thr
                //         ];

                //         Spt::whereId($pphid)->update($data_detail_pph);
                //     }
                // }

                // $step3thr = $this->getDataStep3Thr($pphid, $tahun);
                // if (!empty($step3thr)) {
                //     foreach ($step3thr as $step3thr) {

                //         $data_detail_pph = [
                //             'pph_thr'        => $step3thr->pph_thr
                //         ];

                //         Spt::whereId($pphid)->update($data_detail_pph);
                //     }
                // }

                //PERHITUNGAN PPH Bonus

                // $step1bonus = $this->getDataStep1Bonus($pphid, $tahun);
                // if (!empty($step1bonus)) {
                //     foreach ($step1bonus as $step1bonus) {

                //         $data_detail_pph = [
                //             'tax_allowance_bonus'        => $step1bonus->tax_allowance_bonus
                //         ];

                //         Spt::whereId($pphid)->update($data_detail_pph);
                //     }
                // }

                // $step2bonus = $this->getDataStep2Bonus($pphid, $tahun);
                // if (!empty($step2bonus)) {
                //     foreach ($step2bonus as $step2bonus) {

                //         $data_detail_pph = [
                //             'tax_allowance_bonus'        => $step2bonus->tax_allowance_bonus
                //         ];

                //         Spt::whereId($pphid)->update($data_detail_pph);
                //     }
                // }

                // $step3bonus = $this->getDataStep3Bonus($pphid, $tahun);
                // if (!empty($step3bonus)) {
                //     foreach ($step3bonus as $step3bonus) {

                //         $data_detail_pph = [
                //             'pph_bonus'        => $step3bonus->pph_bonus
                //         ];

                //         Spt::whereId($pphid)->update($data_detail_pph);
                //     }
                // }

                //PERHITUNGAN SPT TAHUNAN

                $step1spt = $this->getDataStep1Spt($pphid, $tahun);
                if (!empty($step1spt)) {
                    foreach ($step1spt as $step1spt) {

                        $data_detail_pph = [
                            'tax_allowance_yearly'        => $step1spt->tax_allowance_yearly
                        ];

                        Spt::whereId($pphid)->update($data_detail_pph);
                    }
                }

                $step2spt = $this->getDataStep2Spt($pphid, $tahun);
                if (!empty($step2spt)) {
                    foreach ($step2spt as $step2spt) {

                        $data_detail_pph = [
                            'tax_allowance_yearly'        => $step2spt->tax_allowance_yearly
                        ];

                        Spt::whereId($pphid)->update($data_detail_pph);
                    }
                }

                $step3spt = $this->getDataStep3Spt($pphid, $tahun);
                if (!empty($step3spt)) {
                    foreach ($step3spt as $step3spt) {

                        $data_detail_pph = [
                            'gross_yearly'          => $step3spt->gross_yearly,
                            'position_cost_yearly'  => $step3spt->position_cost_yearly,
                            'jht_employee_yearly'   => $step3spt->jht_employee_yearly,
                            'jp_employee_yearly'    => $step3spt->jp_employee_yearly,
                            'jht_yearly'            => $step3spt->jht_yearly,
                            'netto_yearly'          => $step3spt->netto_yearly,
                            'ptkp'                  => $step3spt->ptkp,
                            'pkp'                   => $step3spt->pkp,
                            'pph_due'               => $step3spt->pph_due,
                            'pph_paid'              => $step3spt->pph_paid,
                        ];

                        Spt::whereId($pphid)->update($data_detail_pph);
                    }
                }
            }

            return response()->json(['message' => __('Berhasil Proses SPT Tahunan.')]);
        }
        // proses pph end
    }

    public function getGajiStaff($tahun)
    {
        $gajistaff = DB::select('SELECT a.kantor, a.bagian, a.nrp, a.nik, a.nama, a.stapajak, a.npwp, 0 as tunjanganpph,
        COALESCE((SELECT  sum(coalesce(thr,0)) FROM thr WHERE nik = a.nik AND periode_tahun = :tahun1),0) AS thr,
        COALESCE((SELECT  sum(coalesce(bonus,0)) FROM bonus WHERE nik = a.nik AND periode_tahun = :tahun2),0) AS bonus,
        COALESCE((SELECT  sum(coalesce(pesangon,0)) FROM pesangon WHERE nik = a.nik AND periode_tahun = :tahun3),0) AS pesangon,
        sum(coalesce(a.gapok,0)) as jumlahgapok,
        sum(coalesce(a.premihadir,0)) as premihadir,
        sum(coalesce(a.premiprod,0)) as premiprod,
        sum(coalesce(a.uangjabatan,0)) as uangjabatan,
        sum(coalesce(a.totallembur,0)) as totallembur,
        0 as tlain,
        sum(coalesce(a.jkm,0)) as jkm,
        sum(coalesce(a.jkk,0)) as jkk,
        sum(coalesce(a.bpjskes,0)) as bpjskesper,

        sum(coalesce(a.potjhtkar,0)) as jht,
        sum(coalesce(a.potpensiunkar,0)) as pensiunkar,
        sum(coalesce(a.totalpotonganabsensi,0)) as absensi,

        0 as biayajabatan,

        year(a.date_created) as tahun
        from payroll a where a.stapajak is not null and year(a.date_created) = :tahun4
        group by a.kantor, a.npwp, a.nrp, a.nik, a.nama, a.stapajak, a.bagian, year(a.date_created)',
        array('tahun1' => $tahun, 'tahun2' => $tahun, 'tahun3' => $tahun, 'tahun4' => $tahun));

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

    public function getPphDibayar($pphid, $tahun)
    {
        $pkppph = DB::select('SELECT a.id, a.nik, (SELECT SUM(pph21_terutang_sebulan) FROM pph WHERE nik = a.nik and periode_tahun = a.periode_tahun) AS pphdibayar
        FROM spt a WHERE a.id = :pphid and a.periode_tahun = :tahun',
        array('pphid' => $pphid, 'tahun' => $tahun));

        return $pkppph;
    }

    public function GetTunjanganPphId($pphid)
    {
        $pphdetailid = DB::select("select id from spt_detail where spt_id = :sptid and parameter_id = (select id from parameter_pph where parameter_code = 'tunjanganpph')",
         array('sptid' => $pphid));

        return $pphdetailid;
    }

    public function GetBiayaJabatanId($pphid)
    {
        $pphdetailid = DB::select("select id from spt_detail where spt_id = :pphid and parameter_id = (select id from parameter_pph where parameter_code = 'biayajabatan')",
         array('pphid' => $pphid));

        return $pphdetailid;
    }

    public function downloadSPT()
    {
        return Excel::download(new SPTExport, 'SPT.xlsx');
        return ['st'=>'err'];
    }

    public function printSPT(Request $request)
    {
        $id = ($request->filled('id')) ? $request->id : 1;

        return Excel::download(new PrintSPTExport($id), 'SPTEmployee.xlsx');
        return ['st'=>'err'];
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
