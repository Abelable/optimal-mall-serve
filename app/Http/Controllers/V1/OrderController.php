<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\CartGoods;
use App\Models\Coupon;
use App\Models\FreightTemplate;
use App\Models\Order;
use App\Services\AddressService;
use App\Services\CartGoodsService;
use App\Services\CouponService;
use App\Services\FreightTemplateService;
use App\Services\OrderGoodsService;
use App\Services\OrderService;
use App\Services\UserCouponService;
use App\Utils\CodeResponse;
use App\Utils\Enums\OrderEnums;
use App\Utils\Inputs\CreateOrderInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yansongda\LaravelPay\Facades\Pay;

class OrderController extends Controller
{
    public function preOrderInfo()
    {
        $addressId = $this->verifyId('addressId');
        $cartGoodsIds = $this->verifyArrayNotEmpty('cartGoodsIds');
        $couponId = $this->verifyId('couponId');

        $addressColumns = ['id', 'name', 'mobile', 'region_code_list', 'region_desc', 'address_detail'];
        if (is_null($addressId)) {
            /** @var Address $address */
            $address = AddressService::getInstance()->getDefaultAddress($this->userId(), $addressColumns);
        } else {
            /** @var Address $address */
            $address = AddressService::getInstance()->getById($this->userId(), $addressId, $addressColumns);
        }

        $cartGoodsListColumns = ['cover', 'name', 'freight_template_id', 'selected_sku_name', 'price', 'number'];
        $cartGoodsList = CartGoodsService::getInstance()->getCartGoodsListByIds($this->userId(), $cartGoodsIds, $cartGoodsListColumns);

        $freightTemplateIds = $cartGoodsList->pluck('freight_template_id')->toArray();
        $freightTemplateList = FreightTemplateService::getInstance()
            ->getListByIds($freightTemplateIds)
            ->map(function (FreightTemplate $freightTemplate) {
                $freightTemplate->area_list = json_decode($freightTemplate->area_list);
                return $freightTemplate;
            })->keyBy('id');

        $errMsg = '';
        $totalFreightPrice = 0;
        $couponDenomination = 0;
        $totalPrice = 0;
        $totalNumber = 0;

        // 优惠券逻辑
        $couponList = $this->getCouponList($cartGoodsIds, $cartGoodsList);
        if (is_null($couponId)) {
            $couponDenomination = $couponList->first()->denomination ?: 0;
        } else {
            $couponDenomination = $couponList->get($couponId)->denomination ?: 0;
        }

        foreach ($cartGoodsList as $cartGoods) {
            $price = bcmul($cartGoods->price, $cartGoods->number, 2);
            $totalPrice = bcadd($totalPrice, $price, 2);
            $totalNumber = $totalNumber + $cartGoods->number;

            // 计算运费
            if (is_null($address) || $cartGoods->freight_template_id == 0) {
                $freightPrice = 0;
            } else {
                /** @var FreightTemplate $freightTemplate */
                $freightTemplate = $freightTemplateList->get($cartGoods->freight_template_id);
                if ($freightTemplate->free_quota != 0 && $price > $freightTemplate->free_quota) {
                    $freightPrice = 0;
                } else {
                    $cityCode = substr(json_decode($address->region_code_list)[1], 0, 4);
                    $area = collect($freightTemplate->area_list)->first(function ($area) use ($cityCode) {
                        return in_array($cityCode, explode(',', $area->pickedCityCodes));
                    });
                    if (is_null($area)) {
                        $errMsg = '商品"' . $cartGoods->name . '"暂不支持配送至当前地址，请更换收货地址';
                        $freightPrice = 0;
                    } else {
                        if ($freightTemplate->compute_mode == 1) {
                            $freightPrice = $area->fee;
                        } else {
                            $freightPrice = bcmul($area->fee, $cartGoods->number, 2);
                        }
                    }
                }
            }
            $totalFreightPrice = bcadd($totalFreightPrice, $freightPrice, 2);
        }

        $paymentAmount = bcadd($totalPrice, $totalFreightPrice, 2);
        $paymentAmount = bcsub($paymentAmount, $couponDenomination, 2);

        return $this->success([
            'errMsg' => $errMsg,
            'addressInfo' => $address,
            'goodsList' => $cartGoodsList,
            'freightPrice' => $totalFreightPrice,
            'couponDenomination' => $couponDenomination,
            'totalPrice' => $totalPrice,
            'totalNumber' => $totalNumber,
            'paymentAmount' => $paymentAmount
        ]);
    }

