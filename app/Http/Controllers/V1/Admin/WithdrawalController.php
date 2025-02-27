<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Services\BankCardService;
use App\Services\CommissionService;
use App\Services\GiftCommissionService;
use App\Services\TeamCommissionService;
use App\Services\UserService;
use App\Services\WithdrawalService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\WithdrawalPageInput;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yansongda\LaravelPay\Facades\Pay;

class WithdrawalController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var WithdrawalPageInput $input */
        $input = WithdrawalPageInput::new();
        $page = WithdrawalService::getInstance()->getList($input);
        $recordList = collect($page->items());

        $userIds = $recordList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getListByIds($userIds, ['id', 'avatar', 'nickname'])->keyBy('id');
        $bankCardList = BankCardService::getInstance()->getListByUserIds($userIds, ['user_id', 'code', 'name', 'bank_name'])->keyBy('user_id');

        $list = $recordList->map(function (Withdrawal $withdrawal) use ($bankCardList, $userList) {
            $userInfo = $userList->get($withdrawal->user_id);
            $withdrawal['userInfo'] = $userInfo;
            if ($withdrawal->path == 2) {
                $bankCard = $bankCardList->get($withdrawal->user_id);
                unset($bankCard->user_id);
                $withdrawal['bankCardInfo'] = $bankCard;
            }
            unset($withdrawal->user_id);
            return $withdrawal;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $record = WithdrawalService::getInstance()->getRecordById($id);
        if (is_null($record)) {
            return $this->fail(CodeResponse::NOT_FOUND, '提现申请不存在');
        }
        return $this->success($record);
    }

    public function approved()
    {
        $id = $this->verifyRequiredId('id');
        $record = WithdrawalService::getInstance()->getRecordById($id);
        if (is_null($record)) {
            return $this->fail(CodeResponse::NOT_FOUND, '提现申请不存在');
        }

        $user = UserService::getInstance()->getUserById($record->user_id);

        // 校验提现金额
        if ($record->scene == 3) {
            $giftCommissionSum = GiftCommissionService::getInstance()->getCommissionSumByWithdrawalId($record->user_id, $record->id);
            $teamCommissionSum = 0;
            if ($user->promoterInfo->level > 1) {
                $teamCommissionSum = TeamCommissionService::getInstance()->getCommissionSumByWithdrawalId($record->id);
            }
            $commissionSum = bcadd($giftCommissionSum, $teamCommissionSum, 2);
        } else {
            $commissionSum = CommissionService::getInstance()->getCommissionSumByWithdrawalId($record->id);
        }
        if (bccomp($commissionSum, $record->withdraw_amount, 2) != 0) {
            $errMsg = "用户（ID：{$record->user_id}）提现金额（{$record->withdraw_amount}）与实际可提现金额（{$commissionSum}）不一致，请检查";
            Log::error($errMsg);
            return $this->fail(CodeResponse::INVALID_OPERATION, $errMsg);
        }

        DB::transaction(function () use ($user, $record) {
            if ($record->scene == 3) {
                GiftCommissionService::getInstance()->settleCommissionByWithdrawalId($record->id);
                if ($user->promoterInfo->level > 1) {
                    TeamCommissionService::getInstance()->settleCommissionByWithdrawalId($record->id);
                }
            } else {
                CommissionService::getInstance()->settleCommissionByWithdrawalId($record->id);
            }

            $record->status = 1;
            $record->save();

            if ($record->path == 1) {
                // todo 微信转账
                $params = [
                    'partner_trade_no' => time(),
                    'openid' => $user->openid,
                    'check_name' => 'NO_CHECK',
                    'amount' => bcmul($record->actual_amount, 100),
                    'desc' => '佣金提现',
                ];
                $result = Pay::wechat()->transfer($params);
                Log::info('commission_wx_transfer', $result->toArray());
            }
        });

        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');
        $record = WithdrawalService::getInstance()->getRecordById($id);
        if (is_null($record)) {
            return $this->fail(CodeResponse::NOT_FOUND, '提现申请不存在');
        }

        $user = UserService::getInstance()->getUserById($record->user_id);
        DB::transaction(function () use ($reason, $user, $record) {
            if ($record->scene == 3) {
                GiftCommissionService::getInstance()->restoreUserCommission($record->user_id);
                if ($user->promoterInfo->level > 1) {
                    TeamCommissionService::getInstance()->restoreUserCommission($record->user_id);
                }
            } else {
                CommissionService::getInstance()->restoreUserCommission($record->user_id, $record->scene);
            }

            $record->status = 2;
            $record->failure_reason = $reason;
            $record->save();
        });

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $record = WithdrawalService::getInstance()->getRecordById($id);
        if (is_null($record)) {
            return $this->fail(CodeResponse::NOT_FOUND, '提现申请不存在');
        }
        $record->delete();
        return $this->success();
    }

    public function getPendingCount()
    {
        $count = WithdrawalService::getInstance()->getCountByStatus(0);
        return $this->success($count);
    }
}
