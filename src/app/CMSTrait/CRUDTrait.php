<?php

namespace App\CMSTrait;

use App\Util\WebooshCore\CodingConstant;

trait CRUDTrait {

	protected static function boot() {
		parent::boot();

		$createdBy = CodingConstant::ConvertCase('createdBy');
		$updatedBy = CodingConstant::ConvertCase('updatedBy');

		static::saving(function($table) use($updatedBy){
			if (\Auth::user() != null) {
				$table->$updatedBy = \Auth::user()->id;
			}
			else {
				$table->$updatedBy = (int) env('SYSTEM_USER_ID', '0');
			}
		});
		static::creating(function($table) use($createdBy, $updatedBy){
			if (\Auth::user() != null) {
				$table->$createdBy = \Auth::user()->id;
				$table->$updatedBy = \Auth::user()->id;
			}
			else {
				$table->$createdBy = (int) env('SYSTEM_USER_ID', '0');
				$table->$updatedBy = (int) env('SYSTEM_USER_ID', '0');
			}
		});
	}
	protected function runSoftDelete(){
		$deletedBy = CodingConstant::ConvertCase('deletedBy');

		$query = $this->newQueryWithoutScopes()->where($this->getKeyName(), $this->getKey());
		$this->{$this->getDeletedAtColumn()} = $time = $this->freshTimestamp();

		$updateColumn = [$this->getDeletedAtColumn() => $this->fromDateTime($time)];
		if (\Auth::user() != null) {
			$updateColumn[$deletedBy] = \Auth::user()->id;
		}
		else {
			$updateColumn[$deletedBy] = (int) env('SYSTEM_USER_ID', '0');
		}
		$query->update($updateColumn);
	}

}
