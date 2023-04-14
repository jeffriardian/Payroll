<?php

namespace Modules\Recruitment\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class GenderResource extends Resource
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
            'code' => $this->code,
            'name' => $this->name,
            'url_edit' => route('recruitment.gender.update', [$this->code]),
            'url_delete' => route('ajax.recruitment.destroy.gender', [$this->code]),
        ];
    }
}
