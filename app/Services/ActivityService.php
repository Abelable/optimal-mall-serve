<?php

namespace App\Services;

use App\Models\Activity;
use App\Utils\Inputs\ActivityPageInput;

class ActivityService extends BaseService
{
    public function getActivityPage(ActivityPageInput $input, $columns = ['*'])
    {
        $query = Activity::query();
        if (!is_null($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        if (!is_null($input->goodsType)) {
            $query = $query->where('goods_type', $input->goodsType);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getActivityList($columns = ['*'])
    {
        return Activity::query()->get($columns);
    }

    public function getActivityListByGoodsIds(array $goodsIds, $columns = ['*'])
    {
        return Activity::query()->whereIn('goods_id', $goodsIds)->get($columns);
    }

    public function getActivityById($id, $columns = ['*'])
    {
        return Activity::query()->find($id, $columns);
    }
}
