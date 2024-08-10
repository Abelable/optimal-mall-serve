<?php

namespace App\Models;

/**
 * App\Models\RuralGoods
 *
 * @property int $id
 * @property int $region_id 地区id
 * @property int $goods_id 商品id
 * @property string $goods_cover 商品图片
 * @property string $goods_name 商品名称
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|RuralGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RuralGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|RuralGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RuralGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|RuralGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuralGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuralGoods whereGoodsCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuralGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuralGoods whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuralGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuralGoods whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuralGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|RuralGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|RuralGoods withoutTrashed()
 * @mixin \Eloquent
 */
class RuralGoods extends BaseModel
{
}
