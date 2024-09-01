<?php

namespace App\Models;

/**
 * App\Models\UserCoupon
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $coupon_id 优惠券id
 * @property int $status 优惠券状态：1-未使用，2-已使用，3-已过期
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserCoupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCoupon newQuery()
 * @method static \Illuminate\Database\Query\Builder|UserCoupon onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCoupon query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCoupon whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCoupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCoupon whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCoupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCoupon whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCoupon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCoupon whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|UserCoupon withTrashed()
 * @method static \Illuminate\Database\Query\Builder|UserCoupon withoutTrashed()
 * @mixin \Eloquent
 */
class UserCoupon extends BaseModel
{
}
