<!DOCTYPE html>
<html lang="en">
<head>
    <title>Confetti CMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/resources/view__tailwind/tailwind.output.css"/>
    <link rel="stylesheet" href="/view/assets/css/fonts.css"/>
    <script defer>
        @stack('style_*')
    </script>
</head>
<body class="text-md overflow-x-hidden">
{{--@guest()--}}
{{--    @include('website.under_construction')--}}
{{--@else()--}}

@include('website.header')

@switch(true)

@endswitch

@php($target = newRoot(new \model\footer)->selectFile('template')->match(['/view/footers/*.blade.php'])->required()->default('/view/footers/footer_small.blade.php'))
@include($target, ['model' => $target])

{{--    @endguest--}}

@stack('script_*')
</body>
</html>

