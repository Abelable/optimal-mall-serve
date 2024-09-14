<?php

namespace App\Models;

use App\Utils\Traits\OrderStatusTrait;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property string $order_sn 订单编号
 * @property int $status 订单状态
 * @property string $remarks 订单备注
 * @property int $user_id 用户id
 * @property string $consignee 收件人姓名
 * @property string $mobile 收件人手机号
 * @property string $address 具体收货地址
 * @property float $goods_price 商品总价格
 * @property float $freight_price 运费
 * @property float $coupon_denomination 优惠券抵扣
 * @property float $payment_amount 支付金额
 * @property string $pay_id 支付id
 * @property string $pay_time 支付时间
 * @property string $ship_sn 发货编号
 * @property string $ship_channel 快递公司
 * @property string $ship_time 发货时间
 * @property string $confirm_time 用户确认收货时间
 * @property string $finish_time 订单关闭时间
 * @property float $refund_amount 退款金额
 * @property string $refund_type 退款方式
 * @property string $refund_remarks 退款备注
 * @property string $refund_time 退款时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Query\Builder|Order onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereConfirmTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereConsignee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCouponDenomination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereFinishTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereFreightPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereGoodsPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePayTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRefundAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRefundRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRefundTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRefundType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShipChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShipSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShipTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Order withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Order withoutTrashed()
 * @mixin \Eloquent
 */
class Order extends BaseModel
{
    use OrderStatusTrait;
}
