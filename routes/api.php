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
    Route::get('customer_data', 'UserController@customerData');
    Route::get('today_new_customer_list', 'UserController@todayNewCustomerList');
    Route::get('today_ordering_customer_list', 'UserController@todayOrderingCustomerList');
    Route::get('customer_list', 'UserController@customerList');
    Route::get('promoter_data', 'UserController@promoterData');
    Route::get('today_new_promoter_list', 'UserController@todayNewPromoterList');
    Route::get('today_ordering_promoter_list', 'UserController@todayOrderingPromoterList');
    Route::get('promoter_list', 'UserController@promoterList');
});

Route::prefix('auth_info')->group(function () {
    Route::get('detail', 'AuthInfoController@detail');
    Route::post('add', 'AuthInfoController@add');
    Route::post('edit', 'AuthInfoController@edit');
    Route::post('delete', 'AuthInfoController@delete');
});

Route::prefix('enterprise_info')->group(function () {
    Route::get('detail', 'EnterpriseInfoController@detail');
    Route::post('add', 'EnterpriseInfoController@add');
    Route::post('edit', 'EnterpriseInfoController@edit');
    Route::post('delete', 'EnterpriseInfoController@delete');
});

Route::get('oss_config', 'CommonController@ossConfig');
Route::get('shipping_info', 'CommonController@shippingInfo');

Route::prefix('wx')->group(function () {
    Route::post('pay_notify', 'CommonController@wxPayNotify');
    Route::get('qrcode', 'CommonController@wxQRCode');
});

Route::prefix('fan')->group(function () {
    Route::post('follow', 'FanController@follow');
    Route::post('cancel_follow', 'FanController@cancelFollow');
    Route::get('follow_status', 'FanController@followStatus');
    Route::get('follow_list', 'FanController@followList');
    Route::get('fan_list', 'FanController@fanList');
});

Route::prefix('notification')->group(function () {
    Route::get('unread_count', 'NotificationController@unreadCount');
    Route::get('list', 'NotificationController@list');
    Route::post('clear', 'NotificationController@clear');
    Route::post('clear_all', 'NotificationController@clearAll');
    Route::post('delete', 'NotificationController@delete');
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
    Route::post('recommend_list', 'GoodsController@recommendList');
    Route::get('search', 'GoodsController@search');
    Route::get('detail', 'GoodsController@detail');
    Route::get('merchant_info', 'GoodsController@getMerchantInfo');
    Route::get('purchased_list', 'GoodsController@getPurchasedList');
    Route::get('pickup_address_list', 'GoodsController@getPickupAddressList');

    Route::prefix('evaluation')->group(function () {
        Route::get('summary', 'GoodsEvaluationController@summary');
        Route::get('list', 'GoodsEvaluationController@list');
        Route::get('detail', 'GoodsEvaluationController@detail');
        Route::post('add', 'GoodsEvaluationController@add');
        Route::post('edit', 'GoodsEvaluationController@edit');
        Route::post('delete', 'GoodsEvaluationController@delete');
    });
});

Route::prefix('cart')->group(function () {
    Route::get('goods_number', 'CartController@goodsNumber');
    Route::get('new_year_goods_number', 'CartController@newYearGoodsNumber');
    Route::get('list', 'CartController@list');
    Route::post('fast_add', 'CartController@fastAdd');
    Route::post('add', 'CartController@add');
    Route::post('edit', 'CartController@edit');
    Route::post('delete', 'CartController@delete');
});

Route::prefix('address')->group(function () {
    Route::get('default', 'AddressController@defaultAddress');
    Route::get('list', 'AddressController@list');
    Route::get('detail', 'AddressController@detail');
    Route::post('add', 'AddressController@add');
    Route::post('edit', 'AddressController@edit');
    Route::post('delete', 'AddressController@delete');
    Route::post('analyze', 'CommonController@analyzeAddress');
});

