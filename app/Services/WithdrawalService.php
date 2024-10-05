<?php

namespace App\Services;

use App\Models\Withdrawal;
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
}
