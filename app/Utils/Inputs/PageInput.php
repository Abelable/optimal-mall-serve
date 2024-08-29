<?php

namespace App\Utils\Inputs;
use Illuminate\Validation\Rule;

class PageInput extends BaseInput
{
    public $page = 1;
    public $limit = 10;
    public $sort = 'id';
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
