<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\IntegrityGoods;
use App\Services\GoodsService;
use App\Services\IntegrityGoodsService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\GoodsListInput;
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
        /** @var GoodsListInput $input */
        $input = GoodsListInput::new();

        $integrityGoodsList = IntegrityGoodsService::getInstance()->getFilterGoodsList($input);
        if (count($integrityGoodsList) != 0) {
            return $this->fail(CodeResponse::DATA_EXISTED, '当前地区已添加相同商品');
        }

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($input->goodsIds, ['id', 'cover', 'name']);

        foreach ($goodsList as $goods) {
            $integrityGoods = IntegrityGoods::new();
            $integrityGoods->goods_id = $goods->id;
            $integrityGoods->goods_cover = $goods->cover;
            $integrityGoods->goods_name = $goods->name;
            $integrityGoods->save();
        }

        return $this->success();
    }

    public function editSort() {
        $id = $this->verifyRequiredId('id');
        $sort = $this->verifyRequiredInteger('sort');

        $goods = IntegrityGoodsService::getInstance()->getGoodsById($id);
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
        $goods = IntegrityGoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods->delete();
        return $this->success();
    }
}
