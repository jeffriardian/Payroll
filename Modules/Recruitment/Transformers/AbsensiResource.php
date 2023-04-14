<?php

namespace Modules\Recruitment\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class AbsensiResource extends Resource
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
            'pin' => $this->pin,
            'jam_masuk' => $this->jam_masuk,
            'jam_pulang' => $this->jam_pulang,
        ];
    }
}
