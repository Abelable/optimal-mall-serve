<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Services\GoodsCategoryService;
use App\Services\GoodsService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\GoodsListInput;
use App\Utils\Inputs\GoodsInput;
use Illuminate\Support\Facades\DB;

class GoodsController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var GoodsListInput $input */
        $input = GoodsListInput::new();
        $page = GoodsService::getInstance()->getGoodsList($input);
        $list = collect($page->items())->map(function (Goods $goods) {
            $goods['categoryIds'] = $goods->categories->pluck('category_id')->toArray();
            unset($goods->categories);
            return $goods;
        });
        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods['categoryIds'] = $goods->categories->pluck('category_id')->toArray();
        unset($goods->categories);
        $goods->image_list = json_decode($goods->image_list);
        $goods->detail_image_list = json_decode($goods->detail_image_list);
        $goods->sku_list = json_decode($goods->sku_list);
        $goods->spec_list = json_decode($goods->spec_list);
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
        DB::transaction(function () use ($input) {
            $goods = GoodsService::getInstance()->createGoods($input);
            GoodsCategoryService::getInstance()->createList($goods->id, $input->categoryIds);
        });

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

        DB::transaction(function () use ($input, $goods) {
            GoodsService::getInstance()->updateGoods($goods, $input);
            GoodsCategoryService::getInstance()->deleteListByGoodsId($goods->id);
            GoodsCategoryService::getInstance()->createList($goods->id, $input->categoryIds);
        });

        return $this->success();
    }

    public function editSales()
    {
        $id = $this->verifyRequiredId('id');
        $sales_volume = $this->verifyRequiredInteger('sales_volume');

        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $goods->sales_volume = $sales_volume;
        $goods->save();

        return $this->success();
    }

    public function options()
    {
        $keywords = $this->verifyString('keywords');
        $goodsOptions = GoodsService::getInstance()->getGoodsOptions($keywords, ['id', 'cover', 'name']);
        return $this->success($goodsOptions);
    }
}
