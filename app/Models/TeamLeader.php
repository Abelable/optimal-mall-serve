<?php

namespace App\Models;

/**
 * App\Models\TeamLeader
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $status 申请状态：0-待审核，1-审核通过，2-审核失败
 * @property string $failure_reason 审核失败原因
 * @property string $name 联系人姓名
 * @property string $mobile 手机号
 * @property string $email 邮箱
 * @property string $id_card_number 身份证号
 * @property string $id_card_front_photo 身份证正面照片
 * @property string $id_card_back_photo 身份证反面照片
 * @property string $hold_id_card_photo 手持身份证照片
 * @property string $qualification_photo 团长资质证明
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TeamLeader newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamLeader newQuery()
 * @method static \Illuminate\Database\Query\Builder|TeamLeader onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamLeader query()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamLeader whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamLeader whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamLeader whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamLeader whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamLeader whereHoldIdCardPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamLeader whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamLeader whereIdCardBackPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamLeader whereIdCardFrontPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamLeader whereIdCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamLeader whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamLeader whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamLeader whereQualificationPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamLeader whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamLeader whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamLeader whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|TeamLeader withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TeamLeader withoutTrashed()
 * @mixin \Eloquent
 */
class TeamLeader extends BaseModel
{
}
