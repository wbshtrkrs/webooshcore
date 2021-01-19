<?php

namespace App\Http\Controllers\WebooshCore;

use App\Util\WebooshCore\ResponseUtil;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class Controller extends BaseController
{
    use AuthorizesRequests,  DispatchesJobs, ValidatesRequests;

    public function saveSession() {
        $input = Input::all();

        Session::put('cmsLanguage', @$input['cmsLanguage']);

        return ResponseUtil::Success();
    }
}
