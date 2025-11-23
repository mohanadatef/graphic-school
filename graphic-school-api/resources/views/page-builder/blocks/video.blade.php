<div class="py-16 px-4">
    <div class="max-w-4xl mx-auto">
        @if(isset($block['config']['title']) && $block['config']['title'])
            <h2 class="text-3xl font-bold text-center mb-8">{{ $block['config']['title'] }}</h2>
        @endif
        <div class="aspect-video bg-slate-200 rounded-lg overflow-hidden">
            @if(isset($block['config']['url']) && $block['config']['url'])
                @php
                    $videoId = '';
                    if (str_contains($block['config']['url'], 'youtube.com') || str_contains($block['config']['url'], 'youtu.be')) {
                        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $block['config']['url'], $matches);
                        $videoId = $matches[1] ?? '';
                    }
                @endphp
                @if($videoId)
                    <iframe src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0" allowfullscreen class="w-full h-full"></iframe>
                @else
                    <div class="w-full h-full flex items-center justify-center text-slate-500">Invalid video URL</div>
                @endif
            @else
                <div class="w-full h-full flex items-center justify-center text-slate-500">No video URL provided</div>
            @endif
        </div>
    </div>
</div>

