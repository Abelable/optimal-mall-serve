<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Services\WithdrawalService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\WithdrawalInput;
use Illuminate\Support\Carbon;

class WithdrawalController extends Controller
{
    public function submit()
    {
        $date = Carbon::now()->day;
        if ($date < 25) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '每月25-31号才可提现');
        }

        /** @var WithdrawalInput $input */
        $input = WithdrawalInput::new();


        return $this->success();
    }
}
