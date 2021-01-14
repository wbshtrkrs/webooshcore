<?php

namespace App\Blade;

class Assets {
    public static function js(){
        \Blade::directive('js', function($expression) {
            $valid = \File::exists(public_path($expression));

            if ($valid) {
                $asset = asset($expression).'?v='.md5_file(public_path($expression));

                return "<script src=\"$asset\"></script>";
            }
        });
    }

    public static function css(){
        \Blade::directive('css', function($expression) {
            $valid = \File::exists(public_path($expression));

            if ($valid) {
                $asset = asset($expression).'?v='.md5_file(public_path($expression));

                return "<link href=\"$asset\" rel=\"stylesheet\"></link>";
            }
        });
    }
}
