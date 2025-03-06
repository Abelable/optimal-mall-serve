<?php

namespace App\Models;

/**
 * App\Models\MerchantPickupAddress
 *
 * @property int $id
 * @property int $merchant_id 商家id
 * @property string $longitude 提货点经度
 * @property string $latitude 提货点纬度
 * @property string $address_detail 提货点地址
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPickupAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPickupAddress newQuery()
 * @method static \Illuminate\Database\Query\Builder|MerchantPickupAddress onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPickupAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPickupAddress whereAddressDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPickupAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPickupAddress whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPickupAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPickupAddress whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPickupAddress whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPickupAddress whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPickupAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|MerchantPickupAddress withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MerchantPickupAddress withoutTrashed()
 * @mixin \Eloquent
 */
class MerchantPickupAddress extends BaseModel
{
}
