<?php

namespace App\Services;

use App\Jobs\OverTimeCancelCommission;
use App\Models\Address;
use App\Models\CartGoods;
use App\Models\Coupon;
use App\Models\FreightTemplate;
use App\Models\Commission;
use App\Models\CommissionGoods;
use App\Utils\CodeResponse;
use App\Utils\Enums\CommissionEnums;
use App\Utils\Inputs\Admin\CommissionPageInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommissionService extends BaseService
{
    public function getCommissionListByStatus($userId, $statusList, PageInput $input, $columns = ['*'])
    {
        $query = Commission::query()->where('user_id', $userId);
        if (count($statusList) != 0) {
            $query = $query->whereIn('status', $statusList);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getListTotal($userId, $statusList)
    {
        return Commission::query()->where('user_id', $userId)->whereIn('status', $statusList)->count();
    }

    public function getCommissionList(CommissionPageInput $input, $columns = ['*'])
    {
        $query = Commission::query()->whereIn('status', [101, 102, 103, 104, 201, 301, 401, 402]);
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

    public function getUnpaidList(int $userId, array $orderIds, $columns = ['*'])
    {
        return Commission::query()
            ->where('user_id', $userId)
            ->whereIn('id', $orderIds)
            ->where('status', CommissionEnums::STATUS_CREATE)
            ->get($columns);
    }

    public function getUnpaidListBySn(array $orderSnList, $columns = ['*'])
    {
        return Commission::query()
            ->whereIn('order_sn', $orderSnList)
            ->where('status', CommissionEnums::STATUS_CREATE)
            ->get($columns);
    }

    public function generateCommissionSn()
    {
        return retry(5, function () {
            $orderSn = date('YmdHis') . rand(100000, 999999);
            if ($this->isCommissionSnExists($orderSn)) {
                Log::warning('当前订单号已存在，orderSn：' . $orderSn);
                $this->throwBusinessException(CodeResponse::FAIL, '订单号生成失败');
            }
            return $orderSn;
        });
    }

    public function isCommissionSnExists(string $orderSn)
    {
        return Commission::query()->where('order_sn', $orderSn)->exists();
    }

    public function createCommission($userId, $cartGoodsList, $freightTemplateList, Address $address, Coupon $coupon = null)
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

            // 优惠券
            if (!is_null($coupon) && $coupon->goods_id == $cartGoods->goods_id) {
                $couponDenomination = $coupon->denomination;
            }

            // 商品减库存
            $row = GoodsService::getInstance()->reduceStock($cartGoods->goods_id, $cartGoods->number, $cartGoods->selected_sku_index);
            if ($row == 0) {
                $this->throwBusinessException(CodeResponse::GOODS_NO_STOCK);
            }
        }

        $paymentAmount = bcadd($totalPrice, $totalFreightPrice, 2);
        $paymentAmount = bcsub($paymentAmount, $couponDenomination, 2);

        $order = Commission::new();
        $order->order_sn = $this->generateCommissionSn();
        $order->status = CommissionEnums::STATUS_CREATE;
        $order->user_id = $userId;
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
        dispatch(new OverTimeCancelCommission($userId, $order->id));

        return $order->id;
    }

    public function createWxPayCommission($userId, array $orderIds, $openid)
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

        return $orderList->map(function (Commission $order) use ($payId) {
            $order->pay_id = $payId;
            $order->pay_time = now()->toDateTimeString();
            $order->status = CommissionEnums::STATUS_PAY;
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
            $orderList = $this->getUserCommissionList($userId, [$orderId]);
            if (count($orderList) == 0) {
                $this->throwBadArgumentValue();
            }
            return $this->cancel($orderList);
        });
    }

    public function systemCancel($userId, $orderId)
    {
        return DB::transaction(function () use ($userId, $orderId) {
            $orderList = $this->getUserCommissionList($userId, [$orderId]);
            if (count($orderList) == 0) {
                $this->throwBadArgumentValue();
            }
            return $this->cancel($orderList, 'system');
        });
    }

    public function adminCancel($orderIds)
    {
        return DB::transaction(function () use ($orderIds) {
            $orderList = $this->getCommissionListByIds($orderIds);
            if (count($orderList) == 0) {
                $this->throwBadArgumentValue();
            }
            return $this->cancel($orderList, 'admin');
        });
    }

    public function cancel($orderList, $role = 'user')
    {
        return $orderList->map(function (Commission $order) use ($role) {
            if ($order->status != CommissionEnums::STATUS_CREATE) {
                $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单不能取消');
            }
            switch ($role) {
                case 'system':
                    $order->status = CommissionEnums::STATUS_AUTO_CANCEL;
                    break;
                case 'admin':
                    $order->status = CommissionEnums::STATUS_ADMIN_CANCEL;
                    break;
                case 'user':
                    $order->status = CommissionEnums::STATUS_CANCEL;
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
    }

    public function returnStock($orderId)
    {
        $goodsList = CommissionGoodsService::getInstance()->getListByCommissionId($orderId);
        /** @var CommissionGoods $goods */
        foreach ($goodsList as $goods)
        {
            $row = GoodsService::getInstance()->addStock($goods->goods_id, $goods->number, $goods->selected_sku_index);
            if ($row == 0) {
                $this->throwUpdateFail();
            }
        }
    }

    public function restoreCoupon($couponId)
    {
        $userCoupon = UserCouponService::getInstance()->getUserCouponByCouponId($couponId);
        $userCoupon->status = 1;
        $userCoupon->save();
        return $userCoupon;
    }

    public function confirm($userId, $orderId, $isAuto = false)
    {
        $order = $this->getUserCommissionById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if ($order->status != CommissionEnums::STATUS_SHIP) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '该订单不能被确认收货');
        }

        $order->status = $isAuto ? CommissionEnums::STATUS_AUTO_CONFIRM : CommissionEnums::STATUS_CONFIRM;
        $order->confirm_time = now()->toDateTimeString();
        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }

        // todo 设置7天之后打款商家的定时任务，并通知管理员及商家。中间有退货的，取消定时任务。

        return $order;
    }

    public function finish($userId, $orderId)
    {
        $order = $this->getUserCommissionById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if (!$order->canFinishHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单不能设置为完成状态');
        }
        $order->status = CommissionEnums::STATUS_FINISHED;
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
            CommissionGoodsService::getInstance()->delete($order->id);
            $order->delete();
        }
    }

    public function refund($userId, $orderId)
    {
        $order = $this->getUserCommissionById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if (!$order->canRefundHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '该订单不能申请退款');
        }

        $order->status = CommissionEnums::STATUS_REFUND;

        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }

        // todo 通知商家
        // todo 开启自动退款定时任务

        return $order;
    }

    public function getCommissionById($id, $columns = ['*'])
    {
        return Commission::query()->find($id, $columns);
    }
    public function getCommissionListByIds(array $ids, $columns = ['*'])
    {
        return Commission::query()->whereIn('id', $ids)->get($columns);
    }

    public function getUserCommissionById($userId, $id, $columns = ['*'])
    {
        return Commission::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getUserCommissionList($userId, $ids, $columns = ['*'])
    {
        return Commission::query()->where('user_id', $userId)->whereIn('id', $ids)->get($columns);
    }
}
