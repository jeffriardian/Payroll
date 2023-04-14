<?php

namespace Modules\Recruitment\Http\Controllers\Bonus;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Recruitment\Entities\Bonus;
use Maatwebsite\Excel\Facades\Excel;
// use Barryvdh\DomPDF\PDF;
use Barryvdh\DomPDF\Facade as PDF;
use App\Imports\BonusImport;

// use Maatwebsite\Excel\Excel as ExcelExcel;

class BonusController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('recruitment::bonus.index');
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

    }

    public function import(Request $request)
    {
        Excel::import(new BonusImport, $request->file('file'));

        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        DB::beginTransaction();
        try {
            $databonus = $this->getBonus();

            foreach($databonus as $databonus)
            {
                $nik = $databonus->nik;
                $nama = $databonus->nama;

                $check = $this->checkBonus($bulan, $tahun, $nik, $nama);

                if (!empty($check))
                {
                    foreach($check as $check)
                    {
                        $data = [
                            'kantor'        => $databonus->kantor,
                            'nik'           => $databonus->nik,
                            'nama'          => $databonus->nama,
                            'bonus'           => $databonus->bonus,
                            'nama_rekening' => $databonus->nama_rekening,
                            'norek'         => $databonus->norek,
                            'periode_bulan' => $bulan,
                            'periode_tahun' => $tahun,
                        ];

                        Bonus::whereId($check->id)->update($data);
                    }
                }else{
                    $data = [
                        'kantor'        => $databonus->kantor,
                        'nik'           => $databonus->nik,
                        'nama'          => $databonus->nama,
                        'bonus'         => $databonus->bonus,
                        'nama_rekening' => $databonus->nama_rekening,
                        'norek'         => $databonus->norek,
                        'periode_bulan' => $bulan,
                        'periode_tahun' => $tahun,
                    ];

                    Bonus::create($data);
                }
            }

            DB::table('bonus_upload')->update(['status_bonus' => '0']);
            DB::commit();

            return response()->json(['message' => __('Berhasil Proses Bonus')]);

        }catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 400);
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

    public function getBonus()
    {
        $databonus = DB::select('select * from bonus_upload where status_bonus = 1');

        return $databonus;
    }

    public function checkBonus($bulan, $tahun, $nik, $nama)
    {
        $databonus = DB::select('SELECT id FROM bonus WHERE nik = :nik AND nama = :nama AND periode_bulan = :bulan
        AND periode_tahun = :tahun',
        array('bulan'=>$bulan, 'tahun'=>$tahun, 'nik'=>$nik, 'nama'=>$nama));

        return $databonus;
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
