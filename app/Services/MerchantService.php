<?php

namespace App\Services;

use App\Models\Merchant;
use App\Utils\Inputs\Admin\MerchantInput;
use App\Utils\Inputs\Admin\MerchantListInput;

class MerchantService extends BaseService
{
    public function getMerchantPage(MerchantListInput $input, $columns = ['*'])
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
        if (!empty($input->companyName)) {
            $merchant->company_name = $input->companyName;
        }
        if (!empty($input->consigneeName)) {
            $merchant->consignee_name = $input->consigneeName;
        }
        if (!empty($input->mobile)) {
            $merchant->mobile = $input->mobile;
        }
        if (!empty($input->address)) {
            $merchant->address_detail = $input->addressDetail;
        }
        $merchant->license = json_encode($input->license);
        if (!empty($input->supplement)) {
            $merchant->supplement = $input->supplement;
        }
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
