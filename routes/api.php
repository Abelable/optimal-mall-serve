<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('auth')->group(function () {
    Route::prefix('wx_mp')->group(function () {
        Route::post('mobile', 'AuthController@getWxMpUserMobile');
        Route::post('register', 'AuthController@wxMpRegister');
        Route::post('login', 'AuthController@wxMpLogin');
    });

    Route::get('token_refresh', 'AuthController@refreshToken');
});

Route::prefix('user')->group(function () {
    Route::get('info', 'UserController@userInfo');
    Route::post('update', 'UserController@updateUserInfo');
    Route::get('tim_login_info', 'UserController@timLoginInfo');
    Route::get('superior_info', 'UserController@superiorInfo');
});

Route::get('oss_config', 'CommonController@ossConfig');

Route::prefix('wx')->group(function () {
    Route::post('pay_notify', 'CommonController@wxPayNotify');
    Route::get('qrcode', 'CommonController@wxQRCode');
});

Route::prefix('keyword')->group(function () {
    Route::get('list', 'KeywordController@list');
    Route::post('add', 'KeywordController@add');
    Route::post('clear', 'KeywordController@clear');
    Route::get('hot_list', 'KeywordController@hotList');
});

Route::prefix('goods')->group(function () {
    Route::get('category_options', 'GoodsController@categoryOptions');
    Route::get('list', 'GoodsController@list');
    Route::get('search', 'GoodsController@search');
    Route::post('media_relative_list', 'GoodsController@mediaRelativeList');
    Route::get('detail', 'GoodsController@detail');

    Route::prefix('evaluation')->group(function () {
        Route::get('summary', 'GoodsEvaluationController@summary');
        Route::get('list', 'GoodsEvaluationController@list');
        Route::post('add', 'GoodsEvaluationController@add');
        Route::post('delete', 'GoodsEvaluationController@delete');
    });
});

Route::prefix('cart')->group(function () {
    Route::get('goods_number', 'CartController@goodsNumber');
    Route::get('list', 'CartController@list');
    Route::post('fast_add', 'CartController@fastAdd');
    Route::post('add', 'CartController@add');
    Route::post('edit', 'CartController@edit');
    Route::post('delete', 'CartController@delete');
});

Route::prefix('address')->group(function () {
    Route::get('list', 'AddressController@list');
    Route::get('detail', 'AddressController@detail');
    Route::post('add', 'AddressController@add');
    Route::post('edit', 'AddressController@edit');
    Route::post('delete', 'AddressController@delete');
});

Route::prefix('order')->group(function () {
    Route::post('pre_order_info', 'OrderController@preOrderInfo');
    Route::post('submit', 'OrderController@submit');
    Route::post('pay_params', 'OrderController@payParams');
    Route::get('totals', 'OrderController@orderListTotals');
    Route::get('list', 'OrderController@list');
    Route::get('detail', 'OrderController@detail');
    Route::post('confirm', 'OrderController@confirm');
    Route::post('refund', 'OrderController@refund');
    Route::post('cancel', 'OrderController@cancel');
    Route::post('delete', 'OrderController@delete');
});

Route::prefix('mall')->group(function () {
    Route::get('banner_list', 'MallController@bannerList');
    Route::get('today_goods_list', 'MallController@todayGoodsList');
    Route::get('advance_goods_list', 'MallController@advanceGoodsList');
});

Route::prefix('rural')->group(function () {
    Route::get('banner_list', 'RuralController@bannerList');
    Route::get('region_options', 'RuralController@regionOptions');
    Route::get('goods_list', 'RuralController@goodsList');
});

Route::prefix('integrity')->group(function () {
    Route::get('banner_list', 'IntegrityController@bannerList');
    Route::get('goods_list', 'IntegrityController@goodsList');
});

Route::prefix('gift')->group(function () {
    Route::get('goods_list', 'GiftController@goodsList');
});

