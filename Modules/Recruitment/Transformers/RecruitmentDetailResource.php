<?php

namespace Modules\Recruitment\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class RecruitmentDetailResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'recruitment_id' => $this->recruitment_id,
            'parameter_id' => $this->parameter_id,
            'parameter_name' => $this->parameter->nama_parameter,
            'score' => $this->score,
            'urlStoreAssessment' => route('recruitment.candidate.assessment.store', [$this->id]),
            /*'url_delete' => route('ajax.recruitment.destroy.kandidat', [$this->id]),
            'url_status_update' => route('recruitment.kandidat.update.status', [$this->id]),*/
        ];
    }
}
