<?php

namespace App\Models;

/**
 * App\Models\AdminTodoEnums
 *
 * @property int $id
 * @property int $type 类型：1-订单待发货，2-售后，3-实名认证，4-企业认证，5-佣金提现
 * @property string $reference_id 外部参考ID，如订单ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AdminTodo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminTodo newQuery()
 * @method static \Illuminate\Database\Query\Builder|AdminTodo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminTodo query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminTodo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminTodo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminTodo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminTodo whereReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminTodo whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminTodo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|AdminTodo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AdminTodo withoutTrashed()
 * @mixin \Eloquent
 */
class AdminTodo extends BaseModel
{
}
