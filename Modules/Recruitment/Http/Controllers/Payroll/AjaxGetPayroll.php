<?php

namespace Modules\Recruitment\Http\Controllers\Payroll;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Entities\Payroll;
use Modules\Recruitment\Transformers\PayrollResource;

class AjaxGetPayroll extends Controller
{
    protected $orderBy;
    protected $sortBy = 'DESC';

    public function __invoke(Request $request)
    {
        if (($request->month) == NULL){
            IF($request->filter=="1"){

                $datapayroll = payroll::query()
                ->where('nama', 'LIKE', '%' . $request->keyword . '%')
                ->whereYear('date_created', '=', date('Y'))
                ->orderBy('date_created', 'DESC')
                ->paginate($request->per_page);

                return PayrollResource::collection($datapayroll);

            }else
            IF($request->filter=="2"){

                $datapayroll = payroll::query()
                ->where('nama', 'LIKE', '%' . $request->keyword . '%')
                ->where('kantor', '=', 'SMM')
                ->whereYear('date_created', '=', date('Y'))
                ->orderBy('date_created', 'DESC')
                ->paginate($request->per_page);

                return PayrollResource::collection($datapayroll);

            }else
            IF($request->filter=="3"){

                $datapayroll = payroll::query()
                ->where('nama', 'LIKE', '%' . $request->keyword . '%')
                ->where('kantor', '=', 'ATM')
                ->whereYear('date_created', '=', date('Y'))
                ->orderBy('date_created', 'DESC')
                ->paginate($request->per_page);

                return PayrollResource::collection($datapayroll);
            }
        }else{
            IF($request->filter=="1"){

                $datapayroll = payroll::query()
                ->where('nama', 'LIKE', '%' . $request->keyword . '%')
                ->whereMonth('date_created', '=', $request->month)
                ->whereYear('date_created', '=', date('Y'))
                ->orderBy('date_created', 'DESC')
                ->paginate($request->per_page);

                return PayrollResource::collection($datapayroll);

            }else
            IF($request->filter=="2"){

                $datapayroll = payroll::query()
                ->where('nama', 'LIKE', '%' . $request->keyword . '%')
                ->where('kantor', '=', 'SMM')
                ->whereMonth('date_created', '=', $request->month)
                ->whereYear('date_created', '=', date('Y'))
                ->orderBy('date_created', 'DESC')
                ->paginate($request->per_page);

                return PayrollResource::collection($datapayroll);

            }else
            IF($request->filter=="3"){

                $datapayroll = payroll::query()
                ->where('nama', 'LIKE', '%' . $request->keyword . '%')
                ->where('kantor', '=', 'ATM')
                ->whereMonth('date_created', '=', $request->month)
                ->whereYear('date_created', '=', date('Y'))
                ->orderBy('date_created', 'DESC')
                ->paginate($request->per_page);

                return PayrollResource::collection($datapayroll);

            }
        }
    }
}
