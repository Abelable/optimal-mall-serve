<?php

namespace App\Services;

use App\Models\ThemeZone;
use App\Utils\Inputs\Admin\ThemeZoneInput;
use App\Utils\Inputs\PageInput;

class ThemeZoneService extends BaseService
{
    public function updateThemeZone(ThemeZone $banner, ThemeZoneInput $input)
    {
        $banner->name = $input->name;
        $banner->cover = $input->cover;
        $banner->bg = $input->bg ?? '';
        $banner->scene = $input->scene;
        $banner->param = $input->param ?? '';
        $banner->save();
        return $banner;
    }

    public function getThemeZonePage(PageInput $input, $columns = ['*'])
    {
        return ThemeZone::query()
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getThemeZoneById($id, $columns = ['*'])
    {
        return ThemeZone::query()->find($id, $columns);
    }

    public function getThemeZoneByName($name, $columns = ['*'])
    {
        return ThemeZone::query()->where('name', $name)->first($columns);
    }

    public function getThemeZoneOptions($columns = ['*'])
    {
        return ThemeZone::query()
            ->where('status', 1)
            ->orderBy('sort', 'desc')
            ->get($columns);
    }
}
