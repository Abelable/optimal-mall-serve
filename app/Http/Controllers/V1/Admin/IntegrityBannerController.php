<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\IntegrityBanner;
use App\Services\IntegrityBannerService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\BannerInput;
use App\Utils\Inputs\BannerPageInput;

class IntegrityBannerController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var BannerPageInput $input */
        $input = BannerPageInput::new();
        $list = IntegrityBannerService::getInstance()->getBannerPage($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $banner = IntegrityBannerService::getInstance()->getBannerById($id);
        if (is_null($banner)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前活动banner不存在');
        }
        return $this->success($banner);
    }

    public function add()
    {
        /** @var BannerInput $input */
        $input = BannerInput::new();
        $banner = IntegrityBanner::new();
        IntegrityBannerService::getInstance()->updateBanner($banner, $input);
        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var BannerInput $input */
        $input = BannerInput::new();

        $banner = IntegrityBannerService::getInstance()->getBannerById($id);
        if (is_null($banner)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前活动banner不存在');
        }

        IntegrityBannerService::getInstance()->updateBanner($banner, $input);

        return $this->success();
    }

    public function up()
    {
        $id = $this->verifyRequiredId('id');
        $banner = IntegrityBannerService::getInstance()->getBannerById($id);
        if (is_null($banner)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前活动banner不存在');
        }

        $banner->status = 1;
        $banner->save();

        return $this->success();
    }

    public function down()
    {
        $id = $this->verifyRequiredId('id');
        $banner = IntegrityBannerService::getInstance()->getBannerById($id);
        if (is_null($banner)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前活动banner不存在');
        }

        $banner->status = 2;
        $banner->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $banner = IntegrityBannerService::getInstance()->getBannerById($id);
        if (is_null($banner)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前活动banner不存在');
        }
        $banner->delete();
        return $this->success();
    }
}
