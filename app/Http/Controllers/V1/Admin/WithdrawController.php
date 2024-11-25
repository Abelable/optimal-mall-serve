<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Services\UserService;
use App\Services\WithdrawalService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\StatusPageInput;

class WithdrawController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();
        $page = WithdrawalService::getInstance()->getList($input);
        $recordList = collect($page->items());

        $userIds = $recordList->pluck('user_id');
        $userList = UserService::getInstance()->getListByIds($userIds, ['id', 'avatar', 'nickname'])->keyBy('id');

        $list = $recordList->map(function (Withdrawal $withdrawal) use ($userList) {
            $userInfo = $userList->get($withdrawal->user_id);
            $withdrawal['userInfo'] = $userInfo;
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
        $record->status = 1;
        $record->save();
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
        $record->status = 2;
        $record->failure_reason = $reason;
        $record->save();
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
}
