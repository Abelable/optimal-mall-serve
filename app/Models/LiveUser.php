<?php

namespace App\Models;

/**
 * App\Models\LiveUser
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $avatar 头像
 * @property string $nickname 昵称
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|LiveUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveUser newQuery()
 * @method static \Illuminate\Database\Query\Builder|LiveUser onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveUser whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveUser whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveUser whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|LiveUser withTrashed()
 * @method static \Illuminate\Database\Query\Builder|LiveUser withoutTrashed()
 * @mixin \Eloquent
 */
class LiveUser extends BaseModel
{
}
