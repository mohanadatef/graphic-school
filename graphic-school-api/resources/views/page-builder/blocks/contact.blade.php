<div class="py-16 px-4 bg-slate-50 dark:bg-slate-800">
    <div class="max-w-2xl mx-auto">
        <h2 class="text-3xl font-bold text-center mb-12">{{ $block['config']['title'] ?? 'Contact Us' }}</h2>
        <div class="space-y-6">
            @if(isset($block['config']['email']) && $block['config']['email'])
                <div class="flex items-center gap-4">
                    <i class="fas fa-envelope text-2xl" style="color: var(--brand-primary);"></i>
                    <span>{{ $block['config']['email'] }}</span>
                </div>
            @endif
            @if(isset($block['config']['phone']) && $block['config']['phone'])
                <div class="flex items-center gap-4">
                    <i class="fas fa-phone text-2xl" style="color: var(--brand-primary);"></i>
                    <span>{{ $block['config']['phone'] }}</span>
                </div>
            @endif
            @if(isset($block['config']['location']) && $block['config']['location'])
                <div class="flex items-center gap-4">
                    <i class="fas fa-map-marker-alt text-2xl" style="color: var(--brand-primary);"></i>
                    <span>{{ $block['config']['location'] }}</span>
                </div>
            @endif
        </div>
    </div>
</div>

