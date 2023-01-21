<?php

namespace App\Helper;

use App\Jobs\UserActivityJob;

class Activity
{
    public static function payload($user_id, $purpose, $description)
    {
        $payload = [
            'user_id' => $user_id,
            'purpose' => $purpose,
            'description' => $description
        ];

        dispatch(new UserActivityJob((object) $payload));
    }
}
