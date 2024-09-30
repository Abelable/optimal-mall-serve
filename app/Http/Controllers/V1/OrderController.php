<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\CartGoods;
use App\Models\Commission;
use App\Models\Coupon;
use App\Models\FreightTemplate;
use App\Models\GiftCommission;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Services\AddressService;
use App\Services\CartGoodsService;
use App\Services\CommissionService;
use App\Services\CouponService;
use App\Services\FreightTemplateService;
use App\Services\GiftCommissionService;
use App\Services\GiftGoodsService;
use App\Services\OrderGoodsService;
use App\Services\OrderService;
use App\Services\RelationService;
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

        $cartGoodsListColumns = ['goods_id', 'cover', 'name', 'freight_template_id', 'selected_sku_name', 'price', 'number'];
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
        $couponList = $this->getCouponList($cartGoodsList);
        if (count($couponList) != 0) {
            if (is_null($couponId)) {
                $couponDenomination = $couponList->first()->denomination;
            } else if ($couponId != 0) {
                $couponDenomination = $couponList->keyBy('id')->get($couponId)->denomination;
            }
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
            'couponList' => $couponList,
            'couponDenomination' => $couponDenomination,
            'totalPrice' => $totalPrice,
            'totalNumber' => $totalNumber,
            'paymentAmount' => $paymentAmount
        ]);
    }

    private function getCouponList($cartGoodsList)
    {
        $couponIds = UserCouponService::getInstance()->getUserCouponList($this->userId())->pluck('coupon_id')->toArray();
        $couponList = CouponService::getInstance()->getAvailableCouponListByIds($couponIds)->keyBy('goods_id');
        $giftGoodsIds = GiftGoodsService::getInstance()->getGoodsList([1, 2])->pluck('goods_id')->toArray();
        return $cartGoodsList->map(function (CartGoods $cartGoods) use ($giftGoodsIds, $couponList) {
            // 礼包商品不可使用优惠券
            if (in_array($cartGoods->goods_id, $giftGoodsIds)) {
                return null;
            }

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
        })->filter()->sortBy('denomination');
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

        $orderIds = DB::transaction(function () use ($input) {
            // 1.获取地址
            $address = AddressService::getInstance()->getById($this->userId(), $input->addressId);
            if (is_null($address)) {
                return $this->fail(CodeResponse::NOT_FOUND, '用户地址不存在');
            }

            // 2.获取优惠券
            $coupon = null;
            if (!is_null($input->couponId) && $input->couponId != 0) {
                $userCoupon = UserCouponService::getInstance()->getUserCoupon($this->userId(), $input->couponId);
                if (is_null($userCoupon)) {
                    return $this->fail(CodeResponse::NOT_FOUND, '优惠券无法使用');
                }
                $coupon = CouponService::getInstance()->getAvailableCouponById($input->couponId);
                if (is_null($coupon)) {
                    return $this->fail(CodeResponse::NOT_FOUND, '优惠券不存在');
                }
            }

            // 3.获取购物车商品
            $cartGoodsList = CartGoodsService::getInstance()->getCartGoodsListByIds($this->userId(), $input->cartGoodsIds);

            // 4.获取运费模板列表
            $freightTemplateIds = $cartGoodsList->pluck('freight_template_id')->toArray();
            $freightTemplateList = FreightTemplateService::getInstance()
                ->getListByIds($freightTemplateIds)
                ->map(function (FreightTemplate $freightTemplate) {
                    $freightTemplate->area_list = json_decode($freightTemplate->area_list);
                    return $freightTemplate;
                })->keyBy('id');

            // 5.按商家进行订单拆分，生成对应订单
            $merchantIds = collect(array_unique($cartGoodsList->pluck('merchant_id')->toArray()));

            $promoterInfo = $this->user()->promoterInfo;
            $superiorId = $this->user()->superiorId();
            $userId = $this->userId();

            $managerId = null;
            if (!is_null($superiorId)) {
                $managerId = RelationService::getInstance()->getSuperiorId($superiorId);
            }

            $orderIds = $merchantIds->map(function ($merchantId) use ($managerId, $superiorId, $promoterInfo, $userId, $coupon, $address, $cartGoodsList, $freightTemplateList) {
                $filterCartGoodsList = $cartGoodsList->groupBy('merchant_id')->get($merchantId);
                $orderId = OrderService::getInstance()->createOrder($userId, $merchantId, $filterCartGoodsList, $freightTemplateList, $address, $coupon);

                // 6.生成订单商品快照
                OrderGoodsService::getInstance()->createList($filterCartGoodsList, $orderId);

                /** @var CartGoods $cartGoods */
                foreach ($filterCartGoodsList as $cartGoods) {
                    if ($cartGoods->is_gift && is_null($promoterInfo)) {
                        // 7.礼包佣金逻辑（前提：礼包商品，普通用户）
                        // 场景1：普通用户没有上级 - 生成佣金记录，只作为记录用
                        // 场景2：普通用户上级为推广员，没有上上级，或上上级也为推广员 - 生成15%上级佣金的佣金记录
                        // 场景3：普通用户上级为推广员，上上级为C级 - 生成包含15%上级佣金、5%上上级佣金的佣金记录
                        // 场景4：普通用户上级为C级 - 生成包含20%上级佣金的佣金记录
                        GiftCommissionService::getInstance()->createCommission($userId, $orderId, $cartGoods, $superiorId, $managerId);
                    } else {
                        // 8.生成商品佣金记录（前提：非礼包商品）
                        // 场景1：普通用户且没有上级 - 不需要生成佣金记录
                        // 场景2：普通用户拥有上级 - 生成"分享场景"佣金记录
                        // 场景3：推官员 - 生成"自购场景"佣金记录
                        if (!is_null($promoterInfo) || !is_null($superiorId)) {
                            $scene = !is_null($promoterInfo) ? 1 : 2;
                            $superiorId = !is_null($promoterInfo) ? null : $superiorId;
                            CommissionService::getInstance()->createCommission($scene, $userId, $orderId, $cartGoods, $superiorId, $coupon);
                        }
                    }
                }

                return $orderId;
            });

            // 9.清空购物车
            CartGoodsService::getInstance()->deleteCartGoodsList($this->userId(), $input->cartGoodsIds);

            // 10.优惠券已使用
            if (!is_null($input->couponId)) {
                UserCouponService::getInstance()->useCoupon($this->userId(), $input->couponId);
            }

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

    public function orderListTotals()
    {
        return $this->success([
            OrderService::getInstance()->getListTotal($this->userId(), $this->statusList(1)),
            OrderService::getInstance()->getListTotal($this->userId(), $this->statusList(2)),
            OrderService::getInstance()->getListTotal($this->userId(), $this->statusList(3)),
            OrderService::getInstance()->getListTotal($this->userId(), $this->statusList(4)),
            OrderService::getInstance()->getListTotal($this->userId(), $this->statusList(5)),
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
                $statusList = [OrderEnums::STATUS_SHIP];
                break;
            case 4:
                $statusList = [OrderEnums::STATUS_CONFIRM, OrderEnums::STATUS_AUTO_CONFIRM, OrderEnums::STATUS_ADMIN_CONFIRM];
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
        $goodsListColumns = ['order_id', 'goods_id', 'merchant_id', 'is_gift', 'refund_status', 'cover', 'name', 'selected_sku_name', 'price', 'number'];
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
        OrderService::getInstance()->userConfirm($this->userId(), $id);
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
            'user_id',
            'consignee',
            'mobile',
            'address',
            'goods_price',
            'freight_price',
            'coupon_id',
            'coupon_denomination',
            'payment_amount',
            'pay_time',
            'ship_time',
            'confirm_time',
            'created_at',
            'updated_at',
        ];
        $order = OrderService::getInstance()->getOrderById($id, $columns);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }
        $goodsList = OrderGoodsService::getInstance()->getListByOrderId($order->id);
        $order['goods_list'] = $goodsList;
        return $this->success($order);
    }

    public function commissionOrderList()
    {
        $scene = $this->verifyRequiredInteger('scene');
        $timeType = $this->verifyRequiredInteger('timeType');
        /** @var PageInput $input */
        $input = PageInput::new();

        $commissionList = CommissionService::getInstance()->getUserCommissionListByTimeType($this->userId(), $timeType, $scene);
        $groupCommissionList = $commissionList->groupBy('order_id');
        $keyCommissionList = $commissionList->keyBy('goods_id');
        $orderIds = $commissionList->pluck('order_id')->toArray();

        $goodsIds = $commissionList->pluck('goods_id')->toArray();
        $goodsColumns = ['order_id', 'goods_id', 'cover', 'name', 'selected_sku_name', 'price', 'number'];
        $groupGoodsList = OrderGoodsService::getInstance()->getListByGoodsIds($goodsIds, $goodsColumns)->groupBy('order_id');

        $page = OrderService::getInstance()->getOrderPageByIds($orderIds, $input);
        $list = collect($page->items())->map(function (Order $order) use ($groupGoodsList, $keyCommissionList, $groupCommissionList) {
            $orderCommissionList = $groupCommissionList->get($order->id);
            $commissionBaseSum = $orderCommissionList->sum('commission_base');
            $commissionAmountSum = $orderCommissionList->sum('commission_amount');
            /** @var Commission $firstCommission */
            $firstCommission = $orderCommissionList->first();

            $orderGoodsList = $groupGoodsList->get($order->id);
            $orderGoodsList->map(function (OrderGoods $goods) use ($keyCommissionList) {
                /** @var Commission $commission */
                $commission = $keyCommissionList->get($goods->goods_id);
                $goods['commission'] = $commission->commission_amount;
                unset($goods->order_id);
                return $goods;
            });

            return [
                'id' => $order->id,
                'orderSn' => $order->order_sn,
                'status' => $firstCommission->status,
                'createdAt' => $order->created_at,
                'commissionBase' => $commissionBaseSum,
                'commissionAmount' => $commissionAmountSum,
                'scene' => $firstCommission->scene,
                'goodsList' => $orderGoodsList
            ];
        });

        return $this->success($this->paginate($page, $list));
    }

    public function teamCommissionOrderList()
    {
        $timeType = $this->verifyRequiredInteger('timeType');
        /** @var PageInput $input */
        $input = PageInput::new();

        $commissionList = CommissionService::getInstance()->getUserCommissionListByTimeType($this->userId(), $timeType);
        $orderIds = $commissionList->pluck('order_id')->toArray();

        $goodsIds = $commissionList->pluck('goods_id')->toArray();
        $goodsColumns = ['order_id', 'goods_id', 'cover', 'name', 'selected_sku_name', 'price', 'number'];
        $goodsList = OrderGoodsService::getInstance()->getListByGoodsIds($goodsIds, $goodsColumns)->groupBy('order_id');

        $page = OrderService::getInstance()->getOrderPageByIds($orderIds, $input);
        $list = collect($page->items())->map(function (Order $order) use ($commissionList, $goodsList) {
            $commissionList = $commissionList->groupBy('order_id')->get($order->id);

            $commissionSum = 0;
            if (!is_null($this->user()->promoterInfo)) {
                $GMV = $commissionList->sum('commission_base');
                switch ($this->user()->promoterInfo->level) {
                    case 1:
                        $commissionSum = bcmul($GMV, 0.01, 2);
                        break;
                    case 2:
                        $commissionSum = bcmul($GMV, 0.02, 2);
                        break;
                    case 3:
                        $commissionSum = bcmul($GMV, 0.03, 2);
                        break;
                }
            }

            /** @var Commission $firstCommission */
            $firstCommission = $commissionList->first();

            $goodsList = $goodsList->get($order->id);
            $goodsList->map(function (OrderGoods $goods) use ($commissionList) {
                /** @var Commission $commission */
                $commission = $commissionList->keyBy('goods_id')->get($goods->id);
                $goods['commission'] = $commission->commission_amount;
                unset($goods->order_id);
                return $goods;
            });

            return [
                'id' => $order->id,
                'orderSn' => $order->order_sn,
                'status' => $firstCommission->status,
                'createdAt' => $order->created_at,
                'commission' => $commissionSum,
                'goodsList' => $goodsList
            ];
        });

        return $this->success($this->paginate($page, $list));
    }

    public function giftCommissionOrderList()
    {
        $timeType = $this->verifyRequiredInteger('timeType');
        /** @var PageInput $input */
        $input = PageInput::new();

        $commissionList = GiftCommissionService::getInstance()->getListByTimeType($this->userId(), $timeType);
        $orderIds = $commissionList->pluck('order_id')->toArray();

        $goodsIds = $commissionList->pluck('goods_id')->toArray();
        $goodsColumns = ['order_id', 'goods_id', 'cover', 'name', 'selected_sku_name', 'price', 'number'];
        $goodsList = OrderGoodsService::getInstance()->getListByGoodsIds($goodsIds, $goodsColumns)->keyBy('order_id');

        $page = OrderService::getInstance()->getOrderPageByIds($orderIds, $input);
        $list = collect($page->items())->map(function (Order $order) use ($commissionList, $goodsList) {
            /** @var GiftCommission $commission */
            $commission = $commissionList->keyBy('order_id')->get($order->id);
            $commissionSum = $commission->promoter_id == $this->user() ? $commission->promoter_commission : $commission->manager_commission;
            $goods = $goodsList->get($order->id);

            return [
                'id' => $order->id,
                'orderSn' => $order->order_sn,
                'status' => $commission->status,
                'createdAt' => $order->created_at,
                'commission' => $commissionSum,
                '$goods' => $goods
            ];
        });

        return $this->success($this->paginate($page, $list));
    }
}
