<?php

namespace App\Services;

use App\Models\BankCard;

class BankCardService extends BaseService
{
    public function getUserBankCard($userId, $columns=['*'])
    {
        return BankCard::query()->where('user_id', $userId)->first($columns);
    }
}
