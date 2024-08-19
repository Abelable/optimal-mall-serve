<?php

namespace App\Services;

use App\Models\GiftGoods;
use App\Utils\Inputs\GiftGoodsPageInput;

class GiftGoodsService extends BaseService
{
    public function getGoodsPage(GiftGoodsPageInput $input, $columns = ['*'])
    {
        return GiftGoods::query()
            ->where('type', $input->type)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getGoodsList($type, $columns = ['*'])
    {
        return GiftGoods::query()->where('type', $type)->get($columns);
    }

    public function getGoodsById($id, $columns = ['*'])
    {
        return GiftGoods::query()->find($id, $columns);
    }
}
