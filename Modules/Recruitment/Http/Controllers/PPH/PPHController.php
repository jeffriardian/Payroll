<?php

namespace Modules\Recruitment\Http\Controllers\PPH;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Recruitment\Entities\pph;
use Modules\Recruitment\Entities\pphdetail;
use Modules\Recruitment\Entities\ptkp;

class PPHController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('recruitment::pph.index');
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
            'bulan' => 'required',
            'tahun' => 'required',
        ]);

        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $gaji = $this->getGaji($bulan, $tahun);

        if (!empty($gaji)) {
            foreach ($gaji as $gaji) {
                $data_pph = [
                    'nrp'                       => $gaji->nrp,
                    'nik'                       => $gaji->nik,
                    'nama'                      => $gaji->nama,
                    'ptkp_code'                 => $gaji->stapajak,
                    'gaji'                      => '0',
                    'bruto'                     => '0',
                    'potongan'                  => '0',
                    'netto_sebulan'             => '0',
                    'netto_setahun'             => '0',
                    'ptkp'                      => '0',
                    'pkp'                       => '0',
                    'pph21_terutang_setahun'    => '0',
                    'pph21_terutang_sebulan'    => '0',
                    'periode_bulan'             => $bulan,
                    'periode_tahun'             => $tahun,
                ];

                DB::beginTransaction();
                try {
                    $save = pph::create($data_pph);

                    $parameterpph = $this->getParameterPph();

                    if (!empty($parameterpph)) {
                        foreach ($parameterpph as $parameterpph) {
                            $code = $parameterpph->parameter_code;

                            $data_detail_pph = [
                                'pph_id'        => $save->id,
                                'parameter_id'  => $parameterpph->id,
                                'jumlah'        => $gaji->$code,
                            ];

                            $savedetail = pphdetail::create($data_detail_pph);
                        }
                    }

                    if (($gaji->biayajabatan) > 500000){
                        $pphid = $save->id;
                        $pphdetailbiayajabatanid = $this->GetBiayaJabatanId($pphid);

                        if (!empty($pphdetailbiayajabatanid)) {
                            foreach ($pphdetailbiayajabatanid as $pphdetailbiayajabatanid) {

                                $detailid = $pphdetailbiayajabatanid->id;

                                $update_biayajabatan = [
                                    'jumlah'        => '500000',
                                ];

                                pphdetail::whereId($detailid)->update($update_biayajabatan);
                            }
                        }
                    }

                    $pphid = $save->id;
                    $ptkpcode = $gaji->stapajak;
                    $pphdetailid = $this->GetTunjanganPphId($pphid);
                    $pkpsetahun = $this->GetpkpSetahun($pphid, $ptkpcode);

                    if (!empty($pphdetailid)) {
                        foreach ($pphdetailid as $pphdetailid) {
                            $detailid = $pphdetailid->id;

                            if (!empty($pkpsetahun)) {
                                foreach ($pkpsetahun as $pkpsetahun) {
                                    $pkp = $pkpsetahun->pkp;

                                    if ($pkp <= 47500000){
                                        $pkpsetahunpengurang= $pkp;
                                        $tunjanganpph = (($pkpsetahunpengurang)*(5/95)/12);

                                        $update_tunjanganpph = [
                                            'jumlah'        => $tunjanganpph,
                                        ];

                                        pphdetail::whereId($detailid)->update($update_tunjanganpph);

                                    }else if ($pkp > 47500000 and $pkp < 217500000){
                                        $pkpsetahunpengurang= $pkp - 47500000;
                                        $tunjanganpph = ((($pkpsetahunpengurang)*(15/85)+2500000)/12);

                                        $update_tunjanganpph = [
                                            'jumlah'        => $tunjanganpph,
                                        ];

                                        pphdetail::whereId($detailid)->update($update_tunjanganpph);

                                    }else if ($pkp > 217500000 and $pkp < 405000000){
                                        $pkpsetahunpengurang= $pkp - 217500000;
                                        $tunjanganpph = ((($pkpsetahunpengurang)*(25/75)+32500000)/12);

                                        $update_tunjanganpph = [
                                            'jumlah'        => $tunjanganpph,
                                        ];

                                        pphdetail::whereId($detailid)->update($update_tunjanganpph);

                                    }else if ($pkp > 405000000){
                                        $pkpsetahunpengurang= $pkp - 405000000;
                                        $tunjanganpph = ((($pkpsetahunpengurang)*(30/70)+95000000)/12);

                                        $update_tunjanganpph = [
                                            'jumlah'        => $tunjanganpph,
                                        ];

                                        pphdetail::whereId($detailid)->update($update_tunjanganpph);
                                    }

                                    // if ($pkp > 47500000)
                                    // {
                                    //     $tunjanganpph1 = (47500000*(1/19));

                                    //     $pkp1 = $pkp - 47500000;

                                    //     if ($pkp1 > 170000000)
                                    //     {
                                    //         $tunjanganpph2 = (170000000*(3/17));

                                    //         $pkp2 = $pkp1 - 170000000;

                                    //         if ($pkp2 > 187500000)
                                    //         {
                                    //             $tunjanganpph3 = (187500000*(1/3));

                                    //             $pkp3 = $pkp2 - 187500000;

                                    //             if ($pkp3 > 405000000)
                                    //             {
                                    //                 $tunjanganpph = ($tunjanganpph1)+($tunjanganpph2)+($tunjanganpph3)+($pkp3*(3/7));

                                    //                 $update_tunjanganpph = [
                                    //                     'jumlah'        => $tunjanganpph,
                                    //                 ];

                                    //                 pphdetail::whereId($detailid)->update($update_tunjanganpph);
                                    //             }
                                    //         }
                                    //         else
                                    //         {
                                    //             $tunjanganpph = ($tunjanganpph1)+($tunjanganpph2)+($pkp2*(1/3));

                                    //             $update_tunjanganpph = [
                                    //                 'jumlah'        => $tunjanganpph,
                                    //             ];

                                    //             pphdetail::whereId($detailid)->update($update_tunjanganpph);
                                    //         }
                                    //     }
                                    //     else
                                    //     {
                                    //         $tunjanganpph = ($tunjanganpph1) + ($pkp1*(3/17));

                                    //         $update_tunjanganpph = [
                                    //             'jumlah'        => $tunjanganpph,
                                    //         ];

                                    //         pphdetail::whereId($detailid)->update($update_tunjanganpph);
                                    //     }
                                    // }
                                    // else
                                    // {
                                    //     $tunjanganpph = ($pkp*(1/19));

                                    //     $update_tunjanganpph = [
                                    //         'jumlah'        => $tunjanganpph,
                                    //     ];

                                    //     pphdetail::whereId($detailid)->update($update_tunjanganpph);
                                    // }
                                }
                            }
                        }
                    }

                    $pphid = $save->id;
                    $gajipph = $this->getGajipph($pphid);

                    if (!empty($gajipph)) {
                        foreach ($gajipph as $gajipph) {
                            $update_gaji = [
                                'gaji'        => $gajipph->gaji,
                            ];

                            pph::whereId($pphid)->update($update_gaji);
                        }
                    }

                    $pphid = $save->id;
                    $jaminanpph = $this->getJaminanpph($pphid);

                    if (!empty($jaminanpph)) {
                        foreach ($jaminanpph as $jaminanpph) {
                            $update_jaminan = [
                                'jaminan_perusahaan'        => $jaminanpph->jaminan,
                            ];

                            pph::whereId($pphid)->update($update_jaminan);
                        }
                    }

                    $brutopph = $this->getBrutopph($pphid);

                    if (!empty($brutopph)) {
                        foreach ($brutopph as $brutopph) {
                            $update_bruto = [
                                'bruto'        => $brutopph->bruto,
                            ];

                            pph::whereId($pphid)->update($update_bruto);
                        }
                    }

                    $potonganpph = $this->getPotonganpph($pphid);

                    if (!empty($potonganpph)) {
                        foreach ($potonganpph as $potonganpph) {
                            $update_potongan = [
                                'potongan'        => $potonganpph->potongan,
                            ];

                            pph::whereId($pphid)->update($update_potongan);
                        }
                    }

                    $ptkpcode = $gaji->stapajak;
                    $ptkp = $this->GetPtkp($ptkpcode);

                    if (!empty($ptkp)) {
                        foreach ($ptkp as $ptkp) {
                            $jumlahptkp = [
                                'ptkp'        => $ptkp->jumlah_ptkp,
                            ];

                            pph::whereId($pphid)->update($jumlahptkp);
                        }
                    }

                    $pkppph = $this->getPkppph($pphid);

                    if (!empty($pkppph)) {
                        foreach ($pkppph as $pkppph) {
                            $update_pkp = [
                                'netto_sebulan'           => $pkppph->nettosebulan,
                                'netto_setahun'           => $pkppph->nettosetahun,
                                'pkp'                     => $pkppph->pkp,
                            ];

                            pph::whereId($pphid)->update($update_pkp);

                            $pkp = $pkppph->pkp;

                            if ($pkp <= 50000000){
                                $pph21setahun = (0.05*$pkp);
                                $pph21sebulan = (0.05*$pkp)/12;

                                $update_pph21 = [
                                    'pph21_terutang_setahun'  => $pph21setahun,
                                    'pph21_terutang_sebulan'  => $pph21sebulan,
                                ];

                                pph::whereId($pphid)->update($update_pph21);

                            }else if($pkp > 50000000 and $pkp <= 250000000){
                                $pph21setahun = ((0.15*($pkp-50000000))+2500000);
                                $pph21sebulan = ((0.15*($pkp-50000000))+2500000)/12;

                                $update_pph21 = [
                                    'pph21_terutang_setahun'  => $pph21setahun,
                                    'pph21_terutang_sebulan'  => $pph21sebulan,
                                ];

                                pph::whereId($pphid)->update($update_pph21);

                            }else if($pkp > 250000000 and $pkp <= 500000000){
                                $pph21setahun = ((0.25*($pkp-250000000))+32500000);
                                $pph21sebulan = ((0.25*($pkp-250000000))+32500000)/12;

                                $update_pph21 = [
                                    'pph21_terutang_setahun'  => $pph21setahun,
                                    'pph21_terutang_sebulan'  => $pph21sebulan,
                                ];

                                pph::whereId($pphid)->update($update_pph21);

                            }else if($pkp > 500000000){
                                $pph21setahun = ((0.30*($pkp-500000000))+95000000);
                                $pph21sebulan = ((0.30*($pkp-500000000))+95000000)/12;

                                $update_pph21 = [
                                    'pph21_terutang_setahun'  => $pph21setahun,
                                    'pph21_terutang_sebulan'  => $pph21sebulan,
                                ];

                                pph::whereId($pphid)->update($update_pph21);

                            }
                        }
                    }

                    DB::commit();
                }catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['message' => $e->getMessage()], 400);
                }
            }

            return response()->json(['message' => __('Berhasil Proses PPH 21.')]);
        }
    }

    public function getPkppph($pphid)
    {
        $pkppph = DB::select('select (bruto-potongan) as nettosebulan,
        (12*(bruto-potongan)) as nettosetahun,
        (IF(((12*(bruto-potongan))-(ptkp))<0,0,((12*(bruto-potongan))-(ptkp)))) as pkp,
        (round((0.05)*(IF(((12*(bruto-potongan))-(ptkp))<0,0,((12*(bruto-potongan))-(ptkp)))),0)) as pph21setahun,
        round(((round((0.05)*(IF(((12*(bruto-potongan))-(ptkp))<0,0,((12*(bruto-potongan))-(ptkp)))),0))/12),0) as pph21sebulan
        from pph where id = :pphid',
        array('pphid' => $pphid));

        return $pkppph;
    }

    public function GetPtkp($ptkpcode)
    {
        $ptkp = DB::select('select jumlah_ptkp from ptkp where code = :ptkpcode',
         array('ptkpcode' => $ptkpcode));

        return $ptkp;
    }

    public function GetTunjanganPphId($pphid)
    {
        $pphdetailid = DB::select('select id from pph_detail where pph_id = :pphid and parameter_id = (select id from parameter_pph where parameter_code = :tunjanganpph)',
         array('pphid' => $pphid, 'tunjanganpph' => 'tunjanganpph'));

        return $pphdetailid;
    }

    public function GetBiayaJabatanId($pphid)
    {
        $pphdetailid = DB::select('select id from pph_detail where pph_id = :pphid and parameter_id = (select id from parameter_pph where parameter_code = :tunjanganpph)',
         array('pphid' => $pphid, 'tunjanganpph' => 'biayajabatan'));

        return $pphdetailid;
    }

    public function GetpkpSetahun($pphid, $ptkpcode)
    {
        $pkpsetahun = DB::select('select (if ((coalesce((12*((SELECT sum(jumlah) as bruto FROM pph_detail b WHERE b.pph_id =
        a.pph_id and b.parameter_id in (select c.id from parameter_pph c where c.status_parameter_pph = :gaji or
         c.status_parameter_pph = :jaminan)) - (SELECT sum(jumlah) as bruto FROM pph_detail d WHERE d.pph_id = :pphid and
         d.parameter_id in (select e.id from parameter_pph e where e.status_parameter_pph = :potongan))))-
         (select jumlah_ptkp from ptkp where code = :ptkpcode))) < 0, 0, (coalesce((12*((SELECT sum(jumlah) as bruto
         FROM pph_detail b WHERE b.pph_id = a.pph_id and b.parameter_id in (select c.id from parameter_pph c where
          c.status_parameter_pph = :gaji1 or c.status_parameter_pph = :jaminan1)) - (SELECT sum(jumlah) as bruto
           FROM pph_detail d WHERE d.pph_id = :pphid1 and d.parameter_id in (select e.id from parameter_pph e where
           e.status_parameter_pph = :potongan1))))-(select jumlah_ptkp from ptkp where code = :ptkpcode1))))) as pkp
        from pph_detail a where a.pph_id = :pphid2 limit 1',
         array('pphid' => $pphid, 'ptkpcode' => $ptkpcode, 'gaji' => 'gaji', 'jaminan' => 'jaminan', 'potongan' => 'potongan',
        'pphid1' => $pphid, 'ptkpcode1' => $ptkpcode, 'gaji1' => 'gaji', 'jaminan1' => 'jaminan', 'potongan1' => 'potongan',
        'pphid2' => $pphid
        ));

        return $pkpsetahun;
    }

    public function getGajipph($pphid)
    {
        $gajipph = DB::select('SELECT sum(jumlah) as gaji FROM pph_detail a WHERE a.pph_id = :idpph and
         a.parameter_id in (select b.id from parameter_pph b where b.status_parameter_pph = :statusgaji)',
         array('idpph' => $pphid, 'statusgaji' => 'gaji'));

        return $gajipph;
    }

    public function getJaminanpph($pphid)
    {
        $jaminanpph = DB::select('SELECT sum(jumlah) as jaminan FROM pph_detail a WHERE a.pph_id = :idpph and
         a.parameter_id in (select b.id from parameter_pph b where b.status_parameter_pph = :statusgaji)',
         array('idpph' => $pphid, 'statusgaji' => 'jaminan'));

        return $jaminanpph;
    }

    public function getBrutopph($pphid)
    {
        $brutopph = DB::select('SELECT sum(jumlah) as bruto FROM pph_detail a WHERE a.pph_id = :idpph and
         a.parameter_id in (select b.id from parameter_pph b where b.status_parameter_pph = :statusgaji or
         b.status_parameter_pph = :statusgaji1)',
         array('idpph' => $pphid, 'statusgaji' => 'gaji', 'statusgaji1' => 'jaminan'));

        return $brutopph;
    }

    public function getPotonganpph($pphid)
    {
        $potonganpph = DB::select('SELECT sum(jumlah) as potongan FROM pph_detail a WHERE a.pph_id = :idpph and
         a.parameter_id in (select b.id from parameter_pph b where b.status_parameter_pph = :statusgaji)',
         array('idpph' => $pphid, 'statusgaji' => 'potongan'));

        return $potonganpph;
    }

    public function getParameterPph()
    {
        $parameterpph = DB::table('parameter_pph')
                     ->select('*')
                     ->groupBy('id')
                     ->get();

        return $parameterpph;
    }

    public function getGaji($bulan, $tahun)
    {
        $gaji = DB::connection('sqlsrv')->select('select a.NRP as nrp, c.nik, c.nama, d.Namabag, e.NmJabatan, c.stapajak, 0 as tunjanganpph,
        sum(a.jmlgapok) as jumlahgapok,sum(a.jabat) as uangjabatan,sum(a.premi_hdr) as premihadir,sum(a.premi) as premiprod,
        sum(coalesce(a.jmllbr1,0)) as lembur1, sum(coalesce(a.jmllbr2,0)) as lembur2,
        sum(coalesce(a.jmllbr3,0)) as lembur3, sum(coalesce(a.jmllbr4,0)) as lembur4,
        sum(coalesce(a.jmllbr1,0)+coalesce(a.jmllbr2,0)+coalesce(a.jmllbr3,0)+coalesce(a.jmllbr4,0)) as totallembur,
        coalesce(sum(a.bonus),0) as bonus, sum(coalesce(a.rapel,0)+coalesce(a.um,0)+coalesce(a.uang,0)) as tlain,
        sum(coalesce(a.jmlgapok,0)+coalesce(a.jabat,0)+coalesce(a.uang,0)+coalesce(a.um,0)+coalesce(a.rapel,0)+coalesce(a.jmllbr1,0)
        +coalesce(a.jmllbr2,0)+coalesce(a.jmllbr3,0)+coalesce(a.jmllbr4,0)+coalesce(a.premi_hdr,0)+coalesce(a.premi,0)+coalesce(a.bonus,0))
        as gajikotor,
        sum(coalesce(a.bpjstkkan,0)) as bpjstkper,
        sum(coalesce(a.jpkan,0)) as pensiunper,
        sum(coalesce(a.bpjskeskan,0)) as bpjskesper,

        (0.05*((sum(a.jmlgapok))+(sum(a.premi_hdr))+(sum(a.premi))+(sum(a.jabat))+(sum(coalesce(a.jmllbr1,0)+coalesce(a.jmllbr2,0)+
        coalesce(a.jmllbr3,0)+coalesce(a.jmllbr4,0)))+(sum(coalesce(a.rapel,0)+coalesce(a.um,0)+coalesce(a.uang,0)))+
        coalesce(sum(a.bonus),0)+(sum(coalesce(a.bpjstkkan,0)))+(sum(coalesce(a.jpkan,0)))+sum(coalesce(a.bpjskeskan,0)))) as biayajabatan,

        sum(coalesce(a.poas,0)) as bpjstkkar,
        sum(coalesce(a.jpkar,0)) as pensiunkar,
        sum(coalesce(a.bpjs,0)) as bpjskeskar,
        sum(coalesce(a.pokop,0)) as potkop,
        sum(coalesce(a.aoc,0)+coalesce(a.pol,0)) as potlain
        from GAJIPeg a
        inner join karyawan c on a.NRP=c.NRP
        inner join bagian d on a.kodebag=d.kodebag
        inner join DaftarJabatan e on c.kdjab=e.kdjab
        where a.Notran in (select b.notran from gaji b where month(b.tglbuat) = :bulan and YEAR(b.tglbuat) = :tahun and b.kodeprs = :prs)
        and c.stapajak is not null
        group by a.NRP, c.nik, c.nama, d.Namabag, e.NmJabatan, c.stapajak', array(
            'bulan' => $bulan, 'tahun' => $tahun, 'prs' => '001'
          ));

        return $gaji;
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
