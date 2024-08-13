<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\IntegrityGoods;
use App\Services\GoodsService;
use App\Services\AdvanceGoodsService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\GoodsListInput;
use App\Utils\Inputs\PageInput;

class AdvanceGoodsController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $list = AdvanceGoodsService::getInstance()->getGoodsPage($input);
        return $this->successPaginate($list);
    }

    public function add()
    {
        /** @var GoodsListInput $input */
        $input = GoodsListInput::new();

        $advanceGoodsList = AdvanceGoodsService::getInstance()->getFilterGoodsList($input);
        if (count($advanceGoodsList) != 0) {
            return $this->fail(CodeResponse::DATA_EXISTED, '当前地区已添加相同商品');
        }

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($input->goodsIds, ['id', 'cover', 'name']);

        foreach ($goodsList as $goods) {
            $advanceGoods = IntegrityGoods::new();
            $advanceGoods->goods_id = $goods->id;
            $advanceGoods->goods_cover = $goods->cover;
            $advanceGoods->goods_name = $goods->name;
            $advanceGoods->save();
        }

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $goods = AdvanceGoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods->delete();
        return $this->success();
    }
}
