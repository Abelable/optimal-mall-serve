<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThemeZoneGoods;
use App\Services\GoodsService;
use App\Services\ThemeZoneGoodsService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class ThemeZoneGoodsController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $list = ThemeZoneGoodsService::getInstance()->getGoodsPage($input);
        return $this->successPaginate($list);
    }

    public function add()
    {
        $themeId = $this->verifyRequiredId('themeId');
        $goodsIds = $this->verifyArray('ids');

        $zoneGoodsList = ThemeZoneGoodsService::getInstance()->getFilterGoodsList($goodsIds);
        if (count($zoneGoodsList) != 0) {
            return $this->fail(CodeResponse::DATA_EXISTED, '已添加相同商品');
        }

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds, ['id', 'cover', 'name']);

        foreach ($goodsList as $goods) {
            $zoneGoods = ThemeZoneGoods::new();
            $zoneGoods->theme_id = $themeId;
            $zoneGoods->goods_id = $goods->id;
            $zoneGoods->goods_cover = $goods->cover;
            $zoneGoods->goods_name = $goods->name;
            $zoneGoods->save();
        }

        return $this->success();
    }

    public function editSort() {
        $id = $this->verifyRequiredId('id');
        $sort = $this->verifyRequiredInteger('sort');

        $goods = ThemeZoneGoodsService::getInstance()->getGoodsById($id);
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
        $goods = ThemeZoneGoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods->delete();
        return $this->success();
    }
}
