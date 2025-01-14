<?php

namespace App\Models;

/**
 * App\Models\GoodsRealImage
 *
 * @property int $id
 * @property int $goods_id 商品id
 * @property string $image_list 图片列表
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsRealImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsRealImage newQuery()
 * @method static \Illuminate\Database\Query\Builder|GoodsRealImage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsRealImage query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsRealImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsRealImage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsRealImage whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsRealImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsRealImage whereImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsRealImage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|GoodsRealImage withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GoodsRealImage withoutTrashed()
 * @mixin \Eloquent
 */
class GoodsRealImage extends BaseModel
{
}
