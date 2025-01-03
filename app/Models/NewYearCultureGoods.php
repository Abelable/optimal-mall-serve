<?php

namespace App\Models;

/**
 * App\Models\NewYearCultureGoods
 *
 * @property int $id
 * @property int $goods_id 商品id
 * @property string $goods_cover 商品图片
 * @property string $goods_name 商品名称
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearCultureGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearCultureGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|NewYearCultureGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearCultureGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearCultureGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearCultureGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearCultureGoods whereGoodsCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearCultureGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearCultureGoods whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearCultureGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearCultureGoods whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearCultureGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|NewYearCultureGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|NewYearCultureGoods withoutTrashed()
 * @mixin \Eloquent
 */
class NewYearCultureGoods extends BaseModel
{
}
