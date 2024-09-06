<?php

namespace App\Services;

use App\Models\AuthInfo;
use App\Utils\Inputs\AuthInfoInput;
use App\Utils\Inputs\AuthInfoListInput;

class AuthInfoService extends BaseService
{
    public function createAuthInfo($userId, AuthInfoInput $input)
    {
        $authInfo = AuthInfo::new();
        $authInfo->user_id = $userId;
        return $this->updateAuthInfo($authInfo, $input);
    }

    public function updateAuthInfo(AuthInfo $authInfo, AuthInfoInput $input)
    {
        if ($authInfo->status == 2) {
            $authInfo->status = 0;
            $authInfo->failure_reason = '';
        }
        $authInfo->name = $input->name;
        $authInfo->mobile = $input->mobile;
        $authInfo->id_card_number = $input->idCardNumber;
        $authInfo->id_card_front_photo = $input->idCardFrontPhoto;
        $authInfo->id_card_back_photo = $input->idCardBackPhoto;
        $authInfo->hold_id_card_photo = $input->holdIdCardPhoto;
        $authInfo->save();

        return $authInfo;
    }

    public function getAuthInfoList(AuthInfoListInput $input, $columns = ['*'])
    {
        $query = AuthInfo::query();
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!empty($input->mobile)) {
            $query = $query->where('mobile', $input->mobile);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getAuthInfoById($id, $columns = ['*'])
    {
        return AuthInfo::query()->find($id, $columns);
    }

    public function getAuthInfoByUserId($userId, $columns = ['*'])
    {
        return AuthInfo::query()->where('user_id', $userId)->first($columns);
    }

    public function getListByIds(array $ids, $columns = ['*'])
    {
        return AuthInfo::query()->whereIn('id', $ids)->get($columns);
    }
}
