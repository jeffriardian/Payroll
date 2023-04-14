<?php

namespace Modules\Company\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Company\Entities\Position;
use Modules\Company\Transformers\PositionResource;

class AjaxGetPositionController extends Controller
{
    public function getPositions(Request $request)
    {
        $positions = Position::query()
            ->where('name', 'LIKE', '%' . $request->name . '%')
            ->get();

        return PositionResource::collection($positions);
    }
}
