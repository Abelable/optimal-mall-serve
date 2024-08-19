<?php

namespace App\Models;

/**
 * App\Models\GiftGoods
 *
 * @property int $id
 * @property int $type 类型：1-认养专区，2-礼包专区
 * @property int $goods_id 商品id
 * @property string $goods_cover 商品图片
 * @property string $goods_name 商品名称
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|GiftGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GiftGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|GiftGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GiftGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|GiftGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftGoods whereGoodsCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftGoods whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftGoods whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|GiftGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GiftGoods withoutTrashed()
 * @mixin \Eloquent
 */
class GiftGoods extends BaseModel
{
}
