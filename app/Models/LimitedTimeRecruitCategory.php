<?php

namespace App\Models;

/**
 * App\Models\LimitedTimeRecruitCategory
 *
 * @property int $id
 * @property int $status 状态: 1-显示,2-隐藏
 * @property string $name 分类名称
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|LimitedTimeRecruitCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LimitedTimeRecruitCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|LimitedTimeRecruitCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LimitedTimeRecruitCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|LimitedTimeRecruitCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LimitedTimeRecruitCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LimitedTimeRecruitCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LimitedTimeRecruitCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LimitedTimeRecruitCategory whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LimitedTimeRecruitCategory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LimitedTimeRecruitCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|LimitedTimeRecruitCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|LimitedTimeRecruitCategory withoutTrashed()
 * @mixin \Eloquent
 */
class LimitedTimeRecruitCategory extends BaseModel
{
}
