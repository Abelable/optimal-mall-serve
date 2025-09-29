<?php

namespace App\Utils;

use App\Models\Activity;
use App\Models\LiveRoom;
use App\Models\Order;
use App\Models\OrderPackage;
use App\Utils\Traits\HttpClient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WxMpServe
{
    use HttpClient;

    const ACCESS_TOKEN_KEY = 'wx_mp_access_token';
    const GET_ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
    const STABLE_ACCESS_TOKEN_KEY = 'wx_mp_stable_access_token';
    const GET_STABLE_ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/cgi-bin/stable_token';
    const GET_PHONE_NUMBER_URL = 'https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token=%s';
    const GET_OPENID_URL = 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code';
    const GET_QRCODE_URL = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=%s';
    const SEND_MSG_URL = 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=%s';
    const UPLOAD_SHIPPING_INFO_URL = 'https://api.weixin.qq.com/wxa/sec/order/upload_shipping_info?access_token=%s';
    const TRACE_WAYBILL_URL = 'https://api.weixin.qq.com/cgi-bin/express/delivery/open_msg/trace_waybill?access_token=%s';

    // ———————————————————————————————————————————————————— 带货机构 ————————————————————————————————————————————————————
    // 机构 - 订阅商品列表
    const SUBSCRIBE_PRODUCT_URL = 'https://api.weixin.qq.com/channels/ec/league/headsupplier/subscription/getsubscribe?access_token=%s';
    // 机构 - 商品基础详情
    const PRODUCT_BASE_INFO_URL = 'https://api.weixin.qq.com/channels/ec/league/headsupplier/productdetail/get?access_token=%s';
    // 机构 - 商品推广参数详情
    const PRODUCT_PROMOTION_INFO_URL = 'https://api.weixin.qq.com/channels/ec/league/headsupplier/item/promotiondetail/get?access_token=%s';
    // 机构 - 合作小店列表
    const SHOP_LIST_URL = 'https://api.weixin.qq.com/channels/ec/league/headsupplier/shop/list/get?access_token=%s';
    // 机构 - 合作小店详情
    const SHOP_DETAIL_URL = 'https://api.weixin.qq.com/channels/ec/league/headsupplier/shop/get?access_token=%s';
    // 机构 - 佣金订单列表
    const COMMISSION_ORDER_LIST_URL = 'https://api.weixin.qq.com/channels/ec/league/headsupplier/order/list/get?access_token=%s';
    // 机构 - 佣金订单详情
    const COMMISSION_ORDER_DETAIL_URL = 'https://api.weixin.qq.com/channels/ec/league/headsupplier/order/get?access_token=%s';
    // 机构 - 内容推广 - 订单列表
    const ORDER_LIST_URL = 'https://api.weixin.qq.com/channels/ec/league/headsupplier/clue/list/get?access_token=%s';
    // 机构 - 内容推广 - 订单详情
    const ORDER_DETAIL_URL = 'https://api.weixin.qq.com/channels/ec/league/headsupplier/clue/get?access_token=%s';

    // 机构 - 达人橱窗商品列表
    const STAR_WINDOW_PRODUCT_LIST_URL = 'https://api.weixin.qq.com/channels/ec/league/headsupplier/window/getall?access_token=%s';
    // 机构 - 达人橱窗商品详情
    const STAR_WINDOW_PRODUCT_DETAIL_URL = 'https://api.weixin.qq.com/channels/ec/league/headsupplier/window/getdetail?access_token=%s';

    // 推客 - 状态
    const PROMOTER_STATUS_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_promoter_register_and_bind_status?access_token=%s';
    // 推客 - 合作小店列表
    const PROMOTER_SHOP_LIST_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_bind_shop_list?access_token=%s';
    // 推客 - 商品列表
    const PROMOTER_PRODUCT_LIST_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_promote_product_list?access_token=%s';
    // 推客 - 商品详情
    const PROMOTER_PRODUCT_DETAIL_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_promote_product_detail?access_token=%s';
    // 推客 - 商品内嵌卡片
    const PRODUCT_PRODUCT_LINK_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_promoter_single_product_promotion_info?access_token=%s';
    // 推客 - 商品推广分佣比例
    const PRODUCT_COMMISSION_INFO_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_sharer_product_commission_info?access_token=%s';
    // 推客 - 商品推广链接
    const PRODUCT_SHARE_LINK_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_product_promotion_link_info?access_token=%s';
    // 推客 - 商品推广二维码
    const PRODUCT_SHARE_QRCODE_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_product_promotion_qrcode_info?access_token=%s';

    // ———————————————————————————————————————————————————— 直播推广 ————————————————————————————————————————————————————
    // 机构 - 直播提报二维码
    const LIVE_PROTECTION_QRCODE_URL = 'https://api.weixin.qq.com/channels/ec/league/headsupplier/liveprotection/getqrcode?access_token=%s';

    // 机构 - 带货者 - 直播预告列表
    const LIVE_NOTICE_LIST_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_live_notice_record_list?access_token=%s';
    // 机构 - 带货者 - 直播列表
    const LIVE_LIST_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_live_record_list?access_token=%s';
    // 机构 - 带货者 - 直播间商品列表
    const LIVE_PRODUCT_LIST_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_live_commission_product_list?access_token=%s';

    // 机构 - 小店 - 直播预告列表
    const SHOP_LIVE_NOTICE_LIST_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_shop_live_notice_record_list?access_token=%s';
    // 机构 - 小店 - 直播列表
    const SHOP_LIVE_LIST_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_shop_live_record_list?access_token=%s';
    // 机构 - 小店 - 直播间自营商品列表
    const SHOP_LIVE_PRODUCT_LIST_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_shop_live_commission_product_list?access_token=%s';

    // 推客 - 带货者 - 直播预告推广二维码
    const LIVE_NOTICE_SHARE_QRCODE_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_live_notice_record_qr_code?access_token=%s';
    // 推客 - 带货者 - 直播预告推广链接
    const LIVE_NOTICE_SHARE_LINK_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_live_notice_promoter_share_link?access_token=%s';
    // 推客 - 带货者 - 直播推广二维码
    const LIVE_SHARE_QRCODE_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_live_record_qr_code?access_token=%s';

    // 推客 - 小店 - 直播预告推广二维码
    const SHOP_LIVE_NOTICE_SHARE_QRCODE_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_shop_live_notice_record_qr_code?access_token=%s';
    // 推客 - 小店 - 直播预告推广链接
    const SHOP_LIVE_NOTICE_SHARE_LINK_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_shop_live_notice_promoter_share_link?access_token=%s';
    // 推客 - 小店 - 直播推广二维码
    const SHOP_LIVE_SHARE_QRCODE_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_shop_live_record_qr_code?access_token=%s';

    // 推客 - 直播预告分享 - 订阅人数
    const LIVE_NOTICE_RESERVATION_INFO_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_live_notice_reservation_info?access_token=%s';

    // ——————————————————————————————————————————————————— 短视频推广 ———————————————————————————————————————————————————
    // 机构 - 带货者 - 短视频列表
    const FEED_LIST_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_feed_list?access_token=%s';
    // 机构 - 小店 - 短视频列表
    const SHOP_FEED_LIST_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_shop_feed_list?access_token=%s';

    // 推客 - 带货者 - 短视频推广信息
    const FEED_PROMOTION_INFO_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_feed_promotion_info?access_token=%s';
    // 推客 - 小店 - 短视频推广信息
    const SHOP_FEED_PROMOTION_INFO_URL = 'https://api.weixin.qq.com/channels/ec/promoter/get_shop_feed_promotion_info?access_token=%s';


    protected $accessToken;
    protected $stableAccessToken;

    public static function new()
    {
        return new static();
    }

    public function __construct()
    {
        $this->accessToken = Cache::has(self::ACCESS_TOKEN_KEY) ? Cache::get(self::ACCESS_TOKEN_KEY) : $this->getAccessToken();
        $this->stableAccessToken = Cache::has(self::STABLE_ACCESS_TOKEN_KEY) ? Cache::get(self::STABLE_ACCESS_TOKEN_KEY) : $this->getStableAccessToken();
    }

    private function getAccessToken()
    {
        $result = $this->httpGet(sprintf(self::GET_ACCESS_TOKEN_URL, env('WX_MP_APPID'), env('WX_MP_SECRET')));
        if (!empty($result['errcode'])) {
            throw new \Exception('获取微信小程序access_token异常：' . $result['errcode'] . $result['errmsg']);
        }
        $accessToken = $result['access_token'];
        Cache::put(self::ACCESS_TOKEN_KEY, $accessToken, now()->addSeconds($result['expires_in'] - 300));
        return $accessToken;
    }

    private function getStableAccessToken()
    {
        $result = $this->httpPost(self::GET_STABLE_ACCESS_TOKEN_URL, ['grant_type' => 'client_credential', 'appid' => env('WX_MP_APPID'), 'secret' => env('WX_MP_SECRET')]);
        if (!empty($result['errcode'])) {
            throw new \Exception('获取微信小程序stable_access_token异常：' . $result['errcode'] . $result['errmsg']);
        }
        $stableAccessToken = $result['access_token'];
        Cache::put(self::STABLE_ACCESS_TOKEN_KEY, $stableAccessToken, now()->addSeconds($result['expires_in'] - 300));
        return $stableAccessToken;
    }

    public function getUserPhoneNumber($code)
    {
        $result = $this->httpPost(sprintf(self::GET_PHONE_NUMBER_URL, $this->accessToken), ['code' => $code]);
        if ($result['errcode'] != 0) {
            throw new \Exception('获取微信小程序用户手机号异常：' . $result['errcode'] . $result['errmsg']);
        }
        return $result['phone_info']['purePhoneNumber'];
    }

    public function getUserOpenid($code)
    {
        $result = $this->httpGet(sprintf(self::GET_OPENID_URL, env('WX_MP_APPID'), env('WX_MP_SECRET'), $code));
        if (!empty($result['errcode'])) {
            throw new \Exception('获取微信小程序openid异常：' . $result['errcode'] . $result['errmsg']);
        }
        return $result;
    }

    public function getQRCode($scene, $page)
    {
        return $this->httpPost(sprintf(self::GET_QRCODE_URL, $this->accessToken), ['scene' => $scene, 'page' => $page], 1, false);
    }

    public function sendActivityStartMsg($openid, Activity $activity)
    {
        $endTime = Carbon::parse($activity->end_time)->format('Y-m-d H:i:s');
        $data = [
            'thing7' => ['value' => $activity->name],
            'thing8' => ['value' => $activity->goods_name],
            'date5' => ['value' => $endTime]
        ];
        return $this->httpPost(
            sprintf(self::SEND_MSG_URL, $this->stableAccessToken),
            [
                'template_id' => env('WX_ACTIVITY_TEMPLATE_ID'),
                'page' => env('WX_ACTIVITY_PAGE') . $activity->goods_id,
                'touser' => $openid,
                'data' => $data
            ]
        );
    }

    public function sendLiveStartMsg($openid, LiveRoom $liveRoom)
    {
        $startTime = Carbon::parse($liveRoom->start_time)->format('Y-m-d H:i:s');
        $data = [
            'thing1' => ['value' => $liveRoom->title],
            'time2' => ['value' => $startTime],
            'thing3' => ['value' => $liveRoom->anchorInfo->nickname],
        ];
        return $this->httpPost(
            sprintf(self::SEND_MSG_URL, $this->stableAccessToken),
            [
                'template_id' => env('WX_LIVE_TEMPLATE_ID'),
                'page' => env('WX_LIVE_PAGE') . $liveRoom->id,
                'touser' => $openid,
                'data' => $data
            ]
        );
    }

    public function uploadShippingInfo($openid, Order $order, array $orderPackageList, $isAllDelivered)
    {
        $shippingList = [];
        /** @var OrderPackage $orderPackage */
        foreach ($orderPackageList as $orderPackage) {
            $shippingList[] = [
                'tracking_no' => $orderPackage->ship_sn,
                'express_company' => $orderPackage->ship_code,
                'item_desc' => $orderPackage->goodsList()->pluck('goods_name')->implode('，'),
                'contact' => [
                    'receiver_contact' => substr($order->mobile,0, 3) . '****' .substr($order->mobile,-4)
                ]
            ];
        }

        return $this->httpPost(
            sprintf(self::UPLOAD_SHIPPING_INFO_URL, $this->stableAccessToken),
            [
                'order_key' => [
                    'order_number_type' => 2,
                    'transaction_id' => $order->pay_id
                ],
                'logistics_type' => 1,
                'delivery_mode' => 2,
                'is_all_delivered' => $isAllDelivered,
                'shipping_list' => $shippingList,
                'upload_time' => Carbon::now()->format('Y-m-d\TH:i:s.uP'),
                'payer' => [
                    'openid' => $openid
                ]
            ],
            3
        );
    }

    public function verify($openid, $payId)
    {
        return $this->httpPost(
            sprintf(self::UPLOAD_SHIPPING_INFO_URL, $this->stableAccessToken),
            [
                'order_key' => [
                    'order_number_type' => 2,
                    'transaction_id' => $payId
                ],
                'logistics_type' => 4,
                'delivery_mode' => 1,
                'shipping_list' => [],
                'upload_time' => Carbon::now()->format('Y-m-d\TH:i:s.uP'),
                'payer' => [
                    'openid' => $openid
                ]
            ],
            3
        );
    }

    public function getWaybillToken($openid, $shipCode, $shipSn, $packageGoodsList, Order $order)
    {
        $goodsList = [];
        foreach ($packageGoodsList as $goods) {
            $goodsList[] = [
                'goods_img_url' => $goods->cover ?: '',
                'goods_name' => $goods->name,
            ];
        }
        $result = $this->httpPost(
            sprintf(self::TRACE_WAYBILL_URL, $this->stableAccessToken),
            [
                'openid' => $openid,
                'delivery_id' => $shipCode,
                'waybill_id' => $shipSn,
                'receiver_phone' => $order->mobile,
                'goods_info' => [
                    'detail_list' => $goodsList
                ],
                'trans_id' => $order->pay_id,
                'order_detail_path' => 'pages/mine/subpages/order-center/subpages/order-detail/index?id=' . $order->id,
            ],
            3
        );

        if ($result['errcode'] != 0) {
            throw new \Exception('获取微信小程序waybillToken异常：' . $result['errcode'] . $result['errmsg']);
        }

        return $result['waybill_token'];
    }

    public function getPromoterProductList($nextKey = '', $pageSize = 10, $planType = 2)
    {
        $result = $this->httpPost(
            sprintf(self::PROMOTER_PRODUCT_LIST_URL, $this->accessToken),
            [
                'shop_appid' => env('WX_SHOP_APPID'),
                'plan_type' => $planType,
                'page_size' => $pageSize,
                'next_key' => $nextKey,
            ],
        );

        if ($result['errcode'] != 0) {
            throw new \Exception('获取推客商品列表异常：' . $result['errcode'] . $result['errmsg']);
        }

        return $result;
    }

    public function getProductBaseInfo($productId)
    {
        $result = $this->httpPost(
            sprintf(self::PRODUCT_BASE_INFO_URL, $this->accessToken),
            [
                'shop_appid' => env('WX_SHOP_APPID'),
                'product_id' => $productId
            ],
        );

        if ($result['errcode'] != 0) {
            throw new \Exception('获取推客商品基础信息异常：' . $result['errcode'] . $result['errmsg']);
        }

        return $result['product'];
    }
}
