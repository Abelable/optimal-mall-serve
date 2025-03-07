<?php

namespace App\Models;

/**
 * App\Models\MerchantManager
 *
 * @property int $id
 * @property int $merchant_id 商家id
 * @property int $user_id 用户id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantManager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantManager newQuery()
 * @method static \Illuminate\Database\Query\Builder|MerchantManager onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantManager query()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantManager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantManager whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantManager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantManager whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantManager whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantManager whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|MerchantManager withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MerchantManager withoutTrashed()
 * @mixin \Eloquent
 */
class MerchantManager extends BaseModel
{
}
