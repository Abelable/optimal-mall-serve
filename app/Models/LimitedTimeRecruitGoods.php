<?php

namespace App\Models;

/**
 * App\Models\LimitedTimeRecruitGoods
 *
 * @property int $id
 * @property int $category_id 分类id
 * @property int $goods_id 商品id
 * @property string $goods_cover 商品图片
 * @property string $goods_name 商品名称
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|LimitedTimeRecruitGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LimitedTimeRecruitGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|LimitedTimeRecruitGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LimitedTimeRecruitGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|LimitedTimeRecruitGoods whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LimitedTimeRecruitGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LimitedTimeRecruitGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LimitedTimeRecruitGoods whereGoodsCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LimitedTimeRecruitGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LimitedTimeRecruitGoods whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LimitedTimeRecruitGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LimitedTimeRecruitGoods whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LimitedTimeRecruitGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|LimitedTimeRecruitGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|LimitedTimeRecruitGoods withoutTrashed()
 * @mixin \Eloquent
 */
class LimitedTimeRecruitGoods extends BaseModel
{
}
