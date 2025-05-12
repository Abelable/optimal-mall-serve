<?php

namespace App\Models;

use App\Utils\Traits\OrderStatusTrait;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property string $order_sn 订单编号
 * @property int $status 订单状态
 * @property int $user_id 用户id
 * @property int $delivery_mode 配送方式：1-快递，2-自提
 * @property string $consignee 收件人姓名
 * @property string $mobile 收件人手机号
 * @property string $address 具体收货地址
 * @property int $pickup_address_id 提货地址id
 * @property string $pickup_time 提货时间
 * @property string $pickup_mobile 提货预留手机号
 * @property int $merchant_id 商家id
 * @property float $goods_price 商品总价格
 * @property float $freight_price 运费
 * @property int $coupon_id 优惠券id
 * @property float $coupon_denomination 优惠券抵扣
 * @property float $deduction_balance 余额抵扣
 * @property float $payment_amount 支付金额
 * @property float $total_payment_amount 总支付金额，拆单场景
 * @property string $pay_id 支付id
 * @property string $pay_time 支付时间
 * @property string $ship_channel 快递公司
 * @property string $ship_code 快递公司编号
 * @property string $ship_sn 快递编号
 * @property string $ship_time 发货时间
 * @property string $confirm_time 确认收货/使用时间
 * @property string $finish_time 订单关闭时间
 * @property float $refund_amount 退款金额
 * @property string $refund_id 退款id
 * @property string $refund_type 退款方式
 * @property string $refund_remarks 退款备注
 * @property string $refund_time 退款时间
 * @property string $remarks 订单备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderGoods[] $goodsList
 * @property-read int|null $goods_list_count
 * @property-read \App\Models\Merchant|null $merchantInfo
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderPackage[] $packages
 * @property-read int|null $packages_count
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Query\Builder|Order onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereConfirmTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereConsignee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCouponDenomination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeductionBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeliveryMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereFinishTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereFreightPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereGoodsPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePayTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePickupAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePickupMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePickupTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRefundAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRefundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRefundRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRefundTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRefundType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShipChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShipCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShipSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShipTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTotalPaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Order withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Order withoutTrashed()
 * @mixin \Eloquent
 */
class Order extends BaseModel
{
    use OrderStatusTrait;

    public function goodsList()
    {
        return $this->hasMany(OrderGoods::class);
    }

    public function merchantInfo()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function packages()
    {
        return $this->hasMany(OrderPackage::class, 'order_id');
    }
}
