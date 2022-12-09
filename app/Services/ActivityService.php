<?php

namespace App\Services;

use App\Helper\Pagination;
use App\Models\Activity;
use App\Repository\ActivityRepository;
use Illuminate\Support\Arr;

class ActivityService extends Service implements ActivityRepository
{
    public function findBy($criteria = [], $page, $size) {
        $offset = Pagination::getOffset($page, $size);
        $activities = Activity::where(Arr::only($criteria, ["user_id"]))->offset($offset)->take($size)->get();

        return $activities;
    }

    public function findOneBy($criteria = []) {
        $activity = Activity::where(Arr::only($criteria, ["user_id"]))->first();

        return $activity;
    }

    public function create(Activity $activity): object {
        if($activity->save()){
            return $this->serviceReturn(true, $activity);
        }

        return $this->serviceReturn(false);
    }

}