Route::prefix('order')->group(function () {
    Route::post('pre_order_info', 'OrderController@preOrderInfo');
    Route::post('submit', 'OrderController@submit');
    Route::post('pay_params', 'OrderController@payParams');
    Route::get('totals', 'OrderController@orderListTotals');
    Route::get('list', 'OrderController@list');
    Route::get('detail', 'OrderController@detail');
    Route::get('search', 'OrderController@search');
    Route::post('modify_address_info', 'OrderController@modifyOrderAddressInfo');
    Route::get('qr_code', 'OrderController@qrCode');
    Route::get('verify_code', 'OrderController@verifyCode');
    Route::post('verify', 'OrderController@verify');
    Route::post('confirm', 'OrderController@confirm');
    Route::post('refund', 'OrderController@refund');
    Route::post('cancel', 'OrderController@cancel');
    Route::post('delete', 'OrderController@delete');
    Route::post('commission_list', 'OrderController@commissionOrderList');
    Route::post('team_commission_list', 'OrderController@teamCommissionOrderList');
    Route::get('gift_commission_list', 'OrderController@giftCommissionOrderList');
    Route::get('waybill_token', 'OrderController@waybillToken');

    Route::prefix('keyword')->group(function () {
        Route::get('list', 'OrderKeywordController@list');
        Route::post('add', 'OrderKeywordController@add');
        Route::post('clear', 'OrderKeywordController@clear');
    });
});

Route::prefix('refund')->group(function () {
    Route::get('amount', 'RefundController@refundAmount');
    Route::get('detail', 'RefundController@detail');
    Route::post('add', 'RefundController@add');
    Route::post('edit', 'RefundController@edit');
    Route::post('submit_shipping_info', 'RefundController@submitShippingInfo');
    Route::post('delete', 'RefundController@delete');
});

Route::prefix('banner')->group(function () {
    Route::get('pop', 'BannerController@pop');
    Route::get('list', 'BannerController@list');
});

Route::prefix('mall')->group(function () {
    Route::get('activity_tag_options', 'MallController@activityTagOptions');
    Route::get('activity_list', 'MallController@activityList');
    Route::post('activity_subscribe', 'MallController@subscribeActivity');
    Route::get('grain_goods_list', 'MallController@grainGoodsList');
    Route::get('fresh_goods_list', 'MallController@freshGoodsList');
    Route::get('snack_goods_list', 'MallController@snackGoodsList');
    Route::get('gift_goods_list', 'MallController@giftGoodsList');
});

Route::prefix('coupon')->group(function () {
    Route::post('receive', 'CouponController@receiveCoupon');
    Route::get('user_list', 'CouponController@userCouponList');
});

Route::prefix('rural')->group(function () {
    Route::get('region_options', 'RuralController@regionOptions');
    Route::get('goods_list', 'RuralController@goodsList');
});

Route::prefix('integrity')->group(function () {
    Route::get('goods_list', 'IntegrityController@goodsList');
});

Route::prefix('gift')->group(function () {
    Route::get('goods_list', 'GiftController@goodsList');
});

Route::prefix('live')->group(function () {
    Route::post('create', 'LivePushController@createRoom');
    Route::get('room_status', 'LivePushController@roomStatus');
    Route::get('notice_room', 'LivePushController@noticeRoomInfo');
    Route::post('delete_notice_room', 'LivePushController@deleteNoticeRoom');
    Route::get('push_room', 'LivePushController@pushRoomInfo');
    Route::post('start', 'LivePushController@startLive');
    Route::post('stop', 'LivePushController@stopLive');
    Route::get('list', 'LivePlayController@roomList');
    Route::get('search', 'LivePlayController@search');
    Route::get('push_room_goods_list', 'LivePushController@pushRoomGoodsList');
    Route::post('listing_goods', 'LivePushController@listingGoods');
    Route::post('de_listing_goods', 'LivePushController@delistingGoods');
    Route::post('set_hot_goods', 'LivePushController@setHotGoods');
    Route::post('cancel_hot_goods', 'LivePushController@cancelHotGoods');
    Route::get('goods_list', 'LivePlayController@roomGoodsList');
    Route::get('hot_goods', 'LivePlayController@roomHotGoods');
    Route::post('join_room', 'LivePlayController@joinRoom');
    Route::post('praise', 'LivePlayController@praise');
    Route::post('comment', 'LivePlayController@comment');
    Route::post('subscribe', 'LivePlayController@subscribe');
    Route::get('user_ids', 'LivePlayController@liveUserIds');
});

