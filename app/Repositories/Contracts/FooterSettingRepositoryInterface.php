<?php

namespace App\Repositories\Contracts;

use App\Models\FooterSetting;

interface FooterSettingRepositoryInterface
{
    public function get(): FooterSetting;

    public function update(array $data): FooterSetting;
}
