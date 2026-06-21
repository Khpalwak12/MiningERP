<?php

namespace App\Contracts\Repositories;

interface CustomerRepositoryInterface extends BaseRepositoryInterface
{
    public function withBalances(array $filters = []);
}
