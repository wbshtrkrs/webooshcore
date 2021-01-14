<?php

namespace App\Util\WebooshCore;

class Response {

    public static function success($data = '') {
        header('Content-Type: application/json');
        echo json_encode(['status'=>'success', 'data'=>$data]);
        die();
    }
    public static function error($msg) {
        header('Content-Type: application/json');
        echo json_encode(['status'=>'error', 'msg'=>$msg]);
        die();
    }
    public static function unauthorized($msg) {
        header('Content-Type: application/json');
        echo json_encode(['status'=>'unauthorized', 'msg'=>$msg]);
        die();
    }

}
