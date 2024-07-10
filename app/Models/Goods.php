<?php

namespace App\Models;

use Laravel\Scout\Searchable;

/**
 * App\Models\Goods
 *
 * @property int $id
 * @property int $status 商品状态：1-销售中，2-下架
 * @property int $category_id 商品分类id
 * @property int $merchant_id 商家id
 * @property string $video 商品视频
 * @property string $cover 商品封面
 * @property string $image_list 主图图片列表
 * @property string $detail_image_list 详情图片列表
 * @property string $default_spec_image 默认规格图片
 * @property string $name 商品名称
 * @property string $introduction 商品介绍
 * @property int $freight_template_id 运费模板id：0-包邮
 * @property float $price 商品价格
 * @property float $market_price 市场价格
 * @property int $stock 商品库存
 * @property int $original_stock 原始库存
 * @property float $leader_commission_rate 团队长佣金比例%
 * @property float $share_commission_rate 分享佣金比例%
 * @property string $spec_list 商品规格列表
 * @property string $sku_list 商品sku
 * @property int $sales_volume 商品销量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Goods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Goods newQuery()
 * @method static \Illuminate\Database\Query\Builder|Goods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Goods query()
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereDefaultSpecImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereDetailImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereFreightTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereIntroduction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereLeaderCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereMarketPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereOriginalStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereSalesVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereShareCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereSkuList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereSpecList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereVideo($value)
 * @method static \Illuminate\Database\Query\Builder|Goods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Goods withoutTrashed()
 * @mixin \Eloquent
 */
class Goods extends BaseModel
{
    use Searchable;

    /**
     * 索引的字段
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return $this->only('id', 'name');
    }
}
