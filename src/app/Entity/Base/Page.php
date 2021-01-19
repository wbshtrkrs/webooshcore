<?php

namespace App\Entity\Base;

use Illuminate\Support\Facades\Config;

class Page extends CMS {
    const CMS_TYPE = 'Page';
    const FORM_REQUIRED = [];
    const ALLOW_DELETION = false;

    static function getPage(){
        $subtype = static::getClassName();

        return CMS::where('type', 'Page')->where('subtype', $subtype)->get()->first();
    }

    const INDEX_FIELD = [
        'name',
        'info',
        'sitemap',
    ];

    public function getValue($key, $listItem, $language){
        if (isset($this->$key)) {
            return @$this->$key;
        }

        return parent::getValue($key, $listItem, $language);
    }
}
