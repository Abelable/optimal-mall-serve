<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\RuralBannerService;

class RuralController extends Controller
{
    protected $except = ['bannerList', 'list'];

    public function bannerList()
    {
        $list = RuralBannerService::getInstance()->getBannerList();
        return $this->success($list);
    }
}
