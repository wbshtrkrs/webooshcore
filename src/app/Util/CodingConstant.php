<?php

namespace App\Util\WebooshCore;

use Illuminate\Support\Str;

class CodingConstant {
    const CASE_CAMEL = 'CAMEL';
    const CASE_SNAKE = 'SNAKE';

    private static function CodingCase() {
        $case = env('CODING_CASE');
        if (empty($case)) $case = CodingConstant::CASE_SNAKE;
        return $case;
    }

    public static function ConvertCase($string) {
        $case = static::CodingCase();
        if ($case == CodingConstant::CASE_SNAKE) {
            $string = Str::snake($string);
        } else if ($case == CodingConstant::CASE_CAMEL) {
            $string = Str::camel($string);
        }
        return $string;
    }


}