/*
|--------------------------------------------------------------------------
| 管理后台接口
|--------------------------------------------------------------------------
*/
Route::namespace('Admin')->prefix('admin')->group(function () {
    Route::get('oss_config', 'CommonController@ossConfig');

    Route::prefix('auth')->group(function () {
        Route::post('login', 'AuthController@login');
        Route::get('logout', 'AuthController@logout');
        Route::get('me', 'AuthController@info');
        Route::get('token_refresh', 'AuthController@refreshToken');
    });

    Route::post('list', 'AdminController@list');
    Route::get('detail', 'AdminController@detail');
    Route::post('add', 'AdminController@add');
    Route::post('edit', 'AdminController@edit');
    Route::post('delete', 'AdminController@delete');

    Route::prefix('role')->group(function () {
        Route::post('list', 'RoleController@list');
        Route::get('detail', 'RoleController@detail');
        Route::post('add', 'RoleController@add');
        Route::post('edit', 'RoleController@edit');
        Route::post('delete', 'RoleController@delete');
        Route::get('options', 'RoleController@options');
    });

    Route::prefix('user')->group(function () {
        Route::post('list', 'UserController@list');
        Route::get('detail', 'UserController@detail');
        Route::post('delete', 'UserController@delete');
    });

    Route::prefix('team')->group(function () {
        Route::prefix('promoter')->group(function () {
            Route::post('list', 'PromoterController@list');
            Route::post('delete', 'PromoterController@delete');
            Route::get('options', 'PromoterController@options');
        });

        Route::prefix('goods')->group(function () {
            Route::post('list', 'GiftGoodsController@list');
            Route::post('add', 'GiftGoodsController@add');
            Route::post('delete', 'GiftGoodsController@delete');
        });
    });

    Route::prefix('mall')->group(function () {
        Route::prefix('banner')->group(function () {
            Route::post('list', 'MallBannerController@list');
            Route::get('detail', 'MallBannerController@detail');
            Route::post('add', 'MallBannerController@add');
            Route::post('edit', 'MallBannerController@edit');
            Route::post('up', 'MallBannerController@up');
            Route::post('down', 'MallBannerController@down');
            Route::post('delete', 'MallBannerController@delete');
        });

        Route::prefix('today_goods')->group(function () {
            Route::post('list', 'TodayGoodsController@list');
            Route::post('add', 'TodayGoodsController@add');
            Route::post('delete', 'TodayGoodsController@delete');
        });

        Route::prefix('advance_goods')->group(function () {
            Route::post('list', 'AdvanceGoodsController@list');
            Route::post('add', 'AdvanceGoodsController@add');
            Route::post('delete', 'AdvanceGoodsController@delete');
        });

        Route::prefix('activity')->group(function () {
            Route::post('list', 'ActivityController@list');
            Route::post('add', 'ActivityController@add');
            Route::post('delete', 'ActivityController@delete');
        });
    });

    Route::prefix('rural')->group(function () {
        Route::prefix('banner')->group(function () {
            Route::post('list', 'RuralBannerController@list');
            Route::get('detail', 'RuralBannerController@detail');
            Route::post('add', 'RuralBannerController@add');
            Route::post('edit', 'RuralBannerController@edit');
            Route::post('up', 'RuralBannerController@up');
            Route::post('down', 'RuralBannerController@down');
            Route::post('delete', 'RuralBannerController@delete');
        });

        Route::prefix('region')->group(function () {
            Route::post('list', 'RuralRegionController@list');
            Route::get('detail', 'RuralRegionController@detail');
            Route::post('add', 'RuralRegionController@add');
            Route::post('edit', 'RuralRegionController@edit');
            Route::post('edit_sort', 'RuralRegionController@editSort');
            Route::post('edit_status', 'RuralRegionController@editStatus');
            Route::post('delete', 'RuralRegionController@delete');
            Route::get('options', 'RuralRegionController@options');
        });

        Route::prefix('goods')->group(function () {
            Route::post('list', 'RuralGoodsController@list');
            Route::post('add', 'RuralGoodsController@add');
            Route::post('delete', 'RuralGoodsController@delete');
        });
    });

    Route::prefix('integrity')->group(function () {
        Route::prefix('banner')->group(function () {
            Route::post('list', 'IntegrityBannerController@list');
            Route::get('detail', 'IntegrityBannerController@detail');
            Route::post('add', 'IntegrityBannerController@add');
            Route::post('edit', 'IntegrityBannerController@edit');
            Route::post('up', 'IntegrityBannerController@up');
            Route::post('down', 'IntegrityBannerController@down');
            Route::post('delete', 'IntegrityBannerController@delete');
        });

        Route::prefix('goods')->group(function () {
            Route::post('list', 'IntegrityGoodsController@list');
            Route::post('add', 'IntegrityGoodsController@add');
            Route::post('delete', 'IntegrityGoodsController@delete');
        });
    });

    Route::prefix('express')->group(function () {
        Route::post('list', 'ExpressController@list');
        Route::get('detail', 'ExpressController@detail');
        Route::post('add', 'ExpressController@add');
        Route::post('edit', 'ExpressController@edit');
        Route::post('delete', 'ExpressController@delete');
        Route::get('options', 'ExpressController@options');
    });

    Route::prefix('merchant')->group(function () {
        Route::post('list', 'MerchantController@list');
        Route::get('detail', 'MerchantController@detail');
        Route::post('add', 'MerchantController@add');
        Route::post('edit', 'MerchantController@edit');
        Route::post('delete', 'MerchantController@delete');
        Route::get('options', 'MerchantController@options');
    });

    Route::prefix('freight_template')->group(function () {
        Route::post('list', 'FreightTemplateController@list');
        Route::get('detail', 'FreightTemplateController@detail');
        Route::post('add', 'FreightTemplateController@add');
        Route::post('edit', 'FreightTemplateController@edit');
        Route::post('delete', 'FreightTemplateController@delete');
        Route::get('options', 'FreightTemplateController@options');
    });

    Route::prefix('goods')->group(function () {
        Route::prefix('category')->group(function () {
            Route::post('list', 'GoodsCategoryController@list');
            Route::get('detail', 'GoodsCategoryController@detail');
            Route::post('add', 'GoodsCategoryController@add');
            Route::post('edit', 'GoodsCategoryController@edit');
            Route::post('edit_sort', 'GoodsCategoryController@editSort');
            Route::post('edit_status', 'GoodsCategoryController@editStatus');
            Route::post('delete', 'GoodsCategoryController@delete');
            Route::get('options', 'GoodsCategoryController@options');
            Route::get('filter_options', 'GoodsCategoryController@filterOptions');
        });

        Route::post('list', 'GoodsController@list');
        Route::get('detail', 'GoodsController@detail');
        Route::get('owner_list', 'GoodsController@ownerList');
        Route::get('owner_detail', 'GoodsController@ownerDetail');
        Route::post('up', 'GoodsController@up');
        Route::post('down', 'GoodsController@down');
        Route::post('reject', 'GoodsController@reject');
        Route::post('delete', 'GoodsController@delete');
        Route::post('add', 'GoodsController@add');
        Route::post('edit', 'GoodsController@edit');
        Route::get('options', 'GoodsController@options');
    });

    Route::prefix('order')->group(function () {
        Route::post('list', 'OrderController@list');
        Route::get('detail', 'OrderController@detail');
        Route::post('confirm', 'OrderController@confirm');
        Route::post('refund', 'OrderController@refund');
        Route::post('cancel', 'OrderController@cancel');
        Route::post('delete', 'OrderController@delete');
    });
});
