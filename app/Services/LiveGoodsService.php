<?php

namespace App\Services\Media\Live;

use App\Models\LiveGoods;
use App\Services\BaseService;
use App\Utils\Inputs\PageInput;

class LiveGoodsService extends BaseService
{
    public function newGoods($roomId, $goodsId)
    {
        $goods = LiveGoods::new();
        $goods->room_id = $roomId;
        $goods->goods_id = $goodsId;
        $goods->save();
        return $goods;
    }

    public function list($roomId, $columns = ['*'])
    {
        return LiveGoods::query()->where('room_id', $roomId)->get($columns);
    }

    public function pageList($roomId, PageInput $input, $columns = ['*'])
    {
        return LiveGoods::query()
            ->where('room_id', $roomId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function goods($roomId, $goodsId, $columns = ['*'])
    {
        return LiveGoods::query()->where('room_id', $roomId)->where('goods_id', $goodsId)->first($columns);
    }

    public function goodsIds($roomId)
    {
        $list = $this->list($roomId);
        return $list->pluck('goods_id')->toArray();
    }

    public function deleteGoods($roomId, $goodsIds)
    {
        return LiveGoods::query()->where('room_id', $roomId)->whereIn('goods_id', $goodsIds)->delete();
    }

    public function hotGoods($roomId, $columns = ['*'])
    {
        return LiveGoods::query()->where('room_id', $roomId)->where('is_hot', 1)->first($columns);
    }

    public function hotGoodsId($roomId)
    {
        $goods = $this->hotGoods($roomId);
        return $goods->goods_id ?? 0;
    }
}
