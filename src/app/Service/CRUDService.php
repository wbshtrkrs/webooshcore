<?php

namespace App\Service\WebooshCore;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;


class CRUDService {
    public static function SaveWithData($id, $class) {
        return static::SaveWithDataFromUser($id, $class, Input::all());
    }

    public static function SaveWithDataFromUser($id, $class, $dataFromUser) {
        $sessionLanguage = getLanguageSession();
        $parentLang = getParentLanguage();

        if ($class::USE_MULTI_LANG && $parentLang != $sessionLanguage) {
            $object = $class::where('parentId', $id)->where('lang', $sessionLanguage)->get()->first();
            if (!$object) {
                $object = new $class;
                $object->parentId = $id;
                $object->lang = $sessionLanguage;
            }
        } else {
            $object = $class::get($id);
        }

        $updatedData = static::FormToJson($object->toArray(), $dataFromUser, $object);
        $object->fill($updatedData);
        if (count($class::REMOVE_APPENDS)) {
            foreach($class::REMOVE_APPENDS as $appendName){
                unset($object->$appendName);
            }
        }
        if ($class::USE_META_SET) {
            foreach($class::FORM_META_TYPE as $formName=>$formType){
                $object->$formName = $updatedData[$formName];
            }
        }
        $object->save();
        return $object;
    }

    public static function SaveWithDataCMS($model) {
        $sessionLanguage = getLanguageSession();
        $data = Input::all();

        $json = $model->getContent($sessionLanguage);
        $json = static::FormToJson((array)$json, $data, $model);
        $model->saveContent($sessionLanguage, $json);

        /*if (count(Config::get('cms.LANGUAGE')) > 1){
            foreach(Config::get('cms.LANGUAGE') as $language) {
                if (!isset($data[$language])) continue;

                $json = $model->getContent($language);
                $json = static::FormToJson((array)$json, $data[$language], $model);
                $model->saveContent($language, $json);
            }
        } else {
            $model->json = static::FormToJson((array)$model->json, $data, $model);
        }

        static::addIndexKey($model);
        $model->save();

        return $model;*/
    }

    public static function FormToJson($json, $data, $model) {

        if (@$model::USE_META_SET) {
            foreach($model::FORM_META_TYPE as $formName=>$formType){
                $json[$formName] = static::updateData((array)$json, $data, $formType, $formName, $model);
            }
        }

        foreach($model::FORM_TYPE as $formName=>$formType){
            if (in_array($formName, $model::MANUAL_SAVE_FIELD)) {
                unset($json[$formName]);
                continue;
            }
            $listType = $model::FORM_LIST;
            if (isset($listType[$formName])) continue;

            if ($formType == 'SelectMultiple'){
                $parentModel = class_basename(get_parent_class($model));

                if ($parentModel != 'Page') {
                    $relation = $model->$formName();
                    $associatedClass = $relation->getRelated();
                    $model->save();
                    if(!isset($data[$formName])){
                        $data[$formName] = [];
                    }
                    if ($relation instanceof  \Illuminate\Database\Eloquent\Relations\BelongsToMany){
                        $model->$formName()->sync( $associatedClass::whereIn('id', $data[$formName] )->get() );
                    }
                }else {
                    $json[$formName] = json_encode($data[$formName]);
                }
            } else if ($formType == 'DateRange' || $formType == 'TimeRange' || $formType == 'NumberRange') {
                $json[$formName.'From'] = static::updateData((array)$json, $data, $formType, $formName.'From', $model);
                $json[$formName.'To'] = static::updateData((array)$json, $data, $formType, $formName.'To', $model);
            } else {
                $json[$formName] = static::updateData((array)$json, $data, $formType, $formName, $model);
            }
        }
        foreach($model::FORM_LIST as $formName=>$list){
            if (in_array($formName, $model::MANUAL_SAVE_FIELD)) {
                unset($json[$formName]);
                continue;
            }
            if (count($list) == 0) break;
            if (!isset($data[$formName])) continue;

            $listIndex = 0;

            $updatedjson = [$formName => []];
            while(isset($data[$formName][$listIndex])) {
                if (!isset($json[$formName][$listIndex])) $json[$formName][$listIndex] = [];

                foreach ($list as $listItemFormName => $listItemFormType) {
                    $updatedjson[$formName][$listIndex][$listItemFormName] = static::updateData((array)$json[$formName][$listIndex], $data[$formName][$listIndex], $listItemFormType, $listItemFormName, $model, true);
                }
                $listIndex++;
            }
            if (!$model::IS_CMS) {
                $json[$formName] = json_encode($updatedjson[$formName]);
            }else {
                $json[$formName] = $updatedjson[$formName];
            }
        }

        return $json;
    }

    public static function useUpdateData($json, $data, $formType, $formName, $model){
        $updatedData = static::updateData($json, $data, $formType, $formName, $model);


        return @$updatedData;
    }

    private static function updateData($json, $data, $formType, $formName, $model, $isList = false){
        $dataKey = array_keys($data);
        if (substr($formType,0,5) == 'Image') {
            $json[$formName] = [];

            $imageKeys = preg_grep('/'.$formName.'/', $dataKey);
            $updatedImage = [];
            foreach($imageKeys as $imageKey){
                $imageIndex = substr($imageKey, strlen($formName), strlen($imageKey));

                if (empty($data[$formName.$imageIndex])){
                    continue;
                }

                if ($data[$formName.$imageIndex] == 'DELETE_IMAGE'){
                    if (isset($json[$formName][$imageIndex]))ImageService::delete( $json[$formName][$imageIndex] );
                    $json[$formName][$imageIndex] = '';
                } else if (!is_string($data[$formName.$imageIndex])){
                    $json[$formName][$imageIndex] = ImageService::uploadImage($data[$formName.$imageIndex]);
                } else {
                    $json[$formName][$imageIndex] = $data[$formName.$imageIndex];
                }

                if (!empty($json[$formName][$imageIndex])) $updatedImage[] = $json[$formName][$imageIndex];
            }
            $json[$formName] = $updatedImage;

            if (!$model::IS_CMS) $json[$formName] = json_encode($json[$formName]);
        } elseif ($formType == 'File') {
            if (isset($data[$formName]) && !is_string($data[$formName])) {
                FileService::delete(@$json[$formName]);
                $json[$formName] = FileService::UploadFile($data[$formName]);
            }elseif (isset($data[$formName]) && $data[$formName] == 'DELETE_FILE'){
                FileService::delete(@$json[$formName]);
                $json[$formName] = null;
            }
        } else {
            if (array_key_exists($formName, $data)){
                $json[$formName] = $data[$formName];
                if (($formType == 'Date' || $formType == 'DateRange') && !$model::IS_CMS){
                    $json[$formName] = new \Carbon($data[$formName]);
                } else {
                    $json[$formName] = $data[$formName];
                }
            }
        }
        return @$json[$formName];
    }

    private static function addIndexKey($object){
        if (count($object::INDEX_KEY) == 0) return;

        if (count(Config::get('cms.LANGUAGE')) > 1){
            $language = Config::get('cms.LANGUAGE')[0];
            $json = $object->getContent($language);
            foreach($object::INDEX_KEY as $key=>&$column){
                if (isset($json->$key)) $object->$column = $json->$key;
            }
        } else {
            foreach($object::INDEX_KEY as $key=>&$column){
                if (isset($json->$key)) $object->$column = $object->json->$key;
            }
        }

    }
}
