<div class="py-16 px-4 bg-slate-50 dark:bg-slate-800">
    <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl font-bold text-center mb-12">{{ $block['config']['title'] ?? 'Testimonials' }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @php
                $testimonials = [];
                if (($block['config']['source'] ?? 'dynamic') === 'dynamic') {
                    // Load from database
                    $testimonials = \Modules\CMS\Testimonials\Models\Testimonial::where('is_active', true)->take(4)->get();
                }
            @endphp
            @forelse($testimonials as $testimonial)
                <div class="bg-white dark:bg-slate-700 p-6 rounded-lg shadow">
                    <p class="text-slate-700 dark:text-slate-300 mb-4">"{{ $testimonial->content }}"</p>
                    <p class="font-semibold">{{ $testimonial->name }}</p>
                </div>
            @empty
                <div class="bg-white dark:bg-slate-700 p-6 rounded-lg shadow">
                    <p class="text-slate-700 dark:text-slate-300 mb-4">"No testimonials available yet."</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

