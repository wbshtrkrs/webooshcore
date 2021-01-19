<?php

namespace App\Scopes\WebooshCore;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class UserRoleScope implements Scope {
    protected $role;

    public function __construct($role) {
        if (!is_array($role)) $role = array($role);
        $this->role = $role;
    }


    public function apply(Builder $builder, Model $model) {
        $builder->whereHas('roles', function ($q) {
            $q->whereIn('name', $this->role);
        });
    }
}