<?php

namespace App\Entity\Base;

use App\Service\WebooshCore\CRUDService;
use App\CMSTrait\CodingCaseTrait;
use App\CMSTrait\CRUDTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;


class BaseEntity extends Authenticatable {

    use SoftDeletes, CRUDTrait, CodingCaseTrait {
        CodingCaseTrait::getDeletedAtColumn insteadof SoftDeletes;
        CRUDTrait::runSoftDelete insteadof SoftDeletes;
    }

    protected $dates = ['deleted_at'];

    const REMOVE_APPENDS = [];

    const FORM_LAYOUT = [];

    const FORM_LABEL = [];
    const FORM_LABEL_HELP = [];
    const FORM_PLACEHOLDER = [];
    const FORM_TYPE = [];
    const FORM_TYPE_REMOVE = [];
    const FORM_IMAGE_TYPE = [];
    const FORM_IMAGE_LIMIT = [];
    const FORM_SELECT_LIST = [];
    const FORM_DISABLED = [];
    const FORM_LANGUAGE_DISABLED = [];
    const FORM_READONLY = [];
    const FORM_REQUIRED = ['ALL'];
    const FORM_LIST = [];
    const INDEX_FIELD = [];
    const MANUAL_SAVE_FIELD = [];
    const IS_CMS = false;
    const LANGUAGE = ['en'];
    const LANGUAGE_MULTI = ['ALL'];
    const AMOUNT_CURRENCY = 'Rp';

    const USE_MULTI_LANG = false;
    const USE_META_SET = false;
    const ALLOW_CREATE = true;
    const ALLOW_DELETE = true;

    const FORM_META_TYPE = [
        'metaTitle'                 => 'Text',
        'metaDescription'           => 'TextArea',
        'metaKeywords'              => 'Text',
    ];

    const FORM_META_LABEL = [
        'metaTitle'                 => 'Judul Halaman',
        'metaDescription'           => 'Deskripsi Halaman',
        'metaKeywords'              => 'Keywords',
    ];

    const FORM_META_LABEL_HELP = [
        'metaTitle'                 => 'Untuk keperluan SEO',
        'metaDescription'           => 'Untuk keperluan SEO',
        'metaKeywords'              => 'Untuk keperluan SEO',
    ];

    const INDEX_KEY = [];

    const TITLE = '';
    const TITLE_INDEX = '';
    const TITLE_DETAILS = '';
    const URL_INDEX = '';
    const URL_DETAILS = '';
    const ROUTE_INDEX = '';
    const ROUTE_DETAILS = '';

