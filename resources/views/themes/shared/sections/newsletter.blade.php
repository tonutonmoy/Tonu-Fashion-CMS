@if(isset($sections['newsletter']))
<section class="theme-section theme-newsletter">
    <div class="theme-container text-center">
        <h2 class="theme-section-title">{{ $sections['newsletter']['settings']['title'] ?? 'Subscribe' }}</h2>
        <p class="theme-newsletter-subtitle">{{ $sections['newsletter']['settings']['subtitle'] ?? '' }}</p>
        <form action="{{ route('newsletter.subscribe') }}" method="POST" class="theme-newsletter-form">
            @csrf
            <input type="email" name="email" placeholder="Your email address" required class="theme-input">
            <button type="submit" class="theme-btn theme-btn-primary">Subscribe</button>
        </form>
    </div>
</section>
@endif
