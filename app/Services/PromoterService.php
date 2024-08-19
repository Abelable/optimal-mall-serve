<?php

namespace App\Services;

use App\Models\Promoter;
use App\Utils\CodeResponse;
use App\Utils\Enums\PromoterScene;
use App\Utils\Inputs\PromoterPageInput;

class PromoterService extends BaseService
{
    public function initPromoter($userId)
    {
        $promoter = $this->getPromoterByUserId($userId);
        if (!is_null($promoter)) {
            $this->throwBusinessException(CodeResponse::DATA_EXISTED, '非新用户，无法初始化等级');
        }

        $promoter = Promoter::new();
        $promoter->user_id = $userId;
        $promoter->save();
        return $promoter;
    }

    public function toBePromoter($userId)
    {
        $promoter = $this->getExactPromoter($userId, PromoterScene::LEVEL_USER, PromoterScene::SCENE_USER);
        if (is_null($promoter)) {
            $this->throwBusinessException(CodeResponse::INVALID_OPERATION, '非普通用户，无法升级为推广员');
        }
        $promoter->level = PromoterScene::LEVEL_PROMOTER;
        $promoter->scene = PromoterScene::SCENE_PROMOTER;
        $promoter->save();
        return $promoter;
    }

    public function toBeC1Organizer($userId)
    {
        $promoter = $this->getExactPromoter($userId, PromoterScene::LEVEL_PROMOTER, PromoterScene::SCENE_PROMOTER);
        if (is_null($promoter)) {
            $this->throwBusinessException(CodeResponse::INVALID_OPERATION, '非推广员，无法升级为C1');
        }
        $promoter->level = PromoterScene::LEVEL_ORGANIZER_C1;
        $promoter->scene = PromoterScene::SCENE_ORGANIZER_C1;
        $promoter->save();
        return $promoter;
    }

    public function toBeC2Organizer($userId)
    {
        $promoter = $this->getExactPromoter($userId, PromoterScene::LEVEL_ORGANIZER_C1, PromoterScene::SCENE_ORGANIZER_C1);
        if (is_null($promoter)) {
            $this->throwBusinessException(CodeResponse::INVALID_OPERATION, '非C1，无法升级为C2');
        }
        $promoter->level = PromoterScene::LEVEL_ORGANIZER_C2;
        $promoter->scene = PromoterScene::SCENE_ORGANIZER_C2;
        $promoter->save();
        return $promoter;
    }

    public function toBeC3Organizer($userId)
    {
        $promoter = $this->getExactPromoter($userId, PromoterScene::LEVEL_ORGANIZER_C2, PromoterScene::SCENE_ORGANIZER_C2);
        if (is_null($promoter)) {
            $this->throwBusinessException(CodeResponse::INVALID_OPERATION, '非C2，无法升级为C3');
        }
        $promoter->level = PromoterScene::LEVEL_ORGANIZER_C3;
        $promoter->scene = PromoterScene::SCENE_ORGANIZER_C3;
        $promoter->save();
        return $promoter;
    }

    public function toBeCommittee($userId)
    {
        $promoter = $this->getExactPromoter($userId, PromoterScene::LEVEL_ORGANIZER_C3, PromoterScene::SCENE_ORGANIZER_C3);
        if (is_null($promoter)) {
            $this->throwBusinessException(CodeResponse::INVALID_OPERATION, '非C3，无法升级为委员会');
        }
        $promoter->level = PromoterScene::LEVEL_COMMITTEE;
        $promoter->scene = PromoterScene::SCENE_COMMITTEE;
        $promoter->save();
        return $promoter;
    }

    public function getPromoterByUserId($userId, $columns = ['*'])
    {
        return Promoter::query()->where('user_id', $userId)->first($columns);
    }

    public function getExactPromoter($userId, $level, $scene, $columns = ['*'])
    {
        return Promoter::query()->where('user_id', $userId)->where('level', $level)->where('scene', $scene)->first($columns);
    }

    public function getListByUserIds(array $userIds, $columns = ['*'])
    {
        return Promoter::query()->whereIn('user_id', $userIds)->get($columns);
    }

    public function getPromoterPage(PromoterPageInput $input, $columns = ['*'])
    {
       return Promoter::query()
           ->whereIn('level', $input->levelList)
           ->orderBy($input->sort, $input->order)
           ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getOptionsByLevelList(array $levelList, $columns = ['*'])
    {
        return Promoter::query()->whereIn('level', $levelList)->get($columns);
    }
}
