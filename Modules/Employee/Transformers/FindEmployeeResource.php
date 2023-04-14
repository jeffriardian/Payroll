<?php

namespace Modules\Employee\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Modules\Employee\Entities\WorkingArea;

class FindEmployeeResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $working_area_code = getWorkingAreaCode($this->nik);
        return [
            'identity' => $this->nik . ', ' . getEmployeeFullName($this->nik),
            'nik' => $this->nik,
            'name' => getEmployeeFullName($this->nik),
            'departement_code' => $working_area_code,
            'departement' => getWorkingAreaName($working_area_code),
        ];
    }


}
