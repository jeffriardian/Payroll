<?php

namespace Modules\Recruitment\Http\Controllers\Bonus;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Entities\Bonus;
use Modules\Recruitment\Transformers\BonusResource;

class AjaxGetBonus extends Controller
{
    protected $orderBy;
    protected $sortBy = 'DESC';

    public function __invoke(Request $request)
    {
        $data = Bonus::query()
            ->where('nama', 'LIKE', '%' . $request->keyword . '%')
            ->orderBy('created_at', 'DESC')
            ->paginate($request->per_page);

        return BonusResource::collection($data);
    }
}
