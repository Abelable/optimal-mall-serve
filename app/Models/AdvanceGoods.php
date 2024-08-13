<?php

namespace App\Models;

/**
 * App\Models\AdvanceGoods
 *
 * @property int $id
 * @property int $goods_id 商品id
 * @property string $goods_cover 商品图片
 * @property string $goods_name 商品名称
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AdvanceGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdvanceGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|AdvanceGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AdvanceGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdvanceGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdvanceGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdvanceGoods whereGoodsCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdvanceGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdvanceGoods whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdvanceGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdvanceGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|AdvanceGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AdvanceGoods withoutTrashed()
 * @mixin \Eloquent
 */
class AdvanceGoods extends BaseModel
{
}
