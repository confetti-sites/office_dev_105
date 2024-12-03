@php(newRoot(new \model\homepage))

@include('website.hero')
@include('website.usps')
@include('website.demo')
@include('website.compare')

@include('website.steps')

@include('website.newsletter')


{{-- Use one instead of first --}}
{{--@php($homepage = \model\homepage::query()->one())--}}