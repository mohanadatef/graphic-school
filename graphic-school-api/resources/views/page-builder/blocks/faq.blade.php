<div class="py-16 px-4">
    <div class="max-w-3xl mx-auto">
        <h2 class="text-3xl font-bold text-center mb-12">{{ $block['config']['title'] ?? 'Frequently Asked Questions' }}</h2>
        <div class="space-y-4">
            @foreach(($block['config']['items'] ?? []) as $item)
                <div class="bg-white dark:bg-slate-800 p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-2">{{ $item['question'] ?? '' }}</h3>
                    <p class="text-slate-600 dark:text-slate-400">{{ $item['answer'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

