<?php

namespace App\Entity\Base;

use App\Util\WebooshCore\CodingConstant;
use Illuminate\Support\Facades\Config;

class CMS extends BaseEntity {

    protected $table = 'cms';
    protected $fillable = ['type', 'subtype', 'name'];
    protected $appends = ['info', 'sitemap'];

    const CMS_TYPE = '';
    const CMS_SUBTYPE = '';
    const CMS_NAME = '';
    const CMS_INFO = '';
    const CMS_SITEMAP = '';
    const ALLOW_MULTIPLE = false;
    const ALLOW_DELETION = true;
    const IS_CMS = true;

    public function getUrlIndex($extraParams = []){
        return route('admin.pages');
    }

    public function getUrlDetails($extraParams = []){
        return route('admin.page-details.save', ['subtype' => $this::getClassName()]);
    }

    public function contents(){
        return $this->hasMany(CMSContent::class, CodingConstant::ConvertCase('cms_id'));
    }
    public function getContent($language){
        $object = $this->getObject($language);
        if (empty($object)) return '';
        return $object->json;
    }

    public function getObject($language){
        if (count(Config::get('cms.LANGUAGE')) == 1){
            return $this;
        }
        if (empty($language)) $language = Config::get('cms.LANGUAGE')[0];
        if (empty($this->id)) return [];

        $content = $this->contents()->where('language', $language)->first();
        if (empty($content)) {
            $content = new CMSContent(['language'=>$language, CodingConstant::ConvertCase('cms_id')=>$this->id]);
            $content->save();
        }
        return $content;
    }

    public function saveContent($language, $json){
        $content = $this->contents()->where('language', $language)->first();
        if (empty($content)) {
            $content = new CMSContent(['language'=>$language, CodingConstant::ConvertCase('cms_id')=>$this->id]);
        }
        $content->json = $json;
        $content->save();
    }

    public function setJsonAttribute($value){
        $this->attributes['json'] = json_encode($value);
    }
    public function getJsonAttribute($value){
        return json_decode($value);
    }

    public function getNameAttribute($value){
        if (empty($value)) return $this::CMS_NAME;
        return $value;
    }
    public function getInfoAttribute($value){
        return $this::CMS_INFO;
    }
    public function getSitemapAttribute($value){
        return $this::CMS_SITEMAP;
    }

    public function getValue($key, $listItem, $language){
        if (!empty($language)) {
            $json = $this->getContent($language);

            if (empty($listItem)) return @$json->$key;
            else return @$listItem->$key;
        }
        else {
            $json = $this->getContent($language);

            if (empty($listItem)) {
                if (!isset($json->key) && substr(static::formType($key),0,5) == 'Image') {
                    return [];
                }else {
                    return @$json->$key;
                }
            }
            else return @$listItem->$key;
        }
    }

}
