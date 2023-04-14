<?php

namespace Modules\Recruitment\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class WorkingExperienceDataCandidateResource extends Resource
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
            'company_name' => $this->company_name,
            'company_address' => $this->company_address,
            'company_phone_number' => $this->company_phone_number,
            'position' => $this->position,
            'start_year' => $this->start_year,
            'until_year' => $this->until_year,
            'personal_data_candidate_id' => $this->personal_data_candidate_id,
            /*'url_edit' => route('recruitment.kandidat.update', [$this->id]),
            'url_delete' => route('ajax.recruitment.destroy.kandidat', [$this->id]),
            'url_status_update' => route('recruitment.kandidat.update.status', [$this->id]),*/
        ];
    }
}
