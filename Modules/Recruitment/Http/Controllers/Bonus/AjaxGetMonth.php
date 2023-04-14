<?php

namespace Modules\Recruitment\Http\Controllers\Payroll;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Entities\Bulan;
use Modules\Recruitment\Transformers\MonthResource;

class AjaxGetMonth extends Controller
{
    public function getMonths()
    {

        $bulans = Bulan::get();

        return MonthResource::collection($bulans);
    }
}
