<?php

namespace App\Models;

/**
 * App\Models\IntegrityBanner
 *
 * @property int $id
 * @property int $status 活动状态：1-活动中，2-活动结束
 * @property string $cover 活动封面
 * @property string $desc 活动描述
 * @property int $scene 链接跳转场景值：1-h5活动，2-景点详情，3-酒店详情，4-餐饮门店详情， 5-商品详情
 * @property string $param 链接参数值
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrityBanner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrityBanner newQuery()
 * @method static \Illuminate\Database\Query\Builder|IntegrityBanner onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrityBanner query()
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrityBanner whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrityBanner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrityBanner whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrityBanner whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrityBanner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrityBanner whereParam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrityBanner whereScene($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrityBanner whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrityBanner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|IntegrityBanner withTrashed()
 * @method static \Illuminate\Database\Query\Builder|IntegrityBanner withoutTrashed()
 * @mixin \Eloquent
 */
class IntegrityBanner extends BaseModel
{
}
