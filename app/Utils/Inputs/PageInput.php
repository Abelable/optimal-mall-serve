<?php

namespace App\Utils\Inputs;

class PageInput extends BaseInput
{
    public $page = 1;
    public $limit = 10;
    public $sort = 'created_at';
    public $order = 'desc';

    public function rules()
    {
        return [
            'page' => 'integer',
            'limit' => 'integer',
            'sort' => 'string',
            'order' => Rule::in(['desc', 'asc'])
        ];
    }
}
