<?php

namespace App\Http\Controllers\WebooshCore\Admin;

use App\Entity\Base\CMS;
use App\Entity\Base\Page;
use App\Http\Controllers\WebooshCore\Controller;
use App\Service\WebooshCore\NotificationService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;

class PageController extends Controller {

    public function dashboard() {

        return view('admin.dashboard');
    }

    public function index() {
        $typeEntity = Page::class;

        $list = [];
        $pageList = Config::get('cms.Pages');
        foreach($pageList as $entity){
            $subentity = nameToEntity($entity);
            $list[] = new $subentity();
        }

        return view('cms::default.index', [
            'typeEntity' => $typeEntity,
            'list' => $list
        ]);
    }

    public function details($subtype) {
        $class = nameToEntity($subtype);
        if (!class_exists($class)) return redirect ('/');

        $model = $class::where(['type' => 'Page', 'subtype' => $subtype])->get()->first();
        if (empty($model)) $model = new $class();

        return view('cms::default.details', [
            'class' => $class,
            'model' => $model,
        ]);
    }

    public function save($subtype) {
        $input = Input::all();

        $class = nameToEntity($subtype);
        if (!class_exists($class)) return redirect ('/');

        $model = $class::where(['type' => 'Page', 'subtype' => $subtype])->get()->first();

        if (empty($model)) {
            $model = new CMS();
            $model->type = 'Page';
            $model->subtype = $subtype;
            $model->save();
        }

        $class::SaveWithDataCMS($model);

        $notification = NotificationService::DefaultNotification('success');


        return redirect()->route('admin.page.list')->with('notification', $notification);
    }
}
