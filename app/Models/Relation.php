<?php

namespace App\Models;

/**
 * App\Models\Relation
 *
 * @property int $id
 * @property int $superior_id 上级id
 * @property int $fan_id 粉丝id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Relation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Relation newQuery()
 * @method static \Illuminate\Database\Query\Builder|Relation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Relation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Relation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Relation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Relation whereFanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Relation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Relation whereSuperiorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Relation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Relation withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Relation withoutTrashed()
 * @mixin \Eloquent
 */
class Relation extends BaseModel
{
}
