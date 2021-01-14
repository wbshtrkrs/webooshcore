<?php

namespace App\Http\Controllers\WebooshCore\Admin;

use App\Entity\User\Admin;
use App\Http\Controllers\WebooshCore\Controller;
use App\Service\WebooshCore\NotificationService;
use App\Util\WebooshCore\Constant;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller {

    public function loginPage() {
        return view('admin.auth.login');
    }


    public function submitLogin() {
        $input = (object)Input::all();

        $admin = Admin::where('email', $input->email)->with('roles')->first();

        if ($admin == null) {

            $notification = NotificationService::DefaultNotification('warning', 'Username / password salah, mohon ulang kembali.');

            return redirect()->back()->with('notification', $notification);
        } else if ($admin->status != Constant::STATUS_ACTIVE) {

            $notification = NotificationService::DefaultNotification('warning', 'Akun belum terverifikasi, silahkan verifikasi terlebih dahulu.');

            return redirect()->back()->with('notification', $notification);
        }

        if(!\Auth::attempt(['email' => $input->email, 'password' => $input->password])){
            $notification = NotificationService::DefaultNotification('warning', 'Username / password salah, mohon ulang kembali.');

            return redirect()->back()->with('notification', $notification);
        }

        return redirect()->route('admin.dashboard');
    }

    public function logout() {
        \Auth::logout();

        return redirect()->route('admin.login-page');
    }


}
