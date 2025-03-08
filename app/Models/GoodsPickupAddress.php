<?php

namespace App\Models;

/**
 * App\Models\GoodsPickupAddress
 *
 * @property int $id
 * @property int $goods_id 商品id
 * @property int $pickup_address_id 自提地址id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsPickupAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsPickupAddress newQuery()
 * @method static \Illuminate\Database\Query\Builder|GoodsPickupAddress onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsPickupAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsPickupAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsPickupAddress whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsPickupAddress whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsPickupAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsPickupAddress wherePickupAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsPickupAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|GoodsPickupAddress withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GoodsPickupAddress withoutTrashed()
 * @mixin \Eloquent
 */
class GoodsPickupAddress extends BaseModel
{
}
