<?php

namespace Modules\Employee\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Entities\WorkingArea;
use Modules\Employee\Entities\Employee;
use Modules\Employee\Transformers\EmployeeOptionsResource;

class AjaxGetEmployeeOptions extends Controller
{

    public function __invoke(Request $request)
    {
        $company_working_area_code = $request->company_working_area_code;

        if (!empty($company_working_area_code)) {
            $employees = WorkingArea::leftJoin('company_working_areas', 'company_working_areas.code', '=', 'employee_working_areas.company_working_area_code')
                ->leftJoin('employee_identities','employee_identities.employee_nik', '=', 'employee_working_areas.employee_nik')
                ->select("employee_working_areas.employee_nik as nik",DB::raw("CONCAT(employee_working_areas.employee_nik,'-',employee_identities.first_name,' ',employee_identities.last_name,' (',company_working_areas.name,')') as detail"))
                ->where('is_current_working_area',1)
                ->where('company_working_area_code',$company_working_area_code)
                ->get();
        } else {
            $employees = WorkingArea::leftJoin('company_working_areas', 'company_working_areas.code', '=', 'employee_working_areas.company_working_area_code')
                ->leftJoin('employee_identities','employee_identities.employee_nik', '=', 'employee_working_areas.employee_nik')
                ->select("employee_working_areas.employee_nik as nik",DB::raw("CONCAT(employee_working_areas.employee_nik,'-',employee_identities.first_name,' ',employee_identities.last_name,' (',company_working_areas.name,')') as detail"))
                ->where('is_current_working_area',1)
                ->get();
        }

        return EmployeeOptionsResource::collection($employees);
    }
}
