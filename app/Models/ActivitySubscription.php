<?php

namespace App\Models;

/**
 * App\Models\ActivitySubscription
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $openid 用户openid
 * @property int $activity_id 活动id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ActivitySubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivitySubscription newQuery()
 * @method static \Illuminate\Database\Query\Builder|ActivitySubscription onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivitySubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivitySubscription whereActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivitySubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivitySubscription whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivitySubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivitySubscription whereOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivitySubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivitySubscription whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|ActivitySubscription withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ActivitySubscription withoutTrashed()
 * @mixin \Eloquent
 */
class ActivitySubscription extends BaseModel
{
}
