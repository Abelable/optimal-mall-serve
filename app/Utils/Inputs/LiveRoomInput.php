<?php

namespace App\Utils\Inputs;

class LiveRoomInput extends BaseInput
{
    public $title;
    public $cover;
    public $shareCover;
    public $direction;
    public $goodsIds;
    public $noticeTime;

    public function rules()
    {
        return [
            'title' => 'required|string',
            'cover' => 'required|string',
            'shareCover' => 'required|string',
            'direction' => 'required|integer|in:1,2',
            'goodsIds' => 'array',
            'noticeTime' => 'string',
        ];
    }
}
