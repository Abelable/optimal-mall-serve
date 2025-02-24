<?php

namespace App\Services;

use App\Models\Transaction;
use App\Utils\Inputs\PageInput;

class TransactionService extends BaseService
{
    public function getPage($accountId, PageInput $input, $columns = ['*'])
    {
        return Transaction::query()
            ->where('account_id', $accountId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }
}
