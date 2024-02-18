<?php

namespace App\Models;

/**
 * App\Models\CartGoods
 *
 * @property int $id
 * @property int $scene 场景值：1-添加购物车，2-直接购买
 * @property int $status 购物车商品状态：1-正常状态，2-所选规格库存为0、所选规格已不存在，3-商品库存为0、商品已下架、商品已删除
 * @property string $status_desc 购物车商品状态描述
 * @property int $user_id 用户id
 * @property int $shop_id 商品所属店铺id
 * @property int $goods_id 商品id
 * @property int $category_id 商品分类id
 * @property int $freight_template_id 运费模板id
 * @property string $image 商品图片
 * @property string $name 商品名称
 * @property string $selected_sku_name 选中的规格名称
 * @property int $selected_sku_index 选中的规格索引
 * @property float $price 商品价格
 * @property float $market_price 市场价格
 * @property int $number 商品数量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|CartGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereFreightTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereMarketPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereScene($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereSelectedSkuIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereSelectedSkuName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereStatusDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|CartGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CartGoods withoutTrashed()
 * @mixin \Eloquent
 */
class CartGoods extends BaseModel
{
}
