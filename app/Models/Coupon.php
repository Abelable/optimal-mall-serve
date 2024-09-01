<?php

namespace App\Models;

/**
 * App\Models\Coupon
 *
 * @property int $id
 * @property int $status 优惠券状态：1-有效，2-过期，3-下架
 * @property string $name 优惠券名称
 * @property int $denomination 优惠券面额
 * @property string $description 优惠券说明
 * @property int $num_limit 优惠券商品数量门槛
 * @property int $price_limit 优惠券价格门槛
 * @property string $expiration_time 优惠券失效时间
 * @property int $goods_id 商品id
 * @property string $goods_cover 商品图片
 * @property string $goods_name 商品名称
 * @property int $received_num 优惠券领取数量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon newQuery()
 * @method static \Illuminate\Database\Query\Builder|Coupon onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon query()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereDenomination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereExpirationTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereGoodsCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereNumLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon wherePriceLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereReceivedNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Coupon withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Coupon withoutTrashed()
 * @mixin \Eloquent
 */
class Coupon extends BaseModel
{
}