Route::prefix('new_year')->group(function () {
    Route::get('goods_list', 'NewYearController@goodsList');
    Route::get('culture_goods_list', 'NewYearController@cultureGoodsList');
    Route::get('region_options', 'NewYearController@regionOptions');
    Route::get('local_goods_list', 'NewYearController@localGoodsList');
});

Route::prefix('limited_time_recruit')->group(function () {
    Route::get('category_options', 'LimitedTimeRecruitController@categoryOptions');
    Route::get('goods_list', 'LimitedTimeRecruitController@goodsList');
});

Route::prefix('commission')->group(function () {
    Route::get('sum', 'CommissionController@sum');
    Route::get('time_data', 'CommissionController@timeData');
    Route::get('cash', 'CommissionController@cash');
    Route::get('achievement', 'CommissionController@achievement');
});

Route::prefix('gift_commission')->group(function () {
    Route::get('sum', 'GiftCommissionController@sum');
    Route::get('time_data', 'GiftCommissionController@timeData');
    Route::get('cash', 'GiftCommissionController@cash');
});

Route::prefix('team_commission')->group(function () {
    Route::get('sum', 'TeamCommissionController@sum');
    Route::get('time_data', 'TeamCommissionController@timeData');
    Route::get('cash', 'TeamCommissionController@cash');
    Route::get('achievement', 'TeamCommissionController@achievement');
});

Route::prefix('bank_card')->group(function () {
    Route::get('detail', 'BankCardController@detail');
    Route::post('add', 'BankCardController@add');
    Route::post('edit', 'BankCardController@edit');
});

Route::prefix('withdraw')->group(function () {
    Route::post('submit', 'WithdrawalController@submit');
    Route::get('record_list', 'WithdrawalController@recordList');
});

Route::prefix('account')->group(function () {
    Route::get('info', 'AccountController@accountInfo');
    Route::get('transaction_record_list', 'AccountController@transactionRecordList');
});

