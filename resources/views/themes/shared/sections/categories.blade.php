@if(isset($sections['categories']) && ($sections['categories']['categories'] ?? collect())->isNotEmpty())
@php
    $categoryItems = $sections['categories']['categories'];
    $limit = (int) ($sections['categories']['settings']['limit'] ?? 6);
    $cols = min(max($categoryItems->count(), 1), $limit);
    $colsMd = min($cols, 6);
@endphp
<section class="theme-section">
    <div class="theme-container">
        <h2 class="theme-section-title">Shop by Category</h2>
        <div class="theme-category-grid" style="--category-cols: {{ $colsMd }};">
            @foreach($categoryItems as $category)
            <a href="{{ route('categories.show', $category) }}" class="theme-category-card">
                @if($category->image)
                    <img src="{{ image_url($category->image) }}" alt="{{ $category->name }}" loading="lazy" decoding="async" class="theme-category-img">
                @endif
                <span>{{ $category->name }}</span>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif
