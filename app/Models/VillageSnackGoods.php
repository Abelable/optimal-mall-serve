<?php

namespace App\Models;

/**
 * App\Models\VillageSnackGoods
 *
 * @property int $id
 * @property int $goods_id 商品id
 * @property string $goods_cover 商品图片
 * @property string $goods_name 商品名称
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|VillageSnackGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VillageSnackGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|VillageSnackGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|VillageSnackGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|VillageSnackGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageSnackGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageSnackGoods whereGoodsCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageSnackGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageSnackGoods whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageSnackGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageSnackGoods whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageSnackGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|VillageSnackGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|VillageSnackGoods withoutTrashed()
 * @mixin \Eloquent
 */
class VillageSnackGoods extends BaseModel
{
}
