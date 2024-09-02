<?php

namespace App\Models;

/**
 * App\Models\Activity
 *
 * @property int $id
 * @property int $status 活动状态：0-预告，1-进行中，2-结束
 * @property int $tag 活动标签：0-无标签，1-今日主推，2-活动预告
 * @property string $name 活动名称
 * @property string $start_time 活动开始时间
 * @property string $end_time 活动结束时间
 * @property int $goods_type 商品类型：1-农产品，2-爆品
 * @property int $goods_id 商品id
 * @property string $goods_cover 商品图片
 * @property string $goods_name 商品名称
 * @property int $followers 活动关注数
 * @property int $sales 活动销量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Activity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity newQuery()
 * @method static \Illuminate\Database\Query\Builder|Activity onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity query()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereFollowers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereGoodsCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereGoodsType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereSales($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Activity withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Activity withoutTrashed()
 * @mixin \Eloquent
 */
class Activity extends BaseModel
{
}
