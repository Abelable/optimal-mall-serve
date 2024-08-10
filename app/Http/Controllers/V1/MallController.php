<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\MallBannerService;
use App\Services\MallService;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;

class MallController extends Controller
{
    protected $except = ['bannerList', 'list'];

    public function bannerList()
    {
        $list = MallBannerService::getInstance()->getBannerList();
        return $this->success($list);
    }

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();

        $scenicColumns = [
            'id',
            DB::raw('NULL as shop_id'),
            DB::raw('NULL as status'),
            'name',
            DB::raw('NULL as cover'),
            'image_list',
            'price',
            DB::raw('NULL as market_price'),
            'sales_volume',
            'longitude',
            'latitude',
            'address',
            'feature_tag_list',
            DB::raw('NULL as facility_list'),
            'created_at',
        ];
        $hotelColumns = [
            'id',
            DB::raw('NULL as shop_id'),
            DB::raw('NULL as status'),
            'name',
            'cover',
            DB::raw('NULL as image_list'),
            'price',
            DB::raw('NULL as market_price'),
            'sales_volume',
            'longitude',
            'latitude',
            'address',
            'feature_tag_list',
            DB::raw('NULL as facility_list'),
            'created_at',
        ];
        $restaurantColumns = [
            'id',
            DB::raw('NULL as shop_id'),
            DB::raw('NULL as status'),
            'name',
            'cover',
            DB::raw('NULL as image_list'),
            'price',
            DB::raw('NULL as market_price'),
            'sales_volume',
            'longitude',
            'latitude',
            'address',
            DB::raw('NULL as feature_tag_list'),
            'facility_list',
            'created_at',
        ];
        $goodsColumns = [
            'id',
            'shop_id',
            'status',
            'name',
            'cover',
            'image_list',
            'price',
            'market_price',
            'sales_volume',
            DB::raw('NULL as longitude'),
            DB::raw('NULL as latitude'),
            DB::raw('NULL as address'),
            DB::raw('NULL as feature_tag_list'),
            DB::raw('NULL as facility_list'),
            'created_at',
        ];

        $page = MallService::getInstance()->pageList($input, $scenicColumns, $hotelColumns, $restaurantColumns, $goodsColumns);
        $list = collect($page->items())->map(function ($commodity) {
            if ($commodity['type'] == 1) {
                $commodity['cover'] = json_decode($commodity['image_list'])[0];
            }
            if ($commodity['type'] == 1 || $commodity['type'] == 2) {
                $commodity['feature_tag_list'] = json_decode($commodity['feature_tag_list']);
            }
            if ($commodity['type'] == 3) {
                $commodity['facility_list'] = json_decode($commodity['facility_list']);
            }
            if ($commodity['type'] == 4 && $commodity->shop_id != 0) {
                $shopInfo = ShopService::getInstance()->getShopById($commodity->shop_id, ['id', 'type', 'avatar', 'name']);
                $commodity['shop_info'] = $shopInfo;
            }
            return $commodity;
        });

        return $this->success($this->paginate($page, $list));
    }
}
