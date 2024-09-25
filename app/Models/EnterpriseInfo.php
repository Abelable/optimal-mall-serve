<?php

namespace App\Models;

/**
 * App\Models\EnterpriseInfo
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $status 申请状态：0-待审核，1-审核通过，2-审核失败
 * @property string $failure_reason 审核失败原因
 * @property string $name 姓名
 * @property string $bank_name 银行名称
 * @property string $bank_card_code 银行卡号
 * @property string $bank_address 银行地址
 * @property string $business_license_photo 营业执照照片
 * @property string $id_card_front_photo 身份证正面照片
 * @property string $id_card_back_photo 身份证反面照片
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|EnterpriseInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseInfo whereBankAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseInfo whereBankCardCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseInfo whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseInfo whereBusinessLicensePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseInfo whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseInfo whereIdCardBackPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseInfo whereIdCardFrontPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseInfo whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseInfo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnterpriseInfo whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|EnterpriseInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EnterpriseInfo withoutTrashed()
 * @mixin \Eloquent
 */
class EnterpriseInfo extends BaseModel
{
}
