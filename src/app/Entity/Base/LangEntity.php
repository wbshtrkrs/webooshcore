<?php

namespace App\Entity\Base;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Config;

class LangEntity extends BaseEntity {

    private $parentLang;

    const USE_MULTI_LANG = true;

    public function __construct() {
        parent::__construct();
        $this->parentLang = Config::get('cms.PARENT_LANGUAGE');
    }

    public function getChildModelByLang($language) {
        $class = get_class($this);
        $childModel = $class::where('parentId', $this->id)->where('lang', $language)->get()->first();

        if (!$childModel) {
            $childModel = new $class();
        }

        return $childModel;
    }

    public function getValue($key, $listItem, $language) {
        if ($language == '') {
            $language = $this->parentLang;
        }

        if (empty($listItem)) {

            if ($this::USE_MULTI_LANG && $language != $this->parentLang) {
                $model = $this->getChildModelByLang($language);
            } else {
                $model = $this;
            }

            if (!static::IS_CMS && (substr(static::formType($key),0,5) == 'Image' || substr(static::formType($key),0,4) == 'File') && !is_array(@$model->$key) ){
                if (substr(@$model->$key,0,1) == '[')
                    return json_decode(@$model->$key);
                else
                    return [@$model->$key];
            }

            if (static::formType($key) == 'SelectMultiple'){
                return @$model->$key->pluck('id')->toArray();
            }

            // if (substr(@$model->$key,0,1) == '[') return json_decode(@$model->$key);

            return @$model->$key;
        } else {
            // if (substr(@$listItem->$key,0,1) == '[') return json_decode(@$listItem->$key);

            return @$listItem->$key;
        }
    }

    public function getFrontendValue($key) {
        $selectedLang = getSelectedFrontendLang();

        $class = get_class($this);
        $formLangDisabled = $class::FORM_LANGUAGE_DISABLED;
        $selectedLangData = new $class();

        if ($this::USE_MULTI_LANG && $this->lang != $selectedLang) {
            $selectedLangData = $class::where('parentId', $this->id)->where('lang', $selectedLang)->get()->first();

            if (!$selectedLangData) {
                $selectedLangData = $this;
            } else if (empty($selectedLangData->$key)) {
                $selectedLangData = $this;
            }
        }else {
            $selectedLangData = $this;
        }

        if (substr(@$selectedLangData->$key,0,1) == '[') return json_decode(@$selectedLangData->$key);

        return $selectedLangData->$key;
    }

    public function getSelectedLangDataAttribute() {
        $selectedLang = getSelectedFrontendLang();

        $class = get_class($this);

        $selectedLangData = new $class();

        if ($this::USE_MULTI_LANG && $this->lang != $selectedLang) {
            $selectedLangData = $class::where('parentId', $this->id)->where('lang', $selectedLang)->get()->first();

            if (!$selectedLangData){
                $selectedLangData = $this;
            }
        }else {
            $selectedLangData = $this;
        }

        return $selectedLangData;
    }

    public function isChild() {
        if ($this->parentId){
            return true;
        }

        return false;
    }

    public function isParent() {
        if ($this->parentId){
            return false;
        }

        return true;
    }
}
