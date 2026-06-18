@extends('layouts.admin')
@section('title', 'Courier Activity Logs')
@section('content')
<div class="mb-6 flex flex-wrap gap-4 border-b border-gray-200 pb-4 text-sm">
    <a href="{{ route('admin.courier.index') }}" class="text-gray-600 hover:text-brand-600">Courier Settings</a>
    <a href="{{ route('admin.courier.activity') }}" class="font-semibold text-brand-600">Activity Logs</a>
</div>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">Time</th>
                <th class="px-4 py-3 text-left">Action</th>
                <th class="px-4 py-3 text-left">User</th>
                <th class="px-4 py-3 text-left">Description</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($logs as $log)
            <tr>
                <td class="px-4 py-3 text-gray-500">{{ $log->created_at->format('d M Y H:i') }}</td>
                <td class="px-4 py-3 font-mono text-xs">{{ $log->action }}</td>
                <td class="px-4 py-3">{{ $log->user?->name ?? 'System' }}</td>
                <td class="px-4 py-3">{{ $log->description }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">No activity yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $logs->links() }}
@endsection
