<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Services\ActivityService;
use App\Services\GiftGoodsService;
use App\Services\GoodsCategoryService;
use App\Services\GoodsService;
use App\Services\IntegrityGoodsService;
use App\Services\LimitedTimeRecruitGoodsService;
use App\Services\NewYearCultureGoodsService;
use App\Services\NewYearGoodsService;
use App\Services\NewYearLocalGoodsService;
use App\Services\RuralGoodsService;
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

        DB::transaction(function () use ($goods) {
            $goods->status = 2;
            $goods->save();

            // 下架商品活动
            $activity = ActivityService::getInstance()->getActivityByGoodsId($goods->id, [1]);
            if (!is_null($activity)) {
                $activity->status = 2;
                $activity->save();
            }
            RuralGoodsService::getInstance()->deleteByGoodsId($goods->id);
            GiftGoodsService::getInstance()->deleteByGoodsId($goods->id);
            IntegrityGoodsService::getInstance()->deleteByGoodsId($goods->id);
            NewYearGoodsService::getInstance()->deleteByGoodsId($goods->id);
            NewYearCultureGoodsService::getInstance()->deleteByGoodsId($goods->id);
            NewYearLocalGoodsService::getInstance()->deleteByGoodsId($goods->id);
            LimitedTimeRecruitGoodsService::getInstance()->deleteByGoodsId($goods->id);
        });

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
        $sales = $this->verifyRequiredInteger('sales');

        $goods = GoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $goods->sales_volume = $sales;
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
