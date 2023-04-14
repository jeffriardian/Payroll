<?php

namespace Modules\Recruitment\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class SptDetailResource extends Resource
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
            'spt_id' => $this->spt_id,
            'parameter_id' => $this->parameter_id,
            'jumlah' => number_format($this->jumlah,0),
            'nama_parameter_pph' => $this->nama_parameter_pph
        ];
    }
}
