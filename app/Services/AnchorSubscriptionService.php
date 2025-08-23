<?php

namespace App\Services;

use App\Models\AnchorSubscription;

class AnchorSubscriptionService extends BaseService
{
    public function create($userId, $openid, $anchorId)
    {
        $subscription = $this->getUserSubscription($userId, $anchorId);
        if (!is_null($subscription)) {
            $subscription->times = $subscription->times + 1;
        } else {
            $subscription = AnchorSubscription::new();
            $subscription->user_id = $userId;
            $subscription->openid = $openid;
            $subscription->anchor_id = $anchorId;
        }
        $subscription->save();

        return $subscription;
    }

    public function getListByAnchorId($anchorId, $columns = ['*'])
    {
        return AnchorSubscription::query()
            ->where('anchor_id', $anchorId)
            ->where('times', '!=', 0)
            ->get($columns);
    }

    public function getUserList($userId, $columns = ['*'])
    {
        return AnchorSubscription::query()->where('user_id', $userId)->get($columns);
    }

    public function getUserSubscription($userId, $anchorId, $columns = ['*'])
    {
        return AnchorSubscription::query()
            ->where('user_id', $userId)
            ->where('anchor_id', $anchorId)
            ->first($columns);
    }
}
