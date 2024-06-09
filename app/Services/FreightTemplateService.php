<?php

namespace App\Services;

use App\Models\FreightTemplate;
use App\Utils\Inputs\Admin\NamePageInput;

class FreightTemplateService extends BaseService
{
    public function getFreightTemplateList(NamePageInput $input, $columns = ['*'])
    {
        $query = FreightTemplate::query();
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        return $query
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
