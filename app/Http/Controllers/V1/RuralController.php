<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\RuralBannerService;
use App\Services\RuralRegionService;

class RuralController extends Controller
{
    protected $except = ['bannerList', 'list'];

    public function bannerList()
    {
        $list = RuralBannerService::getInstance()->getBannerList();
        return $this->success($list);
    }

    public function regionOptions()
    {
        $list = RuralRegionService::getInstance()->getRegionOptions(['id', 'name']);
        return $this->success($list);
    }
}
