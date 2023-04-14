<?php

namespace Modules\Recruitment\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class EducationResource extends Resource
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
            'level' => $this->level,
            'school_name' => $this->school_name,
            'major' => $this->major,
            'city' => $this->city,
            'start_year' => $this->start_year,
            'graduation_year' => $this->graduation_year,
            'score' => $this->score,
            'personal_data_candidate_id' => $this->personal_data_candidate_id,
            /*'url_edit' => route('recruitment.kandidat.update', [$this->id]),
            'url_delete' => route('ajax.recruitment.destroy.kandidat', [$this->id]),
            'url_status_update' => route('recruitment.kandidat.update.status', [$this->id]),*/
        ];
    }
}
