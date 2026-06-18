<?php
    $chat = app(\App\Repositories\Contracts\SettingRepositoryInterface::class);
    $social = $chat->getByGroup('social_chat')->pluck('value', 'key')->toArray();
?>
<?php if(($social['whatsapp_enabled'] ?? false) && ($social['whatsapp_number'] ?? null)): ?>
<a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $social['whatsapp_number'])); ?>" target="_blank" rel="noopener" class="social-chat-btn social-chat-whatsapp" aria-label="WhatsApp" title="WhatsApp">
    <svg viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492a.5.5 0 00.614.614l4.458-1.495A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.006-1.372l-.36-.214-2.64.886.886-2.574-.234-.374A9.818 9.818 0 1121.818 12 9.828 9.828 0 0112 21.818z"/></svg>
</a>
<?php endif; ?>
<?php if(($social['messenger_enabled'] ?? false) && ($social['messenger_link'] ?? null)): ?>
<a href="<?php echo e($social['messenger_link']); ?>" target="_blank" rel="noopener" class="social-chat-btn social-chat-messenger" aria-label="Messenger" title="Messenger">💬</a>
<?php endif; ?>
<?php if(($social['instagram_enabled'] ?? false) && ($social['instagram_link'] ?? null)): ?>
<a href="<?php echo e($social['instagram_link']); ?>" target="_blank" rel="noopener" class="social-chat-btn social-chat-instagram" aria-label="Instagram" title="Instagram">📷</a>
<?php endif; ?>
<?php if(($social['telegram_enabled'] ?? false) && ($social['telegram_link'] ?? null)): ?>
<a href="<?php echo e($social['telegram_link']); ?>" target="_blank" rel="noopener" class="social-chat-btn social-chat-telegram" aria-label="Telegram" title="Telegram">✈️</a>
<?php endif; ?>
<style>
.social-chat-btn{position:fixed;bottom:1.25rem;right:1.25rem;z-index:60;width:3rem;height:3rem;border-radius:9999px;display:flex;align-items:center;justify-content:center;color:#fff;box-shadow:0 4px 12px rgba(0,0,0,.2);text-decoration:none;font-size:1.25rem}
.social-chat-whatsapp{background:#25D366;bottom:5.5rem}
.social-chat-messenger{background:#0084FF;bottom:9rem}
.social-chat-instagram{background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888);bottom:12.5rem}
.social-chat-telegram{background:#0088cc;bottom:16rem}
</style>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/components/social-chat.blade.php ENDPATH**/ ?>