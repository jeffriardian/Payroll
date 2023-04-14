<?php

namespace Modules\Employee\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Employee\Entities\Employee;
use Modules\Employee\Transformers\FindEmployeeResource;

class AjaxGetEmployeeDetail extends Controller
{
    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request)
    {
        $nik = $request->nik;
        $employees = Employee::with('identity')
            ->where('nik',$nik)
            ->first();


        if (!empty($employees)) {
            return new FindEmployeeResource($employees);
        }
    }
}
