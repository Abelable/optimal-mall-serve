<?php

namespace App\Services;

use App\Models\BankCard;

class BankCardService extends BaseService
{
    public function getUserBankCard($userId, $columns = ['*'])
    {
        return BankCard::query()->where('user_id', $userId)->first($columns);
    }

    public function getListByUserIds(array $userIds, $columns = ['*'])
    {
        return BankCard::query()->whereIn('user_id', $userIds)->get($columns);
    }
}
