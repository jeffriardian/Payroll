<?php

namespace Modules\Company\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Company\Entities\WorkingArea;
use Modules\Company\Transformers\WorkingAreaResource;

class AjaxGetWorkingAreaController extends Controller
{
    public function getWorkingAreas(Request $request)
    {
        $departemens = WorkingArea::query()
            ->where('name', 'LIKE', '%' . $request->name . '%')
            ->get();

        return WorkingAreaResource::collection($departemens);
    }
}
