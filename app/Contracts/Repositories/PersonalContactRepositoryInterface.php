<?php

namespace App\Contracts\Repositories;

interface PersonalContactRepositoryInterface extends BaseRepositoryInterface
{
    public function withBalances(array $filters = []);
}
