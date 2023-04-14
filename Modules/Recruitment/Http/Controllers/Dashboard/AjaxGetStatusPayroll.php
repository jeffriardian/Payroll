<?php

namespace Modules\Recruitment\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class AjaxGetStatusPayroll extends Controller
{
    protected $orderBy;
    protected $sortBy = 'DESC';

    public function __invoke(Request $request)
    {
        $datapph = Spt::query()
            ->where('id', 'LIKE', '%' . $request->keyword . '%')
            ->orderBy('nama', 'ASC')
            ->paginate($request->per_page);

        return SptResource::collection($datapph);
    }
}
