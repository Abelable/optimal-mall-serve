<?php

namespace App\Services;

use App\Models\User;
use App\Utils\Inputs\Admin\UserPageInput;
use App\Utils\Inputs\SearchPageInput;
use App\Utils\Inputs\WxMpRegisterInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserService extends BaseService
{
    public function register($openid, WxMpRegisterInput $input)
    {
        $user = User::new();
        $user->openid = $openid;
        $user->avatar = $input->avatar;
        $user->nickname = $input->nickname;
        $user->gender = $input->gender;
        $user->mobile = $input->mobile;
        $user->save();
        return $user;
    }

    public function getByOpenid($openid)
    {
        return User::query()->where('openid', $openid)->first();
    }

    public function getByMobile($mobile)
    {
        return User::query()->where('mobile', $mobile)->first();
    }

    public function getUserPage(UserPageInput $input, $userIds = null, $columns = ['*'])
    {
        $query = User::query();
        if (!is_null($userIds)) {
            $query = $query->whereIn('id', $userIds);
        }
        if (!empty($input->nickname)) {
            $query = $query->where('nickname', 'like', "%$input->nickname%");
        }
        if (!empty($input->mobile)) {
            $query = $query->where('mobile', $input->mobile);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserById($id, $columns = ['*'])
    {
        return User::query()->find($id, $columns);
    }

    public function getListByIds($ids, $columns = ['*'])
    {
        return User::query()->whereIn('id', $ids)->get($columns);
    }

    public function searchPage(SearchPageInput $input)
    {
        return User::search($input->keywords)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, 'page', $input->page);
    }

    public function getNormalList($promoterIds, $columns = ['*'])
    {
        return User::query()->whereNotIn('id', $promoterIds)->get($columns);
    }

    public function getList($columns = ['*'])
    {
        return User::query()->get($columns);
    }

    public function searchList($keywords)
    {
        return User::search($keywords)->get();
    }

    public function searchUserIds($keywords)
    {
        $list = $this->searchList($keywords);
        return $list->pluck('id')->toArray();
    }

    public function searchListByUserIds(array $userIds, $keywords, $columns = ['*'])
    {
        return User::query()
            ->whereIn('id', $userIds)
            ->where(function($query) use ($keywords) {
                $query->where('nickname', 'like', "%$keywords%")
                    ->orWhere('mobile', $keywords);
            })->get($columns);
    }

    public function getPageByUserIds(array $userIds, SearchPageInput $input, $columns = ['*'])
    {
        return User::query()
            ->whereIn('id', $userIds)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function userCountSum()
    {
        return User::query()->count();
    }

    public function dailyUserCountList()
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(17);

        return User::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as created_at'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();
    }

    public function dailyUserCountGrowthRate()
    {
        $query = User::query();

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $todayUserCount = (clone $query)->whereDate('created_at', $today)->count();
        $yesterdayUserCount = (clone $query)->whereDate('created_at', $yesterday)->count();

        if ($yesterdayUserCount > 0) {
            $dailyGrowthRate = round((($todayUserCount - $yesterdayUserCount) / $yesterdayUserCount) * 100);
        } else {
            $dailyGrowthRate = 0;
        }

        return $dailyGrowthRate;
    }

    public function weeklyUserCountGrowthRate()
    {
        $query = User::query();

        $startOfThisWeek = Carbon::now()->startOfWeek();
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

        $thisWeekUserCount = (clone $query)->whereBetween('created_at', [$startOfThisWeek, now()])->count();
        $lastWeekUserCount = (clone $query)->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->count();

        if ($lastWeekUserCount > 0) {
            $weeklyGrowthRate = round((($thisWeekUserCount - $lastWeekUserCount) / $lastWeekUserCount) * 100);
        } else {
            $weeklyGrowthRate = 0; // 防止除以零
        }

        return $weeklyGrowthRate;
    }
}
