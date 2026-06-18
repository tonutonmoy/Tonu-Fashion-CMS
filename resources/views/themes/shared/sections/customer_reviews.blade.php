@if(isset($sections['customer_reviews']) && ($sections['customer_reviews']['reviews'] ?? collect())->isNotEmpty())
<section class="theme-section theme-reviews">
    <div class="theme-container">
        <h2 class="theme-section-title">Customer Reviews</h2>
        <div class="theme-review-grid">
            @foreach($sections['customer_reviews']['reviews'] as $review)
            <div class="theme-review-card">
                <div class="theme-review-stars">{{ str_repeat('★', $review->rating) }}</div>
                <p class="theme-review-text">{{ $review->comment }}</p>
                <p class="theme-review-author">— {{ $review->user?->name }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
