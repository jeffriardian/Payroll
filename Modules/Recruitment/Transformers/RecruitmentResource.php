<?php

namespace Modules\Recruitment\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class RecruitmentResource extends Resource
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
            'id'                            => $this->id,
            'total_score'                   => $this->total_score,
            'status_recruitment'            => $this->status_recruitment,
            'candidateid'                   => $this->personal_data_candidate_id,
            'first_name'                    => $this->PersonalDataCandidate->first_name,
            'last_name'                     => $this->PersonalDataCandidate->last_name,
            'identity_card_number'          => $this->PersonalDataCandidate->identity_card_number,
            'sim_type'                      => $this->PersonalDataCandidate->sim_type,
            'sim_number'                    => $this->PersonalDataCandidate->sim_number,
            'gender'                        => $this->PersonalDataCandidate->gender_code,
            'religion'                      => $this->PersonalDataCandidate->religion_code,
            'marital_status'                => $this->PersonalDataCandidate->marital_status,
            'place_of_birth'                => $this->PersonalDataCandidate->place_of_birth,
            'birthday'                      => $this->PersonalDataCandidate->birthday,
            'nation'                        => $this->PersonalDataCandidate->nation,
            'phone_number'                  => $this->PersonalDataCandidate->phone_number,
            'home_phone_number'             => $this->PersonalDataCandidate->home_phone_number,
            'email'                         => $this->PersonalDataCandidate->email,
            'company_working_area_code'     => $this->PersonalDataCandidate->WorkingArea->code,
            'company_position_code'         => $this->PersonalDataCandidate->Position->code,
            'company_working_area_name'     => $this->PersonalDataCandidate->WorkingArea->name,
            'company_position_name'         => $this->PersonalDataCandidate->Position->name,
            'test_date'                     => $this->test_date,
            'datatahapid'                   => $this->tahap->id,
            'namatahap'                     => $this->tahap->nama_tahap,
            'min_score'                     => $this->tahap->min_score,
            'tahap_sebelumnya'              => $this->tahap->tahap_sebelumnya,
            'url_edit'                      => route('recruitment.assessment.update', [$this->id]),
            'urlStoreDetail'                => route('recruitment.candidate.detail.store', [$this->personal_data_candidate_id]),
            'urlStoreAssessment'            => route('recruitment.candidate.assessment.store'),
            /*'url_delete' => route('ajax.recruitment.destroy.kandidat', [$this->id]),
            'url_status_update' => route('recruitment.kandidat.update.status', [$this->id]),*/
        ];
    }
}
