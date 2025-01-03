<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\VillageGiftGoods;
use App\Services\GoodsService;
use App\Services\VillageGiftGoodsService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\RegionGoodsListInput;
use App\Utils\Inputs\PageInput;

class VillageGiftGoodsController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $list = VillageGiftGoodsService::getInstance()->getGoodsPage($input);
        return $this->successPaginate($list);
    }

    public function add()
    {
        /** @var RegionGoodsListInput $input */
        $input = RegionGoodsListInput::new();

        $giftGoodsList = VillageGiftGoodsService::getInstance()->getFilterGoodsList($input);
        if (count($giftGoodsList) != 0) {
            return $this->fail(CodeResponse::DATA_EXISTED, '已添加相同商品');
        }

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($input->goodsIds, ['id', 'cover', 'name']);

        foreach ($goodsList as $goods) {
            $giftGoods = VillageGiftGoods::new();
            $giftGoods->goods_id = $goods->id;
            $giftGoods->goods_cover = $goods->cover;
            $giftGoods->goods_name = $goods->name;
            $giftGoods->save();
        }

        return $this->success();
    }

    public function editSort() {
        $id = $this->verifyRequiredId('id');
        $sort = $this->verifyRequiredInteger('sort');

        $goods = VillageGiftGoodsService::getInstance()->getGoodsById($id);
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
        $goods = VillageGiftGoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods->delete();
        return $this->success();
    }
}
