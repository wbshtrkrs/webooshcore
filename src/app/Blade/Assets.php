<?php

namespace App\Blade;

class Assets {
	public static function js(){
		\Blade::directive('js', function($expression) {
            $valid = \File::exists(public_path($expression));

            if ($valid) {
                $url = url('/').$expression.'?v='.md5_file(public_path($expression));

                return "<script src=\"$url\"></script>";
            }
		});
	}
	public static function css(){
		\Blade::directive('css', function($expression) {
		    $valid = \File::exists(public_path($expression));

		    if ($valid) {
		        $url = url('/').$expression.'?v='.md5_file(public_path($expression));

                return "<link href=\"$url\" rel=\"stylesheet\"></link>";
            }
		});
	}
}
