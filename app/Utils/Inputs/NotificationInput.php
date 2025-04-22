<?php

namespace App\Utils\Inputs;

class NotificationInput extends BaseInput
{
    public $type;
    public $userId;
    public $cover;
    public $title;
    public $content;
    public $contentNum;
    public $referenceId;

    public function rules()
    {
        return [
            'type' => 'required|integer|in:1,2,3',
            'userId' => 'integer|digits_between:1,20',
            'cover' => 'string',
            'title' => 'required|string',
            'content' => 'required|string',
            'contentNum' => 'integer',
            'referenceId' => 'string',
        ];
    }
}
