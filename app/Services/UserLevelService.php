<?php

namespace App\Services;

use App\Models\UserLevel;
use App\Utils\CodeResponse;
use App\Utils\Enums\UserLevelScene;

class UserLevelService extends BaseService
{
    public function initUserLevel($userId)
    {
        $userLevel = $this->getUserLevelByUserId($userId);
        if (!is_null($userLevel)) {
            $this->throwBusinessException(CodeResponse::DATA_EXISTED, '非新用户，无法初始化等级');
        }

        $userLevel = UserLevel::new();
        $userLevel->user_id = $userId;
        $userLevel->save();
        return $userLevel;
    }

    public function toBePromoter($userId)
    {
        $userLevel = $this->getExactUserLevel($userId, UserLevelScene::LEVEL_USER, UserLevelScene::SCENE_USER);
        if (is_null($userLevel)) {
            $this->throwBusinessException(CodeResponse::INVALID_OPERATION, '非普通用户，无法升级为推广员');
        }
        $userLevel->level = UserLevelScene::LEVEL_PROMOTER;
        $userLevel->scene = UserLevelScene::SCENE_PROMOTER;
        $userLevel->save();
        return $userLevel;
    }

    public function toBeC1Organizer($userId)
    {
        $userLevel = $this->getExactUserLevel($userId, UserLevelScene::LEVEL_PROMOTER, UserLevelScene::SCENE_PROMOTER);
        if (is_null($userLevel)) {
            $this->throwBusinessException(CodeResponse::INVALID_OPERATION, '非推广员，无法升级为C1');
        }
        $userLevel->level = UserLevelScene::LEVEL_ORGANIZER_C1;
        $userLevel->scene = UserLevelScene::SCENE_ORGANIZER_C1;
        $userLevel->save();
        return $userLevel;
    }

    public function toBeC2Organizer($userId)
    {
        $userLevel = $this->getExactUserLevel($userId, UserLevelScene::LEVEL_ORGANIZER_C1, UserLevelScene::SCENE_ORGANIZER_C1);
        if (is_null($userLevel)) {
            $this->throwBusinessException(CodeResponse::INVALID_OPERATION, '非C1，无法升级为C2');
        }
        $userLevel->level = UserLevelScene::LEVEL_ORGANIZER_C2;
        $userLevel->scene = UserLevelScene::SCENE_ORGANIZER_C2;
        $userLevel->save();
        return $userLevel;
    }

    public function toBeC3Organizer($userId)
    {
        $userLevel = $this->getExactUserLevel($userId, UserLevelScene::LEVEL_ORGANIZER_C2, UserLevelScene::SCENE_ORGANIZER_C2);
        if (is_null($userLevel)) {
            $this->throwBusinessException(CodeResponse::INVALID_OPERATION, '非C2，无法升级为C3');
        }
        $userLevel->level = UserLevelScene::LEVEL_ORGANIZER_C3;
        $userLevel->scene = UserLevelScene::SCENE_ORGANIZER_C3;
        $userLevel->save();
        return $userLevel;
    }

    public function toBeCommittee($userId)
    {
        $userLevel = $this->getExactUserLevel($userId, UserLevelScene::LEVEL_ORGANIZER_C3, UserLevelScene::SCENE_ORGANIZER_C3);
        if (is_null($userLevel)) {
            $this->throwBusinessException(CodeResponse::INVALID_OPERATION, '非C3，无法升级为委员会');
        }
        $userLevel->level = UserLevelScene::LEVEL_COMMITTEE;
        $userLevel->scene = UserLevelScene::SCENE_COMMITTEE;
        $userLevel->save();
        return $userLevel;
    }

    public function getUserLevelByUserId($userId, $columns = ['*'])
    {
        return UserLevel::query()->where('user_id', $userId)->first($columns);
    }

    public function getExactUserLevel($userId, $level, $scene, $columns = ['*'])
    {
        return UserLevel::query()->where('user_id', $userId)->where('level', $level)->where('scene', $scene)->first($columns);
    }

    public function getListByUserIds(array $userIds, $columns = ['*'])
    {
        return UserLevel::query()->whereIn('user_id', $userIds)->get($columns);
    }
}
