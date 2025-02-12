<?php

namespace App\Models;

/**
 * App\Models\ActivityTag
 *
 * @property int $id
 * @property int $status 状态: 1-显示,2-隐藏
 * @property string $name 活动标签名称
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityTag newQuery()
 * @method static \Illuminate\Database\Query\Builder|ActivityTag onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityTag whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityTag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityTag whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityTag whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityTag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ActivityTag withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ActivityTag withoutTrashed()
 * @mixin \Eloquent
 */
class ActivityTag extends BaseModel
{
}
