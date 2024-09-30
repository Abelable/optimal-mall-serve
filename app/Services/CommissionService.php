<?php

namespace App\Services;

use App\Jobs\CommissionConfirm;
use App\Models\CartGoods;
use App\Models\Coupon;
use App\Models\Commission;
use App\Utils\CodeResponse;
use Illuminate\Support\Carbon;

class CommissionService extends BaseService
{
    public function createCommission($scene, $userId, $orderId, CartGoods $cartGoods, $superiorId = null, Coupon $coupon = null)
    {
        $couponDenomination = 0;
        if (!is_null($coupon) && $coupon->goods_id == $cartGoods->goods_id) {
            $couponDenomination = $coupon->denomination;
        }
        $totalPrice = bcmul($cartGoods->price, $cartGoods->number, 2);
        $commissionBase = bcsub($totalPrice, $couponDenomination, 2);
        $commissionRate = bcdiv($cartGoods->commission_rate, 100, 2);
        $commissionAmount = bcmul($commissionBase, $commissionRate, 2);

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
        $commission->commission_base = $commissionBase;
        $commission->commission_rate = $cartGoods->commission_rate;
        $commission->commission_amount = $commissionAmount;
        $commission->save();

        return $commission;
    }

    public function updateListToOrderPaidStatus(array $orderIds)
    {
        $commissionList = $this->getUnpaidListByOrderIds($orderIds);
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

    public function getUnpaidListByOrderIds(array $orderIds, $columns = ['*'])
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
        return $this->getUserCommissionQuery($userId, $status)->sum('commission_amount');
    }

    public function getUserGMV($userId, $status)
    {
        return $this->getUserCommissionQuery($userId, $status)->sum('commission_base');
    }

    public function getUserCommissionQuery($userId, $status)
    {
        return Commission::query()
            ->where(function($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('superior_id', $userId);
            })->where('status', $status);
    }

    public function getUserCommissionListByTimeType($userId, $timeType, $scene = null, $columns = ['*'])
    {
        $query = $this->getUserCommissionQueryByTimeType($userId, $timeType, $scene);
        return $query->whereIn('status', [1, 2, 3])->get($columns);
    }

    /**
     * @param $userId
     * @param $timeType: 1-今日数据，2-昨日数据，3-本月数据，4-上月数据，5-上上月数据
     * @param $scene
     * @return Commission|\Illuminate\Database\Eloquent\Builder
     */
    public function getUserCommissionQueryByTimeType($userId, $timeType, $scene = null)
    {
        $query = Commission::query()
            ->where(function($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('superior_id', $userId);
            });

        if (!is_null($scene)) {
            $query = $query->where('scene', $scene);
        }

        switch ($timeType) {
            case 1:
                $query = $query->whereDate('updated_at', Carbon::today());
                break;
            case 2:
                $query = $query->whereDate('updated_at', Carbon::yesterday());
                break;
            case 3:
                $query = $query->whereBetween('updated_at', [Carbon::now()->startOfMonth(), Carbon::now()]);
                break;
            case 4:
                $query = $query->whereBetween('updated_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()]);
                break;
            case 5:
                $query = $query->whereBetween('updated_at', [Carbon::now()->subMonths(2)->startOfMonth(), Carbon::now()->subMonths(2)->endOfMonth()]);
                break;
        }
        return $query;
    }

    public function getSettledCommissionListByUserIds(array $userIds, $columns = ['*'])
    {
        return Commission::query()
            ->whereIn('user_id', $userIds)
            ->whereIn('status', [2, 3])
            ->get($columns);
    }

    public function getSettledCommissionListBySuperiorIds(array $superiorIds, $columns = ['*'])
    {
        return Commission::query()
            ->whereIn('superior_id', $superiorIds)
            ->whereIn('status', [2, 3])
            ->get($columns);
    }

    public function getUserGMVByTimeType($userId, $timeType)
    {
        return $this->getUserCommissionQueryByTimeType($userId, $timeType)->whereIn('status', [2, 3])->sum('commission_base');
    }
}
