<?php

namespace App\Models;

/**
 * App\Models\GiftCommission
 *
 * @property int $id
 * @property int $status 佣金状态：0-订单待支付，1-待结算, 2-可提现，3-提现中，4-已结算
 * @property int $withdrawal_id 提现记录id
 * @property int $user_id 用户id
 * @property int $promoter_id 推广员id
 * @property int $manager_id 组织者id
 * @property int $order_id 订单id
 * @property int $goods_id 商品id
 * @property int $refund_status 是否支持7天无理由：0-不支持，1-支持
 * @property string $selected_sku_name 选中的规格名称
 * @property float $goods_price 商品价格
 * @property int $promoter_commission_rate 推广员佣金比例%
 * @property int $manager_commission_rate 组织者佣金比例%
 * @property float $promoter_commission 推广员佣金
 * @property float $manager_commission 组织者佣金
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission newQuery()
 * @method static \Illuminate\Database\Query\Builder|GiftCommission onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission query()
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereGoodsPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereManagerCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereManagerCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission wherePromoterCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission wherePromoterCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission wherePromoterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereRefundStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereSelectedSkuName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftCommission whereWithdrawalId($value)
 * @method static \Illuminate\Database\Query\Builder|GiftCommission withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GiftCommission withoutTrashed()
 * @mixin \Eloquent
 */
class GiftCommission extends BaseModel
{
}
