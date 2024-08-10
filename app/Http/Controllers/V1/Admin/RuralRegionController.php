<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\RuralRegion;
use App\Services\RuralRegionService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class RuralRegionController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $list = RuralRegionService::getInstance()->getRegionList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $region = RuralRegionService::getInstance()->getRegionById($id);
        if (is_null($region)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前地区不存在');
        }
        return $this->success($region);
    }

    public function add()
    {
        $name = $this->verifyRequiredString('name');

        $region = RuralRegionService::getInstance()->getRegionByName($name);
        if (!is_null($region)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '地区已存在');
        }

        $region = RuralRegion::new();
        $region->name = $name;
        $region->save();

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        $name = $this->verifyRequiredString('name');

        $region = RuralRegionService::getInstance()->getRegionByName($name);
        if (!is_null($region)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '地区已存在');
        }

        $region = RuralRegionService::getInstance()->getRegionById($id);
        if (is_null($region)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前地区不存在');
        }

        $region->name = $name;
        $region->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $region = RuralRegionService::getInstance()->getRegionById($id);
        if (is_null($region)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前地区不存在');
        }
        $region->delete();
        return $this->success();
    }

    public function options()
    {
        $options = RuralRegionService::getInstance()->getRegionOptions(['id', 'name']);
        return $this->success($options);
    }
}
