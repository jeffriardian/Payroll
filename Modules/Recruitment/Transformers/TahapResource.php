<?php

namespace Modules\Recruitment\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class TahapResource extends Resource
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
            'nama_tahap' => $this->nama_tahap,
            'tahap_sebelumnya' => $this->tahap_sebelumnya,
            'min_score' => $this->min_score,
            'url_edit' => route('recruitment.tahap.update', [$this->id]),
            'url_delete' => route('ajax.recruitment.destroy.tahap', [$this->id]),
        ];
    }
}
