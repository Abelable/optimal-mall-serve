<?php

namespace App\Services;

use App\Jobs\OverTimeCancelCommission;
use App\Models\Address;
use App\Models\CartGoods;
use App\Models\Coupon;
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
    public function createCommission($scene, $userId, $orderId, CartGoods $cartGoods, $freightTemplateList, Address $address, $superiorId = null, Coupon $coupon = null)
    {
        $couponDenomination = 0;
        if (!is_null($coupon) && $coupon->goods_id == $cartGoods->goods_id) {
            $couponDenomination = $coupon->denomination;
        }

        $totalPrice = bcmul($cartGoods->price, $cartGoods->number, 2);

        $freightTemplate = $freightTemplateList->get($cartGoods->freight_template_id);
        $freightPrice = OrderService::getInstance()->calcFreightPrice($freightTemplate, $address, $totalPrice, $cartGoods->number);

        $paymentAmount = bcadd($totalPrice, $freightPrice, 2);
        $paymentAmount = bcsub($paymentAmount, $couponDenomination, 2);

        $commission = Commission::new();
        $commission->scene = $scene;
        $commission->user_id = $userId;
        if (!is_null($superiorId)) {
            $commission->superior_id = $superiorId;
        }
        $commission->order_id = $orderId;
        $commission->goods_id = $cartGoods->goods_id;
        $commission->selected_sku_name = $cartGoods->selected_sku_name;
        $commission->goods_price = $cartGoods->price;
        $commission->goods_number = $cartGoods->number;
        $commission->total_price = $totalPrice;
        $commission->freight_price = $freightPrice;
        $commission->coupon_denomination = $couponDenomination;
        $commission->payment_amount = $paymentAmount;
        $commission->commission_rate = $cartGoods->commission_rate;
        $commission->commission = bcdiv(bcmul($paymentAmount, $cartGoods->commission_rate, 2), 100, 2);
        $commission->save();

        return $commission;
    }

    public function updateListToOrderPaidStatus(array $orderIds)
    {
        $commissionList = $this->getUnpaidListByIds($orderIds);
        return $commissionList->map(function (Commission $commission) {
            $commission->status = 1;
            $commission->save();
            return $commission;
        });
    }

    public function deleteUnpaidListByOrderIds(array $orderIds)
    {
        return Commission::query()->where('status', 0)->whereIn('order_id', $orderIds)->delete();
    }

    public function getUnpaidListByIds(array $orderIds, $columns = ['*'])
    {
        return Commission::query()
            ->where('status', 0)
            ->whereIn('order_id', $orderIds)
            ->get($columns);
    }

    public function updateListToOrderConfirmStatus($orderId)
    {
        $commissionList = $this->getPaidListByOrderId($orderId);
        $commissionList->map(function (Commission $commission) {
            // 商品支持7天无理由，设置7天之后变更任务
            // 商品不支持7天无理由，立即变更
        });
    }

    public function getPaidListByOrderId($orderId, $columns = ['*'])
    {
        return Commission::query()
            ->where('status', 1)
            ->where('order_id', $orderId)
            ->get($columns);
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
