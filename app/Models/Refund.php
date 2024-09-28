<?php

namespace App\Models;

/**
 * App\Models\Refund
 *
 * @property int $id
 * @property int $status 申请状态：0-待审核，1-审核通过，等待买家寄回，2-买家已寄出，待确认，3-退款成功，4-审核失败
 * @property string $failure_reason 审核失败原因
 * @property int $user_id 用户id
 * @property int $order_id 订单id
 * @property string $order_sn 订单编号
 * @property int $goods_id 商品id
 * @property int $coupon_id 优惠券id
 * @property float $refund_amount 退款金额
 * @property int $refund_type 售后类型：1-仅退款，2-退货退款
 * @property string $refund_reason 退款说明
 * @property string $image_list 图片说明
 * @property string $ship_code 快递公司编号
 * @property string $ship_sn 快递编号
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Refund newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Refund newQuery()
 * @method static \Illuminate\Database\Query\Builder|Refund onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Refund query()
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereRefundAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereRefundReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereRefundType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereShipCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereShipSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Refund withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Refund withoutTrashed()
 * @mixin \Eloquent
 */
class Refund extends BaseModel
{
}
