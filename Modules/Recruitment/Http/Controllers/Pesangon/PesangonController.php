<?php

namespace Modules\Recruitment\Http\Controllers\Pesangon;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Recruitment\Entities\Pesangon;
use Maatwebsite\Excel\Facades\Excel;
// use Barryvdh\DomPDF\PDF;
use Barryvdh\DomPDF\Facade as PDF;
use App\Imports\PesangonImport;

// use Maatwebsite\Excel\Excel as ExcelExcel;

class PesangonController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('recruitment::pesangon.index');
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
        Excel::import(new PesangonImport, $request->file('file'));

        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        DB::beginTransaction();
        try {
            $datapesangon = $this->getPesangon();

            foreach($datapesangon as $datapesangon)
            {
                $nik = $datapesangon->nik;
                $nama = $datapesangon->nama;

                $check = $this->checkPesangon($bulan, $tahun, $nik, $nama);

                if (!empty($check))
                {
                    foreach($check as $check)
                    {
                        $data = [
                            'kantor'        => $datapesangon->kantor,
                            'nik'           => $datapesangon->nik,
                            'nama'          => $datapesangon->nama,
                            'pesangon'      => $datapesangon->pesangon,
                            'nama_rekening' => $datapesangon->nama_rekening,
                            'norek'         => $datapesangon->norek,
                            'periode_bulan' => $bulan,
                            'periode_tahun' => $tahun,
                        ];

                        Pesangon::whereId($check->id)->update($data);
                    }
                }else{
                    $data = [
                        'kantor'        => $datapesangon->kantor,
                        'nik'           => $datapesangon->nik,
                        'nama'          => $datapesangon->nama,
                        'pesangon'      => $datapesangon->pesangon,
                        'nama_rekening' => $datapesangon->nama_rekening,
                        'norek'         => $datapesangon->norek,
                        'periode_bulan' => $bulan,
                        'periode_tahun' => $tahun,
                    ];

                    Pesangon::create($data);
                }
            }

            DB::table('pesangon_upload')->update(['status_pesangon' => '0']);
            DB::commit();

            return response()->json(['message' => __('Berhasil Proses Pesangon')]);

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

    public function getPesangon()
    {
        $datapesangon = DB::select('select * from pesangon_upload where status_pesangon = 1');

        return $datapesangon;
    }

    public function checkPesangon($bulan, $tahun, $nik, $nama)
    {
        $datapesangon = DB::select('SELECT id FROM pesangon WHERE nik = :nik AND nama = :nama AND periode_bulan = :bulan
        AND periode_tahun = :tahun',
        array('bulan'=>$bulan, 'tahun'=>$tahun, 'nik'=>$nik, 'nama'=>$nama));

        return $datapesangon;
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
