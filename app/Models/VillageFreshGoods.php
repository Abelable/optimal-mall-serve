<?php

namespace App\Models;

/**
 * App\Models\VillageFreshGoods
 *
 * @property int $id
 * @property int $goods_id 商品id
 * @property string $goods_cover 商品图片
 * @property string $goods_name 商品名称
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|VillageFreshGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VillageFreshGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|VillageFreshGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|VillageFreshGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|VillageFreshGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageFreshGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageFreshGoods whereGoodsCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageFreshGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageFreshGoods whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageFreshGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageFreshGoods whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageFreshGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|VillageFreshGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|VillageFreshGoods withoutTrashed()
 * @mixin \Eloquent
 */
class VillageFreshGoods extends BaseModel
{
}
