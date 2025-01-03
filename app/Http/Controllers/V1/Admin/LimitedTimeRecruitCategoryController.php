<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\LimitedTimeRecruitCategory;
use App\Services\LimitedTimeRecruitCategoryService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class LimitedTimeRecruitCategoryController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $list = LimitedTimeRecruitCategoryService::getInstance()->getCategoryList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $region = LimitedTimeRecruitCategoryService::getInstance()->getCategoryById($id);
        if (is_null($region)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前分类不存在');
        }
        return $this->success($region);
    }

    public function add()
    {
        $name = $this->verifyRequiredString('name');

        $region = LimitedTimeRecruitCategoryService::getInstance()->getCategoryByName($name);
        if (!is_null($region)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '分类已存在');
        }

        $region = LimitedTimeRecruitCategory::new();
        $region->name = $name;
        $region->save();

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        $name = $this->verifyRequiredString('name');

        $region = LimitedTimeRecruitCategoryService::getInstance()->getCategoryByName($name);
        if (!is_null($region)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '分类已存在');
        }

        $region = LimitedTimeRecruitCategoryService::getInstance()->getCategoryById($id);
        if (is_null($region)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前分类不存在');
        }

        $region->name = $name;
        $region->save();

        return $this->success();
    }

    public function editSort() {
        $id = $this->verifyRequiredId('id');
        $sort = $this->verifyRequiredInteger('sort');

        $region = LimitedTimeRecruitCategoryService::getInstance()->getCategoryById($id);
        if (is_null($region)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前分类不存在');
        }

        $region->sort = $sort;
        $region->save();

        return $this->success();
    }

    public function editStatus() {
        $id = $this->verifyRequiredId('id');
        $status = $this->verifyRequiredInteger('status');

        $region = LimitedTimeRecruitCategoryService::getInstance()->getCategoryById($id);
        if (is_null($region)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前分类不存在');
        }

        $region->status = $status;
        $region->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $region = LimitedTimeRecruitCategoryService::getInstance()->getCategoryById($id);
        if (is_null($region)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前分类不存在');
        }
        $region->delete();
        return $this->success();
    }

    public function options()
    {
        $options = LimitedTimeRecruitCategoryService::getInstance()->getCategoryOptions(['id', 'name']);
        return $this->success($options);
    }
}
