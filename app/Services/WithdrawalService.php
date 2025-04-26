<?php

namespace App\Services;

use App\Models\Withdrawal;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\WithdrawalPageInput;
use App\Utils\Inputs\WithdrawalInput;
use Illuminate\Support\Facades\DB;

class WithdrawalService extends BaseService
{
    public function addWithdrawal($userId, $withdrawAmount, WithdrawalInput $input)
    {
        $withdrawal = Withdrawal::new();

        if ($input->path == 3) { // 提现至余额
            $taxFee = 0;
            $actualAmount = $withdrawAmount;
            $withdrawal->status = 1;
            $withdrawal->handling_fee = 0;
        } else {
            $taxFee = $input->scene == 1 ? 0 : bcmul($withdrawAmount, 0.06, 2);
            $actualAmount = bcsub($withdrawAmount, $taxFee + 1, 2);
            $withdrawal->handling_fee = 1;
        }

        $withdrawal->user_id = $userId;
        $withdrawal->scene = $input->scene;
        $withdrawal->withdraw_amount = $withdrawAmount;
        $withdrawal->tax_fee = $taxFee;
        $withdrawal->actual_amount = $actualAmount;
        $withdrawal->path = $input->path;
        if (!empty($input->remark)) {
            $withdrawal->remark = $input->remark;
        }
        $withdrawal->save();

        return $withdrawal;
    }

    public function getUserRecordList($userId, PageInput $input, $columns = ['*'])
    {
        return Withdrawal::query()
            ->where('user_id', $userId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getList(WithdrawalPageInput $input, $columns = ['*'])
    {
        $query = Withdrawal::query();
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        if (!is_null($input->scene)) {
            $query = $query->where('scene', $input->scene);
        }
        if (!is_null($input->path)) {
            $query = $query->where('path', $input->path);
        }
        if (!is_null($input->userId)) {
            $query = $query->where('user_id', $input->userId);
        }
        return $query
            ->orderByRaw("FIELD(status, 0) DESC")
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getRecordById($id, $columns = ['*'])
    {
        return Withdrawal::query()->find($id, $columns);
    }

    public function getUserApplication($userId, $scene, $columns = ['*'])
    {
        return Withdrawal::query()
            ->where('user_id', $userId)
            ->where('scene', $scene)
            ->where('status', 0)
            ->first($columns);
    }

    public function getCountByStatus($status)
    {
        return Withdrawal::query()->where('status', $status)->count();
    }

    public function getWithdrawSumListByUserIds(array $userIds)
    {
        return Withdrawal::query()
            ->where('status', 1)
            ->whereIn('user_id', $userIds)
            ->select('user_id', DB::raw('SUM(withdraw_amount) as sum'))
            ->groupBy('user_id')
            ->get();
    }
}
