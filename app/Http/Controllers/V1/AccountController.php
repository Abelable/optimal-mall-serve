<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\BankCard;
use App\Services\AccountChangeLogService;
use App\Services\AccountService;
use App\Services\BankCardService;
use App\Services\TransactionService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class AccountController extends Controller
{
    protected $except = [];
    public function accountInfo()
    {
        $account = AccountService::getInstance()->getUserAccount($this->userId());
        if (is_null($account)) {
            $account = AccountService::getInstance()->createUserAccount($this->userId());
        }
        return $this->success([
            'id' => $account->id,
            'balance' => $account->balance ?: 0,
        ]);
    }

    public function transactionRecordList()
    {
        $accountId = $this->verifyRequiredInteger('accountId');
        /** @var PageInput $input */
        $input = PageInput::new();
        $columns = ['id', 'type', 'amount', 'reference_id', 'created_at'];
        $page = TransactionService::getInstance()->getPage($accountId, $input, $columns);
        return $this->successPaginate($page);
    }
}
