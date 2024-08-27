<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\TodayGoods;
use App\Services\GoodsService;
use App\Services\TodayGoodsService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class TodayGoodsController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $list = TodayGoodsService::getInstance()->getGoodsPage($input);
        return $this->successPaginate($list);
    }

    public function add()
    {
        $goodsIds = $this->verifyArrayNotEmpty('goodsIds');
        $type = $this->verifyRequiredInteger('type');

        $todayGoodsList = TodayGoodsService::getInstance()->getFilterGoodsList($goodsIds);
        if (count($todayGoodsList) != 0) {
            return $this->fail(CodeResponse::DATA_EXISTED, '已添加相同商品');
        }

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds, ['id', 'cover', 'name']);

        foreach ($goodsList as $goods) {
            $todayGoods = TodayGoods::new();
            $todayGoods->goods_id = $goods->id;
            $todayGoods->goods_cover = $goods->cover;
            $todayGoods->goods_name = $goods->name;
            $todayGoods->type = $type;
            $todayGoods->save();
        }

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $goods = TodayGoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods->delete();
        return $this->success();
    }
}
