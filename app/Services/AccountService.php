<?php

namespace App\Services;

use App\Models\Account;

class AccountService extends BaseService
{
    public function getUserAccount($userId)
    {
        $account = Account::query()->where('user_id', $userId)->first();
        if (is_null($account)) {
            $account = $this->createUserAccount($userId);
        }
        return $account;
    }

    public function createUserAccount($userId)
    {
        $account = Account::new();
        $account->user_id = $userId;
        $account->save();
        return $account;
    }

    public function updateBalance($userId, $type, $amount, $referenceId = '')
    {
        $account = $this->getUserAccount($userId);
        $oldBalance = $account->balance;
        $newBalance = bcadd($oldBalance, $amount, 2);
        $account->balance = $newBalance;
        $account->save();
        TransactionService::getInstance()->createTransaction($account->id, $type, $amount, $referenceId);
        AccountChangeLogService::getInstance()->createLog($account->id, $oldBalance, $newBalance, $type, $amount);
    }
}
