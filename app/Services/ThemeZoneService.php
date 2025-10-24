<?php

namespace App\Services;

use App\Models\ThemeZone;
use App\Utils\Inputs\PageInput;

class ThemeZoneService extends BaseService
{
    public function getTagList(PageInput $input, $columns = ['*'])
    {
        return ThemeZone::query()
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getTagById($id, $columns = ['*'])
    {
        return ThemeZone::query()->find($id, $columns);
    }

    public function getTagByName($name, $columns = ['*'])
    {
        return ThemeZone::query()->where('name', $name)->first($columns);
    }

    public function getTagOptions($columns = ['*'])
    {
        return ThemeZone::query()
            ->where('status', 1)
            ->orderBy('sort', 'desc')
            ->get($columns);
    }
}
