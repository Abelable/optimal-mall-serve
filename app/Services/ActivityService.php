<?php

namespace App\Services;

use App\Models\Activity;
use App\Utils\CodeResponse;
use App\Utils\Inputs\ActivityPageInput;
use App\Utils\WxMpServe;

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
        if (!is_null($input->tag)) {
            $query = $query->where('tag', $input->tag);
        }
        if (!is_null($input->goodsTag)) {
            $query = $query->where('goods_tag', $input->goodsTag);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getActivityList($tag, $columns = ['*'])
    {
        return Activity::query()->whereIn('status', [0, 1])->where('tag', $tag)->get($columns);
    }

    public function getActivityListByGoodsIds(array $goodsIds, $columns = ['*'])
    {
        return Activity::query()->whereIn('status', [0, 1])->whereIn('goods_id', $goodsIds)->get($columns);
    }

    public function getActivityByGoodsId($goodsId, $columns = ['*'])
    {
        return Activity::query()->whereIn('status', [0, 1])->where('goods_id', $goodsId)->first($columns);
    }

    public function getActivityById($id, $columns = ['*'])
    {
        return Activity::query()->find($id, $columns);
    }

    public function getAdvanceActivityById($id, $columns = ['*'])
    {
        return Activity::query()->where('status', 0)->find($id, $columns);
    }

    public function startActivity($id)
    {
        $activity = $this->getActivityById($id);
        if (is_null($activity)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '活动不存在');
        }
        $activity->status = 1;
        $activity->tag = 0;
        $activity->save();

        $openidList = ActivitySubscriptionService::getInstance()->getListByActivityId($activity->id)->pluck('openid')->toArray();

        foreach ($openidList as $openid) {
            WxMpServe::new()->sendActivityStartMsg($openid, $activity);
        }

        return $activity;
    }

    public function endActivity($id)
    {
        $activity = $this->getActivityById($id);
        if (is_null($activity)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '活动不存在');
        }
        $activity->status = 2;
        $activity->tag = 0;
        $activity->save();
        return $activity;
    }
}
