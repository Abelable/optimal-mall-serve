<?php

namespace App\Services;

use App\Models\Promoter;
use App\Utils\CodeResponse;
use App\Utils\Enums\PromoterScene;
use App\Utils\Inputs\Admin\UserPageInput;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\SearchPageInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PromoterService extends BaseService
{
    public function adminCreate($userId, $level, $scene)
    {
        $promoter = Promoter::new();
        $promoter->user_id = $userId;
        $promoter->level = $level;
        $promoter->scene = $scene;
        $promoter->path = 1;
        $promoter->save();
        return $promoter;
    }

    public function toBePromoter($userId, $path, array $goodsIds)
    {
        $promoter = $this->getExactPromoter($userId, PromoterScene::LEVEL_PROMOTER, PromoterScene::SCENE_PROMOTER);
        if (is_null($promoter)) {
            $promoter = Promoter::new();
            $promoter->user_id = $userId;
            $promoter->level = PromoterScene::LEVEL_PROMOTER;
            $promoter->scene = PromoterScene::SCENE_PROMOTER;
            $promoter->path = $path;
            $promoter->goods_ids = implode(',', $goodsIds);
            $promoter->save();
        }
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

    public function getPromoterById($id, $columns = ['*'])
    {
        return Promoter::query()->find($id, $columns);
    }

    public function getExactPromoter($userId, $level, $scene, $columns = ['*'])
    {
        return Promoter::query()->where('user_id', $userId)->where('level', $level)->where('scene', $scene)->first($columns);
    }

    public function getListByUserIds(array $userIds, $columns = ['*'])
    {
        return Promoter::query()->whereIn('user_id', $userIds)->get($columns);
    }

    public function getPromoterPage(UserPageInput $input, $columns = ['*'])
    {
        $query = Promoter::query();
        if (!empty($input->level)) {
            $query->where('level', $input->level);
        }
       return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getOptions($columns = ['*'])
    {
        return Promoter::query()->get($columns);
    }

    public function getPromoterCountByUserIds(array $userIds)
    {
        return Promoter::query()->whereIn('user_id', $userIds)->count();
    }

    public function getPromoterListByUserIds(array $userIds, $columns = ['*'])
    {
        return Promoter::query()->whereIn('user_id', $userIds)->get($columns);
    }

    public function getPromoterPageByUserIds(array $userIds, SearchPageInput $input, $columns = ['*'])
    {
        return Promoter::query()->whereIn('user_id', $userIds)->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function promoterCountSum()
    {
        return Promoter::query()->count();
    }

    public function dailyPromoterCountList()
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(17);

        return Promoter::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as created_at'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();
    }

    public function dailyPromoterCountGrowthRate()
    {
        $query = Promoter::query();

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $todayPromoterCount = (clone $query)->whereDate('created_at', $today)->count();
        $yesterdayPromoterCount = (clone $query)->whereDate('created_at', $yesterday)->count();

        if ($yesterdayPromoterCount > 0) {
            $dailyGrowthRate = round((($todayPromoterCount - $yesterdayPromoterCount) / $yesterdayPromoterCount) * 100);
        } else {
            $dailyGrowthRate = 0;
        }

        return $dailyGrowthRate;
    }

    public function weeklyPromoterCountGrowthRate()
    {
        $query = Promoter::query();

        $startOfThisWeek = Carbon::now()->startOfWeek();
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

        $thisWeekPromoterCount = (clone $query)->whereBetween('created_at', [$startOfThisWeek, now()])->count();
        $lastWeekPromoterCount = (clone $query)->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->count();

        if ($lastWeekPromoterCount > 0) {
            $weeklyGrowthRate = round((($thisWeekPromoterCount - $lastWeekPromoterCount) / $lastWeekPromoterCount) * 100);
        } else {
            $weeklyGrowthRate = 0; // 防止除以零
        }

        return $weeklyGrowthRate;
    }

    public function getRecentlyPromoter($userId, $days = 7, $columns = ['*'])
    {
        return Promoter::query()
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->first($columns);
    }

    public function getUserPromoterByPathList($userId, array $pathList, $columns = ['*'])
    {
        return Promoter::query()->where('user_id', $userId)->whereIn('path', $pathList)->first($columns);
    }

    public function getPromoterLevelsCount()
    {
        return Promoter::query()
            ->select('level', DB::raw('COUNT(*) as number'))
            ->whereIn('level', [1, 2, 3, 4])
            ->groupBy('level')
            ->get();
    }

    public function getTopPromoterPage(PageInput $input, $columns = ['*'])
    {
        return Promoter::query()
            ->select('*', DB::raw('(commission_sum + gift_commission_sum + team_commission_sum) as total_commission'))
            ->orderByDesc('promoted_user_number')
            ->orderByDesc('total_commission')
            ->paginate($input->limit, $columns, 'page', $input->page);
    }
}
