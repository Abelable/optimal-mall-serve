<?php

namespace App\Services;

use App\Models\AccountChangeLog;
use App\Utils\Inputs\PageInput;

class AccountChangeLogService extends BaseService
{
    public function getLogPage(PageInput $input, $columns = ['*'])
    {
        return AccountChangeLog::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }
}
