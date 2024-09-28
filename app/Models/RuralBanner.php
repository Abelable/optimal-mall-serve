<?php

namespace App\Models;

/**
 * App\Models\RuralBanner
 *
 * @property int $id
 * @property int $status 活动状态：1-活动中，2-活动结束
 * @property string $cover 活动封面
 * @property string $desc 活动描述
 * @property string $scene 链接跳转场景值：1-h5活动，2-商品详情
 * @property string $param 链接参数值
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|RuralBanner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RuralBanner newQuery()
 * @method static \Illuminate\Database\Query\Builder|RuralBanner onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RuralBanner query()
 * @method static \Illuminate\Database\Eloquent\Builder|RuralBanner whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuralBanner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuralBanner whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuralBanner whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuralBanner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuralBanner whereParam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuralBanner whereScene($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuralBanner whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuralBanner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|RuralBanner withTrashed()
 * @method static \Illuminate\Database\Query\Builder|RuralBanner withoutTrashed()
 * @mixin \Eloquent
 */
class RuralBanner extends BaseModel
{
}
