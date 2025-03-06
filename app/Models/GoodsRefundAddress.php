<?php

namespace App\Models;

/**
 * App\Models\GoodsRefundAddress
 *
 * @property int $id
 * @property int $goods_id 商品id
 * @property int $refund_address_id 退货地址id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsRefundAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsRefundAddress newQuery()
 * @method static \Illuminate\Database\Query\Builder|GoodsRefundAddress onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsRefundAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsRefundAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsRefundAddress whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsRefundAddress whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsRefundAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsRefundAddress whereRefundAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsRefundAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|GoodsRefundAddress withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GoodsRefundAddress withoutTrashed()
 * @mixin \Eloquent
 */
class GoodsRefundAddress extends BaseModel
{
}
