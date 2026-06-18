<?php

namespace App\Services;

use App\Repositories\Contracts\FooterSettingRepositoryInterface;
use App\Repositories\Contracts\ThemeSettingRepositoryInterface;

class FooterBuilderService
{
    public function __construct(
        private FooterSettingRepositoryInterface $footer,
        private BuilderPublishService $publish,
        private ImageService $images
    ) {}

    public function get(): \App\Models\FooterSetting
    {
        if (should_use_builder_draft() || request()->routeIs('admin.theme.footer')) {
            return $this->publish->getEffectiveFooterSettings();
        }

        return $this->footer->get();
    }

    public function update(array $data, $logoFile = null): \App\Models\FooterSetting
    {
        if ($logoFile) {
            $current = $this->publish->getEffectiveFooterSettings();
            $this->images->delete($current->logo);
            $data['logo'] = $this->images->upload($logoFile, 'footer', 400);
        }

        $this->publish->setDraftFooter($data);

        return $this->publish->getEffectiveFooterSettings();
    }
}
