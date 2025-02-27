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
        $query = Activity::query()->orderByRaw("FIELD(status, 0, 1, 2)");
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
        if (!is_null($input->goodsId)) {
            $query = $query->where('goods_id', $input->goodsId);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getActivityList($tag, $columns = ['*'])
    {
        return Activity::query()->whereIn('status', [0, 1])->where('tag', $tag)->orderBy('sort', 'desc')->get($columns);
    }

    public function getActivityListByStatus($status, $columns = ['*'])
    {
        return Activity::query()->where('status', $status)->orderBy('sort', 'desc')->get($columns);
    }

    public function getActivityListByGoodsIds(array $goodsIds, $statusList, $columns = ['*'])
    {
        return Activity::query()->whereIn('status', $statusList)->whereIn('goods_id', $goodsIds)->get($columns);
    }

    public function getActivityByGoodsId($goodsId, array $statusList, $columns = ['*'])
    {
        return Activity::query()->where('goods_id', $goodsId)->whereIn('status', $statusList)->first($columns);
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
        $activity->start_time = '';
        if ($activity->tag == 2) {
            $activity->tag = 1;
        }

        $activity->save();

        $openidList = ActivitySubscriptionService::getInstance()->getListByActivityId($activity->id)->pluck('openid')->toArray();
        foreach ($openidList as $openid) {
            WxMpServe::new()->sendActivityStartMsg($openid, $activity);
        }
        ActivitySubscriptionService::getInstance()->deleteList($activity->id);

        return $activity;
    }

    public function endActivity($id)
    {
        $activity = $this->getActivityById($id);
        if (is_null($activity)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '活动不存在');
        }
        $activity->status = 2;
        $activity->save();
        return $activity;
    }

    public function addActivitySales($goodsId, $goodsNumber)
    {
        $activity = $this->getActivityByGoodsId($goodsId, [1]);
        if (is_null($activity)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '活动不存在');
        }
        $activity->sales = $activity->sales + $goodsNumber;
        $activity->save();
        return $activity;
    }

    public function handelExpiredActivity()
    {
        return Activity::query()
            ->where('status', 1)
            ->where('end_time', '<=', date('Y-m-d H:i:s', time()))
            ->update(['status' => 2]);
    }
}
