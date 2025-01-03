<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewYearLocalRegion;
use App\Services\NewYearLocalRegionService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class NewYearLocalRegionController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $list = NewYearLocalRegionService::getInstance()->getRegionList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $region = NewYearLocalRegionService::getInstance()->getRegionById($id);
        if (is_null($region)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前地区不存在');
        }
        return $this->success($region);
    }

    public function add()
    {
        $name = $this->verifyRequiredString('name');

        $region = NewYearLocalRegionService::getInstance()->getRegionByName($name);
        if (!is_null($region)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '地区已存在');
        }

        $region = NewYearLocalRegion::new();
        $region->name = $name;
        $region->save();

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        $name = $this->verifyRequiredString('name');

        $region = NewYearLocalRegionService::getInstance()->getRegionByName($name);
        if (!is_null($region)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '地区已存在');
        }

        $region = NewYearLocalRegionService::getInstance()->getRegionById($id);
        if (is_null($region)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前地区不存在');
        }

        $region->name = $name;
        $region->save();

        return $this->success();
    }

    public function editSort() {
        $id = $this->verifyRequiredId('id');
        $sort = $this->verifyRequiredInteger('sort');

        $region = NewYearLocalRegionService::getInstance()->getRegionById($id);
        if (is_null($region)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前地区不存在');
        }

        $region->sort = $sort;
        $region->save();

        return $this->success();
    }

    public function editStatus() {
        $id = $this->verifyRequiredId('id');
        $status = $this->verifyRequiredInteger('status');

        $region = NewYearLocalRegionService::getInstance()->getRegionById($id);
        if (is_null($region)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前地区不存在');
        }

        $region->status = $status;
        $region->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $region = NewYearLocalRegionService::getInstance()->getRegionById($id);
        if (is_null($region)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前地区不存在');
        }
        $region->delete();
        return $this->success();
    }

    public function options()
    {
        $options = NewYearLocalRegionService::getInstance()->getRegionOptions(['id', 'name']);
        return $this->success($options);
    }
}
