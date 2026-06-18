<?php

namespace App\Repositories\Contracts;

use App\Models\ThemeSetting;

interface ThemeSettingRepositoryInterface
{
    public function get(): ThemeSetting;

    public function update(array $data): ThemeSetting;

    public function bumpAssetVersion(): ThemeSetting;
}
