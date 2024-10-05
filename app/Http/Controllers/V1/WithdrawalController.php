<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\CommissionService;
use App\Services\GiftCommissionService;
use App\Services\WithdrawalService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\WithdrawalInput;
use Illuminate\Support\Carbon;

class WithdrawalController extends Controller
{
    public function submit()
    {
        $date = Carbon::now()->day;
        if ($date < 25) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '每月25-31号才可提现');
        }

        /** @var WithdrawalInput $input */
        $input = WithdrawalInput::new();

        $withdrawAmount = 0;
        switch ($input->scene) {
            case 1:
                $withdrawAmount = CommissionService::getInstance()
                    ->getUserCommissionQuery($this->userId(), 2)
                    ->whereMonth('created_at', '!=', Carbon::now()->month)
                    ->where('scene', 1)
                    ->sum('commission_amount');
                break;

            case 2:
                $withdrawAmount = CommissionService::getInstance()
                    ->getUserCommissionQuery($this->userId(), 2)
                    ->whereMonth('created_at', '!=', Carbon::now()->month)
                    ->where('scene', 2)
                    ->sum('commission_amount');
                break;

            case 3:
                [$cashGiftCommission, $cashTeamCommission] = GiftCommissionService::getInstance()->cash($this->userId());
                $withdrawAmount = bcadd($cashGiftCommission, $cashTeamCommission, 2);
                break;
        }

        if ($withdrawAmount == 0) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '提现金额不能为0');
        }

        WithdrawalService::getInstance()->addWithdrawal($this->userId(), $withdrawAmount, $input);

        return $this->success();
    }
}
