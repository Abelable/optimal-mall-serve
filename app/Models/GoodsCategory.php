<?php

namespace App\Models;

/**
 * App\Models\GoodsCategory
 *
 * @property int $id
 * @property string $name 商品分类名称
 * @property int $min_leader_commission_rate 最小团队长佣金比例
 * @property int $max_leader_commission_rate 最大团队长佣金比例
 * @property int $min_share_commission_rate 最小分享佣金比例
 * @property int $max_share_commission_rate 最大分享佣金比例
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|GoodsCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereMaxLeaderCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereMaxShareCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereMinLeaderCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereMinShareCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|GoodsCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GoodsCategory withoutTrashed()
 * @mixin \Eloquent
 */
class GoodsCategory extends BaseModel
{
}
