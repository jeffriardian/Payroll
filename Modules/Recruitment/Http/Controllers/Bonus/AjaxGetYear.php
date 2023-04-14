<?php

namespace Modules\Recruitment\Http\Controllers\Payroll;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Entities\Tahun;
use Modules\Recruitment\Transformers\YearResource;

class AjaxGetYear extends Controller
{
    public function getYears()
    {

        $years = Tahun::get();

        return YearResource::collection($years);
    }
}
