<?php

namespace App\Services;

use App\Models\UserLevel;
use App\Utils\CodeResponse;

class UserLevelService extends BaseService
{
    public function initLevel($userId)
    {
        if (!is_null($this->getLevelByUserId($userId))) {
            $this->throwBusinessException(CodeResponse::DATA_EXISTED, '非新用户，无法初始化等级');
        }

        $level = UserLevel::new();
        $level->user_id = $userId;
        $level->save();
        return $level;
    }

    public function getLevelByUserId($userId, $columns = ['*'])
    {
        return UserLevel::query()->where('user_id', $userId)->first($columns);
    }
}
