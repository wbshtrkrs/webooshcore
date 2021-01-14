<?php

namespace App\Entity\Base;

use App\Entity\Base\BaseEntity;
use Illuminate\Notifications\Notifiable;

class UserRole extends BaseEntity
{
    use Notifiable;

    protected $table = 'userRole';
    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','userId'
    ];

    public function user(){
        return $this->belongsTo(User::class,'id','userId');
    }
}
