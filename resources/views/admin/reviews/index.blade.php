@extends('layouts.admin')
@section('title', 'Reviews')
@section('content')
<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Product</th><th class="px-4 py-3 text-left">Customer</th><th class="px-4 py-3 text-left">Rating</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-right">Actions</th></tr></thead>
        <tbody class="divide-y">
            @foreach($reviews as $review)
            <tr>
                <td class="px-4 py-3">{{ $review->product?->name }}</td>
                <td class="px-4 py-3">{{ $review->user?->name }}</td>
                <td class="px-4 py-3">{{ str_repeat('★', $review->rating) }}</td>
                <td class="px-4 py-3">{{ $review->is_approved ? 'Approved' : 'Pending' }}</td>
                <td class="px-4 py-3 text-right">
                    <x-admin.action-group>
                        @unless($review->is_approved)
                        <x-admin.action-btn variant="approve" :action="route('admin.reviews.approve', $review)" method="PATCH" />
                        @endunless
                        <x-admin.action-btn variant="reject" :action="route('admin.reviews.destroy', $review)" method="DELETE" :confirm="true" :confirm-message="__('admin.confirm_delete')" />
                    </x-admin.action-group>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $reviews->links() }}
@endsection
