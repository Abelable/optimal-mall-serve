<?php

namespace App\Models;

/**
 * App\Models\TeamCommission
 *
 * @property int $id
 * @property int $status 佣金状态：0-订单待支付，1-待结算, 2-可提现，3-提现中，4-已结算
 * @property int $path 提现方式：1-微信；2-银行卡；3-余额
 * @property int $manager_id 组织者id
 * @property int $manager_level 组织者等级
 * @property int $user_id 用户id
 * @property int $order_id 订单id
 * @property int $goods_id 商品id
 * @property int $refund_status 是否支持7天无理由：0-不支持，1-支持
 * @property string $selected_sku_name 选中的规格名称
 * @property float $goods_price 商品价格
 * @property int $goods_number 商品数量
 * @property float $total_price 商品总价
 * @property float $coupon_denomination 优惠券抵扣
 * @property float $commission_base 商品佣金计算基数
 * @property float $commission_rate 商品佣金比例%
 * @property float $commission_amount 佣金金额
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission newQuery()
 * @method static \Illuminate\Database\Query\Builder|TeamCommission onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission query()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereCommissionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereCommissionBase($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereCouponDenomination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereGoodsNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereGoodsPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereManagerLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereRefundStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereSelectedSkuName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|TeamCommission withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TeamCommission withoutTrashed()
 * @mixin \Eloquent
 */
class TeamCommission extends BaseModel
{
}
