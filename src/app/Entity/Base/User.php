<?php

namespace App\Entity\Base;

use App\Entity\Base\BaseEntity;
use App\Util\WebooshCore\CodingConstant;
use Illuminate\Notifications\Notifiable;

class User extends BaseEntity
{
    use Notifiable;

    const ROLE_ADMIN = 'ADMIN';
    const STATUS_ACTIVE = 'ACTIVE';

    protected $table = 'user';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // @Override Authenticable function which causes error
    public function getRememberTokenName()
    {
        return CodingConstant::ConvertCase('remember_token');
    }

    public function roles(){
        return $this->hasMany(UserRole::class,'userId');
    }
}
