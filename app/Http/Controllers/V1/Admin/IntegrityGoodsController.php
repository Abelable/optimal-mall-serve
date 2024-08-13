<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\IntegrityGoods;
use App\Services\GoodsService;
use App\Services\IntegrityGoodsService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\IntegrityGoodsInput;
use App\Utils\Inputs\PageInput;

class IntegrityGoodsController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $list = IntegrityGoodsService::getInstance()->getGoodsPage($input);
        return $this->successPaginate($list);
    }

    public function add()
    {
        /** @var IntegrityGoodsInput $input */
        $input = IntegrityGoodsInput::new();

        $ruralGoodsList = IntegrityGoodsService::getInstance()->getFilterGoodsList($input);
        if (count($ruralGoodsList) != 0) {
            return $this->fail(CodeResponse::DATA_EXISTED, '当前地区已添加相同商品');
        }

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($input->goodsIds, ['id', 'cover', 'name']);

        foreach ($goodsList as $goods) {
            $ruralGoods = IntegrityGoods::new();
            $ruralGoods->goods_id = $goods->id;
            $ruralGoods->goods_cover = $goods->cover;
            $ruralGoods->goods_name = $goods->name;
            $ruralGoods->save();
        }

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $goods = IntegrityGoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods->delete();
        return $this->success();
    }
}
