<?php

namespace Modules\Recruitment\Http\Controllers\PPH;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Entities\pph;
use Modules\Recruitment\Transformers\PphResource;

class AjaxGetPPH extends Controller
{
    protected $orderBy;
    protected $sortBy = 'DESC';

    public function __invoke(Request $request)
    {
        $datapph = pph::query()
            ->where('nama', 'LIKE', '%' . $request->keyword . '%')
            ->orderBy('periode_tahun', 'DESC')
            ->orderBy('periode_bulan', 'DESC')
            ->paginate($request->per_page);

        return PphResource::collection($datapph);
    }
}
