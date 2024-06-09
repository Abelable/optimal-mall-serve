<?php

namespace App\Services;

use App\Models\FreightTemplate;
use App\Utils\Inputs\PageInput;

class FreightTemplateService extends BaseService
{
    public function getFreightTemplateList(PageInput $input, $columns = ['*'])
    {
        return FreightTemplate::query()
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getFreightTemplateById($id, $columns = ['*'])
    {
        return FreightTemplate::query()->find($id, $columns);
    }

    public function getListByIds(array $Ids, $columns = ['*'])
    {
        return FreightTemplate::query()->whereIn('id', $Ids)->get($columns);
    }

    public function getFreightTemplateOptions($columns = ['*'])
    {
        return FreightTemplate::query()->orderBy('id', 'asc')->get($columns);
    }
}
