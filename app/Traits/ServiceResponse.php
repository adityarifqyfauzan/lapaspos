<?php

namespace App\Traits;

/**
 * this trait used for Service
 */
trait ServiceResponse
{
    /**
     * serviceReturn
     * serialize return on service
     * @param boolean $process (merepresentasikan proses dalam sebuah service)
     * @param $data (whatever you want)
     * @return object
     */
    public function serviceReturn($process = true, $data = null): object
    {
        return (object) [
            'process' => $process,
            'data' => $data
        ];
    }
}
