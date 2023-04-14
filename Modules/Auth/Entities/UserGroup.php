<?php

namespace Modules\Auth\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserGroup extends Model
{
    use SoftDeletes;

    protected $table = 'user_groups';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    public function user()
    {
        return $this->hasMany(User::class, 'user_group_id', 'id');
    }
}
