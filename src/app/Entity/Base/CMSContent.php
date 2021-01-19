<?php

namespace App\Entity\Base;

class CMSContent extends BaseEntity {

    protected $table = 'cms_content';
    protected $fillable = ['lang', 'json', 'cms_id'];

    public function setJsonAttribute($value) {
        $this->attributes['json'] = json_encode($value);
    }
    public function getJsonAttribute($value) {
        return json_decode($value);
    }

}
