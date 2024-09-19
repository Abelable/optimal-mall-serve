<?php

namespace App\Services;

use App\Jobs\GiftCommissionConfirm;
use App\Models\CartGoods;
use App\Models\GiftCommission;
use App\Utils\CodeResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GiftCommissionService extends BaseService
{
    public function createCommission($userId, $orderId, CartGoods $cartGoods, $promoterId = null, $managerId = null)
    {
        $commission = GiftCommission::new();
        $commission->user_id = $userId;
        $commission->order_id = $orderId;
        $commission->goods_id = $cartGoods->goods_id;
        $commission->refund_status = $cartGoods->refund_status;
        $commission->selected_sku_name = $cartGoods->selected_sku_name;
        $commission->goods_price = $cartGoods->price;

        // 场景1：普通用户没有上级 - 生成空的佣金记录，只作为记录用
        // 场景2：普通用户上级为推广员，没有上上级，或上上级也为推广员 - 生成15%上级佣金的佣金记录
        // 场景3：普通用户上级为推广员，上上级为C级 - 生成包含15%上级佣金、5%上上级佣金的佣金记录
        // 场景4：普通用户上级为C级 - 生成包含20%上级佣金的佣金记录
        if (!is_null($promoterId)) {
            $promoterInfo = PromoterService::getInstance()->getPromoterByUserId($promoterId);
            if ($promoterInfo->level > 1) {
                $commission->promoter_commission_rate = 20;
                $commission->promoter_commission = bcmul($cartGoods->price, 0.2, 2);
            } else {
                $commission->promoter_commission_rate = 15;
                $commission->promoter_commission = bcmul($cartGoods->price, 0.15, 2);
                if (!is_null($managerId)) {
                    $managerInfo = PromoterService::getInstance()->getPromoterByUserId($managerId);
                    if ($managerInfo->level > 1) {
                        $commission->manager_id = $managerId;
                        $commission->manager_commission_rate = 5;
                        $commission->manager_commission = bcmul($cartGoods->price, 0.05, 2);
                    }
                }
            }
            $commission->promoter_id = $promoterId;
        }

        $commission->save();

        return $commission;
    }

    public function updateListToOrderPaidStatus(array $orderIds)
    {
        $commissionList = $this->getUnpaidListByOrderIds($orderIds);
        return $commissionList->map(function (GiftCommission $commission) {
            $commission->status = 1;
            $commission->save();
            return $commission;
        });
    }

    public function deleteUnpaidListByOrderIds(array $orderIds)
    {
        return GiftCommission::query()->where('status', 0)->whereIn('order_id', $orderIds)->delete();
    }

    public function getUnpaidListByOrderIds(array $orderIds, $columns = ['*'])
    {
        return GiftCommission::query()
            ->where('status', 0)
            ->whereIn('order_id', $orderIds)
            ->get($columns);
    }

    public function updateListToOrderConfirmStatus($orderIds)
    {
        $commissionList = $this->getPaidListByOrderIds($orderIds);
        return $commissionList->map(function (GiftCommission $commission) {
            if ($commission->refund_status == 1) {
                // 7天无理由商品：确认收货7天后更新佣金状态，并成为推官员
                dispatch(new GiftCommissionConfirm($commission->id));
            } else {
                $commission->status = 2;
                $commission->save();

                // 成为推官员
                PromoterService::getInstance()->toBePromoter($commission->user_id);
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

        return DB::transaction(function () use ($commission) {
            $commission->status = 2;
            $commission->save();

            // 成为推官员
            PromoterService::getInstance()->toBePromoter($commission->user_id);

            return $commission;
        });
    }

    public function deletePaidListByOrderIds(array $orderIds)
    {
        return GiftCommission::query()->where('status', 1)->whereIn('order_id', $orderIds)->delete();
    }

    public function getPaidListByOrderIds(array $orderIds, $columns = ['*'])
    {
        return GiftCommission::query()
            ->where('status', 1)
            ->whereIn('order_id', $orderIds)
            ->get($columns);
    }

    public function getCommissionById($id, $columns = ['*'])
    {
        return GiftCommission::query()->find($id, $columns);
    }
    public function getCommissionListByIds(array $ids, $columns = ['*'])
    {
        return GiftCommission::query()->whereIn('id', $ids)->get($columns);
    }

    public function getUserCommissionById($userId, $id, $columns = ['*'])
    {
        return GiftCommission::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getUserCommissionList($userId, $ids, $columns = ['*'])
    {
        return GiftCommission::query()->where('user_id', $userId)->whereIn('id', $ids)->get($columns);
    }

    public function getUserCommissionQuery($userId, $status)
    {
        return GiftCommission::query()
            ->where('user_id', $userId)
            ->orWhere('superior_id', $userId)
            ->where('status', $status);
    }

    public function getUserCommissionListByTimeType($userId, $timeType, $scene, $columns = ['*'])
    {
        $query = $this->getUserCommissionQueryByTimeType($userId, $timeType);
        return $query->whereIn('status', [1, 2, 3])->get($columns);
    }

    public function getUserCommissionQueryByTimeType($userId, $timeType, $scene = null)
    {
        $query = GiftCommission::query()
            ->where('user_id', $userId)
            ->orWhere('superior_id', $userId);

        if (!is_null($scene)) {
            $query = $query->where('scene', $scene);
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
        }
        return $query;
    }
}
