<?php

namespace App\Repositories\Contracts;

use App\Models\HomepageSection;
use Illuminate\Database\Eloquent\Collection;

interface HomepageSectionRepositoryInterface extends BaseRepositoryInterface
{
    public function getEnabledOrdered(): Collection;

    public function findByKey(string $key): ?HomepageSection;

    public function syncDefaults(array $sections): void;
}
