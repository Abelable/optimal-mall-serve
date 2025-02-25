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

    public function createUserAccount($userId): Account
    {
        $account = new Account();
        $account->user_id = $userId;
        $account->save();
        return $account;
    }

    public function updateBalance($userId, $amount)
    {
        $account = $this->getUserAccount($userId);
        $account->balance = bcadd($account->balance, $amount, 2);
        $account->save();
        return $account;
    }
}
