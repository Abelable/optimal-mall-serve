<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewYearLocalGoods;
use App\Services\GoodsService;
use App\Services\NewYearLocalGoodsService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\RegionGoodsListInput;
use App\Utils\Inputs\RegionPageInput;

class NewYearLocalGoodsController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var RegionPageInput $input */
        $input = RegionPageInput::new();
        $list = NewYearLocalGoodsService::getInstance()->getGoodsPage($input);
        return $this->successPaginate($list);
    }

    public function add()
    {
        /** @var RegionGoodsListInput $input */
        $input = RegionGoodsListInput::new();

        $newYearLocalGoodsList = NewYearLocalGoodsService::getInstance()->getFilterGoodsList($input);
        if (count($newYearLocalGoodsList) != 0) {
            return $this->fail(CodeResponse::DATA_EXISTED, '当前地区已添加相同商品');
        }

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($input->goodsIds, ['id', 'cover', 'name']);

        foreach ($goodsList as $goods) {
            $newYearLocalGoods = NewYearLocalGoods::new();
            $newYearLocalGoods->region_id = $input->regionId;
            $newYearLocalGoods->goods_id = $goods->id;
            $newYearLocalGoods->goods_cover = $goods->cover;
            $newYearLocalGoods->goods_name = $goods->name;
            $newYearLocalGoods->save();
        }

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $goods = NewYearLocalGoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods->delete();
        return $this->success();
    }
}
