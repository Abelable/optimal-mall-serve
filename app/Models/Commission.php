<?php

namespace App\Models;

/**
 * App\Models\Commission
 *
 * @property int $id
 * @property int $status 佣金状态：0-订单待支付，1-待结算, 2-可提现，3-已结算
 * @property int $scene 场景：1-自购，2-分享
 * @property int $user_id 用户id
 * @property int $superior_id 上级id
 * @property int $order_id 订单id
 * @property int $goods_id 商品id
 * @property int $refund_status 是否支持7天无理由：0-不支持，1-支持
 * @property string $selected_sku_name 选中的规格名称
 * @property float $goods_price 商品价格
 * @property int $goods_number 商品数量
 * @property float $total_price 商品总价
 * @property float $freight_price 运费
 * @property float $coupon_denomination 优惠券抵扣
 * @property float $payment_amount 商品支付金额
 * @property float $commission_rate 商品佣金比例
 * @property float $commission 佣金金额
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Commission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Commission newQuery()
 * @method static \Illuminate\Database\Query\Builder|Commission onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Commission query()
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereCouponDenomination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereFreightPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereGoodsNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereGoodsPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereRefundStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereScene($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereSelectedSkuName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereSuperiorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Commission withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Commission withoutTrashed()
 * @mixin \Eloquent
 */
class Commission extends BaseModel
{
}
