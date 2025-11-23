<div class="py-16 px-4" style="background-color: var(--brand-primary);">
    <div class="max-w-4xl mx-auto text-center text-white">
        <h2 class="text-4xl font-bold mb-4">{{ $block['config']['title'] ?? 'Call to Action' }}</h2>
        <p class="text-xl mb-8">{{ $block['config']['description'] ?? '' }}</p>
        @if(isset($block['config']['button_text']) && $block['config']['button_text'])
            <a href="{{ $block['config']['button_link'] ?? '#' }}" class="bg-white text-primary px-8 py-4 rounded-lg font-semibold text-lg inline-block">
                {{ $block['config']['button_text'] }}
            </a>
        @endif
    </div>
</div>

