@extends('install.layout')
@section('title', 'Database')
@section('step', 2)
@section('content')
<h2 class="text-2xl font-bold mb-2">MongoDB Configuration</h2>
<p class="text-gray-600 mb-6">Connect to your MongoDB Atlas cluster. We will test the connection before saving to <code class="text-sm bg-gray-100 px-1 rounded">.env</code>.</p>

<form action="{{ route('install.database') }}" method="POST" id="db-form" class="space-y-4">
    @csrf
    <input type="hidden" name="db_driver" value="mongodb">
    <div>
        <label class="label">MongoDB URI</label>
        <input name="mongodb_uri" value="{{ old('mongodb_uri', $config['mongodb_uri']) }}" class="input w-full" required placeholder="mongodb+srv://user:pass@cluster.mongodb.net">
        <p class="text-xs text-gray-500 mt-1">Atlas connection string including credentials.</p>
    </div>
    <div>
        <label class="label">Database Name</label>
        <input name="db_database" value="{{ old('db_database', $config['db_database']) }}" class="input w-full" required placeholder="tonu-fashion-cms">
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
