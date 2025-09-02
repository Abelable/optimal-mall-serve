<?php

namespace App\Services;

use App\Models\LiveUser;
use App\Utils\Inputs\PageInput;

class LiveUserService extends BaseService
{
    public function getUserPage(PageInput $input, $columns = ['*'])
    {
        return LiveUser::query()
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserList($columns = ['*'])
    {
        return LiveUser::query()->get($columns);
    }

    public function getFilterUserList(array $userIds, $columns = ['*'])
    {
        return LiveUser::query()->whereIn('user_id', $userIds)->get($columns);
    }

    public function getUserById($id, $columns = ['*'])
    {
        return LiveUser::query()->find($id, $columns);
    }

    public function getOptions($columns = ['*'])
    {
        return LiveUser::query()->get($columns);
    }
}
