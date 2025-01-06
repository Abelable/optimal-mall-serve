<?php

namespace App\Models;

/**
 * App\Models\Promoter
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $level 用户等级：1-乡村推广员，2-乡村组织者C1，3-C2，4-C3，5-乡村振兴委员会
 * @property int $scene 场景值，防串改，与等级对应「等级-场景值」：1-100, 2-201, 3-202, 4-203, 5-300
 * @property int $path 生成路径：1-管理后台添加，2-礼包购买，3-限时活动
 * @property string $goods_ids 礼包商品id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter newQuery()
 * @method static \Illuminate\Database\Query\Builder|Promoter onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter query()
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereGoodsIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereScene($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Promoter withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Promoter withoutTrashed()
 * @mixin \Eloquent
 */
class Promoter extends BaseModel
{
}
