<?php

namespace Modules\Recruitment\Http\Controllers\THR;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Entities\Thr;
use Modules\Recruitment\Transformers\ThrResource;

class AjaxGetTHR extends Controller
{
    protected $orderBy;
    protected $sortBy = 'DESC';

    public function __invoke(Request $request)
    {
        $data = Thr::query()
            ->where('nama', 'LIKE', '%' . $request->keyword . '%')
            ->orderBy('created_at', 'DESC')
            ->paginate($request->per_page);

        return ThrResource::collection($data);
    }
}
