<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService extends BaseService
{
    public function getListByUserId($userId, $columns = ['*'])
    {
        return Notification::query()->where('user_id', $userId)->get($columns);
    }

    public function addNotification($userId, $content)
    {
        $notification = Notification::query()->where('user_id', $userId)->where('content', $content)->first();
        if (!is_null($notification)) {
            $notification->delete();
        }

        $notification = Notification::new();
        $notification->user_id = $userId;
        $notification->content = $content;
        $notification->save();
    }

    public function clearUserNotifications($userId)
    {
        Notification::query()->where('user_id', $userId)->where('status', 0)->update(['status' => 1]);
    }

    public function deleteNotification($id)
    {
        Notification::query()->where('id', $id)->delete();
    }
}
