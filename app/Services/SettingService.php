<?php

namespace App\Services;

use App\Helper\Pagination;
use App\Models\Setting;
use App\Repository\SettingRepository;
use Illuminate\Support\Arr;

class SettingService extends Service implements SettingRepository
{
    public function findBy($criteria = [], $page, $size){
        $offset = Pagination::getOffset($page, $size);
        $settings = Setting::where(Arr::except($criteria, ["name"]));

        if (Arr::exists($criteria, "name")) {
            $settings = $settings->where("name", "like", "%". $criteria["name"] . "%");
        }

        $settings = $settings->offset($offset)->take($size)->get();

        return $settings;
    }

    public function findOneBy($criteria = []){
        $setting = Setting::where(Arr::except($criteria, ["name"]));

        if (Arr::exists($criteria, "name")) {
            $setting = $setting->where("name", "like", "%". $criteria["name"] . "%");
        }

        $setting = $setting->first();

        return $setting;
    }

}
