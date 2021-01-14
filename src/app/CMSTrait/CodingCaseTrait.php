<?php

namespace App\CMSTrait;

use App\Util\WebooshCore\CodingConstant;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

trait CodingCaseTrait {

	public function getTable() {
		return CodingConstant::ConvertCase(parent::getTable());
	}

	protected function updateTimestamps() {
		$time = $this->freshTimestamp();

		if (!$this->isDirty($this->getUpdatedAtColumn())) {
			$this->setUpdatedAt($time);
		}

		if (!$this->exists && !$this->isDirty($this->getCreatedAtColumn())) {
			$this->setCreatedAt($time);
		}
	}

	public function setCreatedAt($value){
		$this->{$this->getCreatedAtColumn()} = $value;
		return $this;
	}
	public function setUpdatedAt($value){
		$this->{$this->getUpdatedAtColumn()} = $value;
		return $this;
	}
	public function getCreatedAtColumn() {
		return CodingConstant::ConvertCase(parent::getCreatedAtColumn());
	}
	public function getUpdatedAtColumn() {
		return CodingConstant::ConvertCase(parent::getUpdatedAtColumn());
	}
	public function getDeletedAtColumn(){
		$deletedAt = defined('static::DELETED_AT') ? static::DELETED_AT : 'deleted_at';
		return CodingConstant::ConvertCase($deletedAt);
	}
	public function getGuarded(){
		$guardeds = [];
		foreach($this->guarded as $guarded){
			$guardeds[] = CodingConstant::ConvertCase($guarded);
		}
		return $guardeds;
	}
	public function getFillable(){
		$fillables = [];
		foreach($this->fillable as $fillable){
			$fillables[] = CodingConstant::ConvertCase($fillable);
		}
		return $fillables;
	}
	public function getHidden(){
		$hiddens = [];
		foreach($this->hidden as $hidden){
			$hiddens[] = CodingConstant::ConvertCase($hidden);
		}
		return $hiddens;
	}

	public function hasMany($related, $foreignKey = null, $localKey = null){
		return parent::hasMany($related, CodingConstant::ConvertCase($foreignKey), CodingConstant::ConvertCase($localKey));
	}
	public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null){
		if (is_null($relation)) {
			list($current, $caller) = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

			$relation = $caller['function'];
		}
		if (is_null($foreignKey)) {
			$foreignKey = CodingConstant::ConvertCase($relation.'_id');
		}
		return parent::belongsTo($related, $foreignKey, CodingConstant::ConvertCase($otherKey), CodingConstant::ConvertCase($relation));
	}

	public function relationsToArray(){
		$attributes = [];
		foreach ($this->getArrayableRelations() as $key => $value) {
			if ($value instanceof Arrayable) {
				$relation = $value->toArray();
			}
			else if (is_null($value)) {
				$relation = $value;
			}

			$key = CodingConstant::ConvertCase($key);
			if (isset($relation) || is_null($value)) {
				$attributes[$key] = $relation;
			}

			unset($relation);
		}

		return $attributes;
	}
	public function getForeignKey(){
		return CodingConstant::ConvertCase(class_basename($this).'_id');;
	}
}
