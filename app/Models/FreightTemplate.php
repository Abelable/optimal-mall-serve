<?php

namespace App\Models;

/**
 * App\Models\FreightTemplate
 *
 * @property int $id
 * @property string $name 模板名称
 * @property string $title 模板标题，可展示在商品详情页
 * @property int $compute_mode 计算方式：1-不计重量和件数，2-按商品件数
 * @property float $free_quota 免费额度
 * @property string $area_list 配送地区列表
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate newQuery()
 * @method static \Illuminate\Database\Query\Builder|FreightTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate whereAreaList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate whereComputeMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate whereFreeQuota($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|FreightTemplate withTrashed()
 * @method static \Illuminate\Database\Query\Builder|FreightTemplate withoutTrashed()
 * @mixin \Eloquent
 */
class FreightTemplate extends BaseModel
{
}
