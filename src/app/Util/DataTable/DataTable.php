<?php

namespace App\Util\WebooshCore\DataTable;

use App\Blade\Util;
use App\Util\WebooshCore\ResponseUtil;
use Illuminate\Database\Query\Expression;
use Yajra\DataTables\DataTables;

class DataTable extends Datatables {
    public static function of($builder) {
        if (is_array($builder)) {
            return ResponseUtil::Error('error! use raw instead of select');
        }
        if ($builder instanceof Expression) {
            $datatables = app(static::class);
            return $datatables->usingRawQuery($builder);
        } else {
            $datatables = app(static::class);
            return $datatables->usingEloquent($builder);
        }

        return parent::of($builder);
    }
    public function usingEloquent($builder) {
        return new EloquentEngine($builder, $this->request);
    }
    public function usingRawQuery($builder) {
        return new RawQueryEngine($builder, $this->request);
    }
}
