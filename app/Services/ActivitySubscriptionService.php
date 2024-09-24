<?php

namespace App\Services;

use App\Models\ActivitySubscription;
use App\Utils\CodeResponse;

class ActivitySubscriptionService extends BaseService
{
    public function create($userId, $activityId)
    {
        $subscription = $this->getUserSubscription($userId, $activityId);
        if (!is_null($subscription)) {
            $this->throwBusinessException(CodeResponse::DATA_EXISTED, '已订阅，请勿重复订阅');
        }

        $subscription = ActivitySubscription::new();
        $subscription->user_id = $userId;
        $subscription->activity_id = $activityId;
        $subscription->save();
        return $subscription;
    }

    public function getUserList($userId, $columns = ['*'])
    {
        return ActivitySubscription::query()->where('user_id', $userId)->get($columns);
    }

    public function getUserSubscription($userId, $activityId, $columns = ['*'])
    {
        return ActivitySubscription::query()->where('user_id', $userId)->where('activity_id', $activityId)->first($columns);
    }

    public function deleteList($activityId)
    {
        return ActivitySubscription::query()->where('activity_id', $activityId)->delete();
    }
}
