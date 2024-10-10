<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\GoodsCategory;
use App\Services\CategoryService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class CategoryController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $list = CategoryService::getInstance()->getCategoryList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $category = CategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品分类不存在');
        }
        return $this->success($category);
    }

    public function add()
    {
        $name = $this->verifyRequiredString('name');

        $category = CategoryService::getInstance()->getCategoryByName($name);
        if (!is_null($category)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '当前商品分类已存在');
        }

        $category = Category::new();
        $category->name = $name;
        $category->save();

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        $name = $this->verifyRequiredString('name');

        $category = CategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品分类不存在');
        }

        $category->name = $name;
        $category->save();

        return $this->success();
    }

    public function editSort() {
        $id = $this->verifyRequiredId('id');
        $sort = $this->verifyRequiredInteger('sort');

        $category = CategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品分类不存在');
        }

        $category->sort = $sort;
        $category->save();

        return $this->success();
    }

    public function editStatus() {
        $id = $this->verifyRequiredId('id');
        $status = $this->verifyRequiredInteger('status');

        $category = CategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品分类不存在');
        }

        $category->status = $status;
        $category->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $category = CategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品分类不存在');
        }
        $category->delete();
        return $this->success();
    }

    public function options()
    {
        $options = CategoryService::getInstance()->getCategoryOptions();
        return $this->success($options);
    }
}
