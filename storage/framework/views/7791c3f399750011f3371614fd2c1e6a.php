<?php $__env->startSection('title', 'Support Chat'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Support Chat</h1>
        <p class="text-sm text-gray-500 mt-1">Real-time customer conversations</p>
    </div>
    <div class="flex items-center gap-2">
        <span id="support-admin-unread" class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-700 <?php echo e($unreadCount ? '' : 'hidden'); ?>"><?php echo e($unreadCount); ?> unread</span>
        <a href="<?php echo e(route('admin.support.index', ['status' => 'open'])); ?>" class="btn-secondary text-sm <?php echo e(request('status', 'open') === 'open' ? 'ring-2 ring-red-500' : ''); ?>">Open</a>
        <a href="<?php echo e(route('admin.support.index', ['status' => 'closed'])); ?>" class="btn-secondary text-sm <?php echo e(request('status') === 'closed' ? 'ring-2 ring-red-500' : ''); ?>">Closed</a>
    </div>
</div>

<div class="card overflow-hidden" id="support-inbox" data-poll-url="<?php echo e(route('admin.support.notifications')); ?>">
    <div class="divide-y divide-gray-100">
        <?php $__empty_1 = true; $__currentLoopData = $conversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conversation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php $latest = $conversation->messages->first(); ?>
        <a href="<?php echo e(route('admin.support.show', $conversation)); ?>" class="flex items-start gap-4 p-4 hover:bg-gray-50 transition support-inbox-item" data-conversation="<?php echo e($conversation->uuid); ?>">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-900 text-white font-semibold text-sm">
                <?php echo e(strtoupper(substr($conversation->guest_name, 0, 1))); ?>

            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <p class="font-medium text-gray-900 truncate"><?php echo e($conversation->guest_name); ?></p>
                    <span class="text-xs text-gray-500 shrink-0"><?php echo e($conversation->last_message_at?->diffForHumans() ?? $conversation->created_at->diffForHumans()); ?></span>
                </div>
                <?php if($conversation->guest_email): ?>
                <p class="text-xs text-gray-500"><?php echo e($conversation->guest_email); ?></p>
                <?php endif; ?>
                <?php if($conversation->guest_phone): ?>
                <p class="text-sm font-medium text-gray-700"><?php echo e($conversation->guest_phone); ?></p>
                <?php endif; ?>
                <p class="text-sm text-gray-600 truncate mt-1"><?php echo e($latest?->body ?: ($latest?->attachment ? '[Image]' : 'No messages yet')); ?></p>
            </div>
            <?php if($conversation->admin_unread_count > 0): ?>
            <span class="support-unread-badge shrink-0 min-w-[1.5rem] h-6 px-2 rounded-full bg-red-600 text-white text-xs font-semibold flex items-center justify-center"><?php echo e($conversation->admin_unread_count); ?></span>
            <?php endif; ?>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="p-12 text-center text-gray-500">No conversations yet.</div>
        <?php endif; ?>
    </div>
</div>

<div class="mt-4"><?php echo e($conversations->withQueryString()->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/admin/support/index.blade.php ENDPATH**/ ?>