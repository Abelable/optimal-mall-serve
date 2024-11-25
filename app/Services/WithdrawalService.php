<?php

namespace App\Services;

use App\Models\Withdrawal;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\WithdrawPageInput;
use App\Utils\Inputs\WithdrawalInput;

class WithdrawalService extends BaseService
{
    public function addWithdrawal($userId, $withdrawAmount, WithdrawalInput $input)
    {
        $taxFee = $input->scene == 1 ? 0 : bcmul($withdrawAmount, 0.06, 2);
        $actualAmount = bcsub($withdrawAmount, $taxFee + 1, 2);

        $withdrawal = Withdrawal::new();
        $withdrawal->user_id = $userId;
        $withdrawal->scene = $input->scene;
        $withdrawal->withdraw_amount = $withdrawAmount;
        $withdrawal->tax_fee = $taxFee;
        $withdrawal->handling_fee = 1;
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

    public function getList(WithdrawPageInput $input, $columns = ['*'])
    {
        $query = Withdrawal::query();
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        if (!is_null($input->scene)) {
            $query = $query->where('scene', $input->scene);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getRecordById($id, $columns = ['*'])
    {
        return Withdrawal::query()->find($id, $columns);
    }

    public function getUserApplication($userId, $columns = ['*'])
    {
        return Withdrawal::query()->where('user_id', $userId)->where('status', 0)->first($columns);
    }
}
