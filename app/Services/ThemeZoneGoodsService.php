<?php

namespace App\Services;

use App\Models\ThemeZoneGoods;
use App\Utils\Inputs\PageInput;

class ThemeZoneGoodsService extends BaseService
{
    public function getGoodsPage(PageInput $input, $columns = ['*'])
    {
        return ThemeZoneGoods::query()
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getGoodsList($themeId, $columns = ['*'])
    {
        return ThemeZoneGoods::query()
            ->where('theme_id', $themeId)
            ->orderBy('sort', 'desc')
            ->get($columns);
    }

    public function getFilterGoodsList($themeId, $goodsIds, $columns = ['*'])
    {
        return ThemeZoneGoods::query()
            ->where('theme_id', $themeId)
            ->whereIn('goods_id', $goodsIds)
            ->get($columns);
    }

    public function getGoodsById($id, $columns = ['*'])
    {
        return ThemeZoneGoods::query()->find($id, $columns);
    }

    public function deleteById($id)
    {
        ThemeZoneGoods::query()->where('id', $id)->delete();
    }
}
