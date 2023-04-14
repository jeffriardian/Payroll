<?php

namespace Modules\Employee\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class EmployeeOptionsResource extends Resource
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
            'nik' => $this->nik,
            'detail' => $this->detail,
        ];
    }
}
