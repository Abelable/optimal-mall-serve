<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoodsCategory;
use App\Services\GoodsCategoryService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\GoodsCategoryInput;
use App\Utils\Inputs\Admin\GoodsCategoryPageInput;

class GoodsCategoryController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var GoodsCategoryPageInput $input */
        $input = GoodsCategoryPageInput::new();

        $list = GoodsCategoryService::getInstance()->getCategoryList($input);

        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $category = GoodsCategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品分类不存在');
        }
        return $this->success($category);
    }

    public function add()
    {
        /** @var GoodsCategoryInput $input */
        $input = GoodsCategoryInput::new();

        $category = GoodsCategoryService::getInstance()->getCategoryByName($input->name);
        if (!is_null($category)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '当前商品分类已存在');
        }

        $category = GoodsCategory::new();
        $category->name = $input->name;
        $category->min_leader_commission_rate = $input->minLeaderCommissionRate;
        $category->max_leader_commission_rate = $input->maxLeaderCommissionRate;
        $category->min_share_commission_rate = $input->minShareCommissionRate;
        $category->max_share_commission_rate = $input->maxShareCommissionRate;
        $category->save();

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyId('id');
        /** @var GoodsCategoryInput $input */
        $input = GoodsCategoryInput::new();

        $category = GoodsCategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品分类不存在');
        }

        $category->name = $input->name;
        $category->min_leader_commission_rate = $input->minLeaderCommissionRate;
        $category->max_leader_commission_rate = $input->maxLeaderCommissionRate;
        $category->min_share_commission_rate = $input->minShareCommissionRate;
        $category->max_share_commission_rate = $input->maxShareCommissionRate;
        $category->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $category = GoodsCategoryService::getInstance()->getCategoryById($id);
        if (is_null($category)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品分类不存在');
        }
        $category->delete();
        return $this->success();
    }

    public function options()
    {
        $options = GoodsCategoryService::getInstance()->getCategoryOptions();
        return $this->success($options);
    }

    public function filterOptions()
    {
        $shopCategoryId = $this->verifyRequiredId('shopCategoryId');
        $options = GoodsCategoryService::getInstance()->getOptionsByShopCategoryId($shopCategoryId, ['id', 'name']);
        return $this->success($options);
    }
}
