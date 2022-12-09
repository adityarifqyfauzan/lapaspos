<?php

namespace App\Repository;


interface PaymentStatusRepository{
    public function findBy($criteria = [], $page, $size);
    public function findOneBy($criteria = []);
}
