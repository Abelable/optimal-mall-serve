<?php

namespace App\Models;

/**
 * App\Models\ThemeZone
 *
 * @property int $id
 * @property int $status 状态: 1-显示,2-隐藏
 * @property string $name 主题名称
 * @property string $cover 主题封面
 * @property string $bg 主题背景
 * @property int $scene 链接跳转场景值：1-主题商品页，2-h5活动页，3-原生活动页
 * @property string $param 链接参数值
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZone newQuery()
 * @method static \Illuminate\Database\Query\Builder|ThemeZone onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZone query()
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZone whereBg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZone whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZone whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZone whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZone whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZone whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZone whereParam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZone whereScene($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZone whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZone whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ThemeZone whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ThemeZone withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ThemeZone withoutTrashed()
 * @mixin \Eloquent
 */
class ThemeZone extends BaseModel
{
}
