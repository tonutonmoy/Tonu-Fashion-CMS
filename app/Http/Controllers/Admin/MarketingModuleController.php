<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MarketingSettingsRequest;
use App\Http\Requests\Admin\SeoSettingsRequest;
use App\Http\Requests\Admin\ShippingSettingsRequest;
use App\Http\Requests\Admin\SmsSettingsRequest;
use App\Http\Requests\Admin\SocialChatSettingsRequest;
use App\Http\Requests\Admin\TestSmsRequest;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Services\MarketingService;
use App\Services\ShippingService;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class MarketingModuleController extends Controller
{
    public function __construct(
        private MarketingService $marketing,
        private ShippingService $shipping,
        private SmsService $sms,
        private SettingRepositoryInterface $settings
    ) {}

    public function marketing(): View
    {
        return view('admin.marketing.index', ['settings' => $this->marketing->all()]);
    }

    public function updateMarketing(MarketingSettingsRequest $request): RedirectResponse
    {
        $this->marketing->update($request->validated());

        return back()->with('success', 'Marketing settings saved.');
    }

    public function shipping(): View
    {
        return view('admin.marketing.shipping', ['settings' => $this->shipping->settings()]);
    }

    public function updateShipping(ShippingSettingsRequest $request): RedirectResponse
    {
        $this->shipping->update($request->validated());

        return back()->with('success', 'Shipping settings saved.');
    }

    public function sms(): View
    {
        return view('admin.marketing.sms', ['settings' => $this->sms->settings()]);
    }

    public function updateSms(SmsSettingsRequest $request): RedirectResponse
    {
        $this->sms->updateSettings($request->validated());

        return back()->with('success', 'SMS settings saved.');
    }

    public function smsBalance(): JsonResponse
    {
        $result = $this->sms->balance();

        return response()->json($result->toArray(), $result->success ? 200 : 422);
    }

    public function testSms(TestSmsRequest $request): JsonResponse
    {
        $result = $this->sms->send(
            $request->validated('phone'),
            $request->validated('message'),
            force: true,
        );

        return response()->json($result->toArray(), $result->success ? 200 : 422);
    }

    public function socialChat(): View
    {
        return view('admin.marketing.social-chat', [
            'settings' => $this->settings->getByGroup('social_chat')->pluck('value', 'key')->toArray(),
        ]);
    }

    public function updateSocialChat(SocialChatSettingsRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $booleanKeys = ['whatsapp_enabled', 'messenger_enabled', 'instagram_enabled', 'telegram_enabled', 'support_chat_enabled'];
        $payload = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $booleanKeys, true)) {
                $payload[$key] = ['value' => $value ? '1' : '0', 'type' => 'boolean'];
            } else {
                $payload[$key] = $value;
            }
        }

        $this->settings->setMany('social_chat', $payload);
        Cache::forget('app_settings_all');

        return back()->with('success', 'Social chat settings saved.');
    }

    public function seo(): View
    {
        return view('admin.marketing.seo', [
            'settings' => $this->settings->getByGroup('seo')->pluck('value', 'key')->toArray(),
        ]);
    }

    public function updateSeo(SeoSettingsRequest $request): RedirectResponse
    {
        $this->settings->setMany('seo', $request->validated());
        Cache::forget('app_settings_all');
        Cache::forget('seo_robots_txt');

        return back()->with('success', 'SEO settings saved.');
    }
}
