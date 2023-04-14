<?php

namespace Modules\Recruitment\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class ParameterResource extends Resource
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
            'tahap_id' => $this->tahap_id,
            'nama_tahap' => $this->tahap->nama_tahap,
            'nama_parameter' => $this->nama_parameter,
            'url_edit' => route('recruitment.parameter.update', [$this->id]),
            'url_delete' => route('ajax.recruitment.destroy.parameter', [$this->id]),
        ];
    }
}
