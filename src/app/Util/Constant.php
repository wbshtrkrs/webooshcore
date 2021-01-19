<?php

namespace App\Util\WebooshCore;

class Constant {

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';
    const STATUS_VERIFY = 'VERIFY';

    const STATUS_LABEL = [
        self::STATUS_ACTIVE => 'Aktif',
        self::STATUS_INACTIVE => 'Tidak Aktif',
        self::STATUS_VERIFY => 'Verifikasi',
    ];


}