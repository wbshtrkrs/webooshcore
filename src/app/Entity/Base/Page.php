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
        if (count(Config::get('cms.LANGUAGE')) == 1 && isset($this->json->$key)) {
            return @$this->json->$key;
        } else if (count(Config::get('cms.LANGUAGE')) == 1 && isset($listItem->$key)) {
            return @$listItem->$key;
        } else if (isset($this->$key)) {
            return @$this->$key;
        } else {
            return parent::getValue($key, $listItem, $language);
        }
    }

}
