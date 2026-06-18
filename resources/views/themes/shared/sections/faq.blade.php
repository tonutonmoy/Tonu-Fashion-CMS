@if(isset($sections['faq']) && !empty($sections['faq']['items']))
<section class="theme-section">
    <div class="theme-container theme-faq">
        <h2 class="theme-section-title">FAQ</h2>
        <div class="space-y-3">
            @foreach($sections['faq']['items'] as $item)
            <details class="theme-faq-item">
                <summary class="theme-faq-question">{{ $item['question'] ?? '' }}</summary>
                <p class="theme-faq-answer">{{ $item['answer'] ?? '' }}</p>
            </details>
            @endforeach
        </div>
    </div>
</section>
@endif