Route::prefix('promoter')->group(function () {
    Route::get('list', 'PromoterController@list');
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
        Route::post('logout', 'AuthController@logout');
        Route::post('token_refresh', 'AuthController@refreshToken');
        Route::get('base_info', 'AuthController@baseInfo');
        Route::post('update_base_info', 'AuthController@updateBaseInfo');
        Route::post('reset_password', 'AuthController@resetPassword');
    });

    Route::prefix('dashboard')->group(function () {
        Route::get('sales_data', 'DashboardController@salesData');
        Route::get('order_count_data', 'DashboardController@orderCountData');
        Route::get('user_count_data', 'DashboardController@userCountData');
        Route::get('promoter_count_data', 'DashboardController@promoterCountData');
        Route::get('top_goods_list', 'DashboardController@topGoodsList');
        Route::get('commission_data', 'DashboardController@commissionData');
        Route::get('todo_list', 'DashboardController@todoList');
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
        Route::get('options', 'UserController@options');
        Route::get('normal_options', 'UserController@normalOptions');
        Route::post('bind_superior', 'UserController@bindSuperior');
        Route::post('delete_superior', 'UserController@deleteSuperior');
    });

    Route::prefix('auth_info')->group(function () {
        Route::post('list', 'AuthInfoController@list');
        Route::get('detail', 'AuthInfoController@detail');
        Route::post('approved', 'AuthInfoController@approved');
        Route::post('reject', 'AuthInfoController@reject');
        Route::post('delete', 'AuthInfoController@delete');
        Route::get('pending_count', 'AuthInfoController@getPendingCount');
    });

    Route::prefix('enterprise_info')->group(function () {
        Route::post('list', 'EnterpriseInfoController@list');
        Route::get('detail', 'EnterpriseInfoController@detail');
        Route::post('approved', 'EnterpriseInfoController@approved');
        Route::post('reject', 'EnterpriseInfoController@reject');
        Route::post('delete', 'EnterpriseInfoController@delete');
        Route::get('pending_count', 'EnterpriseInfoController@getPendingCount');
    });

    Route::prefix('team')->group(function () {
        Route::prefix('promoter')->group(function () {
            Route::post('list', 'PromoterController@list');
            Route::get('detail', 'PromoterController@detail');
            Route::post('add', 'PromoterController@add');
            Route::post('change_level', 'PromoterController@changeLevel');
            Route::post('delete', 'PromoterController@delete');
            Route::get('options', 'PromoterController@options');
            Route::post('top_list', 'PromoterController@topPromoterList');
            Route::post('update_list', 'PromoterController@updateList');
        });

        Route::prefix('goods')->group(function () {
            Route::post('list', 'GiftGoodsController@list');
            Route::post('add', 'GiftGoodsController@add');
            Route::post('delete', 'GiftGoodsController@delete');
        });
    });

    Route::prefix('withdraw')->group(function () {
        Route::post('list', 'WithdrawalController@list');
        Route::get('detail', 'WithdrawalController@detail');
        Route::post('approved', 'WithdrawalController@approved');
        Route::post('reject', 'WithdrawalController@reject');
        Route::post('delete', 'WithdrawalController@delete');
        Route::get('pending_count', 'WithdrawalController@getPendingCount');
    });

    Route::prefix('mall')->group(function () {
        Route::prefix('banner')->group(function () {
            Route::post('list', 'BannerController@list');
            Route::get('detail', 'BannerController@detail');
            Route::post('add', 'BannerController@add');
            Route::post('edit', 'BannerController@edit');
            Route::post('edit_sort', 'BannerController@editSort');
            Route::post('up', 'BannerController@up');
            Route::post('down', 'BannerController@down');
            Route::post('delete', 'BannerController@delete');
        });

        Route::prefix('activity')->group(function () {
            Route::prefix('tag')->group(function () {
                Route::post('list', 'ActivityTagController@list');
                Route::get('detail', 'ActivityTagController@detail');
                Route::post('add', 'ActivityTagController@add');
                Route::post('edit', 'ActivityTagController@edit');
                Route::post('edit_sort', 'ActivityTagController@editSort');
                Route::post('edit_status', 'ActivityTagController@editStatus');
                Route::post('delete', 'ActivityTagController@delete');
                Route::get('options', 'ActivityTagController@options');
            });

            Route::post('list', 'ActivityController@list');
            Route::get('detail', 'ActivityController@detail');
            Route::post('add', 'ActivityController@add');
            Route::post('edit', 'ActivityController@edit');
            Route::post('edit_tag', 'ActivityController@editTag');
            Route::post('edit_goods_tag', 'ActivityController@editGoodsTag');
            Route::post('edit_followers', 'ActivityController@editFollowers');
            Route::post('edit_sales', 'ActivityController@editSales');
            Route::post('edit_sort', 'ActivityController@editSort');
            Route::post('up', 'ActivityController@up');
            Route::post('down', 'ActivityController@down');
            Route::post('delete', 'ActivityController@delete');
        });

        Route::prefix('coupon')->group(function () {
            Route::post('list', 'CouponController@list');
            Route::get('detail', 'CouponController@detail');
            Route::post('add', 'CouponController@add');
            Route::post('edit', 'CouponController@edit');
            Route::post('edit_received_num', 'CouponController@editReceivedNum');
            Route::post('up', 'CouponController@up');
            Route::post('down', 'CouponController@down');
            Route::post('delete', 'CouponController@delete');
        });
    });

    Route::prefix('rural')->group(function () {
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

    Route::prefix('village')->group(function () {
        Route::prefix('grain_goods')->group(function () {
            Route::post('list', 'VillageGrainGoodsController@list');
            Route::post('add', 'VillageGrainGoodsController@add');
            Route::post('edit_sort', 'VillageGrainGoodsController@editSort');
            Route::post('delete', 'VillageGrainGoodsController@delete');
        });

        Route::prefix('fresh_goods')->group(function () {
            Route::post('list', 'VillageFreshGoodsController@list');
            Route::post('add', 'VillageFreshGoodsController@add');
            Route::post('edit_sort', 'VillageFreshGoodsController@editSort');
            Route::post('delete', 'VillageFreshGoodsController@delete');
        });

        Route::prefix('snack_goods')->group(function () {
            Route::post('list', 'VillageSnackGoodsController@list');
            Route::post('add', 'VillageSnackGoodsController@add');
            Route::post('edit_sort', 'VillageSnackGoodsController@editSort');
            Route::post('delete', 'VillageSnackGoodsController@delete');
        });

        Route::prefix('gift_goods')->group(function () {
            Route::post('list', 'VillageGiftGoodsController@list');
            Route::post('add', 'VillageGiftGoodsController@add');
            Route::post('edit_sort', 'VillageGiftGoodsController@editSort');
            Route::post('delete', 'VillageGiftGoodsController@delete');
        });
    });

    Route::prefix('integrity')->group(function () {
        Route::prefix('goods')->group(function () {
            Route::post('list', 'IntegrityGoodsController@list');
            Route::post('add', 'IntegrityGoodsController@add');
            Route::post('edit_sort', 'IntegrityGoodsController@editSort');
            Route::post('delete', 'IntegrityGoodsController@delete');
        });
    });

    Route::prefix('new_year')->group(function () {
        Route::prefix('goods')->group(function () {
            Route::post('list', 'NewYearGoodsController@list');
            Route::post('add', 'NewYearGoodsController@add');
            Route::post('edit_sort', 'NewYearGoodsController@editSort');
            Route::post('delete', 'NewYearGoodsController@delete');
        });

        Route::prefix('culture_goods')->group(function () {
            Route::post('list', 'NewYearCultureGoodsController@list');
            Route::post('add', 'NewYearCultureGoodsController@add');
            Route::post('edit_sort', 'NewYearCultureGoodsController@editSort');
            Route::post('delete', 'NewYearCultureGoodsController@delete');
        });

        Route::prefix('region')->group(function () {
            Route::post('list', 'NewYearLocalRegionController@list');
            Route::get('detail', 'NewYearLocalRegionController@detail');
            Route::post('add', 'NewYearLocalRegionController@add');
            Route::post('edit', 'NewYearLocalRegionController@edit');
            Route::post('edit_sort', 'NewYearLocalRegionController@editSort');
            Route::post('edit_status', 'NewYearLocalRegionController@editStatus');
            Route::post('delete', 'NewYearLocalRegionController@delete');
            Route::get('options', 'NewYearLocalRegionController@options');
        });

        Route::prefix('local_goods')->group(function () {
            Route::post('list', 'NewYearLocalGoodsController@list');
            Route::post('add', 'NewYearLocalGoodsController@add');
            Route::post('edit_sort', 'NewYearLocalGoodsController@editSort');
            Route::post('delete', 'NewYearLocalGoodsController@delete');
        });
    });

    Route::prefix('limited_time_recruit')->group(function () {
        Route::prefix('category')->group(function () {
            Route::post('list', 'LimitedTimeRecruitCategoryController@list');
            Route::get('detail', 'LimitedTimeRecruitCategoryController@detail');
            Route::post('add', 'LimitedTimeRecruitCategoryController@add');
            Route::post('edit', 'LimitedTimeRecruitCategoryController@edit');
            Route::post('edit_sort', 'LimitedTimeRecruitCategoryController@editSort');
            Route::post('edit_status', 'LimitedTimeRecruitCategoryController@editStatus');
            Route::post('delete', 'LimitedTimeRecruitCategoryController@delete');
            Route::get('options', 'LimitedTimeRecruitCategoryController@options');
        });

        Route::prefix('goods')->group(function () {
            Route::post('list', 'LimitedTimeRecruitGoodsController@list');
            Route::post('add', 'LimitedTimeRecruitGoodsController@add');
            Route::post('edit_sort', 'LimitedTimeRecruitGoodsController@editSort');
            Route::post('delete', 'LimitedTimeRecruitGoodsController@delete');
        });
    });

    Route::prefix('merchant')->group(function () {
        Route::post('list', 'MerchantController@list');
        Route::get('detail', 'MerchantController@detail');
        Route::post('add', 'MerchantController@add');
        Route::post('edit', 'MerchantController@edit');
        Route::post('delete', 'MerchantController@delete');
        Route::get('options', 'MerchantController@options');
        Route::post('init_refund_address', 'MerchantController@initRefundAddress');

        Route::prefix('refund_address')->group(function () {
            Route::post('list', 'MerchantRefundAddressController@list');
            Route::get('detail', 'MerchantRefundAddressController@detail');
            Route::post('add', 'MerchantRefundAddressController@add');
            Route::post('edit', 'MerchantRefundAddressController@edit');
            Route::post('delete', 'MerchantRefundAddressController@delete');
            Route::get('options', 'MerchantRefundAddressController@options');
        });

        Route::prefix('pickup_address')->group(function () {
            Route::post('list', 'MerchantPickupAddressController@list');
            Route::get('detail', 'MerchantPickupAddressController@detail');
            Route::post('add', 'MerchantPickupAddressController@add');
            Route::post('edit', 'MerchantPickupAddressController@edit');
            Route::post('delete', 'MerchantPickupAddressController@delete');
            Route::get('options', 'MerchantPickupAddressController@options');
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

    Route::prefix('freight_template')->group(function () {
        Route::post('list', 'FreightTemplateController@list');
        Route::get('detail', 'FreightTemplateController@detail');
        Route::post('add', 'FreightTemplateController@add');
        Route::post('edit', 'FreightTemplateController@edit');
        Route::post('delete', 'FreightTemplateController@delete');
        Route::get('options', 'FreightTemplateController@options');
    });

    Route::prefix('category')->group(function () {
        Route::post('list', 'CategoryController@list');
        Route::get('detail', 'CategoryController@detail');
        Route::post('add', 'CategoryController@add');
        Route::post('edit', 'CategoryController@edit');
        Route::post('edit_sort', 'CategoryController@editSort');
        Route::post('edit_status', 'CategoryController@editStatus');
        Route::post('delete', 'CategoryController@delete');
        Route::get('options', 'CategoryController@options');
    });

    Route::prefix('goods')->group(function () {
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
        Route::post('edit_sales', 'GoodsController@editSales');
        Route::get('options', 'GoodsController@options');
    });

    Route::prefix('order')->group(function () {
        Route::get('ship_order_count', 'OrderController@shipOrderCount');
        Route::get('goods_options', 'OrderController@orderedGoodsOptions');
        Route::get('user_options', 'OrderController@orderedUserOptions');
        Route::post('list', 'OrderController@list');
        Route::get('detail', 'OrderController@detail');
        Route::post('modify_address_info', 'OrderController@modifyAddressInfo');
        Route::post('modify_delivery_info', 'OrderController@modifyDeliveryInfo');
        Route::post('delivery', 'OrderController@delivery');
        Route::get('shipping_info', 'OrderController@shippingInfo');
        Route::post('confirm', 'OrderController@confirm');
        Route::post('cancel', 'OrderController@cancel');
        Route::post('refund', 'OrderController@refund');
        Route::post('delete', 'OrderController@delete');
        Route::post('export', 'OrderController@export');
        Route::post('import', 'OrderController@import');
        Route::post('update_order_goods_status', 'OrderController@updateOrderGoodsStatus');
    });

    Route::prefix('refund')->group(function () {
        Route::get('waiting_count', 'RefundController@waitingRefundCount');
        Route::post('list', 'RefundController@list');
        Route::get('detail', 'RefundController@detail');
        Route::post('approved', 'RefundController@approved');
        Route::get('shipping_info', 'RefundController@shippingInfo');
        Route::post('reject', 'RefundController@reject');
        Route::post('delete', 'RefundController@delete');
    });

    Route::prefix('live')->group(function () {
        Route::post('list', 'LiveRoomController@list');
        Route::post('delete', 'LiveRoomController@delete');

        Route::prefix('user')->group(function () {
            Route::post('list', 'LiveUserController@list');
            Route::post('add', 'LiveUserController@add');
            Route::post('delete', 'LiveUserController@delete');
        });
    });
});
