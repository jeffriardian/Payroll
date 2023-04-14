<?php

namespace Modules\Recruitment\Http\Controllers\THR;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Recruitment\Entities\Thr;
use Maatwebsite\Excel\Facades\Excel;
// use Barryvdh\DomPDF\PDF;
use Barryvdh\DomPDF\Facade as PDF;
use App\Imports\THRImport;

// use Maatwebsite\Excel\Excel as ExcelExcel;

class THRController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('recruitment::thr.index');
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
        Excel::import(new THRImport, $request->file('file'));

        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        DB::beginTransaction();
        try {
            $datathr = $this->getTHR();

            foreach($datathr as $datathr)
            {
                $nik = $datathr->nik;
                $nama = $datathr->nama;

                $check = $this->checkTHR($bulan, $tahun, $nik, $nama);

                if (!empty($check))
                {
                    foreach($check as $check)
                    {
                        $data = [
                            'kantor'        => $datathr->kantor,
                            'nik'           => $datathr->nik,
                            'nama'          => $datathr->nama,
                            'thr'           => $datathr->thr,
                            'nama_rekening' => $datathr->nama_rekening,
                            'norek'         => $datathr->norek,
                            'periode_bulan' => $bulan,
                            'periode_tahun' => $tahun,
                        ];

                        Thr::whereId($check->id)->update($data);
                    }
                }else{
                    $data = [
                        'kantor'        => $datathr->kantor,
                        'nik'           => $datathr->nik,
                        'nama'          => $datathr->nama,
                        'thr'           => $datathr->thr,
                        'nama_rekening' => $datathr->nama_rekening,
                        'norek'         => $datathr->norek,
                        'periode_bulan' => $bulan,
                        'periode_tahun' => $tahun,
                    ];

                    Thr::create($data);
                }
            }

            DB::table('thr_upload')->update(['status_thr' => '0']);
            DB::commit();

            return response()->json(['message' => __('Berhasil Proses THR')]);

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

    public function getTHR()
    {
        $datathr = DB::select('select * from thr_upload where status_thr = 1');

        return $datathr;
    }

    public function checkTHR($bulan, $tahun, $nik, $nama)
    {
        $datathr = DB::select('SELECT id FROM thr WHERE nik = :nik AND nama = :nama AND periode_bulan = :bulan
        AND periode_tahun = :tahun',
        array('bulan'=>$bulan, 'tahun'=>$tahun, 'nik'=>$nik, 'nama'=>$nama));

        return $datathr;
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
