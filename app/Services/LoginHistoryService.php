<?php

namespace App\Services;

use App\Helper\Pagination;
use App\Models\LoginHistory;
use App\Repository\LoginHistoryRepository;
use Illuminate\Support\Arr;

class LoginHistoryService extends Service implements LoginHistoryRepository
{
    public function findBy($criteria = [], $page, $size) {
        $offset = Pagination::getOffset($page, $size);
        $login_histories = LoginHistory::where(Arr::only($criteria, ["user_id"]))->offset($offset)->take($size)->get();

        return $login_histories;
    }

    public function findOneBy($criteria = []) {
        $login_history = LoginHistory::where(Arr::only($criteria, ["user_id"]))->first();

        return $login_history;
    }

    public function create(LoginHistory $login_history): object {
        if ($login_history->save()) {
            return $this->serviceReturn(true, $login_history);
        }
        return $this->serviceReturn(false);
    }

}
