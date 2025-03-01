<?php

namespace App\Services;

use App\Models\AdminTodo;

class AdminTodoService extends BaseService
{
    public function getTodoList($columns=['*'])
    {
        return AdminTodo::query()->get($columns);
    }

    public function createTodo($type, array $referenceIds)
    {
        foreach ($referenceIds as $referenceId) {
            $todo = AdminTodo::new();
            $todo->type = $type;
            $todo->reference_id = $referenceId;
            $todo->save();
        }
    }

    public function deleteTodo($type, $referenceId)
    {
        AdminTodo::query()->where('type', $type)->where('reference_id', $referenceId)->delete();
    }
}
