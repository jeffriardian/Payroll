<?php

namespace Modules\Recruitment\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class CandidateResource extends Resource
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
            'test_date' => $this->test_date,
            'name' => $this->name,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'company_position_code' => $this->company_position_code,
            'status_hadir' => 'Tidak Hadir',
            /*'url_edit' => route('recruitment.kandidat.update', [$this->id]),
            'url_delete' => route('ajax.recruitment.destroy.kandidat', [$this->id]),
            'url_status_update' => route('recruitment.kandidat.update.status', [$this->id]),*/
        ];
    }
}
