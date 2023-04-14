<?php

namespace Modules\Recruitment\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class PesangonResource extends Resource
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
            'kantor' => $this->kantor,
            'nik' => $this->nik,
            'nama' => $this->nama,
            'pesangon' => number_format($this->pesangon),
            'nama_rekening' => $this->nama_rekening,
            'norek' => $this->norek,
            'periode_bulan' => $this->periode_bulan,
            'periode_tahun' => $this->periode_tahun,
        ];
    }
}
