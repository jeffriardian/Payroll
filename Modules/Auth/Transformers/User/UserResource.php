<?php

namespace Modules\Auth\Transformers\User;

use Illuminate\Http\Resources\Json\Resource;
use Modules\Employee\Transformers\FindEmployeeResource;

class UserResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $null = "tidak ditemukan";
        $employee_null = [
            'departement' => '',
            'departement_code' => '',
            'identity' => '',
            'name' => '',
            'nik' => '',
        ];

        $first_name = (!empty($this->employee)) ? (!empty($this->employee->identity)) ? $this->employee->identity->first_name : $null : $null;
        $last_name = (!empty($this->employee)) ? (!empty($this->employee->identity)) ? $this->employee->identity->last_name : $null : $null;
        $group_name = (!empty($this->employee)) ? $this->group->name : $null;
        $employee = (!empty($this->employee)) ? new FindEmployeeResource($this->employee) : $employee_null;

        return [
            'id' => $this->id,
            'employee_nik' => $this->employee_nik,
            'employee' => $employee,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'full_name' => getEmployeeFullName($this->employee_nik),
            'departement_name' => getEmployeeDepartement($this->employee_nik),
            'user_group_id' => $this->user_group_id,
            'group' => $group_name,
            'username' => $this->username,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'status' => $this->account_status,
            'url_edit' => route('user.manage.update', [$this->id]),
            'url_status_update' => route('user.manage.update.status', [$this->id]),
            'url_delete' => route('ajax.user.destroy.manage', [$this->id]),
        ];
    }
}
