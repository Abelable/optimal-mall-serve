<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService extends BaseService
{
    public function getListByUserId($userId, $columns = ['*'])
    {
        return Notification::query()->where('user_id', $userId)->get($columns);
    }

    public function addNotification($type, $title, $content, $userId = 0, $referenceId = '', $cover = '', $contentNum = 1)
    {
        $notification = Notification::new();
        $notification->type = $type;
        $notification->user_id = $userId;
        $notification->cover = $cover;
        $notification->title = $title;
        $notification->content = $content;
        $notification->content_num = $contentNum;
        $notification->reference_id = $referenceId;
        $notification->save();

        return $notification;
    }

    public function clearUserNotifications($userId)
    {
        Notification::query()->where('user_id', $userId)->where('status', 0)->update(['status' => 1]);
    }

    public function clearNotification($id)
    {
        Notification::query()->where('id', $id)->where('status', 0)->update(['status' => 1]);
    }

    public function deleteNotification($id)
    {
        Notification::query()->where('id', $id)->delete();
    }
}
