<?php

namespace App\Services;

use App\Models\TeamLeader;
use App\Utils\Inputs\TeamLeaderInput;
use App\Utils\Inputs\TeamLeaderPageInput;

class TeamLeaderService extends BaseService
{
    public function createMerchant(TeamLeaderInput $input, $userId)
    {
        $teamLeader = TeamLeader::new();
        $teamLeader->user_id = $userId;
        $teamLeader->name = $input->name;
        $teamLeader->mobile = $input->mobile;
        $teamLeader->email = $input->email;
        $teamLeader->id_card_number = $input->idCardNumber;
        $teamLeader->id_card_front_photo = $input->idCardFrontPhoto;
        $teamLeader->id_card_back_photo = $input->idCardBackPhoto;
        $teamLeader->hold_id_card_photo = $input->holdIdCardPhoto;
        $teamLeader->qualification_photo = json_encode($input->qualificationPhoto);
        $teamLeader->save();

        return $teamLeader;
    }

    public function getTeamLeaderList(TeamLeaderPageInput $input, $columns = ['*'])
    {
        $query = TeamLeader::query();
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

    public function getTeamLeaderByUserId($userId, $columns = ['*'])
    {
        return TeamLeader::query()->where('user_id', $userId)->first($columns);
    }

    public function getTeamLeaderById($id, $columns = ['*'])
    {
        return TeamLeader::query()->find($id, $columns);
    }

    public function getTeamLeaderListByIds(array $ids, $columns = ['*'])
    {
        return TeamLeader::query()->whereIn('id', $ids)->get($columns);
    }
}
