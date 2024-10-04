<?php

namespace App\Models;

/**
 * App\Models\BankCard
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $name 姓名
 * @property string $code 银行卡号
 * @property string $bank_name 开户行
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|BankCard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankCard newQuery()
 * @method static \Illuminate\Database\Query\Builder|BankCard onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BankCard query()
 * @method static \Illuminate\Database\Eloquent\Builder|BankCard whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankCard whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankCard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankCard whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankCard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankCard whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankCard whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankCard whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|BankCard withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BankCard withoutTrashed()
 * @mixin \Eloquent
 */
class BankCard extends BaseModel
{
}
