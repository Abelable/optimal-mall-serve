<?php

namespace App\Models;

/**
 * App\Models\NewYearLocalRegion
 *
 * @property int $id
 * @property int $status 状态: 1-显示,2-隐藏
 * @property string $name 地区名称
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLocalRegion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLocalRegion newQuery()
 * @method static \Illuminate\Database\Query\Builder|NewYearLocalRegion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLocalRegion query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLocalRegion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLocalRegion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLocalRegion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLocalRegion whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLocalRegion whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLocalRegion whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLocalRegion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|NewYearLocalRegion withTrashed()
 * @method static \Illuminate\Database\Query\Builder|NewYearLocalRegion withoutTrashed()
 * @mixin \Eloquent
 */
class NewYearLocalRegion extends BaseModel
{
}
