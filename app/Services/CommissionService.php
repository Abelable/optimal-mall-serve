<?php

namespace App\Services;

use App\Jobs\CommissionConfirm;
use App\Models\CartGoods;
use App\Models\Coupon;
use App\Models\Commission;
use App\Utils\CodeResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
        $commission->coupon_denomination = $couponDenomination;
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

            // 更新推广员商品佣金
            if ($commission->scene == 1) {
                PromoterService::getInstance()->updateCommissionSum($commission->user_id, $commission->commission_amount);
            } else {
                PromoterService::getInstance()->updateCommissionSum($commission->superior_id, $commission->commission_amount);
            }

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

    public function getListByOrderIds(array $orderIds, $columns = ['*'])
    {
        return Commission::query()
            ->whereIn('order_id', $orderIds)
            ->get($columns);
    }

    public function updateListToOrderConfirmStatus($orderIds, $role = 'user')
    {
        $commissionList = $this->getPaidListByOrderIds($orderIds);
        return $commissionList->map(function (Commission $commission) use ($role) {
            if ($commission->refund_status == 1 && $role == 'user') {
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
        $commissionList = $this->getPaidListByOrderIds($orderIds);
        $commissionList->map(function (Commission $commission) {
            // 更新推广员商品佣金
            if ($commission->scene == 1) {
                PromoterService::getInstance()->updateCommissionSum($commission->user_id, -$commission->commission_amount);
            } else {
                PromoterService::getInstance()->updateCommissionSum($commission->superior_id, -$commission->commission_amount);
            }

            $commission->delete();
        });
    }

    public function deletePaidCommission($orderId, $goodsId)
    {
        $commission = Commission::query()
            ->where('status', 1)
            ->where('order_id', $orderId)
            ->where('goods_id', $goodsId)
            ->first();

        // 更新推广员商品佣金
        if ($commission->scene == 1) {
            PromoterService::getInstance()->updateCommissionSum($commission->user_id, -$commission->commission_amount);
        } else {
            PromoterService::getInstance()->updateCommissionSum($commission->superior_id, -$commission->commission_amount);
        }

        $commission->delete();
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

    public function getUserCommissionSum($userId, $statusList)
    {
        return $this->getUserCommissionQuery([$userId], $statusList)->sum('commission_amount');
    }

    public function getUserGMV(array $userIds, $statusList)
    {
        return $this->getUserCommissionQuery($userIds, $statusList)->sum('commission_base');
    }

    public function getUserCommissionQuery(array $userIds, array $statusList)
    {
        return Commission::query()
            ->where(function($query) use ($userIds) {
                $query->where(function($query) use ($userIds) {
                    $query->where('scene', 1)
                        ->whereIn('user_id', $userIds);
                })->orWhere(function($query) use ($userIds) {
                    $query->where('scene', 2)
                        ->whereIn('superior_id', $userIds);
                });
            })->whereIn('status', $statusList);
    }

    public function getUserCommissionListByTimeType($userId, $timeType, array $statusList, $scene = null, $columns = ['*'])
    {
        $query = $this->getUserCommissionQueryByTimeType([$userId], $timeType, $scene);
        return $query->whereIn('status', $statusList)->get($columns);
    }

    /**
     * @param array $userIds
     * @param $timeType
     * @param $scene
     * @return Commission|\Illuminate\Database\Eloquent\Builder
     */
    public function getUserCommissionQueryByTimeType(array $userIds, $timeType, $scene = null)
    {
        $query = Commission::query();

        if (!is_null($scene)) {
            if ($scene == 1) {
                $query = $query->whereIn('user_id', $userIds);
            } else {
                $query = $query->whereIn('superior_id', $userIds);
            }
            $query = $query->where('scene', $scene);
        } else {
            $query = $query->where(function($query) use ($userIds) {
                $query->where(function($query) use ($userIds) {
                    $query->where('scene', 1)
                        ->whereIn('user_id', $userIds);
                })->orWhere(function($query) use ($userIds) {
                    $query->where('scene', 2)
                        ->whereIn('superior_id', $userIds);
                });
            });
        }

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
            case 5:
                $query = $query->whereBetween('created_at', [Carbon::now()->subMonths(2)->startOfMonth(), Carbon::now()->subMonths(2)->endOfMonth()]);
                break;
        }
        return $query;
    }

    public function getSettledCommissionListByUserIds(array $userIds, $columns = ['*'])
    {
        return Commission::query()
            ->whereIn('user_id', $userIds)
            ->whereIn('status', [2, 3, 4])
            ->get($columns);
    }

    public function getSettledCommissionListBySuperiorIds(array $superiorIds, $columns = ['*'])
    {
        return Commission::query()
            ->whereIn('superior_id', $superiorIds)
            ->whereIn('status', [2, 3, 4])
            ->get($columns);
    }

    public function getUserGMVByTimeType($userId, $timeType)
    {
        return $this->getUserCommissionQueryByTimeType([$userId], $timeType)->whereIn('status', [2, 3, 4])->sum('commission_base');
    }

    public function withdrawUserCommission($userId, $scene, $withdrawalId)
    {
        $commissionList = $this->getUserCommissionQuery([$userId], [2])
            ->where('scene', $scene)
            ->whereMonth('created_at', '!=', Carbon::now()->month)
            ->get();
        /** @var Commission $commission */
        foreach ($commissionList as $commission) {
            $commission->withdrawal_id = $withdrawalId;
            $commission->status = 3;
            $commission->save();
        }
    }

    public function restoreCommissionByWithdrawalId($withdrawalId)
    {
        $commissionList = Commission::query()->where('withdrawal_id', $withdrawalId)->where('status', 3)->get();
        /** @var Commission $commission */
        foreach ($commissionList as $commission) {
            $commission->status = 2;
            $commission->save();
        }
    }

    public function settleCommissionToBalance($userId, $scene, $withdrawalId)
    {
        $commissionList = $this->getUserCommissionQuery([$userId], [2])
            ->where('scene', $scene)
            ->whereMonth('created_at', '!=', Carbon::now()->month)
            ->get();
        /** @var Commission $commission */
        foreach ($commissionList as $commission) {
            $commission->withdrawal_id = $withdrawalId;
            $commission->status = 4;
            $commission->save();
        }
    }

    public function getCommissionSumByWithdrawalId($withdrawalId, $status = 3)
    {
        return Commission::query()->where('withdrawal_id', $withdrawalId)->where('status', $status)->sum('commission_amount');
    }

    public function settleCommissionByWithdrawalId($withdrawalId)
    {
        $commissionList = Commission::query()->where('withdrawal_id', $withdrawalId)->where('status', 3)->get();
        /** @var Commission $commission */
        foreach ($commissionList as $commission) {
            $commission->status = 4;
            $commission->save();
        }
    }

    public function monthlyCommissionList()
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subMonths(12)->startOfMonth();

        return Commission::query()
            ->whereIn('status', [1, 2, 3, 4])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw("SUM(commission_amount) as sum")
            )
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy('month', 'asc')
            ->get();
    }

    public function getCommissionSumByStatus(array $statusList)
    {
        return Commission::query()->whereIn('status', $statusList)->sum('commission_amount');
    }
}
