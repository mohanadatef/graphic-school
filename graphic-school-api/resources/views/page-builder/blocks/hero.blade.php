<div class="relative min-h-[500px] flex items-center justify-center bg-cover bg-center" style="background-color: var(--brand-primary); @if(isset($block['config']['background_image']) && $block['config']['background_image']) background-image: url('{{ $block['config']['background_image'] }}'); @endif">
    <div class="text-center z-10 px-4 text-white">
        <h1 class="text-5xl font-bold mb-4">{{ $block['config']['title'] ?? '' }}</h1>
        <p class="text-xl mb-6">{{ $block['config']['subtitle'] ?? '' }}</p>
        @if(isset($block['config']['button_text']) && $block['config']['button_text'])
            <a href="{{ $block['config']['button_link'] ?? '#' }}" class="bg-white text-primary px-6 py-3 rounded-lg font-semibold inline-block">
                {{ $block['config']['button_text'] }}
            </a>
        @endif
    </div>
</div>

