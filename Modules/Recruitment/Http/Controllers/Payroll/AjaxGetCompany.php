<?php

namespace Modules\Recruitment\Http\Controllers\Payroll;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Company\Entities\Company;
use Modules\Recruitment\Transformers\CompanyResource;

class AjaxGetCompany extends Controller
{
    public function getCompanies()
    {

        $companies = Company::get();

        return CompanyResource::collection($companies);
    }
}
