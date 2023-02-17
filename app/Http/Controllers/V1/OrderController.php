<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Shop;
use App\Services\AddressService;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\ShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\CreateOrderInput;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yansongda\LaravelPay\Facades\Pay;

class OrderController extends Controller
{
    public function preOrderInfo()
    {
        $addressId = $this->verifyId('addressId');
        $cartIds = json_decode($this->verifyRequiredString('cartIds'));

        $addressColumns = ['name', 'mobile', 'region_code_list', 'region_desc', 'address_detail'];
        if (is_null($addressId)) {
            $address = AddressService::getInstance()->getDefautlAddress($this->userId(), $addressColumns);
        } else {
            $address = AddressService::getInstance()->getById($this->userId(), $addressId, $addressColumns);
        }

        $cartListColumns = ['shop_id', 'goods_image', 'goods_name', 'selected_sku_name', 'price', 'number'];
        $cartList = CartService::getInstance()->getCartListByIds($this->userId(), $cartIds, $cartListColumns);

        $freightPrice = 0;
        $totalPrice = 0;
        $totalNumber = 0;
        foreach ($cartList as $cart) {
            $price = bcmul($cart->price, $cart->number, 2);
            $totalPrice = bcadd($totalPrice, $price, 2);
            $totalNumber = $totalNumber + $cart->number;
            // todo 计算运费
        }
        $paymentAmount = bcadd($totalPrice, $freightPrice, 2);

        $shopIds = array_unique($cartList->pluck('shop_id')->toArray());
        $shopList = ShopService::getInstance()->getShopListByIds($shopIds, ['id', 'avatar', 'name']);
        $goodsLists = $shopList->map(function (Shop $shop) use ($cartList) {
            return [
                'shopInfo' => $shop,
                'goodsList' => $cartList->filter(function (Cart $cart) use ($shop) {
                    return $cart->shop_id == $shop->id;
                })->map(function (Cart $cart) {
                    unset($cart->shop_id);
                    return $cart;
                })
            ];
        });
        if (in_array(0, $shopIds)) {
            $goodsLists->prepend([
                'goodsList' => $cartList->filter(function (Cart $cart) {
                    return $cart->shop_id == 0;
                })->map(function (Cart $cart) {
                    unset($cart->shop_id);
                    return $cart;
                })
            ]);
        }

        return $this->success([
            'addressInfo' => $address,
            'goodsLists' => $goodsLists,
            'freightPrice' => $freightPrice,
            'totalPrice' => $totalPrice,
            'totalNumber' => $totalNumber,
            'paymentAmount' => $paymentAmount
        ]);
    }

    public function submit()
    {
        /** @var CreateOrderInput $input */
        $input = CreateOrderInput::new();

        // 分布式锁，防止重复请求
        $lockKey = sprintf('create_order_%s_%s', $this->userId(), md5(serialize($input)));
        $lock = Cache::lock($lockKey, 5);
        if (!$lock->get()) {
            $this->fail(CodeResponse::FAIL, '请勿重复请求');
        }

        $orderIds = DB::transaction(function () use ($input) {
            // 1.获取地址
            $address = AddressService::getInstance()->getById($this->userId(), $input->addressId);
            if (is_null($address)) {
                return $this->fail(CodeResponse::NOT_FOUND, '用户地址不存在');
            }

            // 2.获取购物车商品
            $cartList = CartService::getInstance()->getCartListByIds($this->userId(), $input->cartIds);

            // 3.按商家进行订单拆分，生成对应订单
            $shopIds = array_unique($cartList->pluck('shop_id')->toArray());
            $shopList = ShopService::getInstance()->getShopListByIds($shopIds);

            $orderIds = $shopList->map(function (Shop $shop) use ($address, $cartList) {
                $filterCartList = $cartList->filter(function (Cart $cart) use ($shop) {
                    return $cart->shop_id == $shop->id;
                });
                return OrderService::getInstance()->createOrder($this->userId(), $filterCartList, $address, $shop);
            });
            if (in_array(0, $shopIds)) {
                $filterCartList = $cartList->filter(function (Cart $cart) {
                    return $cart->shop_id == 0;
                });
                $orderId = OrderService::getInstance()->createOrder($this->userId(), $filterCartList, $address);
                $orderIds->push($orderId);
            }

            // 4.清空购物车
            CartService::getInstance()->deleteCartList($this->userId(), $input->cartIds);

            return $orderIds;
        });

        return $this->success($orderIds);
    }

    public function payParams()
    {
        $orderIds = $this->verifyArrayNotEmpty('orderIds');
        $order = OrderService::getInstance()->createWxPayOrder($this->userId(), $orderIds, $this->user()->openid);
        $payParams = Pay::wechat()->miniapp($order);
        return $this->success($payParams);
    }
}
