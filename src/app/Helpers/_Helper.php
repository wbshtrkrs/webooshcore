<?php

use Illuminate\Support\Str;
use App\Entity\Setting;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

function getDateOnly($stringDate){
    return (new Carbon($stringDate))->format('d M Y');
}

function getMonthYearOnly($stringDate){
    return (new Carbon($stringDate))->format('M\`y');
}

function getTimeOnly($stringDate){
    return (new Carbon($stringDate))->format('h:i:s A');
}

function getDayOnly($stringDate){
    return (new Carbon($stringDate))->format('d');
}

function getMonthOnly($stringDate){
    return (new Carbon($stringDate))->format('n');
}

function getYearOnly($stringDate){
    return (new Carbon($stringDate))->format('Y');
}

function getDays($stringDate){
    return Carbon::now()->diffInDays(new Carbon($stringDate));
}

function getDateForDB($stringDate) {
    return (new Carbon($stringDate))->format('Y-m-d');
}

function nameToBaseEntity($name){
    return 'App\\Entity\\Base\\'.$name;
}

function nameToEntity($name){
    return 'App\\Entity\\Pages\\'.$name;
}

function get_class_short($class) {
    $path = explode('\\', get_class($class));
    return array_pop($path);
}

function keyToLabel($key){
    $key = Str::snake($key);
    return ucfirst(strtolower(str_replace("_", " ", $key)));
}

function getImageUrl($filename){
    if (env('APP_ENV') != 'production') {
        if (empty($filename)) return getNoPhoto();
        return url('/') . '/assets/upload/md/' . $filename;
    } else {
        return getProductionImageUrl($filename);
    }
}

function getProductionImageUrl($filename){
    if (empty($filename)) return getProductionNoPhoto();
    return env('APP_URL') . '/assets/upload/md/' . $filename;
}

function getImageUrlSize($filename, $size){
    if (env('APP_ENV') != 'production') {
        if (empty($filename)) return getNoPhoto();
        return url('/') . '/assets/upload/' . $size .'/'. $filename;
    }else {
        return getProductionImageUrlSize($filename, $size);
    }
}

function getProductionImageUrlSize($filename, $size){
    if (empty($filename)) return getProductionNoPhoto();
    return env('APP_URL') . '/assets/upload/' . $size .'/'. $filename;
}

function getNoPhoto(){
    return url('/') . '/assets/admin/images/broken-image.png';
}

function getProductionNoPhoto(){
    return env('APP_URL') . '/assets/admin/images/broken-image.png';
}

function GetFileURL($filename){
    if(empty($filename)) return '';

    if (!File::exists( public_path(env('UPLOAD_FILE')) . $filename ) ) return '';

    return url(env('UPLOAD_FILE')).'/'.$filename;
}

function isActiveRoute($arrayRouteName){
    foreach($arrayRouteName as $routeName){
        if(Request::url() == route($routeName)){
            return ' active ';
        }
    }
    return '';
}

function getSettingByKey($key){
    $model = Setting::first();

    if ($model) {
        if ($model::USE_MULTI_LANG) {
            return @$model->getFrontendValue($key);
        }

        return @$model->$key;
    }

    return false;
}

function getPermalink($name, $id) {
    if(empty($name) || empty($id)) return '';
    return preg_replace("![^a-z0-9]+!i", "-", $name).'-'.$id;
}

function parsePermalinkToId($permalink) {
    $permalink = explode('-', $permalink);
    return end($permalink);
}

function getNonListDetailsSection($model){
    $nonListType = [];
    $list = $model::FORM_LIST;
    foreach($model::FORM_TYPE as $key=>$type){
        if (!isset($list[$key])){
            $nonListType[] = $key;
        }
    }
    return $nonListType;
}

function countImage($data) {
    if ($data == '') $data = [];

    return count($data);
}

function getMenuConfig() {
    $menuList = json_encode(Config::get('cms.menu'));

    $menuList = json_decode($menuList);

    return $menuList;
}

function checkAccessSidebarCore($access, $roles) {
    if (!isset($access)) return true;

    if(array_intersect($access, $roles)) return true;

    return false;
}

function getParentLanguage() {
    $parent = config('cms.PARENT_LANGUAGE', 'en');

    return $parent;
}

function getCMSLanguage() {
    $languages = config('cms.LANGUAGES', []);

    return $languages;
}

function getLanguageSession() {
    $languageSession = session('cmsLanguage', config('cms.PARENT_LANGUAGE', 'en'));

    return $languageSession;
}

function getSelectedFrontendLang() {
    $selectedLang = session('selectedLang');

    return $selectedLang;
}
?>
