<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\GoodsService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\GoodsListInput;
use App\Utils\Inputs\GoodsInput;

class GoodsController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var GoodsListInput $input */
        $input = GoodsListInput::new();
        $list = GoodsService::getInstance()->getOwnerGoodsList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        return $this->success($goods);
    }

    public function up()
    {
        $id = $this->verifyRequiredId('id');

        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods->status = 1;
        $goods->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods->delete();

        return $this->success();
    }

    public function down()
    {
        $id = $this->verifyRequiredId('id');

        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods->status = 2;
        $goods->save();

        return $this->success();
    }

    public function add()
    {
        /** @var GoodsInput $input */
        $input = GoodsInput::new();
        GoodsService::getInstance()->createGoods($input);
        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var GoodsInput $input */
        $input = GoodsInput::new();

        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        GoodsService::getInstance()->updateGoods($goods, $input);

        return $this->success();
    }
}
