<div class="py-16 px-4">
    <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl font-bold text-center mb-12">{{ $block['config']['title'] ?? 'Features' }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach(($block['config']['features'] ?? []) as $feature)
                <div class="text-center p-6">
                    <i class="{{ $feature['icon'] ?? 'fas fa-star' }} text-4xl mb-4" style="color: var(--brand-primary);"></i>
                    <h3 class="text-xl font-semibold mb-2">{{ $feature['title'] ?? '' }}</h3>
                    <p class="text-slate-600 dark:text-slate-400">{{ $feature['description'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

