<?php

namespace Modules\Recruitment\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class KandidatResource extends Resource
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
            'noktp' => $this->noktp,
            'nama' => $this->nama,
            'nohp' => $this->nohp,
            'bagian_id' => $this->bagian->namabagian,
            'url_edit' => route('recruitment.kandidat.update', [$this->id]),
            'url_delete' => route('ajax.recruitment.destroy.kandidat', [$this->id]),
            'url_status_update' => route('recruitment.kandidat.update.status', [$this->id]),
        ];
    }
}
