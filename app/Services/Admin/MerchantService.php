<?php

namespace App\Services\Admin;

use App\Models\Merchant;
use App\Services\BaseService;
use App\Utils\Inputs\Admin\MerchantInput;
use App\Utils\Inputs\Admin\MerchantListInput;

class MerchantService extends BaseService
{
    public function getMerchantList(MerchantListInput $input, $columns = ['*'])
    {
        $query = Merchant::query();
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!empty($input->consigneeName)) {
            $query = $query->where('consignee_name', 'like', "%$input->consigneeName%");
        }
        if (!empty($input->mobile)) {
            $query = $query->where('mobile', $input->mobile);
        }
        return $query->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getMerchantById($id, $columns = ['*'])
    {
        return Merchant::query()->find($id, $columns);
    }

    public function updateMerchant(Merchant $merchant, MerchantInput $input)
    {
        $merchant->name = $input->name;
        $merchant->consignee_name = $input->consigneeName;
        $merchant->mobile = $input->mobile;
        $merchant->address_detail = $input->addressDetail;
        $merchant->supplement = $input->supplement;
        $merchant->save();

        return $merchant;
    }

    public function getMerchantByName($name, $columns = ['*'])
    {
        return Merchant::query()->where('name', $name)->first($columns);
    }

    public function getMerchantOptions($columns = ['*'])
    {
        return Merchant::query()->get($columns);
    }
}
