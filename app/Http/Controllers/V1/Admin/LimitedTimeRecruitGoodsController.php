<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\LimitedTimeRecruitGoods;
use App\Services\GoodsService;
use App\Services\LimitedTimeRecruitGoodsService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\CategoryGoodsListInput;
use App\Utils\Inputs\CategoryPageInput;

class LimitedTimeRecruitGoodsController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var CategoryPageInput $input */
        $input = CategoryPageInput::new();
        $list = LimitedTimeRecruitGoodsService::getInstance()->getGoodsPage($input);
        return $this->successPaginate($list);
    }

    public function add()
    {
        /** @var CategoryGoodsListInput $input */
        $input = CategoryGoodsListInput::new();

        $limitedTimeRecruitGoodsList = LimitedTimeRecruitGoodsService::getInstance()->getFilterGoodsList($input);
        if (count($limitedTimeRecruitGoodsList) != 0) {
            return $this->fail(CodeResponse::DATA_EXISTED, '当前分类已添加相同商品');
        }

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($input->goodsIds, ['id', 'cover', 'name']);

        foreach ($goodsList as $goods) {
            $limitedTimeRecruitGoods = LimitedTimeRecruitGoods::new();
            $limitedTimeRecruitGoods->category_id = $input->categoryId;
            $limitedTimeRecruitGoods->goods_id = $goods->id;
            $limitedTimeRecruitGoods->goods_cover = $goods->cover;
            $limitedTimeRecruitGoods->goods_name = $goods->name;
            $limitedTimeRecruitGoods->save();
        }

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $goods = LimitedTimeRecruitGoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods->delete();
        return $this->success();
    }
}
