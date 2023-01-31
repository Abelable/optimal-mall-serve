<?php

namespace App\Services;

use App\Models\FreightTemplate;
use App\Utils\Inputs\PageInput;

class FreightTemplateService extends BaseService
{
    public function getListByUserId($userId, $columns = ['*'])
    {
        return FreightTemplate::query()->where('user_id', $userId)->get($columns);
    }

    public function getFreightTemplateById($id, $columns = ['*'])
    {
        return FreightTemplate::query()->find($id, $columns);
    }
}
