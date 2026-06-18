<?php

namespace App\Repositories\Eloquent;

use App\Enums\RecordStatus;
use App\Models\HeroSlide;
use App\Repositories\Contracts\HeroSlideRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class HeroSlideRepository extends BaseRepository implements HeroSlideRepositoryInterface
{
    public function __construct(HeroSlide $model)
    {
        parent::__construct($model);
    }

    public function getActiveOrdered(): Collection
    {
        return $this->model->newQuery()
            ->where('status', RecordStatus::Active)
            ->orderBy('sort_order')
            ->get();
    }
}
