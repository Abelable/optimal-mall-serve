<?php

namespace App\Models;

/**
 * App\Models\AccountChangeLog
 *
 * @property int $id
 * @property int $account_id 账户id
 * @property float $old_balance 变更前余额
 * @property float $new_balance 变更后余额
 * @property float $change_amount 变更金额
 * @property int $change_type 变更类型：1-佣金提现，2-消费，3-退款
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AccountChangeLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountChangeLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|AccountChangeLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountChangeLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountChangeLog whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountChangeLog whereChangeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountChangeLog whereChangeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountChangeLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountChangeLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountChangeLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountChangeLog whereNewBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountChangeLog whereOldBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountChangeLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|AccountChangeLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AccountChangeLog withoutTrashed()
 * @mixin \Eloquent
 */
class AccountChangeLog extends BaseModel
{
}
