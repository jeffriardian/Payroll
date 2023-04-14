<?php

namespace Modules\Recruitment\Http\Controllers\PPH;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Recruitment\Entities\pphdetail;
use Modules\Recruitment\Entities\parameterpph;
use Modules\Recruitment\Transformers\PphDetailResource;

class AjaxGetJaminan extends Controller
{
    public function getJaminans(Request $request)
    {
        $pphid = ($request->filled('id')) ? $request->id : 1;

        $jaminans = DB::select('select a.id, a.pph_id, a.parameter_id, b.nama_parameter_pph, a.jumlah from pph_detail a inner join parameter_pph b
        on a.parameter_id=b.id where a.pph_id = :pphid and b.status_parameter_pph = :statusparameter',
         array('pphid' => $pphid, 'statusparameter' => 'jaminan'));

        return PphDetailResource::collection($jaminans);
    }
}
