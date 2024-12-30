<?php

namespace App\Models;

/**
 * App\Models\VillageGiftGoods
 *
 * @property int $id
 * @property int $goods_id 商品id
 * @property string $goods_cover 商品图片
 * @property string $goods_name 商品名称
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|VillageGiftGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VillageGiftGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|VillageGiftGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|VillageGiftGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|VillageGiftGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageGiftGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageGiftGoods whereGoodsCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageGiftGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageGiftGoods whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageGiftGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageGiftGoods whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VillageGiftGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|VillageGiftGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|VillageGiftGoods withoutTrashed()
 * @mixin \Eloquent
 */
class VillageGiftGoods extends BaseModel
{
}
