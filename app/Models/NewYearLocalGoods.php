<?php

namespace App\Models;

/**
 * App\Models\NewYearLocalGoods
 *
 * @property int $id
 * @property int $region_id 地区id
 * @property int $goods_id 商品id
 * @property string $goods_cover 商品图片
 * @property string $goods_name 商品名称
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLocalGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLocalGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|NewYearLocalGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLocalGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLocalGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLocalGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLocalGoods whereGoodsCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLocalGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLocalGoods whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLocalGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLocalGoods whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLocalGoods whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLocalGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|NewYearLocalGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|NewYearLocalGoods withoutTrashed()
 * @mixin \Eloquent
 */
class NewYearLocalGoods extends BaseModel
{
}
