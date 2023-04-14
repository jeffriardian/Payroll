<?php

namespace Modules\Recruitment\Http\Controllers\SPT;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Entities\Spt;
use Modules\Recruitment\Transformers\SptResource;

class AjaxGetSPT extends Controller
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
