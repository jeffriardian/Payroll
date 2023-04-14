<?php

namespace Modules\Recruitment\Http\Controllers\Pesangon;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Entities\Pesangon;
use Modules\Recruitment\Transformers\PesangonResource;

class AjaxGetPesangon extends Controller
{
    protected $orderBy;
    protected $sortBy = 'DESC';

    public function __invoke(Request $request)
    {
        $data = Pesangon::query()
            ->where('nama', 'LIKE', '%' . $request->keyword . '%')
            ->orderBy('created_at', 'DESC')
            ->paginate($request->per_page);

        return PesangonResource::collection($data);
    }
}
