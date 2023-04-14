<?php

namespace Modules\Employee\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Employee\Entities\Employee;
use Modules\Employee\Transformers\FindEmployeeResource;

class FindEmployee extends Controller
{
    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request)
    {
        $param = $request->keyword;
        $employees = Employee::with('identity')
            ->where('nik', 'LIKE', '%' . $param . '%')
            ->orWhereHas('identity', function ($query) use ($param) {
                $query->where('first_name', 'LIKE', '%' . $param . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $param . '%');
            })
            ->take(15)
            ->get();

        return FindEmployeeResource::collection($employees);
    }
}
