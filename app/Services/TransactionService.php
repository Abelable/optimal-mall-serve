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

    public function createTransaction($accountId, $type, $amount, $referenceId = '')
    {
        $transaction = new Transaction();
        $transaction->account_id = $accountId;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->reference_id = $referenceId;
        $transaction->save();
        return $transaction;
    }
}
