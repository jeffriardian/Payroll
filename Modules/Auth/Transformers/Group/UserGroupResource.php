<?php

namespace Modules\Auth\Transformers\Group;

use Illuminate\Http\Resources\Json\Resource;

class UserGroupResource extends Resource
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
            'name' => $this->name,
            'description' => $this->description,
            'status' => (boolean) $this->is_active,
            'url_edit' => route('user.group.update', [$this->id]),
            'url_status_update' => route('user.group.update.status', [$this->id]),
            'url_delete' => route('ajax.user.destroy.group', [$this->id]),
        ];
    }
}
