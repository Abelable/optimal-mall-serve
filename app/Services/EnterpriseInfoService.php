<?php

namespace App\Services;

use App\Models\EnterpriseInfo;
use App\Utils\Inputs\EnterpriseInfoInput;
use App\Utils\Inputs\EnterpriseInfoPageInput;

class EnterpriseInfoService extends BaseService
{
    public function createEnterpriseInfo($userId, EnterpriseInfoInput $input)
    {
        $authInfo = EnterpriseInfo::new();
        $authInfo->user_id = $userId;
        return $this->updateEnterpriseInfo($authInfo, $input);
    }

    public function updateEnterpriseInfo(EnterpriseInfo $authInfo, EnterpriseInfoInput $input)
    {
        if ($authInfo->status == 2) {
            $authInfo->status = 0;
            $authInfo->failure_reason = '';
        }
        $authInfo->name = $input->name;
        $authInfo->bank_name = $input->bankName;
        $authInfo->bank_card_code = $input->bankCardCode;
        $authInfo->bank_address = $input->bankAdress;
        $authInfo->business_license_photo = $input->businessLicensePhoto;
        $authInfo->id_card_front_photo = $input->idCardFrontPhoto;
        $authInfo->id_card_back_photo = $input->idCardBackPhoto;
        $authInfo->save();

        return $authInfo;
    }

    public function getEnterpriseInfoList(EnterpriseInfoPageInput $input, $columns = ['*'])
    {
        $query = EnterpriseInfo::query();
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        return $query
            ->orderByRaw("FIELD(status, 0) DESC")
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getEnterpriseInfoById($id, $columns = ['*'])
    {
        return EnterpriseInfo::query()->find($id, $columns);
    }

    public function getEnterpriseInfoByUserId($userId, $columns = ['*'])
    {
        return EnterpriseInfo::query()->where('user_id', $userId)->first($columns);
    }

    public function getListByIds(array $ids, $columns = ['*'])
    {
        return EnterpriseInfo::query()->whereIn('id', $ids)->get($columns);
    }

    public function getCountByStatus($status)
    {
        return EnterpriseInfo::query()->where('status', $status)->count();
    }
}
