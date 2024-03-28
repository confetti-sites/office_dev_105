<!DOCTYPE html>
<html lang="en">
<head>
    <title>Confetti CMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/resources/tailwind/tailwind.output.css"/>
    <link rel="stylesheet" href="/view/assets/css/fonts.css"/>
    <script defer>
        @stack('script_*')
    </script>
    <script src="/view/assets/scripts/init-alpine.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@marcreichel/alpine-auto-animate@latest/dist/alpine-auto-animate.min.js"
            defer></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="text-md overflow-x-hidden" :class="{ 'dark': dark }" x-data="data()">
{{--@guest()--}}
{{--    @include('view.under_construction')--}}
{{--@else()--}}
@include('view.header')

@switch(true)
    @case(request()->uri() === '/waiting-list-step-1')
        @include('view.waiting-list-step-1')
        @break
    @case(request()->uri() === '/waiting-list-step-2')
        @include('view.waiting-list-step-2')
        @break
    @case(request()->uri() === '/pricing')
        @include('view.pricing')
        @break
    @case(str_starts_with(request()->uri(), '/docs'))
        @include('view.docs')
        @break
    @default
        @include('view.homepage')
        @break
@endswitch

@php($target = newRoot(new \model\footer)->selectFile('template')->match(['/view/footers/*.blade.php'])->default('/view/footers/footer_big.blade.php'))
@include($target, ['model' => $target])
{{--    @endguest--}}
{{--Can't move to top (refacotor require blade files from inner to out)--}}
@stack('script_*')
</body>
</html>

