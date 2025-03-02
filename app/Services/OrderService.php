<?php

namespace App\Services;

use App\Jobs\OverTimeCancelOrder;
use App\Models\Address;
use App\Models\CartGoods;
use App\Models\Coupon;
use App\Models\FreightTemplate;
use App\Models\Goods;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\Promoter;
use App\Utils\CodeResponse;
use App\Utils\Enums\AdminTodoEnums;
use App\Utils\Enums\OrderEnums;
use App\Utils\Inputs\Admin\OrderPageInput;
use App\Utils\Inputs\PageInput;
use App\Utils\WxMpServe;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yansongda\LaravelPay\Facades\Pay;
use Yansongda\Pay\Exceptions\GatewayException;

class OrderService extends BaseService
{
    public function getOrderListByStatus($userId, $statusList, PageInput $input, $columns = ['*'])
    {
        $query = Order::query()->where('user_id', $userId);
        if (count($statusList) != 0) {
            $query = $query->whereIn('status', $statusList);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getListTotal($userId, $statusList)
    {
        return Order::query()->where('user_id', $userId)->whereIn('status', $statusList)->count();
    }

    public function getOrderList($columns = ['*'])
    {
        return Order::query()->whereIn('status', [201, 204, 301, 401, 402, 403, 501])->get($columns);
    }

    public function getOrderPage(OrderPageInput $input, $columns = ['*'])
    {
        $query = Order::query();
        if (!empty($input->goodsId)) {
            $orderIds = OrderGoodsService::getInstance()->getListByGoodsIds([$input->goodsId])->pluck('order_id')->toArray();
            $query = $query->whereIn('id', $orderIds);
        }
        if (!empty($input->status)) {
            $query = $query->where('status', $input->status);
        }
        if (!empty($input->orderSn)) {
            $query = $query->where('order_sn', $input->orderSn);
        }
        if (!empty($input->merchantId)) {
            $query = $query->where('merchant_id', $input->merchantId);
        }
        if (!empty($input->userId)) {
            $query = $query->where('user_id', $input->userId);
        }
        if (!empty($input->consignee)) {
            $query = $query->where('consignee', $input->consignee);
        }
        if (!empty($input->mobile)) {
            $query = $query->where('mobile', $input->mobile);
        }
        return $query
            ->orderByRaw("FIELD(status, 204, 201) DESC")
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUnpaidList(int $userId, array $orderIds, $columns = ['*'])
    {
        return Order::query()
            ->where('user_id', $userId)
            ->whereIn('id', $orderIds)
            ->where('status', OrderEnums::STATUS_CREATE)
            ->get($columns);
    }

    public function getUnpaidListBySn(array $orderSnList, $columns = ['*'])
    {
        return Order::query()
            ->whereIn('order_sn', $orderSnList)
            ->where('status', OrderEnums::STATUS_CREATE)
            ->get($columns);
    }

    public function getUnpaidListByIds(array $ids, $columns = ['*'])
    {
        return Order::query()
            ->whereIn('id', $ids)
            ->where('status', OrderEnums::STATUS_CREATE)
            ->get($columns);
    }

    public function generateOrderSn()
    {
        return retry(5, function () {
            $orderSn = date('YmdHis') . rand(100000, 999999);
            if ($this->isOrderSnExists($orderSn)) {
                Log::warning('当前订单号已存在，orderSn：' . $orderSn);
                $this->throwBusinessException(CodeResponse::FAIL, '订单号生成失败');
            }
            return $orderSn;
        });
    }

    public function isOrderSnExists(string $orderSn)
    {
        return Order::query()->where('order_sn', $orderSn)->exists();
    }

    public function createOrder($userId, $merchantId, $cartGoodsList, $freightTemplateList, Address $address, Coupon $coupon = null, $useBalance = 0)
    {
        $totalPrice = 0;
        $totalFreightPrice = 0;
        $couponDenomination = 0;

        /** @var CartGoods $cartGoods */
        foreach ($cartGoodsList as $cartGoods) {
            $price = bcmul($cartGoods->price, $cartGoods->number, 2);
            $totalPrice = bcadd($totalPrice, $price, 2);

            // 计算运费
            if ($cartGoods->freight_template_id == 0) {
                $freightPrice = 0;
            } else {
                $freightTemplate = $freightTemplateList->get($cartGoods->freight_template_id);
                $freightPrice = $this->calcFreightPrice($freightTemplate, $address, $price, $cartGoods->number);
            }
            $totalFreightPrice = bcadd($totalFreightPrice, $freightPrice, 2);

            // 优惠券
            if (!is_null($coupon) && $coupon->goods_id == $cartGoods->goods_id) {
                $couponDenomination = $coupon->denomination;
            }

            // 商品减库存加销量
            $row = GoodsService::getInstance()->reduceStock($cartGoods->goods_id, $cartGoods->number, $cartGoods->selected_sku_index);
            if ($row == 0) {
                $this->throwBusinessException(CodeResponse::GOODS_NO_STOCK);
            }

            // 活动商品增加活动销量
            $activity = ActivityService::getInstance()->getActivityByGoodsId($cartGoods->goods_id, [1]);
            if (!is_null($activity)) {
                $activity->sales = $activity->sales + $cartGoods->number;
                $activity->save();
            }
        }

        $paymentAmount = bcadd($totalPrice, $totalFreightPrice, 2);
        $paymentAmount = bcsub($paymentAmount, $couponDenomination, 2);

        $orderSn = $this->generateOrderSn();
        // 余额抵扣
        $deductionBalance = 0;
        if ($useBalance == 1) {
            $account = AccountService::getInstance()->getUserAccount($userId);
            $deductionBalance = min($paymentAmount, $account->balance);
            $paymentAmount = bcsub($paymentAmount, $deductionBalance, 2);

            // 更新余额
            AccountService::getInstance()->updateBalance($userId, 2, -$deductionBalance, $orderSn);
        }

        $order = Order::new();
        $order->order_sn = $orderSn;
        $order->status = OrderEnums::STATUS_CREATE;
        $order->user_id = $userId;
        $order->merchant_id = $merchantId;
        $order->consignee = $address->name;
        $order->mobile = $address->mobile;
        $order->address = $address->region_desc . ' ' . $address->address_detail;
        $order->goods_price = $totalPrice;
        $order->freight_price = $totalFreightPrice;
        if (!is_null($coupon)) {
            $order->coupon_id = $coupon->id;
            $order->coupon_denomination = $couponDenomination;
        }
        $order->deduction_balance = $deductionBalance;
        $order->payment_amount = $paymentAmount;
        $order->refund_amount = $paymentAmount;
        $order->save();

        // 设置订单支付超时任务
        dispatch(new OverTimeCancelOrder($userId, $order->id));

        return $order->id;
    }

    public function calcFreightPrice(FreightTemplate $freightTemplate, Address $address, $totalPrice, $goodsNumber)
    {
        if ($freightTemplate->free_quota != 0 && $totalPrice > $freightTemplate->free_quota) {
            $freightPrice = 0;
        } else {
            $cityCode = substr(json_decode($address->region_code_list)[1], 0, 4);
            $area = collect($freightTemplate->area_list)->first(function ($area) use ($cityCode) {
                return in_array($cityCode, explode(',', $area->pickedCityCodes));
            });
            if (is_null($area)) {
                $freightPrice = 0;
            } else {
                if ($freightTemplate->compute_mode == 1) {
                    $freightPrice = $area->fee;
                } else {
                    $freightPrice = bcmul($area->fee, $goodsNumber, 2);
                }
            }
        }
        return $freightPrice;
    }

    public function createWxPayOrder($userId, array $orderIds, $openid)
    {
        $orderList = $this->getUnpaidList($userId, $orderIds);
        if (count($orderList) == 0) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '订单不存在');
        }

        $orderSnList = $orderList->pluck('order_sn')->toArray();

        $paymentAmount = 0;
        foreach ($orderList as $order) {
            $paymentAmount = bcadd($order->payment_amount, $paymentAmount, 2);
        }

        return [
            'out_trade_no' => time(),
            'body' => '订单编号：' . implode("','", $orderSnList),
            'attach' => json_encode($orderSnList),
            'total_fee' => bcmul($paymentAmount, 100),
            'openid' => $openid
        ];
    }

    public function wxPaySuccess(array $data)
    {
        $orderSnList = json_decode($data['attach']);
        $payId = $data['transaction_id'] ?? '';
        $actualPaymentAmount = $data['total_fee'] ? bcdiv($data['total_fee'], 100, 2) : 0;

        $orderList = $this->getUnpaidListBySn($orderSnList);

        $paymentAmount = 0;
        foreach ($orderList as $order) {
            $paymentAmount = bcadd($order->payment_amount, $paymentAmount, 2);
        }
        if (bccomp($actualPaymentAmount, $paymentAmount, 2) != 0) {
            $errMsg = "支付回调，订单{$data['attach']}金额不一致，请检查，支付回调金额：{$actualPaymentAmount}，订单总金额：{$paymentAmount}";
            Log::error($errMsg);
            $this->throwBusinessException(CodeResponse::FAIL, $errMsg);
        }

        return $this->paySuccess($orderList, $payId, $actualPaymentAmount);
    }

    public function paySuccess($orderList, $payId = null, $actualPaymentAmount = null)
    {
        return DB::transaction(function () use ($actualPaymentAmount, $payId, $orderList) {
            $orderList = $orderList->map(function (Order $order) use ($actualPaymentAmount, $payId) {
                if (!is_null($payId)) {
                    $order->pay_id = $payId;
                }
                if (!is_null($actualPaymentAmount)) {
                    $order->total_payment_amount = $actualPaymentAmount;
                }
                $order->pay_time = now()->toDateTimeString();
                $order->status = OrderEnums::STATUS_PAY;
                if ($order->cas() == 0) {
                    $this->throwUpdateFail();
                }
                // todo 通知（邮件或钉钉）管理员、
                // todo 通知（短信、系统消息）商家
                return $order;
            });

            // 佣金记录状态更新为：已支付待结算
            $orderIds = $orderList->pluck('id')->toArray();
            CommissionService::getInstance()->updateListToOrderPaidStatus($orderIds);
            TeamCommissionService::getInstance()->updateListToOrderPaidStatus($orderIds);

            // todo 礼包逻辑临时改动，付款成功就成为推官员，售后需人工处理产生的佣金记录
            // GiftCommissionService::getInstance()->updateListToOrderPaidStatus($orderIds);
            GiftCommissionService::getInstance()->updateListToOrderConfirmStatus($orderIds);

            // 限时成为推广员活动
            // $activityGoodsIds = LimitedTimeRecruitGoodsService::getInstance()->getAllGoodsList()->pluck('goods_id')->toArray();
            // $orderGoodsIds = array_unique(OrderGoodsService::getInstance()->getListByOrderIds($orderIds)->pluck('goods_id')->toArray());
            // $commonGoodsIds = array_intersect($orderGoodsIds, $activityGoodsIds);
            // $userId = $orderList->first()->user_id;
            // $promoterInfo = UserService::getInstance()->getUserById($userId)->promoterInfo;
            // if (!empty($commonGoodsIds) && is_null($promoterInfo)) {
            //      PromoterService::getInstance()->toBePromoter($userId, 3, $commonGoodsIds);
            // }

            // 生成后台待发货代办事项
            AdminTodoService::getInstance()->createTodo(AdminTodoEnums::ORDER_SHIP_WAITING, $orderIds);

            return $orderList;
        });
    }

    public function userCancel($userId, $orderId)
    {
        return DB::transaction(function () use ($userId, $orderId) {
            $orderList = $this->getUserOrderList($userId, [$orderId]);
            if (count($orderList) == 0) {
                $this->throwBadArgumentValue();
            }
            return $this->cancel($orderList);
        });
    }

    public function systemAutoCancel($userId, $orderId)
    {
        return DB::transaction(function () use ($userId, $orderId) {
            $orderList = $this->getUserOrderList($userId, [$orderId]);
            if (count($orderList) == 0) {
                $this->throwBadArgumentValue();
            }
            return $this->cancel($orderList, 'system');
        });
    }

    public function systemCancel()
    {
        return DB::transaction(function () {
            $orderList = $this->getOverTimeUnpaidList();
            if (count($orderList) == 0) {
                $this->throwBadArgumentValue();
            }
            return $this->cancel($orderList, 'system');
        });
    }

    public function getOverTimeUnpaidList($columns = ['*'])
    {
        return Order::query()
            ->where('status', OrderEnums::STATUS_CREATE)
            ->where('created_at', '<=', now()->subHours(24))
            ->get($columns);
    }

    public function adminCancel($orderIds)
    {
        return DB::transaction(function () use ($orderIds) {
            $orderList = $this->getOrderListByIds($orderIds);
            if (count($orderList) == 0) {
                $this->throwBadArgumentValue();
            }
            return $this->cancel($orderList, 'admin');
        });
    }

    public function cancel($orderList, $role = 'user')
    {
        $orderList = $orderList->map(function (Order $order) use ($role) {
            if ($order->status != OrderEnums::STATUS_CREATE) {
                $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单不能取消');
            }
            switch ($role) {
                case 'system':
                    $order->status = OrderEnums::STATUS_AUTO_CANCEL;
                    break;
                case 'admin':
                    $order->status = OrderEnums::STATUS_ADMIN_CANCEL;
                    break;
                case 'user':
                    $order->status = OrderEnums::STATUS_CANCEL;
                    break;
            }
            $order->finish_time = now()->toDateTimeString();
            if ($order->cas() == 0) {
                $this->throwUpdateFail();
            }

            // 返还库存
            $this->returnStock($order->id);

            // 恢复优惠券
            if ($order->coupon_id != 0) {
                $this->restoreCoupon($order->user_id, $order->coupon_id);
            }

            return $order;
        });

        // 删除佣金记录
        $orderIds = $orderList->pluck('id')->toArray();
        CommissionService::getInstance()->deleteUnpaidListByOrderIds($orderIds);
        GiftCommissionService::getInstance()->deleteUnpaidListByOrderIds($orderIds);
        TeamCommissionService::getInstance()->deleteUnpaidListByOrderIds($orderIds);

        return $orderList;
    }

    public function returnStock($orderId)
    {
        $goodsList = OrderGoodsService::getInstance()->getListByOrderId($orderId);

        /** @var OrderGoods $goods */
        foreach ($goodsList as $goods)
        {
            GoodsService::getInstance()->returnStock($goods->goods_id, $goods->number, $goods->selected_sku_index);
        }
    }

    public function restoreCoupon($userId, $couponId)
    {
        $userCoupon = UserCouponService::getInstance()->getUserUsedCouponByCouponId($userId, $couponId);
        if (!is_null($userCoupon)) {
            $userCoupon->status = 1;
            $userCoupon->save();
        }
        return $userCoupon;
    }

    public function userConfirm($userId, $orderId)
    {
        return DB::transaction(function () use ($userId, $orderId) {
            $orderList = $this->getUserOrderList($userId, [$orderId]);
            if (count($orderList) == 0) {
                $this->throwBadArgumentValue();
            }
            return $this->confirm($orderList);
        });
    }

    public function adminConfirm($orderIds)
    {
        return DB::transaction(function () use ($orderIds) {
            $orderList = $this->getOrderListByIds($orderIds);
            if (count($orderList) == 0) {
                $this->throwBadArgumentValue();
            }
            return $this->confirm($orderList, 'admin');
        });
    }

    public function getTimeoutUnConfirmOrders($columns = ['*'])
    {
        return Order::query()
            ->where('status', OrderEnums::STATUS_SHIP)
            ->where('ship_time', '<=', now()->subDays(15))
            ->where('ship_time', '>', now()->subDays(30))
            ->get($columns);
    }

    public function systemConfirm()
    {
        return DB::transaction(function () {
            $orderList = $this->getTimeoutUnConfirmOrders();
            if (count($orderList) == 0) {
                $this->throwBadArgumentValue();
            }
            return $this->confirm($orderList, 'system');
        });

    }

    public function confirm($orderList, $role = 'user')
    {
        $orderList = $orderList->map(function (Order $order) use ($role) {
            if ($order->status != OrderEnums::STATUS_SHIP) {
                $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单无法确认');
            }
            switch ($role) {
                case 'system':
                    $order->status = OrderEnums::STATUS_AUTO_CONFIRM;
                    break;
                case 'admin':
                    $order->status = OrderEnums::STATUS_ADMIN_CONFIRM;
                    break;
                case 'user':
                    $order->status = OrderEnums::STATUS_CONFIRM;
                    break;
            }
            $order->confirm_time = now()->toDateTimeString();
            if ($order->cas() == 0) {
                $this->throwUpdateFail();
            }

            return $order;
        });

        // 佣金记录变更为待提现
        $orderIds = $orderList->pluck('id')->toArray();
        CommissionService::getInstance()->updateListToOrderConfirmStatus($orderIds, $role);
        TeamCommissionService::getInstance()->updateListToOrderConfirmStatus($orderIds, $role);

        // todo 礼包逻辑临时改动，付款成功就成为推官员，售后需人工处理产生的佣金记录
        // GiftCommissionService::getInstance()->updateListToOrderConfirmStatus($orderIds);

        return $orderList;
    }

    public function ship($orderId, $shipChannel, $shipCode, $shipSn)
    {
        $order = $this->getOrderById($orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if (!$order->canShipHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单未付款，无法发货');
        }

        if (empty($shipCode)) {
            $express = ExpressService::getInstance()->getExpressByName($shipChannel);
            $shipCode = $express->code;
        }

        DB::transaction(function () use ($order, $shipChannel, $shipCode, $shipSn) {
            $order->status = OrderEnums::STATUS_SHIP;
            $order->ship_time = now()->toDateTimeString();
            if ($order->cas() == 0) {
                $this->throwUpdateFail();
            }

            $orderPackage = OrderPackageService::getInstance()->create($order->id, $shipChannel, $shipCode, $shipSn);
            $orderGoodsList = OrderGoodsService::getInstance()->getListByOrderId($order->id);
            foreach ($orderGoodsList as $orderGoods) {
                OrderPackageGoodsService::getInstance()->create($order->id, $orderPackage->id, $orderGoods->goods_id, $orderGoods->cover, $orderGoods->name, $orderGoods->number);
            }

            // 发货同步小程序后台
            if ($order->refund_amount != 0) {
                $openid = UserService::getInstance()->getUserById($order->user_id)->openid;
                WxMpServe::new()->uploadShippingInfo($openid, $order, [$orderPackage], true);
            }

            // 完成后台待发货代办事项
            AdminTodoService::getInstance()->deleteTodo(AdminTodoEnums::ORDER_SHIP_WAITING, $order->id);
        });

        return $order;
    }

    public function splitShip($orderId, array $packageList, $isAllDelivered = false)
    {
        $order = $this->getOrderById($orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if (!$order->canShipHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单未付款，无法发货');
        }

        DB::transaction(function () use ($order, $packageList, $isAllDelivered) {
            if ($isAllDelivered) {
                $order->status = OrderEnums::STATUS_SHIP;
                $order->ship_time = now()->toDateTimeString();
                if ($order->cas() == 0) {
                    $this->throwUpdateFail();
                }
            }

            $orderPackageList = [];
            foreach ($packageList as $package) {
                $shipChannel = $package['shipChannel'];
                $shipCode = $package['shipCode'];
                $shipSn = $package['shipSn'];
                if (empty($shipCode)) {
                    $express = ExpressService::getInstance()->getExpressByName($shipChannel);
                    $shipCode = $express->code;
                }
                $orderPackage = OrderPackageService::getInstance()->create($order->id, $shipChannel, $shipCode, $shipSn);
                $orderPackageList[] = $orderPackage;

                $goodsList = json_decode($package['goodsList']);
                foreach ($goodsList as $goods) {
                    OrderPackageGoodsService::getInstance()->create($order->id, $orderPackage->id, $goods->goodsId, $goods->cover, $goods->name, $goods->number);
                }
            }

            // 发货同步小程序后台
            if ($order->refund_amount != 0) {
                $openid = UserService::getInstance()->getUserById($order->user_id)->openid;
                WxMpServe::new()->uploadShippingInfo($openid, $order, $orderPackageList, $isAllDelivered);
            }
        });

        return $order;
    }

    public function finish($userId, $orderId)
    {
        $order = $this->getUserOrderById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if (!$order->canFinishHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单不能设置为完成状态');
        }
        $order->status = OrderEnums::STATUS_FINISHED;
        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }
        return $order;
    }

    public function delete($orderList)
    {
        foreach ($orderList as $order) {
            if (!$order->canDeleteHandle()) {
                $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单不能删除');
            }
            OrderGoodsService::getInstance()->delete($order->id);
            $order->delete();
        }
    }

    public function afterSale($userId, $orderId)
    {
        $order = $this->getUserOrderById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if (!$order->canAftersaleHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '该订单无法申请售后');
        }
        $order->status = OrderEnums::STATUS_REFUND;
        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }
        return $order;
    }

