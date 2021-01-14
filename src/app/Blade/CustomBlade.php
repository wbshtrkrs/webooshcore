<?php

namespace App\Blade;

class CustomBlade {
	public static function LoadCustomBlade() {
		Assets::js();
		Assets::css();
		DataTable::tableajax();
		DataTable::endtableajax();
		DataTable::tableth();
		DataTable::tableconfig();
	}

}
