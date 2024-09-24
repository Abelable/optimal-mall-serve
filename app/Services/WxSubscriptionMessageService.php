<?php

namespace App\Services;

use App\Models\WxSubscriptionMessage;

class WxSubscriptionMessageService extends BaseService
{
    public function create($templateId, $page, $openid, $data)
    {
        $message = WxSubscriptionMessage::new();
        $message->template_id = $templateId;
        $message->page = $page;
        $message->open_id = $openid;
        $message->data = $data;
        $message->save();
        return $message;
    }

    public function getListByTemplateId($templateId, $columns = ['*'])
    {
        return WxSubscriptionMessage::query()->where('template_id', $templateId)->get($columns);
    }

    public function deleteList($templateId)
    {
        return WxSubscriptionMessage::query()->where('template_id', $templateId)->delete();
    }
}
