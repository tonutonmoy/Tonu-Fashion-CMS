<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface HeroSlideRepositoryInterface extends BaseRepositoryInterface
{
    public function getActiveOrdered(): Collection;
}
