<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\AccountChangeLogService;
use App\Services\AccountService;
use App\Services\CommissionService;
use App\Services\GiftCommissionService;
use App\Services\TeamCommissionService;
use App\Services\TransactionService;
use App\Services\WithdrawalService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\WithdrawalInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawalController extends Controller
{
    public function submit()
    {
        /** @var WithdrawalInput $input */
        $input = WithdrawalInput::new();

        if ($input->path != 3) {
            $date = Carbon::now()->day;
            if ($date < 25) {
                return $this->fail(CodeResponse::INVALID_OPERATION, '每月25-31号才可提现');
            }
        }

        if ($input->withdrawAmount == 0) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '提现金额不能为0');
        }

        $withdrawAmount = 0;
        $commissionQuery = CommissionService::getInstance()
            ->getUserCommissionQuery([$this->userId()], [2])
            ->whereMonth('created_at', '!=', Carbon::now()->month);
        switch ($input->scene) {
            case 1:
                $withdrawAmount = $commissionQuery->where('scene', 1)->sum('commission_amount');
                break;

            case 2:
                $withdrawAmount = $commissionQuery->where('scene', 2)->sum('commission_amount');;
                break;

            case 3:
                [$cashGiftCommission, $cashTeamCommission] = GiftCommissionService::getInstance()->cash($this->userId());
                $withdrawAmount = bcadd($cashGiftCommission, $cashTeamCommission, 2);
                break;
        }

        if (bccomp($withdrawAmount, $input->withdrawAmount, 2) != 0) {
            $errMsg = "用户（ID：{$this->userId()}）提现金额（{$input->withdrawAmount}）与实际可提现金额（{$withdrawAmount}）不一致，请检查";
            Log::error($errMsg);
            return $this->fail(CodeResponse::INVALID_OPERATION, $errMsg);
        }

        DB::transaction(function () use ($withdrawAmount, $input) {
            WithdrawalService::getInstance()->addWithdrawal($this->userId(), $withdrawAmount, $input);

            if ($input->path == 3) {
                if ($input->scene == 3) {
                    GiftCommissionService::getInstance()->settleUserCommission($this->userId(), 2);
                    if ($this->user()->promoterInfo->level > 1) {
                        TeamCommissionService::getInstance()->settleUserCommission($this->userId(), 2);
                    }
                } else {
                    CommissionService::getInstance()->settleUserCommission($this->userId(), $input->scene, 2);
                }

                // 提现至余额
                AccountService::getInstance()->updateBalance($this->userId(), 1, $withdrawAmount);
            } else {
                if ($input->scene == 3) {
                    GiftCommissionService::getInstance()->withdrawUserCommission($this->userId());
                    if ($this->user()->promoterInfo->level > 1) {
                        TeamCommissionService::getInstance()->withdrawUserCommission($this->userId());
                    }
                } else {
                    CommissionService::getInstance()->withdrawUserCommission($this->userId(), $input->scene);
                }
            }
        });

        return $this->success();
    }

    public function recordList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = WithdrawalService::getInstance()->getUserRecordList($this->userId(), $input);
        return $this->successPaginate($page);
    }
}
