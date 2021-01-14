<?php

namespace App\Blade;

class DataTable {
	public static function tableconfig(){
		\Blade::directive('tableconfig', function($expression) {
			return "<?php echo \$__env->make( 'cms.feature.datatable.config' , array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>";
		});
	}
	public static function tableajax(){
		\Blade::directive('tableajax', function($expression) {
			return "<table class=\"datatable table table-striped primary\" data-use-ajax=\"true\" data-ajax-url=\"{{ $expression }}\">";
		});
	}
	public static function endtableajax(){
		\Blade::directive('endtableajax', function($expression) {
			return "</table>";
		});
	}
	public static function tableth(){
		\Blade::directive('tableth', function($expression) {
			$tableField = '$tableField';
			$tableLabel = '$tableLabel';
			return "<?php foreach( getDataTableHeader$expression as $tableField => $tableLabel) { ?><th data-column-name=\"<?= $tableField ?>\"><?= $tableLabel ?></th><?php } ?>";
		});
	}

}


