<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Sean Tymon <tymon148@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace weboosh\webooshcore\Facades;

use App\Service\WebooshCore\ComponentService;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Route;

class WebooshCore extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(){
        return 'weboosh.webooshcore';
    }

    public static function CRUDRoute($single, $plural = '', $routeEnable = []) {
        if (empty($plural)) $plural = $single.'s';
        if (count($routeEnable) != 5) $routeEnable = [true, true, true, true, true];
        $controller = 'Admin\\'.ucfirst($single).'Controller';

        if($routeEnable[0]) Route::get('/'.$plural, $controller.'@index')->name($single.'.list');
        if($routeEnable[1]) Route::get('/'.$single.'/{id?}', $controller.'@details')->name($single.'.details');
        if($routeEnable[2]) Route::post('/'.$single.'/{id?}', $controller.'@save')->name($single.'.save');
        if($routeEnable[3]) Route::get('/'.$single.'/delete/{id?}', $controller.'@delete')->name($single.'.delete');
        if($routeEnable[4]) Route::post('/'.$plural, $controller.'@indexData')->name($single.'.ajaxList');
    }

    public static function AutoComplete($class, $path = '', $controller = '', $routeName = '') {
        $className = explode('\\', $class);
        $className = $className[count($className) - 1];
        $className = strtolower($className);

        if (empty($path)) $path = '/' . $className . '/search';
        if (empty($routeName)) $routeName = $className . '.autocomplete';

        if (empty($controller)){
            Route::any($path, function() use($class){ return ComponentService::AutoCompleteSearch($class); })->name($routeName);
        } else {
            Route::any($path, $controller)->name($routeName);
        }
    }

    public static function GaRoute() {
        Route::any('ga/ajax/sessions', 'WebooshCore\Admin\GoogleAnalyticController@ajaxSessions')->name('admin.ga.ajaxSessions');
        Route::any('ga/ajax/users', 'WebooshCore\Admin\GoogleAnalyticController@ajaxUsers')->name('admin.ga.ajaxUsers');
    }

    public static function Notification() {
        Route::get('notification/my/list',  'WebooshCore\Admin\NotificationController@index')->name('admin.notification.my.list');
        Route::get('notification/my/badge/{type}',  'WebooshCore\Admin\NotificationController@badge')->name('admin.notification.my.badge');
        Route::get('notification/my/readall',  'WebooshCore\Admin\NotificationController@readAll')->name('admin.notification.my.readall');
        Route::get('notification/my/topic/{notificationId}',  'WebooshCore\Admin\NotificationController@topic')->name('admin.notification.my.topic');
    }

}
