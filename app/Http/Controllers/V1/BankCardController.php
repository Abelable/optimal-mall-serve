<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\BankCard;
use App\Services\BankCardService;
use App\Utils\CodeResponse;

class BankCardController extends Controller
{
    public function detail()
    {
        $card = BankCardService::getInstance()->getUserBankCard($this->userId(), ['id', 'name', 'code', 'bank_name']);
        unset($card->user_id);
        return $this->success($card);
    }

    public function add()
    {
        $name = $this->verifyRequiredString('name');
        $code = $this->verifyRequiredString('code');
        $bankName = $this->verifyRequiredString('bankName');

        $card = BankCard::new();
        $card->user_id = $this->userId();
        $this->updateBankCard($card, $name, $code, $bankName);

        return $this->success();
    }

    public function edit()
    {
        $name = $this->verifyRequiredString('name');
        $code = $this->verifyRequiredString('code');
        $bankName = $this->verifyRequiredString('bankName');

        $card = BankCardService::getInstance()->getUserBankCard($this->userId());
        if (is_null($card)) {
            return $this->fail(CodeResponse::NOT_FOUND, '银行卡不存在');
        }
        $this->updateBankCard($card, $name, $code, $bankName);

        return $this->success();
    }

    private function updateBankCard(BankCard $card, $name, $code, $bankName)
    {
        $card->name = $name;
        $card->code = $code;
        $card->bank_name = $bankName;
        $card->save();
        return $card;
    }
}
