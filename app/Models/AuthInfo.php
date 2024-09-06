<?php

namespace App\Models;

/**
 * App\Models\AuthInfo
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $status 申请状态：0-待审核，1-审核通过（待支付），2-审核失败
 * @property string $failure_reason 审核失败原因
 * @property string $name 姓名
 * @property string $mobile 手机号
 * @property string $id_card_number 身份证号
 * @property string $id_card_front_photo 身份证正面照片
 * @property string $id_card_back_photo 身份证反面照片
 * @property string $hold_id_card_photo 手持身份证照片
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AuthInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|AuthInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthInfo whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthInfo whereHoldIdCardPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthInfo whereIdCardBackPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthInfo whereIdCardFrontPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthInfo whereIdCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthInfo whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthInfo whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|AuthInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AuthInfo withoutTrashed()
 * @mixin \Eloquent
 */
class AuthInfo extends BaseModel
{
}
