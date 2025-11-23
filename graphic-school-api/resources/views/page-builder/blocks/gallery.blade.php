<div class="py-16 px-4">
    <div class="max-w-6xl mx-auto">
        @if(isset($block['config']['title']) && $block['config']['title'])
            <h2 class="text-3xl font-bold text-center mb-12">{{ $block['config']['title'] }}</h2>
        @endif
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach(($block['config']['images'] ?? []) as $image)
                <div class="aspect-square bg-slate-200 rounded-lg overflow-hidden">
                    <img src="{{ $image }}" alt="Gallery image" class="w-full h-full object-cover">
                </div>
            @endforeach
        </div>
    </div>
</div>

