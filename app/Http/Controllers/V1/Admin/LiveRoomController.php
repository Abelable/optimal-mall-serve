<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\LiveRoomService;
use App\Utils\Inputs\PageInput;

class LiveRoomController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = LiveRoomService::getInstance()->adminPageList($input);
        return $this->successPaginate($page);
    }
}
