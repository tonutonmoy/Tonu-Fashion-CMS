<?php

namespace App\Repositories\Eloquent;

use App\Models\HomepageSection;
use App\Repositories\Contracts\HomepageSectionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class HomepageSectionRepository extends BaseRepository implements HomepageSectionRepositoryInterface
{
    public function __construct(HomepageSection $model)
    {
        parent::__construct($model);
    }

    public function getEnabledOrdered(): Collection
    {
        return $this->model->newQuery()
            ->where('enabled', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function findByKey(string $key): ?HomepageSection
    {
        return $this->model->newQuery()->where('section_key', $key)->first();
    }

    public function syncDefaults(array $sections): void
    {
        foreach ($sections as $section) {
            $this->model->newQuery()->firstOrCreate(
                ['section_key' => $section['section_key']],
                $section
            );
        }
    }
}
