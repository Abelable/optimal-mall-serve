<?php

namespace App\Services;

use App\Jobs\CommissionConfirm;
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
use Illuminate\Support\Carbon;
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
        $commission->refund_status = $cartGoods->refund_status;
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

    public function updateListToOrderConfirmStatus($orderIds)
    {
        $commissionList = $this->getPaidListByOrderIds($orderIds);
        return $commissionList->map(function (Commission $commission) {
            if ($commission->refund_status == 1) {
                // 7天无理由商品：确认收货7天后更新佣金状态
                dispatch(new CommissionConfirm($commission->id));
            } else {
                $commission->status = 2;
                $commission->save();
            }
            return $commission;
        });
    }

    public function updateToOrderConfirmStatus($id)
    {
        $commission = $this->getCommissionById($id);
        if (is_null($commission)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '佣金记录不存在或已删除');
        }
        $commission->status = 2;
        $commission->save();
        return $commission;
    }

    public function deletePaidListByOrderIds(array $orderIds)
    {
        return Commission::query()->where('status', 1)->whereIn('order_id', $orderIds)->delete();
    }

    public function getPaidListByOrderIds(array $orderIds, $columns = ['*'])
    {
        return Commission::query()
            ->where('status', 1)
            ->whereIn('order_id', $orderIds)
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

    public function getUserCommissionSum($userId, $status)
    {
        return $this->getUserCommissionQuery($userId, $status)->sum('commission');
    }

    public function getUserCommissionQuery($userId, $status)
    {
        return Commission::query()
            ->where('user_id', $userId)
            ->orWhere('superior_id', $userId)
            ->where('status', $status);
    }

    public function getUserCommissionQueryByTimeType($userId, $timeType)
    {
        $query = Commission::query()
            ->where('user_id', $userId)
            ->orWhere('superior_id', $userId);

        switch ($timeType) {
            case 1:
                $query = $query->whereDate('created_at', Carbon::today());
                break;
            case 2:
                $query = $query->whereDate('created_at', Carbon::yesterday());
                break;
            case 3:
                $query = $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()]);
                break;
            case 4:
                $query = $query->whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()]);
                break;
        }
        return $query;
    }
}