    private function getCouponList(array $goodsIds, $cartGoodsList)
    {
        $userCouponList = UserCouponService::getInstance()->getUserCouponList($this->userId());
        $couponIds = $userCouponList->pluck('coupon_id')->toArray();
        $couponList = CouponService::getInstance()->getUserCouponListByGoodsIds($couponIds, $goodsIds)->keyBy('goods_id');
        $suitableCouponList = $cartGoodsList->map(function (CartGoods $cartGoods) use ($couponList) {
            /** @var Coupon $coupon */
            $coupon = $couponList->get($cartGoods->goods_id);
            if (!is_null($coupon)) {
                switch ($coupon->type) {
                    case 1:
                        return $coupon;
                    case 2:
                        if ($cartGoods->number >= $coupon->num_limit) {
                            return $coupon;
                        } else {
                            return null;
                        }
                    case 3:
                        if (bcmul($cartGoods->price, $cartGoods->number, 2) >= $coupon->price_limit) {
                            return $coupon;
                        } else {
                            return null;
                        }
                }
            }
            return null;
        })->filter()->sortBy('denomination')->keyBy('id');;
        return $suitableCouponList;
    }

    public function submit()
    {
        /** @var CreateOrderInput $input */
        $input = CreateOrderInput::new();

        // 分布式锁，防止重复请求
        $lockKey = sprintf('create_order_%s_%s', $this->userId(), md5(serialize($input)));
        $lock = Cache::lock($lockKey, 5);
        if (!$lock->get()) {
            $this->fail(CodeResponse::FAIL, '请勿重复提交订单');
        }

        $orderId = DB::transaction(function () use ($input) {
            // 1.获取地址
            $address = AddressService::getInstance()->getById($this->userId(), $input->addressId);
            if (is_null($address)) {
                return $this->fail(CodeResponse::NOT_FOUND, '用户地址不存在');
            }

            // 2.获取购物车商品
            $cartGoodsList = CartGoodsService::getInstance()->getCartGoodsListByIds($this->userId(), $input->cartGoodsIds);

            // 3.获取运费模板列表
            $freightTemplateIds = $cartGoodsList->pluck('freight_template_id')->toArray();
            $freightTemplateList = FreightTemplateService::getInstance()
                ->getListByIds($freightTemplateIds)
                ->map(function (FreightTemplate $freightTemplate) {
                    $freightTemplate->area_list = json_decode($freightTemplate->area_list);
                    return $freightTemplate;
                })->keyBy('id');

            // 4.生成订单
            $orderId = OrderService::getInstance()->createOrder($this->userId(), $cartGoodsList, $freightTemplateList, $address);

            // 5.生成订单商品快照
            OrderGoodsService::getInstance()->createList($cartGoodsList, $orderId);

            // 6.清空购物车
            CartGoodsService::getInstance()->deleteCartGoodsList($this->userId(), $input->cartGoodsIds);

            return $orderId;
        });

        return $this->success($orderId);
    }

    public function payParams()
    {
        $orderId = $this->verifyRequiredId('orderId');
        $order = OrderService::getInstance()->createWxPayOrder($this->userId(), $orderId, $this->user()->openid);
        $payParams = Pay::wechat()->miniapp($order);
        return $this->success($payParams);
    }

