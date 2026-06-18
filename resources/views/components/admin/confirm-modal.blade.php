<div id="admin-confirm-modal" class="fixed inset-0 z-[110] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40" data-confirm-cancel></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6">
            <h3 class="text-lg font-semibold mb-2" data-confirm-title>Are you sure?</h3>
            <p class="text-sm text-gray-600 mb-6" data-confirm-message>This action cannot be undone.</p>
            <div class="flex gap-3 justify-end">
                <button type="button" class="btn-secondary" data-confirm-cancel>Cancel</button>
                <button type="button" class="btn-danger" data-confirm-ok>Delete</button>
            </div>
        </div>
    </div>
</div>
