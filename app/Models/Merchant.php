<?php

namespace App\Models;

/**
 * App\Models\Merchant
 *
 * @property int $id
 * @property string $name 商家名称
 * @property string $consignee_name 收货人姓名
 * @property string $mobile 手机号
 * @property string $address_detail 收获地址
 * @property string $license 经营资质
 * @property string $supplement 补充说明
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant newQuery()
 * @method static \Illuminate\Database\Query\Builder|Merchant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant query()
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereAddressDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereConsigneeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereLicense($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereSupplement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Merchant withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Merchant withoutTrashed()
 * @mixin \Eloquent
 */
class Merchant extends BaseModel
{
}
