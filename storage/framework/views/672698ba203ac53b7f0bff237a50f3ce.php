<?php $__env->startSection('title', 'Team Members'); ?>
<?php $__env->startSection('content'); ?>
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-semibold">Team Members</h2>
        <p class="text-sm text-gray-500">Create Admin or Staff users to help manage the store.</p>
    </div>
    <a href="<?php echo e(route('admin.users.create')); ?>" class="btn-primary">Add Team Member</a>
</div>

<form method="GET" class="card p-4 mb-4 flex flex-col sm:flex-row gap-3">
    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search name or email..." class="input flex-1">
    <select name="role" class="input sm:w-40">
        <option value="">All roles</option>
        <?php $__currentLoopData = \App\Enums\UserRole::assignableTeamRoles(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($role->value); ?>" <?php if(request('role') === $role->value): echo 'selected'; endif; ?>><?php echo e($role->label()); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button class="btn-primary">Search</button>
</form>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-left">Role</th>
                <th class="px-4 py-3 text-left">Permissions</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="px-4 py-3 font-medium"><?php echo e($member->name); ?></td>
                <td class="px-4 py-3"><?php echo e($member->email); ?></td>
                <td class="px-4 py-3"><?php echo e($member->role->label()); ?></td>
                <td class="px-4 py-3">
                    <div class="flex flex-wrap gap-1">
                        <?php $__empty_2 = true; $__currentLoopData = $member->enabledAdminPermissionLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                        <span class="inline-flex px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 text-xs"><?php echo e($label); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                        <span class="text-gray-400 text-xs">None</span>
                        <?php endif; ?>
                    </div>
                </td>
                <td class="px-4 py-3"><?php echo e($member->status->label()); ?></td>
                <td class="px-4 py-3 text-right">
                    <?php if (isset($component)) { $__componentOriginal7c8dfb2426b346a5c74079e742032e01 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c8dfb2426b346a5c74079e742032e01 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.action-group','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.action-group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                        <?php if (isset($component)) { $__componentOriginal855822e136e046e35259bf760caca26b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal855822e136e046e35259bf760caca26b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.action-btn','data' => ['variant' => 'edit','href' => route('admin.users.edit', $member)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.action-btn'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'edit','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.users.edit', $member))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal855822e136e046e35259bf760caca26b)): ?>
<?php $attributes = $__attributesOriginal855822e136e046e35259bf760caca26b; ?>
<?php unset($__attributesOriginal855822e136e046e35259bf760caca26b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal855822e136e046e35259bf760caca26b)): ?>
<?php $component = $__componentOriginal855822e136e046e35259bf760caca26b; ?>
<?php unset($__componentOriginal855822e136e046e35259bf760caca26b); ?>
<?php endif; ?>
                        <?php if($member->id !== auth()->id()): ?>
                        <?php if (isset($component)) { $__componentOriginal855822e136e046e35259bf760caca26b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal855822e136e046e35259bf760caca26b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.action-btn','data' => ['variant' => 'delete','action' => route('admin.users.destroy', $member),'method' => 'DELETE','confirm' => true,'confirmMessage' => __('admin.confirm_delete').' '.$member->name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.action-btn'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'delete','action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.users.destroy', $member)),'method' => 'DELETE','confirm' => true,'confirm-message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('admin.confirm_delete').' '.$member->name)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal855822e136e046e35259bf760caca26b)): ?>
<?php $attributes = $__attributesOriginal855822e136e046e35259bf760caca26b; ?>
<?php unset($__attributesOriginal855822e136e046e35259bf760caca26b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal855822e136e046e35259bf760caca26b)): ?>
<?php $component = $__componentOriginal855822e136e046e35259bf760caca26b; ?>
<?php unset($__componentOriginal855822e136e046e35259bf760caca26b); ?>
<?php endif; ?>
                        <?php endif; ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c8dfb2426b346a5c74079e742032e01)): ?>
<?php $attributes = $__attributesOriginal7c8dfb2426b346a5c74079e742032e01; ?>
<?php unset($__attributesOriginal7c8dfb2426b346a5c74079e742032e01); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c8dfb2426b346a5c74079e742032e01)): ?>
<?php $component = $__componentOriginal7c8dfb2426b346a5c74079e742032e01; ?>
<?php unset($__componentOriginal7c8dfb2426b346a5c74079e742032e01); ?>
<?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No team members yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php echo e($users->links()); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/admin/users/index.blade.php ENDPATH**/ ?>