    protected $hidden = ['deletedAt', 'deletedBy'];
    protected $guarded = ['id', 'createdAt', 'createdBy', 'updatedAt', 'updatedBy', 'deletedAt', 'deletedBy'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';



    public static function get($id){
        return static::getWith($id, []);
    }
    public static function getWith($id, $withArray){
        $class = get_called_class();
        if ($id > 0){
            $object = $class::with($withArray)->find($id);
        } else {
            $object = new $class();
        }
        return $object;
    }
    public static function SaveWithData($id){
        return CRUDService::SaveWithData($id, get_called_class());
    }

    public static function SaveWithDataCMS($model){
        return CRUDService::SaveWithDataCMS($model);
    }
    public function setAttribute($key, $value){
        return parent::setAttribute($key, $value);
    }

    //FORM ENGINE start
    public static function label($key, $useFor = 'FORM'){
        $labels = static::FORM_LABEL;
        if ($useFor == 'META'){
            $labels = static::FORM_META_LABEL;
        }
        if (isset($labels[$key])) return $labels[$key];

        $label = keyToLabel($key);
        return $label;
    }
    public static function labelHelp($key, $useFor = 'FORM'){
        $helps = static::FORM_LABEL_HELP;
        if ($useFor == 'META'){
            $helps = static::FORM_META_LABEL_HELP;
        }
        if (isset($helps[$key])) return $helps[$key];

        return '';
    }
    public static function placeholder($key){
        $formLabel = static::FORM_LABEL;
        $formPlaceholder = static::FORM_PLACEHOLDER;

        if (isset($formLabel[$key])) return $formLabel[$key];
        if (isset($formPlaceholder[$key])) return $formPlaceholder[$key];

        $label = keyToLabel($key);
        return $label;
    }
    public function formType($key){
        $types = $this::FORM_TYPE;
        if (isset($types[$key])) return $types[$key];
        return false;
    }
    public function isDisabled($key){
        if (empty($this->getKey())) return '';
        $disableds = $this::FORM_DISABLED;
        if (in_array($key, $disableds) || (count($disableds) == 1) && $disableds[0] == 'ALL') return 'disabled';
        return '';
    }
    public function isReadonly($key){
        if (empty($this->getKey())) return '';
        $readonlys = $this::FORM_READONLY;
        if (in_array($key, $readonlys) || (count($readonlys) == 1) && $readonlys[0] == 'ALL') return 'readonly';
        return '';
    }
    public function isRequired($key){
        $requireds = $this::FORM_REQUIRED;
        if (in_array($key, $requireds) || (count($requireds) == 1) && $requireds[0] == 'ALL') return 'required';
        return '';
    }
    public function formSelectList($key){
        $selects = $this::FORM_SELECT_LIST;
        if (isset($selects[$key])) {
            $list = $selects[$key];

            if (!is_array($list)){
                $list = $list();
            }
            asort($list);
            return $list;
        }
        return [];
    }
    public function getValue($key, $listItem, $language){
        if (empty($listItem)) {
            if (!static::IS_CMS && substr(static::formType($key),0,5) == 'Image' && !is_array($this->$key) ){
                if (substr(@$this->$key,0,1) == '[')
                    return json_decode(@$this->$key);
                else
                    return [@$this->$key];
            }
            if (static::formType($key) == 'SelectMultiple'){
                return @$this->$key->pluck('id')->toArray();
            }
            return @$this->$key;
        }
        else return @$listItem->$key;
    }
    public static function getTitle(){
        if (!empty(static::TITLE)) return static::TITLE;
        return keyToLabel(static::getClassName());
    }
    public static function getTitleIndex(){
        if (!empty(static::TITLE_INDEX)) return static::TITLE_INDEX;
        if (!empty(static::TITLE)) return static::TITLE;
        return 'Daftar '.keyToLabel(static::getClassName());
    }
    public static function getTitleDetails(){
        if (!empty(static::TITLE_DETAILS)) return static::TITLE_DETAILS;
        if (!empty(static::TITLE)) return static::TITLE;
        return keyToLabel(static::getClassName());
    }
    public function getUrlIndex($extraParams = []){
        if (!empty(static::ROUTE_INDEX)) return route(static::ROUTE_INDEX, $extraParams);
        if (\Route::has('admin.'.strtolower(self::getClassName()).'list')) return route('admin.'.strtolower(self::getClassName()).'list');
        return static::URL_INDEX;
    }
    public function getUrlDetails($extraParams = []){
        $id = empty($this->getKey()) ? 0 : $this->getKey();

        $extraParams['id'] = isset($extraParams['id']) ? $extraParams['id'] : $id;
        if (!empty(static::ROUTE_DETAILS)) return route(static::ROUTE_DETAILS, $extraParams);
        if (\Route::has('admin.'.strtolower(self::getClassName()).'.details')) return route('admin.'.strtolower(self::getClassName()).'.details', $extraParams);
        return static::URL_DETAILS;
    }
    public static function getClassName(){
        $class = explode('\\',get_called_class());
        $className = $class[count($class)-1];
        return $className;
    }
    public function getFormName($key, $listName, $listIndex, $language, $suffix = ''){
        $formName = '';

        if (!empty($listName) && isset($listIndex)) {
            if (empty($formName)) $formName .= $listName;
            else $formName .= '['.$listName.']';

            $formName .= '['.$listIndex.$suffix.']';
        }

        if (empty($formName)) $formName .= $key.$suffix;
        else $formName .= '['.$key.$suffix.']';

        if ($this->formType($key)=='SelectMultiple') $formName .= '[]';
        return $formName;
    }
    public function getImageLimit($key){
        $imageLimits = static::FORM_IMAGE_LIMIT;
        if (!isset($imageLimits[$key])) return;
        return ' data-image-limit="'. static::FORM_IMAGE_LIMIT[$key] .'"';
    }
    public function isRemoved($key) {
        $removedArray = $this::FORM_TYPE_REMOVE;

        if (in_array($key, $removedArray)) return false;

        return true;
    }
    //FORM ENGINE end

}
