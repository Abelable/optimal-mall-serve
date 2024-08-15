<?php

namespace App\Models;

/**
 * App\Models\UserLevel
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $level 用户等级：0-普通用户，1-乡村推广员，2-乡村组织者C1，3-C2，4-C3，5-乡村振兴委员会
 * @property int $scene 场景值，防串改，与等级对应「等级-场景值」：0-0, 1-100, 2-201, 3-202, 4-203, 5-300
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevel newQuery()
 * @method static \Illuminate\Database\Query\Builder|UserLevel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevel query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevel whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevel whereScene($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevel whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|UserLevel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|UserLevel withoutTrashed()
 * @mixin \Eloquent
 */
class UserLevel extends BaseModel
{
}
