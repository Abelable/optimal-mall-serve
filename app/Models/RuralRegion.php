<?php

namespace App\Models;

/**
 * App\Models\RuralRegion
 *
 * @property int $id
 * @property int $status 状态: 1-显示,2-隐藏
 * @property string $name 地区名称
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|RuralRegion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RuralRegion newQuery()
 * @method static \Illuminate\Database\Query\Builder|RuralRegion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RuralRegion query()
 * @method static \Illuminate\Database\Eloquent\Builder|RuralRegion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuralRegion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuralRegion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuralRegion whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuralRegion whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuralRegion whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuralRegion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|RuralRegion withTrashed()
 * @method static \Illuminate\Database\Query\Builder|RuralRegion withoutTrashed()
 * @mixin \Eloquent
 */
class RuralRegion extends BaseModel
{
}
