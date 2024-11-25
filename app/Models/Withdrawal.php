<?php

namespace App\Models;

/**
 * App\Models\Withdrawal
 *
 * @property int $id
 * @property int $status 状态：0-待审核；1-提现成功; 2-提现失败;
 * @property string $failure_reason 提现失败原因
 * @property int $user_id 用户id
 * @property int $scene 佣金类型：1-商品自购佣金；2-商品分享佣金；3-礼包佣金
 * @property float $withdraw_amount 提现金额
 * @property float $tax_fee 税费
 * @property float $handling_fee 手续费
 * @property float $actual_amount 实际到账金额
 * @property int $path 提现方式：1-微信；2-银行卡
 * @property string $remark 备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal newQuery()
 * @method static \Illuminate\Database\Query\Builder|Withdrawal onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal query()
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal whereActualAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal whereHandlingFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal whereScene($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal whereTaxFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal whereWithdrawAmount($value)
 * @method static \Illuminate\Database\Query\Builder|Withdrawal withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Withdrawal withoutTrashed()
 * @mixin \Eloquent
 */
class Withdrawal extends BaseModel
{
}
