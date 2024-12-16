<!DOCTYPE html>
<html lang="en">
<head>
    <title>Confetti CMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/resources/website__tailwind/tailwind.output.css"/>
    <link rel="stylesheet" href="/website/assets/css/fonts.css"/>
    <!-- Icons from: SVG Repo, www.svgrepo.com, Generator: SVG Repo Mixer Tools -->
    <style>
        @stack('style_*')
    </style>
</head>
<body class="text-md overflow-x-hidden">
{{--@guest()--}}
{{--    @include('website.under_construction')--}}
{{--@else()--}}

@include('website.includes.header')

@yield('content')

@php($target = newRoot(new \model\footer)->selectFile('template')->match(['/website/includes/footers/*.blade.php'])->required()->default('/website/includes/footers/footer_small.blade.php'))
@include($target->getView(), ['model' => $target])

{{--    @endguest--}}

@stack('end_of_body_*')
</body>
</html>

