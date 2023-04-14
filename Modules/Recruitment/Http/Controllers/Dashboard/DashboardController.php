<?php

namespace Modules\Recruitment\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $query = $this->getData();
        $query1 = $this->getData1();
        $tahun = $this->getTahun();
        $tahun1 = $this->getTahun1();

        return view('recruitment::dashboard.index',compact('query', 'query1', 'tahun', 'tahun1'));
    }

    public function getTahun()
    {
        $tahun = DB::select('select year(now()) as tahun_sekarang');

        return $tahun;
    }

    public function getTahun1()
    {
        $tahun1 = DB::select('select year(now())-1 as tahun_kemarin');

        return $tahun1;
    }

    public function getData()
    {
        $query = DB::select('select a.value, a.text,
        (select b.id from payroll b where month(b.date_created) = a.value and year(b.date_created) = year(now()) group by b.id desc limit 1) as status
        from bulan a');

        return $query;
    }

    public function getData1()
    {
        $query1 = DB::select('select a.value, a.text,
        (select b.id from payroll b where month(b.date_created) = a.value and year(b.date_created) = year(now())-1 group by b.id desc limit 1) as status
        from bulan a');

        return $query1;
    }
}
