<?php

namespace App\Services;

use App\Jobs\OverTimeCancelOrder;
use App\Models\Address;
use App\Models\CartGoods;
use App\Models\FreightTemplate;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Utils\CodeResponse;
use App\Utils\Enums\OrderEnums;
use App\Utils\Inputs\Admin\OrderPageInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    public function getOrderById($id, $columns = ['*'])
    {
        return Order::query()->find($id, $columns);
    }

    public function getUserOrderById($userId, $id, $columns = ['*'])
    {
        return Order::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getUnpaidList(int $userId, $orderId, $columns = ['*'])
    {
        return Order::query()
            ->where('user_id', $userId)
            ->where('id', $orderId)
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

    public function createOrder($userId, $cartGoodsList, $freightTemplateList, Address $address)
    {
        $totalPrice = 0;
        $totalFreightPrice = 0;

        /** @var CartGoods $cartGoods */
        foreach ($cartGoodsList as $cartGoods) {
            $price = bcmul($cartGoods->price, $cartGoods->number, 2);
            $totalPrice = bcadd($totalPrice, $price, 2);

            // 计算运费
            if ($cartGoods->freight_template_id == 0) {
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

            // 商品减库存
            $row = GoodsService::getInstance()->reduceStock($cartGoods->goods_id, $cartGoods->number, $cartGoods->selected_sku_index);
            if ($row == 0) {
                $this->throwBusinessException(CodeResponse::GOODS_NO_STOCK);
            }
        }

        $order = Order::new();
        $order->order_sn = $this->generateOrderSn();
        $order->status = OrderEnums::STATUS_CREATE;
        $order->user_id = $userId;
        $order->consignee = $address->name;
        $order->mobile = $address->mobile;
        $order->address = $address->region_desc . ' ' . $address->address_detail;
        $order->goods_price = $totalPrice;
        $order->freight_price = $totalFreightPrice;
        $order->payment_amount = bcadd($totalPrice, $totalFreightPrice, 2);
        $order->refund_amount = $order->payment_amount;
        $order->save();

        // 设置订单支付超时任务
        dispatch(new OverTimeCancelOrder($userId, $order->id));

        return $order->id;
    }

    public function createWxPayOrder($userId, $orderId, $openid)
    {
        $orderList = $this->getUnpaidList($userId, $orderId);
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
            'body' => 'body',
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

        return $orderList->map(function (Order $order) use ($payId) {
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
    }

    public function userCancel($userId, $orderId)
    {
        return DB::transaction(function () use ($userId, $orderId) {
            return $this->cancel($userId, $orderId);
        });
    }

    public function systemCancel($userId, $orderId)
    {
        return DB::transaction(function () use ($userId, $orderId) {
            return $this->cancel($userId, $orderId, 'system');
        });
    }

    public function cancel($userId, $orderId, $role = 'user')
    {
        $order = $this->getUserOrderById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
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

        return $order;
    }

    public function returnStock($orderId)
    {
        $goodsList = OrderGoodsService::getInstance()->getListByOrderId($orderId);
        /** @var OrderGoods $goods */
        foreach ($goodsList as $goods)
        {
            $row = GoodsService::getInstance()->addStock($goods->goods_id, $goods->number, $goods->selected_sku_index);
            if ($row == 0) {
                $this->throwUpdateFail();
            }
        }
    }

    public function confirm($userId, $orderId, $isAuto = false)
    {
        $order = $this->getOrderById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if ($order->status != OrderEnums::STATUS_SHIP) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '该订单不能被确认收货');
        }

        $order->status = $isAuto ? OrderEnums::STATUS_AUTO_CONFIRM : OrderEnums::STATUS_CONFIRM;
        $order->confirm_time = now()->toDateTimeString();
        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }

        // todo 设置7天之后打款商家的定时任务，并通知管理员及商家。中间有退货的，取消定时任务。

        return $order;
    }

    public function finish($userId, $orderId)
    {
        $order = $this->getOrderById($userId, $orderId);
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

    public function delete($userId, $orderId)
    {
        $order = $this->getOrderById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if (!$order->canDeleteHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单不能删除');
        }

        OrderGoodsService::getInstance()->delete($order->id);
        $order->delete();
    }

    public function refund($userId, $orderId)
    {
        $order = $this->getOrderById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if (!$order->canRefundHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '该订单不能申请退款');
        }

        $order->status = OrderEnums::STATUS_REFUND;

        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }

        // todo 通知商家
        // todo 开启自动退款定时任务

        return $order;
    }
}
