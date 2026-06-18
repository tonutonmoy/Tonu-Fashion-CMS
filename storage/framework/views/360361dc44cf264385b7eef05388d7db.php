<?php $__env->startSection('title', 'Chat · '.$conversation->guest_name); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto" id="admin-support-chat"
    data-conversation="<?php echo e($conversation->uuid); ?>"
    data-poll-url="<?php echo e(route('admin.support.poll', $conversation)); ?>"
    data-send-url="<?php echo e(route('admin.support.messages.store', $conversation)); ?>">
    <div class="flex items-center justify-between gap-4 mb-4">
        <div class="flex items-center gap-3 min-w-0">
            <a href="<?php echo e(route('admin.support.index')); ?>" class="text-gray-500 hover:text-gray-800 shrink-0">← Back</a>
            <div class="min-w-0">
                <h1 class="text-xl font-bold text-gray-900 truncate"><?php echo e($conversation->guest_name); ?></h1>
                <p class="text-sm text-gray-500"><?php echo e($conversation->guest_phone ?? '—'); ?></p>
                <?php if($conversation->guest_email): ?>
                <p class="text-xs text-gray-400"><?php echo e($conversation->guest_email); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php if($conversation->isOpen()): ?>
        <form action="<?php echo e(route('admin.support.close', $conversation)); ?>" method="POST" onsubmit="return confirm('Close this conversation?')">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            <button type="submit" class="btn-secondary text-sm">Close chat</button>
        </form>
        <?php else: ?>
        <span class="text-sm text-gray-500">Closed</span>
        <?php endif; ?>
    </div>

    <div class="card flex flex-col h-[calc(100vh-14rem)] min-h-[28rem]">
        <div id="support-chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50">
            <?php $__currentLoopData = $conversation->messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="support-msg <?php echo e($message->sender_type->value === 'admin' ? 'support-msg--admin' : 'support-msg--customer'); ?>" data-id="<?php echo e($message->id); ?>">
                <div class="support-msg-bubble">
                    <?php if($message->attachment): ?>
                    <a href="<?php echo e(image_url($message->attachment)); ?>" target="_blank" rel="noopener"><img src="<?php echo e(image_url($message->attachment)); ?>" alt="" class="support-msg-image max-w-[200px] rounded-lg mb-1"></a>
                    <?php endif; ?>
                    <?php if($message->body): ?>
                    <p class="support-msg-text"><?php echo e($message->body); ?></p>
                    <?php endif; ?>
                    <span class="support-msg-time"><?php echo e($message->created_at->timezone(config('app.timezone'))->format('g:i A')); ?></span>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php if($conversation->isOpen()): ?>
        <form id="support-chat-form" class="border-t border-gray-200 p-4 flex gap-2 bg-white items-end" data-no-loading="1">
            <input type="file" id="support-chat-image" accept="image/*" class="hidden">
            <button type="button" id="support-chat-image-btn" class="btn-secondary shrink-0 px-3" title="Attach image">📷</button>
            <textarea id="support-chat-input" rows="2" class="input flex-1 resize-none" placeholder="Type your reply… (Enter to send)" maxlength="2000"></textarea>
            <button type="submit" class="btn-primary self-end shrink-0">Send</button>
        </form>
        <?php else: ?>
        <div class="border-t border-gray-200 p-4 text-sm text-gray-500 bg-white">This conversation is closed.</div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/admin/support/show.blade.php ENDPATH**/ ?>