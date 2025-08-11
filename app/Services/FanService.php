<?php

namespace App\Services;

use App\Models\Fan;
use App\Utils\Inputs\PageInput;

class FanService extends BaseService
{
    public function fan($authorId, $userId)
    {
        return Fan::query()->where('author_id', $authorId)->where('fan_id', $userId)->first();
    }

    public function newFan($authorId, $fanId)
    {
        $fan = Fan::new();
        $fan->author_id = $authorId;
        $fan->fan_id = $fanId;
        $fan->save();
        return $fan;
    }

    public function followAuthorList($fanId, $columns = ['*'])
    {
        return Fan::query()->where('fan_id', $fanId)->get($columns);
    }

    public function followAuthorIds($fanId)
    {
        $list = $this->followAuthorList($fanId);
        return $list->pluck('author_id')->toArray();
    }

    public function fanPaginate($userId, PageInput $input, $columns=['*'])
    {
        return Fan::query()
            ->where('author_id', $userId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function fanList($authorId, $columns = ['*'])
    {
        return Fan::query()->where('author_id', $authorId)->get($columns);
    }

    public function fanIds($authorId)
    {
        $list = $this->fanList($authorId);
        return $list->pluck('fan_id')->toArray();
    }

    public function fanIdsGroup($authorIds)
    {
        return Fan::query()
            ->whereIn('author_id', $authorIds)
            ->select(['author_id', 'fan_id'])
            ->get()
            ->groupBy('author_id')
            ->map(function ($fan) {
                return $fan->pluck('fan_id')->toArray();
            });
    }

    public function authorIdsGroup($fanIds)
    {
        return Fan::query()
            ->whereIn('fan_id', $fanIds)
            ->select(['author_id', 'fan_id'])
            ->get()
            ->groupBy('fan_id')
            ->map(function ($author) {
                return $author->pluck('author_id')->toArray();
            });
    }

    public function followedAuthorNumber($userId)
    {
        return Fan::query()->where('fan_id', $userId)->count();
    }

    public function fansNumber($authorId)
    {
        return Fan::query()->where('author_id', $authorId)->count();
    }

    public function followPaginate($userId, PageInput $input, $columns=['*'])
    {
        return Fan::query()
            ->where('fan_id', $userId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }
}
