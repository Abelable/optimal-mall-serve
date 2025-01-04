<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\BannerService;

class BannerController extends Controller
{
    protected $only = [];

    public function pop()
    {
        $pop = BannerService::getInstance()->getPop();
        return $this->success($pop);
    }

    public function list()
    {
        $position = $this->verifyRequiredInteger('position');
        $list = BannerService::getInstance()->getBannerList($position);
        return $this->success($list);
    }
}
