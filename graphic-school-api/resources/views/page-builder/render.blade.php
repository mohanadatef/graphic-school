<!DOCTYPE html>
<html lang="{{ $language }}" dir="{{ $language === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }}</title>
    <meta name="description" content="{{ $page->description }}">
    
    <!-- Branding CSS Variables -->
    <style>
        :root {
            --brand-primary: {{ $branding['colors']['primary'] ?? '#6366f1' }};
            --brand-secondary: {{ $branding['colors']['secondary'] ?? '#8b5cf6' }};
            --brand-font: {{ $branding['fonts']['main'] ?? 'Arial' }};
            --brand-heading-font: {{ $branding['fonts']['headings'] ?? 'Arial' }};
        }
        body {
            font-family: var(--brand-font);
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--brand-heading-font);
        }
    </style>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white dark:bg-slate-900">
    <div id="page-content">
        @foreach($structure as $block)
            @include('page-builder.blocks.' . $block['type'], ['block' => $block])
        @endforeach
    </div>
</body>
</html>

