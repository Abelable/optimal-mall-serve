<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\VillageGrainGoods;
use App\Services\GoodsService;
use App\Services\VillageGrainGoodsService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\GoodsListInput;
use App\Utils\Inputs\PageInput;

class VillageGrainGoodsController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $list = VillageGrainGoodsService::getInstance()->getGoodsPage($input);
        return $this->successPaginate($list);
    }

    public function add()
    {
        /** @var GoodsListInput $input */
        $input = GoodsListInput::new();

        $grainGoodsList = VillageGrainGoodsService::getInstance()->getFilterGoodsList($input);
        if (count($grainGoodsList) != 0) {
            return $this->fail(CodeResponse::DATA_EXISTED, '已添加相同商品');
        }

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($input->goodsIds, ['id', 'cover', 'name']);

        foreach ($goodsList as $goods) {
            $grainGoods = VillageGrainGoods::new();
            $grainGoods->goods_id = $goods->id;
            $grainGoods->goods_cover = $goods->cover;
            $grainGoods->goods_name = $goods->name;
            $grainGoods->save();
        }

        return $this->success();
    }

    public function editSort() {
        $id = $this->verifyRequiredId('id');
        $sort = $this->verifyRequiredInteger('sort');

        $goods = VillageGrainGoodsService::getInstance()->getGoodsById($id);
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
        $goods = VillageGrainGoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods->delete();
        return $this->success();
    }
}
