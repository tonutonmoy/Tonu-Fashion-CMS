<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md card p-8">
        <h1 class="text-2xl font-bold text-center mb-6">Admin Login</h1>
        <?php if($errors->any()): ?>
            <div class="bg-red-50 text-red-800 p-3 rounded-lg mb-4 text-sm"><?php echo e($errors->first()); ?></div>
        <?php endif; ?>
        <form method="POST" action="<?php echo e(route('admin.login')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="label">Email / Username</label>
                <input type="text" name="email" class="input" value="<?php echo e(old('email', config('admin.email'))); ?>" required autofocus>
            </div>
            <div>
                <label class="label">Password</label>
                <input type="password" name="password" class="input" required>
            </div>
            <button type="submit" class="btn-primary w-full">Login</button>
        </form>
    </div>
</body>
</html>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/admin/auth/login.blade.php ENDPATH**/ ?>