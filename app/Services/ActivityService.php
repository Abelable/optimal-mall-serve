<?php

namespace App\Services;

use App\Models\Activity;
use App\Utils\Inputs\PageInput;

class ActivityService extends BaseService
{
    public function getActivityPage(PageInput $input, $columns = ['*'])
    {
        return Activity::query()
            ->whereIn('status', [0, 1])
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
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
