<?php

namespace Modules\Recruitment\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class PersonalDataCandidateResource extends Resource
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
            'id'                        => $this->id,
            'first_name'                => $this->first_name,
            'last_name'                 => $this->last_name,
            'phone_number'              => $this->phone_number,
            'email'                     => $this->email,
            'gender_code'               => $this->gender_code,
            'recruitment_id'            => $this->recruitment_id,
            'total_score'               => $this->total_score,
            'company_working_area_code' => $this->WorkingArea->code,
            'company_position_code'     => $this->Position->code,
            'company_working_area_name' => $this->WorkingArea->name,
            'company_position_name'     => $this->Position->name,
            'nama_tahap'                => $this->nama_tahap,
            'jenis_tahap'               => $this->jenis_tahap,
            //'nama_tahap' => $this->recruitment->tahap->nama_tahap,
            //'total_score' => $this->recruitment->total_score,
            /*'url_edit' => route('recruitment.kandidat.update', [$this->id]),
            'url_delete' => route('ajax.recruitment.destroy.kandidat', [$this->id]),
            'url_status_update' => route('recruitment.kandidat.update.status', [$this->id]),*/
        ];
    }
}
