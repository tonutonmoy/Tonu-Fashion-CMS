<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface SettingRepositoryInterface
{
    public function all(): Collection;

    public function getByGroup(string $group): Collection;

    public function get(string $group, string $key, mixed $default = null): mixed;

    public function set(string $group, string $key, mixed $value, string $type = 'string'): void;

    public function setMany(string $group, array $settings): void;
}
