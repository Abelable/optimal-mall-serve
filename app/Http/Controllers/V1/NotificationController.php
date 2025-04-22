<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    protected $except = [];

    public function unreadCount()
    {
        $count = NotificationService::getInstance()->getUnreadNotificationCount($this->userId());
        return $this->success($count);
    }

    public function list()
    {
        $list = NotificationService::getInstance()->getListByUserId($this->userId());
        return $this->success($list);
    }

    public function clear()
    {
        $id = $this->verifyRequiredId('id');
        NotificationService::getInstance()->clearNotification($id);
        return $this->success();
    }

    public function clearAll()
    {
        NotificationService::getInstance()->clearUserNotifications($this->userId());
        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        NotificationService::getInstance()->deleteNotification($id);
        return $this->success();
    }
}
