<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewYearGoods;
use App\Services\GoodsService;
use App\Services\NewYearGoodsService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\RegionGoodsListInput;
use App\Utils\Inputs\PageInput;

class NewYearGoodsController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $list = NewYearGoodsService::getInstance()->getGoodsPage($input);
        return $this->successPaginate($list);
    }

    public function add()
    {
        /** @var RegionGoodsListInput $input */
        $input = RegionGoodsListInput::new();

        $newYearGoodsList = NewYearGoodsService::getInstance()->getFilterGoodsList($input);
        if (count($newYearGoodsList) != 0) {
            return $this->fail(CodeResponse::DATA_EXISTED, '已添加相同商品');
        }

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($input->goodsIds, ['id', 'cover', 'name']);

        foreach ($goodsList as $goods) {
            $newYearGoods = NewYearGoods::new();
            $newYearGoods->goods_id = $goods->id;
            $newYearGoods->goods_cover = $goods->cover;
            $newYearGoods->goods_name = $goods->name;
            $newYearGoods->save();
        }

        return $this->success();
    }

    public function editSort() {
        $id = $this->verifyRequiredId('id');
        $sort = $this->verifyRequiredInteger('sort');

        $goods = NewYearGoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $goods->sort = $sort;
        $goods->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $goods = NewYearGoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods->delete();
        return $this->success();
    }
}