    public function userRefund($userId, $orderId)
    {
        $order = $this->getUserOrderById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        $this->refund($order);
    }

    public function adminRefund($orderIds)
    {
        $orderList = $this->getOrderListByIds($orderIds);
        if (count($orderList) == 0) {
            $this->throwBadArgumentValue();
        }
        foreach ($orderList as $order) {
            $this->refund($order);
        }
    }

    public function refund(Order $order)
    {
        if (!$order->canRefundHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '该订单不能申请退款');
        }
        DB::transaction(function () use ($order) {
            try {
                // 微信退款
                if ($order->refund_amount != 0) {
                    $refundParams = [
                        'transaction_id' => $order->pay_id,
                        'out_refund_no' => time(),
                        'total_fee' => bcmul($order->total_payment_amount, 100),
                        'refund_fee' => bcmul($order->refund_amount, 100),
                        'refund_desc' => '商品退款',
                        'type' => 'miniapp'
                    ];

                    $result = Pay::wechat()->refund($refundParams);
                    $order->refund_id = $result['refund_id'];
                    Log::info('order_wx_refund', $result->toArray());
                }

                $order->status = OrderEnums::STATUS_REFUND_CONFIRM;
                $order->refund_time = now()->toDateTimeString();
                if ($order->cas() == 0) {
                    $this->throwUpdateFail();
                }

                // 退还余额
                if ($order->deduction_balance != 0) {
                    AccountService::getInstance()->updateBalance($order->user_id, 3, $order->deduction_balance, $order->order_sn);
                }

                // 删除佣金记录
                CommissionService::getInstance()->deletePaidListByOrderIds([$order->id]);
                TeamCommissionService::getInstance()->deletePaidListByOrderIds([$order->id]);

                // todo 礼包逻辑临时改动，付款成功就成为推官员，售后需人工处理产生的佣金记录
                // GiftCommissionService::getInstance()->deletePaidListByOrderIds([$order->id]);
                GiftCommissionService::getInstance()->deleteListByOrderIds([$order->id]);

                // 退款删除推官员身份
                /** @var Promoter $promoterInfo */
                $promoterInfo = PromoterService::getInstance()->getUserPromoterByPathList($order->user_id, [2, 3]);
                if (!is_null($promoterInfo)) {
                    $goodsIds = explode(',', $promoterInfo->goods_ids);
                    $orderGoodsIds = array_unique(OrderGoodsService::getInstance()->getListByOrderId($order->id)->pluck('goods_id')->toArray());
                    $commonGoodsIds = array_intersect($orderGoodsIds, $goodsIds);
                    if (!empty($commonGoodsIds)) {
                        $promoterInfo->delete();
                    }
                }

                // 删除后台待发货代办事项
                AdminTodoService::getInstance()->deleteTodo(AdminTodoEnums::ORDER_SHIP_WAITING, $order->id);
            } catch (GatewayException $exception) {
                Log::error('wx_refund_fail', [$exception]);
            }
        });
    }

    public function afterSaleRefund($orderId, $goodsId, $couponId, $refundAmount)
    {
        $order = $this->getOrderById($orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if (!$order->canRefundHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '该订单不支持退款');
        }

        /** @var OrderGoods $orderGoods */
        $orderGoods = OrderGoodsService::getInstance()->getOrderGoods($orderId, $goodsId);
        $totalPrice = bcmul($orderGoods->price, $orderGoods->number, 2);
        $couponDenomination = 0;
        if ($couponId != 0) {
            $coupon = CouponService::getInstance()->getGoodsCoupon($couponId, $goodsId);
            if (!is_null($coupon)) {
                $couponDenomination = $coupon->denomination;
            }
        }
        $actualRefundAmount = bcsub($totalPrice, $couponDenomination, 2);

        if (bccomp($actualRefundAmount, $refundAmount, 2) != 0) {
            $errMsg = "退款申请，订单id为{$orderId}商品id为{$goodsId}，退款金额（{$refundAmount}）与实际可退款金额（{$actualRefundAmount}）不一致";
            Log::error($errMsg);
            $this->throwBusinessException(CodeResponse::FAIL, $errMsg);
        }

        try {
            $refundParams = [
                'transaction_id' => $orderId,
                'out_refund_no' => time(),
                'total_fee' => bcmul($order->payment_amount, 100),
                'refund_fee' => bcmul($actualRefundAmount, 100),
                'refund_desc' => '商品退款',
                'type' => 'miniapp'
            ];

            $result = Pay::wechat()->refund($refundParams);
            Log::info('order_wx_refund', $result->toArray());

            $order->status = OrderEnums::STATUS_REFUND_CONFIRM;
            $order->refund_id = $result['refund_id'];
            $order->refund_time = now()->toDateTimeString();
            if ($order->cas() == 0) {
                $this->throwUpdateFail();
            }

            // 删除佣金记录
            CommissionService::getInstance()->deletePaidCommission($orderId, $goodsId);
            TeamCommissionService::getInstance()->deletePaidCommission($orderId, $goodsId);

            // todo 礼包逻辑临时改动，付款成功就成为推官员，售后需人工处理产生的佣金记录
            // GiftCommissionService::getInstance()->deletePaidListByOrderIds([$order->id]);
            GiftCommissionService::getInstance()->deleteByGoodsId($orderId, $goodsId);

            // 售后删除推官员身份
            /** @var Promoter $promoterInfo */
            $promoterInfo = PromoterService::getInstance()->getUserPromoterByPathList($order->user_id, [2, 3]);
            if (!is_null($promoterInfo)) {
                $goodsIds = explode(',', $promoterInfo->goods_ids);
                if (in_array($goodsId, $goodsIds)) {
                    $promoterInfo->delete();
                }
            }

            return $order;
        } catch (GatewayException $exception) {
            Log::error('wx_refund_fail', [$exception]);
        }
    }

    public function getOrderById($id, $columns = ['*'])
    {
        return Order::query()->find($id, $columns);
    }

    public function getOrderListByIds(array $ids, $columns = ['*'])
    {
        return Order::query()->whereIn('id', $ids)->get($columns);
    }

    public function getOrderPageByIds(array $ids, PageInput $input, $columns = ['*'])
    {
        return Order::query()
            ->whereIn('id', $ids)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserOrderById($userId, $id, $columns = ['*'])
    {
        return Order::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getUserOrderList($userId, $ids, $columns = ['*'])
    {
        return Order::query()->where('user_id', $userId)->whereIn('id', $ids)->get($columns);
    }

    public function getTodayOrderList($columns = ['*'])
    {
        return Order::query()
            ->whereDate('created_at', Carbon::today())
            ->whereIn('status', [201, 204, 301, 401, 402, 403, 501])
            ->get($columns);
    }

    public function getTodayOrderingUserCountByUserIds(array $userIds)
    {
        return Order::query()
            ->whereIn('user_id', $userIds)
            ->whereDate('created_at', Carbon::today())
            ->whereIn('status', [201, 204, 301, 401, 402, 403, 501])
            ->distinct('user_id')
            ->count('user_id');
    }

    public function getTodayOrderListByUserIds(array $userIds, $columns = ['*'])
    {
        return Order::query()
            ->whereIn('user_id', $userIds)
            ->whereDate('created_at', Carbon::today())
            ->whereIn('status', [201, 204, 301, 401, 402, 403, 501])
            ->get($columns);
    }

    public function importOrders(array $row)
    {
        $validator = Validator::make($row, [
            'order_id' => 'required|integer',
            'ship_channel' => 'required|string',
            'ship_code' => 'string',
            'ship_sn' => 'required|string',
        ]);
        if ($validator->fails()) {
            $this->throwBusinessException(CodeResponse::PARAM_VALUE_INVALID, $validator->errors());
        }
        $this->ship($row['order_id'], $row['ship_channel'], $row['ship_code'], $row['ship_sn']);
    }

    public function salesSum()
    {
        return Order::query()
            ->whereIn('status', [201, 204, 301, 401, 402, 403, 501])
            ->sum(DB::raw('refund_amount + deduction_balance'));
    }

    public function dailySalesList()
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(17);

        return Order::query()
            ->whereIn('status', [201, 204, 301, 401, 402, 403, 501])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as created_at'),
                DB::raw('SUM(refund_amount + deduction_balance) as sum')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();
    }

    public function monthlySalesList()
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subMonths(12)->startOfMonth();

        return Order::query()
            ->whereIn('status', [201, 204, 301, 401, 402, 403, 501])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw("SUM(refund_amount + deduction_balance) as sum")
            )
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy('month', 'asc')
            ->get();
    }

    public function dailySalesGrowthRate()
    {
        $query = Order::query()->whereIn('status', [201, 204, 301, 401, 402, 403, 501]);

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $todayPaymentAmount = (clone $query)->whereDate('created_at', $today)->sum('payment_amount');
        $yesterdayPaymentAmount = (clone $query)->whereDate('created_at', $yesterday)->sum('payment_amount');

        if ($yesterdayPaymentAmount > 0) {
            $dailyGrowthRate = round((($todayPaymentAmount - $yesterdayPaymentAmount) / $yesterdayPaymentAmount) * 100);
        } else {
            $dailyGrowthRate = 0;
        }

        return $dailyGrowthRate;
    }

    public function weeklySalesGrowthRate()
    {
        $query = Order::query()->whereIn('status', [201, 204, 301, 401, 402, 403, 501]);

        $startOfThisWeek = Carbon::now()->startOfWeek();
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

        $thisWeekPaymentAmount = (clone $query)->whereBetween('created_at', [$startOfThisWeek, now()])->sum('payment_amount');
        $lastWeekPaymentAmount = (clone $query)->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->sum('payment_amount');

        if ($lastWeekPaymentAmount > 0) {
            $weeklyGrowthRate = round((($thisWeekPaymentAmount - $lastWeekPaymentAmount) / $lastWeekPaymentAmount) * 100);
        } else {
            $weeklyGrowthRate = 0; // 防止除以零
        }

        return $weeklyGrowthRate;
    }

    public function orderCountSum()
    {
        return Order::query()->whereIn('status', [201, 204, 301, 401, 402, 403, 501])->count();
    }

    public function dailyOrderCountList()
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(17);

        return Order::query()
            ->whereIn('status', [201, 204, 301, 401, 402, 403, 501])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as created_at'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();
    }

    public function monthlyOrderCountList()
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subMonths(12)->startOfMonth();

        return Order::query()
            ->whereIn('status', [201, 204, 301, 401, 402, 403, 501])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy('month', 'asc')
            ->get();
    }

    public function dailyOrderCountGrowthRate()
    {
        $query = Order::query()->whereIn('status', [201, 204, 301, 401, 402, 403, 501]);

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $todayOrderCount = (clone $query)->whereDate('created_at', $today)->count();
        $yesterdayOrderCount = (clone $query)->whereDate('created_at', $yesterday)->count();

        if ($yesterdayOrderCount > 0) {
            $dailyGrowthRate = round((($todayOrderCount - $yesterdayOrderCount) / $yesterdayOrderCount) * 100);
        } else {
            $dailyGrowthRate = 0;
        }

        return $dailyGrowthRate;
    }

    public function weeklyOrderCountGrowthRate()
    {
        $query = Order::query()->whereIn('status', [201, 204, 301, 401, 402, 403, 501]);

        $startOfThisWeek = Carbon::now()->startOfWeek();
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

        $thisWeekOrderCount = (clone $query)->whereBetween('created_at', [$startOfThisWeek, now()])->count();
        $lastWeekOrderCount = (clone $query)->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->count();

        if ($lastWeekOrderCount > 0) {
            $weeklyGrowthRate = round((($thisWeekOrderCount - $lastWeekOrderCount) / $lastWeekOrderCount) * 100);
        } else {
            $weeklyGrowthRate = 0; // 防止除以零
        }

        return $weeklyGrowthRate;
    }

    public function exportOrderList(array $ids)
    {
        foreach ($ids as $id) {
            $order = $this->getOrderById($id);
            if ($order->canExportHandle()) {
                $order->status = OrderEnums::STATUS_EXPORTED;
                if ($order->cas() == 0) {
                    $this->throwUpdateFail();
                }
            }
        }
    }

    public function repeatCustomersCount()
    {
        return Order::query()
            ->whereIn('status', [201, 204, 301, 401, 402, 403, 501])
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();
    }

    public function usersWithOrdersCount()
    {
        return Order::query()
            ->whereIn('status', [201, 204, 301, 401, 402, 403, 501])
            ->select('user_id')
            ->distinct()
            ->count();
    }

    public function confirmMissCommission()
    {
        $orderIds = $this->getRemoteUserConfirmList()->pluck('id')->toArray();
        DB::transaction(function () use ($orderIds) {
            CommissionService::getInstance()->updateListToOrderConfirmStatus($orderIds, 'system');
            TeamCommissionService::getInstance()->updateListToOrderConfirmStatus($orderIds, 'system');
            GiftCommissionService::getInstance()->updateListToOrderConfirmStatus($orderIds);
        });
    }

    public function getRemoteUserConfirmList($columns = ['*'])
    {
        return Order::query()
            ->whereIn('status', [401, 501])
            ->where('updated_at', '<', now()->subDays(7))
            ->where('updated_at', '>=', now()->subDays(14))
            ->get($columns);
    }

    public function getOrderCountByStatusList(array $statusList)
    {
        return Order::query()->whereIn('status', $statusList)->count();
    }

    public function modifyAddressInfo($userId, $orderId, $addressId)
    {
        $order = OrderService::getInstance()->getUserOrderById($userId, $orderId);
        if (!$order->canExportHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '非待发货订单，无法修改地址');
        }

        $address = AddressService::getInstance()->getUserAddressById($userId, $addressId);
        if (is_null($address)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '地址不存在');
        }

        // 计算运费
        $orderGoodsList = OrderGoodsService::getInstance()->getListByOrderId($orderId);
        $goodsIds = $orderGoodsList->pluck('goods_id')->toArray();
        $goodsList = GoodsService::getInstance()->getListByIds($goodsIds);
        $groupedGoodsList = $goodsList->keyBy('id');
        $freightTemplateIds = $goodsList->pluck('freight_template_id')->toArray();
        $freightTemplateList = FreightTemplateService::getInstance()
            ->getListByIds($freightTemplateIds)
            ->map(function (FreightTemplate $freightTemplate) {
                $freightTemplate->area_list = json_decode($freightTemplate->area_list);
                return $freightTemplate;
            })->keyBy('id');
        $totalFreightPrice = 0;
        /** @var OrderGoods $goods */
        foreach ($orderGoodsList as $orderGoods) {
            $price = bcmul($orderGoods->price, $orderGoods->number, 2);
            /** @var Goods $goods */
            $goods = $groupedGoodsList->get($orderGoods->goods_id);
            if ($goods->freight_template_id == 0) {
                $freightPrice = 0;
            } else {
                $freightTemplate = $freightTemplateList->get($goods->freight_template_id);
                $freightPrice = $this->calcFreightPrice($freightTemplate, $address, $price, $orderGoods->number);
            }
            $totalFreightPrice = bcadd($totalFreightPrice, $freightPrice, 2);
        }

        if ($totalFreightPrice > $order->freight_price) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '当前地址需额外支付运费，请联系客服处理');
        }

        $order->consignee = $address->name;
        $order->mobile = $address->mobile;
        $order->address = $address->region_desc . ' ' . $address->address_detail;
        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }
        return $order;
    }
}