    public function orderListTotals()
    {
        return $this->success([
            OrderService::getInstance()->getListTotal($this->userId(), $this->statusList(1)),
            OrderService::getInstance()->getListTotal($this->userId(), $this->statusList(2)),
            OrderService::getInstance()->getListTotal($this->userId(), $this->statusList(3)),
            OrderService::getInstance()->getListTotal($this->userId(), $this->statusList([OrderEnums::STATUS_REFUND])),
        ]);
    }

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $status = $this->verifyRequiredInteger('status');

        $statusList = $this->statusList($status);
        $page = OrderService::getInstance()->getOrderListByStatus($this->userId(), $statusList, $input);
        $list = $this->orderList($page);

        return $this->success($this->paginate($page, $list));
    }

    private function statusList($status) {
        switch ($status) {
            case 1:
                $statusList = [OrderEnums::STATUS_CREATE];
                break;
            case 2:
                $statusList = [OrderEnums::STATUS_PAY];
                break;
            case 3:
                $statusList = [OrderEnums::STATUS_CONFIRM, OrderEnums::STATUS_AUTO_CONFIRM];
                break;
            case 4:
                $statusList = [OrderEnums::STATUS_FINISHED];
                break;
            case 5:
                $statusList = [OrderEnums::STATUS_REFUND, OrderEnums::STATUS_REFUND_CONFIRM];
                break;
            default:
                $statusList = [];
                break;
        }

        return $statusList;
    }

    private function orderList($page)
    {
        $orderList = collect($page->items());
        $orderIds = $orderList->pluck('id')->toArray();
        $goodsListColumns = ['order_id', 'goods_id', 'cover', 'name', 'selected_sku_name', 'price', 'number'];
        $groupedGoodsList = OrderGoodsService::getInstance()->getListByOrderIds($orderIds, $goodsListColumns)->groupBy('order_id');
        return $orderList->map(function (Order $order) use ($groupedGoodsList) {
            $goodsList = $groupedGoodsList->get($order->id);
            return [
                'id' => $order->id,
                'status' => $order->status,
                'statusDesc' => OrderEnums::STATUS_TEXT_MAP[$order->status],
                'goodsList' => $goodsList,
                'paymentAmount' => $order->payment_amount,
                'consignee' => $order->consignee,
                'mobile' => $order->mobile,
                'address' => $order->address,
                'orderSn' => $order->order_sn,
                'createdAt' => $order->created_at
            ];
        });
    }

    public function cancel()
    {
        $id = $this->verifyRequiredId('id');
        OrderService::getInstance()->userCancel($this->userId(), $id);
        return $this->success();
    }

    public function confirm()
    {
        $id = $this->verifyRequiredId('id');
        OrderService::getInstance()->confirm($this->userId(), $id);
        return $this->success();
    }

    public function delete()
    {
        $ids = $this->verifyArrayNotEmpty('ids', []);
        $orderList = OrderService::getInstance()->getUserOrderList($this->userId(), $ids);
        if (count($orderList) == 0) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '订单不存在');
        }
        DB::transaction(function () use ($orderList) {
            OrderService::getInstance()->delete($orderList);
        });
        return $this->success();
    }

    public function refund()
    {
        $id = $this->verifyRequiredId('id');
        OrderService::getInstance()->refund($this->userId(), $id);
        return $this->success();
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $columns = [
            'id',
            'order_sn',
            'status',
            'remarks',
            'consignee',
            'mobile',
            'address',
            'goods_price',
            'freight_price',
            'payment_amount',
            'pay_time',
            'ship_time',
            'confirm_time',
            'created_at',
            'updated_at',
        ];
        $order = OrderService::getInstance()->getUserOrderById($this->userId(), $id, $columns);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }
        $goodsList = OrderGoodsService::getInstance()->getListByOrderId($order->id);
        $order['goods_list'] = $goodsList;
        return $this->success($order);
    }
}
