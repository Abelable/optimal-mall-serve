<?php

namespace App\Services;

use App\Models\AccountChangeLog;
use App\Utils\Inputs\PageInput;
use Symfony\Component\Console\Input\Input;

class AccountChangeLogService extends BaseService
{
    public function getLogPage(PageInput $input, $columns = ['*'])
    {
        return AccountChangeLog::query()
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function createLog($accountId, $oldBalance, $newBalance, $changeType, $changeAmount)
    {
        $log = new AccountChangeLog();
        $log->account_id = $accountId;
        $log->old_balance = $oldBalance;
        $log->new_balance = $newBalance;
        $log->change_type = $changeType;
        $log->change_amount = $changeAmount;
        $log->save();
        return $log;
    }
}
