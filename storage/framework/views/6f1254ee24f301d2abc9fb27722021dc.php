<?php $__env->startSection('title', 'SMS Notifications'); ?>
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.marketing._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 max-w-5xl">
    <form action="<?php echo e(route('admin.marketing.sms')); ?>" method="POST" class="card p-6 space-y-5">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

        <div>
            <h3 class="font-semibold text-lg">SMS.net.bd</h3>
            <p class="text-sm text-gray-500 mt-1">Order notifications via <a href="https://sms.net.bd" target="_blank" rel="noopener" class="text-brand-600 hover:underline">sms.net.bd</a> API.</p>
        </div>

        <label class="flex items-center gap-2">
            <input type="hidden" name="sms_enabled" value="0">
            <input type="checkbox" name="sms_enabled" value="1" <?php if($settings['sms_enabled'] ?? false): echo 'checked'; endif; ?>>
            <span class="text-sm font-medium">Enable SMS notifications</span>
        </label>

        <div>
            <label class="label">API Key *</label>
            <input
                name="sms_api_key"
                type="password"
                class="input font-mono text-sm"
                autocomplete="off"
                placeholder="<?php echo e(($settings['sms_api_key_set'] ?? false) ? 'Saved — leave blank to keep current key' : 'Paste your SMS.net.bd API key'); ?>"
            >
            <p class="text-xs text-gray-500 mt-1">Find this in your SMS.net.bd dashboard under API.</p>
        </div>

        <div>
            <label class="label">Sender ID <span class="text-gray-400 font-normal">(optional)</span></label>
            <input name="sms_sender_id" value="<?php echo e($settings['sms_sender_id'] ?? ''); ?>" class="input" maxlength="20" placeholder="Approved sender ID">
            <p class="text-xs text-gray-500 mt-1">Only use if you have an approved Sender ID from SMS.net.bd.</p>
        </div>

        <div class="border-t border-gray-200 pt-4 space-y-2">
            <p class="text-sm font-medium text-gray-700">Send SMS when:</p>
            <label class="flex items-center gap-2 text-sm"><input type="hidden" name="notify_confirmed" value="0"><input type="checkbox" name="notify_confirmed" value="1" <?php if($settings['notify_confirmed'] ?? true): echo 'checked'; endif; ?>> Order confirmed</label>
            <label class="flex items-center gap-2 text-sm"><input type="hidden" name="notify_shipped" value="0"><input type="checkbox" name="notify_shipped" value="1" <?php if($settings['notify_shipped'] ?? true): echo 'checked'; endif; ?>> Order shipped</label>
            <label class="flex items-center gap-2 text-sm"><input type="hidden" name="notify_delivered" value="0"><input type="checkbox" name="notify_delivered" value="1" <?php if($settings['notify_delivered'] ?? true): echo 'checked'; endif; ?>> Order delivered</label>
            <label class="flex items-center gap-2 text-sm"><input type="hidden" name="notify_parcel_created" value="0"><input type="checkbox" name="notify_parcel_created" value="1" <?php if($settings['notify_parcel_created'] ?? true): echo 'checked'; endif; ?>> Parcel created</label>
            <label class="flex items-center gap-2 text-sm"><input type="hidden" name="notify_returned" value="0"><input type="checkbox" name="notify_returned" value="1" <?php if($settings['notify_returned'] ?? true): echo 'checked'; endif; ?>> Order returned</label>
        </div>

        <button type="submit" class="btn-primary">Save SMS Settings</button>
    </form>

    <div class="space-y-6">
        <div class="card p-6 space-y-4" id="sms-balance-card" data-balance-url="<?php echo e(route('admin.marketing.sms.balance')); ?>">
            <div class="flex items-center justify-between gap-3">
                <h3 class="font-semibold text-lg">Account Balance</h3>
                <button type="button" class="btn-secondary text-sm" id="sms-refresh-balance">Refresh</button>
            </div>
            <p class="text-sm text-gray-500">Requires a saved API key.</p>
            <p id="sms-balance-value" class="text-2xl font-semibold text-gray-900">—</p>
            <p id="sms-balance-error" class="text-sm text-red-600 hidden"></p>
        </div>

        <form id="sms-test-form" class="card p-6 space-y-4" data-test-url="<?php echo e(route('admin.marketing.sms.test')); ?>">
            <h3 class="font-semibold text-lg">Send Test SMS</h3>
            <p class="text-sm text-gray-500">Save your API key first. Test works even if notifications are disabled.</p>
            <div>
                <label class="label">Phone</label>
                <input type="tel" name="phone" class="input" placeholder="01XXXXXXXXX or 8801XXXXXXXXX" required pattern="^(880)?01[0-9]{9}$">
            </div>
            <div>
                <label class="label">Message</label>
                <textarea name="message" class="input" rows="3" maxlength="500" required placeholder="Test message from your store"></textarea>
            </div>
            <p id="sms-test-result" class="text-sm hidden"></p>
            <button type="submit" class="btn-secondary" id="sms-test-btn">Send Test SMS</button>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const balanceCard = document.getElementById('sms-balance-card');
    const balanceUrl = balanceCard?.dataset.balanceUrl;
    const balanceValue = document.getElementById('sms-balance-value');
    const balanceError = document.getElementById('sms-balance-error');
    const refreshBtn = document.getElementById('sms-refresh-balance');
    const testForm = document.getElementById('sms-test-form');
    const testResult = document.getElementById('sms-test-result');
    const testBtn = document.getElementById('sms-test-btn');

    async function loadBalance() {
        if (!balanceUrl) return;
        balanceError?.classList.add('hidden');
        if (balanceValue) balanceValue.textContent = 'Loading…';

        try {
            const res = await fetch(balanceUrl, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await res.json();

            if (!res.ok || !data.success) {
                throw new Error(data.message || 'Could not load balance.');
            }

            if (balanceValue) {
                balanceValue.textContent = '৳' + Number(data.balance ?? 0).toFixed(2);
            }
        } catch (err) {
            if (balanceValue) balanceValue.textContent = '—';
            if (balanceError) {
                balanceError.textContent = err.message || 'Could not load balance.';
                balanceError.classList.remove('hidden');
            }
        }
    }

    refreshBtn?.addEventListener('click', loadBalance);
    loadBalance();

    testForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!testForm || !testBtn) return;

        testBtn.disabled = true;
        testResult?.classList.add('hidden');

        const formData = new FormData(testForm);

        try {
            const res = await fetch(testForm.dataset.testUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    phone: formData.get('phone'),
                    message: formData.get('message'),
                }),
            });
            const data = await res.json();

            if (!testResult) return;
            testResult.classList.remove('hidden');
            testResult.classList.toggle('text-green-700', data.success);
            testResult.classList.toggle('text-red-600', !data.success);
            testResult.textContent = data.success
                ? `Sent successfully${data.request_id ? ' (request #' + data.request_id + ')' : ''}.`
                : (data.message || 'SMS failed.');

            if (data.success) {
                loadBalance();
            }
        } catch (err) {
            if (testResult) {
                testResult.classList.remove('hidden', 'text-green-700');
                testResult.classList.add('text-red-600');
                testResult.textContent = err.message || 'Request failed.';
            }
        } finally {
            testBtn.disabled = false;
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/admin/marketing/sms.blade.php ENDPATH**/ ?>