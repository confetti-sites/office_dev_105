@php(newRoot(new \model\homepage))

@include('view.hero')
@include('view.usps')
@include('view.demo')
@include('view.compare')

@include('view.steps')

@include('view.newsletter')


{{-- Use one instead of first --}}
{{--@php($homepage = \model\homepage::query()->one())--}}