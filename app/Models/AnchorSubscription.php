<?php

namespace App\Models;

/**
 * App\Models\AnchorSubscription
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $openid 用户openid
 * @property int $anchor_id 主播id
 * @property int $times 订阅次数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AnchorSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnchorSubscription newQuery()
 * @method static \Illuminate\Database\Query\Builder|AnchorSubscription onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AnchorSubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|AnchorSubscription whereAnchorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnchorSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnchorSubscription whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnchorSubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnchorSubscription whereOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnchorSubscription whereTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnchorSubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnchorSubscription whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|AnchorSubscription withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AnchorSubscription withoutTrashed()
 * @mixin \Eloquent
 */
class AnchorSubscription extends BaseModel
{
}
