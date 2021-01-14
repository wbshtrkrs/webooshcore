<?php

namespace App\CMSTrait;

use App\Util\WebooshCore\CodingConstant;

trait SingleImageTrait {

	public function getAttribute($key){
		if ($key == 'imageFields') return parent::getAttribute($key);

		$value = parent::getAttribute($key);
		return $this->stringJsonToArrayOrString($key, $value);
	}

	private function keyIsImage($key){
		$formType = $this::FORM_TYPE;
		if ( isset($formType[$key]) && strpos( $formType[$key], 'Image_' ) !== false){
			return true;
		} else if (isset($this->imageFields) && in_array($key, $this->imageFields)){
			return true;
		}
		return false;
	}

	private function stringJsonToArrayOrString($key, $value){
		$formType = $this::FORM_TYPE;
		if ( $this->keyIsImage($key) ){
			$value = is_string($value) ? json_decode($value) : $value;
			if (empty($value)) $value = [];

			if (isset($formType[$key]) && $formType[$key] == 'Image_1') {
				if (count($value) > 0) $value = $value[0];
                else $value = '';
			}
			else if (isset($this->imageFields) && in_array($key, $this->imageFields)){
				$value = $value[0];
			}
		}
		return $value;
	}

	public function attributesToArray(){
		$attributes = parent::attributesToArray();
		foreach($attributes as $key => $value){
			if ($this->keyIsImage($key)) $attributes[$key] = $this->stringJsonToArrayOrString($key, $value);
		}
		json_encode($attributes);
		return $attributes;
	}
	protected function mutateAttributeForArray($key, $value){
		$value = parent::mutateAttributeForArray($key, $value);
		return $this->stringJsonToArrayOrString($key, $value);
	}

}
