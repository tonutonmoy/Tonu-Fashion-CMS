<?php
    $settingsRepo = app(\App\Repositories\Contracts\SettingRepositoryInterface::class);
    $supportEnabled = filter_var(
        $settingsRepo->get('social_chat', 'support_chat_enabled', true),
        FILTER_VALIDATE_BOOLEAN
    );
    $storeLogo = ($storeSettings['logo'] ?? null) ? image_url($storeSettings['logo']) : null;
?>
<?php if($supportEnabled): ?>
<div id="support-chat-widget" class="support-widget" data-turbo-permanent
    data-session-url="<?php echo e(route('api.support.session')); ?>"
    data-resume-url="<?php echo e(route('api.support.resume')); ?>"
    data-store-name="<?php echo e($storeSettings['name'] ?? config('app.name')); ?>"
    data-store-logo="<?php echo e($storeLogo); ?>">
    <div id="support-widget-backdrop" class="support-widget-backdrop hidden" aria-hidden="true"></div>

    <div id="support-widget-panel" class="support-widget-panel hidden" hidden role="dialog" aria-label="<?php echo e(__('common.support_chat')); ?>" aria-modal="true">
        <div class="support-widget-header">
            <div class="flex items-center gap-2 min-w-0">
                <?php if($storeLogo): ?>
                <img src="<?php echo e($storeLogo); ?>" alt="" class="support-widget-header-logo" width="32" height="32">
                <?php endif; ?>
                <div class="min-w-0">
                    <p class="support-widget-title"><?php echo e($storeSettings['name'] ?? __('common.support_chat')); ?></p>
                    <p class="support-widget-subtitle"><?php echo e(__('common.support_chat_hint')); ?></p>
                </div>
            </div>
            <button type="button" id="support-widget-close" class="support-widget-close" aria-label="<?php echo e(__('common.close')); ?>">×</button>
        </div>

        <div id="support-widget-intro" class="support-widget-intro">
            <p class="text-sm text-gray-600 mb-4"><?php echo e(__('common.support_intro')); ?></p>
            <form id="support-intro-form" class="space-y-3">
                <input type="text" name="guest_name" class="support-widget-input" placeholder="<?php echo e(__('common.full_name')); ?> *" required maxlength="255" autocomplete="name">
                <input type="tel" name="guest_phone" class="support-widget-input" placeholder="01XXXXXXXXX *" pattern="01[0-9]{9}" required maxlength="11" autocomplete="tel">
                <button type="submit" class="support-widget-send"><?php echo e(__('common.start_chat')); ?></button>
            </form>
        </div>

        <div id="support-widget-chat" class="support-widget-chat hidden">
            <div id="support-widget-messages" class="support-widget-messages"></div>
            <form id="support-widget-form" class="support-widget-compose">
                <input type="file" id="support-widget-image" accept="image/*" class="hidden">
                <button type="button" id="support-widget-image-btn" class="support-widget-attach-btn" aria-label="<?php echo e(__('common.attach_image')); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                </button>
                <input type="text" id="support-widget-input" class="support-widget-input" placeholder="<?php echo e(__('common.type_message')); ?>" maxlength="2000" autocomplete="off">
                <button type="submit" class="support-widget-send-btn" aria-label="<?php echo e(__('common.send')); ?>">
                    <svg viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z"/></svg>
                </button>
            </form>
        </div>
    </div>

    <button type="button" id="support-widget-toggle" class="support-widget-toggle" aria-label="<?php echo e(__('common.support_chat')); ?>" aria-expanded="false" aria-controls="support-widget-panel">
        <svg class="support-widget-icon support-widget-icon--chat" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
        <svg class="support-widget-icon support-widget-icon--close hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        <span id="support-widget-badge" class="support-widget-badge hidden">0</span>
    </button>
</div>
<?php endif; ?>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/components/support-chat-widget.blade.php ENDPATH**/ ?>