<?php

namespace App\Models;

/**
 * App\Models\Express
 *
 * @property int $id
 * @property string $name 快递名称
 * @property string $code 快递编号
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Express newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Express newQuery()
 * @method static \Illuminate\Database\Query\Builder|Express onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Express query()
 * @method static \Illuminate\Database\Eloquent\Builder|Express whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Express whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Express whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Express whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Express whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Express whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Express withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Express withoutTrashed()
 * @mixin \Eloquent
 */
class Express extends BaseModel
{
}
