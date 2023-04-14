<?php

namespace Modules\Recruitment\Http\Controllers\SPT;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Recruitment\Entities\sptdetail;
use Modules\Recruitment\Entities\parameterpph;
use Modules\Recruitment\Transformers\SptDetailResource;

class AjaxGetPotongan extends Controller
{
    public function getPotongans(Request $request)
    {
        $sptid = ($request->filled('id')) ? $request->id : 1;

        $potongans = DB::select('select a.id, a.spt_id, a.parameter_id, b.nama_parameter_pph, a.jumlah from spt_detail a inner join parameter_pph b
        on a.parameter_id=b.id where a.spt_id = :sptid and b.status_parameter_pph = :statusparameter',
         array('sptid' => $sptid, 'statusparameter' => 'potongan'));

        return SptDetailResource::collection($potongans);
    }
}

