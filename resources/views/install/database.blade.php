@extends('install.layout')
@section('title', 'Database')
@section('step', 2)
@section('content')
<h2 class="text-2xl font-bold mb-2">MySQL Configuration</h2>
<p class="text-gray-600 mb-6">Connect to your MySQL database (Hostinger, cPanel, or local). We will test the connection before saving to <code class="text-sm bg-gray-100 px-1 rounded">.env</code>.</p>

<form action="{{ route('install.database') }}" method="POST" id="db-form" class="space-y-4">
    @csrf
    <input type="hidden" name="db_driver" value="mysql">
    <div class="grid md:grid-cols-2 gap-4">
        <div>
            <label class="label">Host</label>
            <input name="db_host" value="{{ old('db_host', $config['db_host'] ?? '127.0.0.1') }}" class="input w-full" required placeholder="127.0.0.1">
        </div>
        <div>
            <label class="label">Port</label>
            <input name="db_port" value="{{ old('db_port', $config['db_port'] ?? '3306') }}" class="input w-full" required placeholder="3306">
        </div>
    </div>
    <div>
        <label class="label">Database Name</label>
        <input name="db_database" value="{{ old('db_database', $config['db_database'] ?? 'fashion_bd') }}" class="input w-full" required placeholder="fashion_bd">
    </div>
    <div class="grid md:grid-cols-2 gap-4">
        <div>
            <label class="label">Username</label>
            <input name="db_username" value="{{ old('db_username', $config['db_username'] ?? 'root') }}" class="input w-full" required>
        </div>
        <div>
            <label class="label">Password</label>
            <input type="password" name="db_password" value="{{ old('db_password', $config['db_password'] ?? '') }}" class="input w-full" autocomplete="new-password">
        </div>
    </div>
    <p id="db-test-result" class="text-sm hidden"></p>
    <div class="flex flex-wrap gap-3 justify-between pt-4">
        <a href="{{ route('install.requirements') }}" class="btn-secondary">← Previous</a>
        <div class="flex gap-3">
            <button type="button" id="test-db-btn" class="btn-secondary">Test Connection</button>
            <button type="submit" class="btn-primary">Save & Next →</button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('test-db-btn')?.addEventListener('click', async function () {
    const form = document.getElementById('db-form');
    const result = document.getElementById('db-test-result');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const data = Object.fromEntries(new FormData(form));
    result.classList.remove('hidden', 'text-green-600', 'text-red-600');
    result.textContent = 'Testing connection...';
    try {
        const res = await fetch('{{ route('install.database.test') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify(data)
        });
        const json = await res.json();
        result.classList.add(json.success ? 'text-green-600' : 'text-red-600');
        result.textContent = json.message;
    } catch (e) {
        result.classList.add('text-red-600');
        result.textContent = 'Connection test failed.';
    }
});
</script>
@endpush
