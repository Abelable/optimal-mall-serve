<?php

namespace App\Services;

use App\Models\GoodsRealImage;

class GoodsRealImageService extends BaseService
{
    public function create($goodsId, $images)
    {
        $goodsRealImage = new GoodsRealImage();
        $goodsRealImage->goods_id = $goodsId;
        $goodsRealImage->image_list = json_encode($images);
        $goodsRealImage->save();
        return $goodsRealImage;
    }

    public function update($goodsId, $images)
    {
        $goodsRealImage = $this->getByGoodsId($goodsId);
        if (is_null($goodsRealImage)) {
            $goodsRealImage = GoodsRealImage::new();
            $goodsRealImage->goods_id = $goodsId;
        }
        $goodsRealImage->image_list = json_encode($images);
        $goodsRealImage->save();
        return $goodsRealImage;
    }

    public function getByGoodsId($goodsId, $columns = ['*'])
    {
        return GoodsRealImage::query()->where('goods_id', $goodsId)->first($columns);
    }

    public function delete($goodsId)
    {
        return GoodsRealImage::query()->where('goods_id', $goodsId)->delete();
    }
}
