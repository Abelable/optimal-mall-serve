<?php

namespace App\Models;

/**
 * App\Models\OrderVerify
 *
 * @property int $id
 * @property int $status 核销状态：0-待核销，1-已核销
 * @property int $order_id 订单id
 * @property string $verify_code 核销码
 * @property int $verifier_id 核销人id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerify newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerify newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrderVerify onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerify query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerify whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerify whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerify whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerify whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerify whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerify whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerify whereVerifierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerify whereVerifyCode($value)
 * @method static \Illuminate\Database\Query\Builder|OrderVerify withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrderVerify withoutTrashed()
 * @mixin \Eloquent
 */
class OrderVerify extends BaseModel
{
    // 生成随机8位核销码
    public static function generateVerifyCode()
    {
        do {
            $code = rand(10000000, 99999999);
        } while (self::where('verify_code', $code)->exists());

        return $code;
    }
}
