<?php

namespace App\Models;

/**
 * App\Models\MerchantRefundAddress
 *
 * @property int $id
 * @property int $merchant_id 商家id
 * @property string $consignee_name 收件人姓名
 * @property string $mobile 手机号
 * @property string $address_detail 收件地址
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantRefundAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantRefundAddress newQuery()
 * @method static \Illuminate\Database\Query\Builder|MerchantRefundAddress onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantRefundAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantRefundAddress whereAddressDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantRefundAddress whereConsigneeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantRefundAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantRefundAddress whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantRefundAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantRefundAddress whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantRefundAddress whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantRefundAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|MerchantRefundAddress withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MerchantRefundAddress withoutTrashed()
 * @mixin \Eloquent
 */
class MerchantRefundAddress extends BaseModel
{
}
