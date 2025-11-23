<div class="py-16 px-4">
    <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl font-bold text-center mb-12">{{ $block['config']['title'] ?? 'Our Programs' }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
                $programs = \App\Models\Program::where('is_active', true)->take(6)->get();
            @endphp
            @forelse($programs as $program)
                <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
                    <h3 class="text-xl font-semibold mb-2">{{ $program->getTranslated('title', app()->getLocale()) }}</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">{{ \Illuminate\Support\Str::limit($program->getTranslated('description', app()->getLocale()), 100) }}</p>
                    <a href="/programs/{{ $program->slug }}" class="text-primary font-semibold">Learn More →</a>
                </div>
            @empty
                <p class="text-center col-span-3 text-slate-500">No programs available yet.</p>
            @endforelse
        </div>
    </div>
</div>

