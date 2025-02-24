<?php

namespace App\Services;

use App\Models\Account;

class AccountService extends BaseService
{
    public function getUserAccount($userId, $columns = ['*'])
    {
        return Account::query()->where('user_id', $userId)->first($columns);
    }

    public function createUserAccount($userId): Account
    {
        $account = new Account();
        $account->user_id = $userId;
        $account->save();
        return $account;
    }
}
