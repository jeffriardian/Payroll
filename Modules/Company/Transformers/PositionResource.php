<?php

namespace Modules\Company\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class PositionResource extends Resource
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
        ];
    }
}
