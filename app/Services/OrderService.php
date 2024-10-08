<?php

namespace App\Services;

use App\Jobs\OverTimeCancelOrder;
use App\Models\Address;
use App\Models\CartGoods;
use App\Models\Coupon;
use App\Models\FreightTemplate;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Utils\CodeResponse;
use App\Utils\Enums\OrderEnums;
use App\Utils\Inputs\Admin\OrderPageInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    public function getOrderList(OrderPageInput $input, $columns = ['*'])
    {
        $query = Order::query()->whereIn('status', [101, 102, 103, 104, 201, 301, 401, 402]);
        if (!empty($input->status)) {
            $query = $query->where('status', $input->status);
        }
        if (!empty($input->orderSn)) {
            $query = $query->where('order_sn', $input->orderSn);
        }
        if (!empty($input->merchantId)) {
            $query = $query->where('merchant_id', $input->merchantId);
        }
        if (!empty($input->consignee)) {
            $query = $query->where('consignee', $input->consignee);
        }
        if (!empty($input->mobile)) {
            $query = $query->where('mobile', $input->mobile);
        }
        return $query
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

    public function createOrder($userId, $merchantId, $cartGoodsList, $freightTemplateList, Address $address, Coupon $coupon = null)
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
            $activity = ActivityService::getInstance()->getActivityByGoodsId($cartGoods->goods_id, 1);
            if (!is_null($activity)) {
                $activity->sales = $activity->sales + $cartGoods->number;
                $activity->save();
            }
        }

        $paymentAmount = bcadd($totalPrice, $totalFreightPrice, 2);
        $paymentAmount = bcsub($paymentAmount, $couponDenomination, 2);

        $order = Order::new();
        $order->order_sn = $this->generateOrderSn();
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
        $order->payment_amount = $paymentAmount;
        $order->refund_amount = $order->payment_amount;
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

        return DB::transaction(function () use ($payId, $orderList) {
            $orderList = $orderList->map(function (Order $order) use ($payId) {
                $order->pay_id = $payId;
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
            GiftCommissionService::getInstance()->updateListToOrderPaidStatus($orderIds);
            TeamCommissionService::getInstance()->updateListToOrderPaidStatus($orderIds);

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

    public function systemCancel($userId, $orderId)
    {
        return DB::transaction(function () use ($userId, $orderId) {
            $orderList = $this->getUserOrderList($userId, [$orderId]);
            if (count($orderList) == 0) {
                $this->throwBadArgumentValue();
            }
            return $this->cancel($orderList, 'system');
        });
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
                $this->restoreCoupon($order->coupon_id);
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

    public function restoreCoupon($couponId)
    {
        $userCoupon = UserCouponService::getInstance()->getUserCouponByCouponId($couponId);
        $userCoupon->status = 1;
        $userCoupon->save();
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

    public function systemConfirm($userId, $orderId)
    {
        return DB::transaction(function () use ($userId, $orderId) {
            $orderList = $this->getUserOrderList($userId, [$orderId]);
            if (count($orderList) == 0) {
                $this->throwBadArgumentValue();
            }
            return $this->confirm($orderList, 'system');
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
        CommissionService::getInstance()->updateListToOrderConfirmStatus($orderIds);
        GiftCommissionService::getInstance()->updateListToOrderConfirmStatus($orderIds);
        TeamCommissionService::getInstance()->updateListToOrderConfirmStatus($orderIds);

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
        $order->status = OrderEnums::STATUS_SHIP;
        $order->ship_channel = $shipChannel;
        $order->ship_code = $shipCode;
        $order->ship_sn = $shipSn;
        $order->ship_time = now()->toDateTimeString();
        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }
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

    public function refund($userId, $orderId)
    {
        $order = $this->getUserOrderById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if (!$order->canRefundHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '该订单不能申请退款');
        }

        DB::transaction(function () use ($order) {
            try {
                $refundParams = [
                    'transaction_id' => $order->pay_id,
                    'out_refund_no' => time(),
                    'total_fee' => bcmul($order->payment_amount, 100),
                    'refund_fee' => bcmul($order->payment_amount, 100),
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

                // todo 通知商家

                // 删除佣金记录
                CommissionService::getInstance()->deletePaidListByOrderIds([$order->id]);
                GiftCommissionService::getInstance()->deletePaidListByOrderIds([$order->id]);
                TeamCommissionService::getInstance()->deletePaidListByOrderIds([$order->id]);
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
                'total_fee' => bcmul($actualRefundAmount, 100),
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

            // todo 通知商家

            // 删除佣金记录
            CommissionService::getInstance()->deletePaidListByOrderIds([$order->id]);
            GiftCommissionService::getInstance()->deletePaidListByOrderIds([$order->id]);
            TeamCommissionService::getInstance()->deletePaidListByOrderIds([$order->id]);

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
            ->paginate($input->limit, $columns, 'page', $input->page);;
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
            ->whereIn('status', [201, 301, 401, 402, 403, 501])
            ->get($columns);
    }

    public function getTodayOrderingUserCountByUserIds(array $userIds)
    {
        return Order::query()
            ->whereIn('user_id', $userIds)
            ->whereDate('created_at', Carbon::today())
            ->whereIn('status', [201, 301, 401, 402, 403, 501])
            ->distinct('user_id')
            ->count('user_id');
    }

    public function getTodayOrderListByUserIds(array $userIds, $columns = ['*'])
    {
        return Order::query()
            ->whereIn('user_id', $userIds)
            ->whereDate('created_at', Carbon::today())
            ->whereIn('status', [201, 301, 401, 402, 403, 501])
            ->get($columns);
    }
}
