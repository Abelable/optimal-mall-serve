<?php

namespace App\Models;

/**
 * App\Models\NewYearGoods
 *
 * @property int $id
 * @property int $goods_id 商品id
 * @property string $goods_cover 商品图片
 * @property string $goods_name 商品名称
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|NewYearGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods whereGoodsCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|NewYearGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|NewYearGoods withoutTrashed()
 * @mixin \Eloquent
 */
class NewYearGoods extends BaseModel
{
}
