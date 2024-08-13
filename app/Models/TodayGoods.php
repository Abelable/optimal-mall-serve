<?php

namespace App\Models;

/**
 * App\Models\TodayGoods
 *
 * @property int $id
 * @property int $goods_id 商品id
 * @property string $goods_cover 商品图片
 * @property string $goods_name 商品名称
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TodayGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TodayGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|TodayGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TodayGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|TodayGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TodayGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TodayGoods whereGoodsCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TodayGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TodayGoods whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TodayGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TodayGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|TodayGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TodayGoods withoutTrashed()
 * @mixin \Eloquent
 */
class TodayGoods extends BaseModel
{
}
