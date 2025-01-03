<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\GiftGoods;
use App\Services\GoodsService;
use App\Services\GiftGoodsService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\GiftRegionGoodsListInput;
use App\Utils\Inputs\GiftGoodsPageInput;

class GiftGoodsController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var GiftGoodsPageInput $input */
        $input = GiftGoodsPageInput::new();
        $list = GiftGoodsService::getInstance()->getGoodsPage($input);
        return $this->successPaginate($list);
    }

    public function add()
    {
        /** @var GiftRegionGoodsListInput $input */
        $input = GiftRegionGoodsListInput::new();

        $giftGoodsList = GiftGoodsService::getInstance()->getFilterGoodsList($input);
        if (count($giftGoodsList) != 0) {
            return $this->fail(CodeResponse::DATA_EXISTED, '已添加相同商品');
        }

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($input->goodsIds, ['id', 'cover', 'name']);

        foreach ($goodsList as $goods) {
            $giftGoods = GiftGoods::new();
            $giftGoods->type = $input->type;
            $giftGoods->goods_id = $goods->id;
            $giftGoods->goods_cover = $goods->cover;
            $giftGoods->goods_name = $goods->name;
            $giftGoods->save();
        }

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $goods = GiftGoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods->delete();
        return $this->success();
    }
}
