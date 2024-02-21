{{--@php use model\homepage\feature\title_first; @endphp--}}
{{--@php use model\homepage\feature\title_second; @endphp--}}
@php($homepage = model(new \model\homepage)->label('Homepage'))

{{--<ul>--}}
{{--    @foreach($homepage->list('feature')->where(new title_first, '!=', new title_second)->get() as $i => $feature)--}}
{{--        <li>{{ $feature->text('title_first')->max(50) }}</li>--}}
{{--        <li>{{ $feature->text('title_second')->max(50) }}</li>--}}
{{--    @endforeach--}}
{{--</ul>--}}

{{--{{ $homepage->text('homepage_title')->default('The default homepage title') }}--}}

{{--<ul>--}}
{{--@foreach($homepage->list('step')->columns(['title'])->min(1)->max(10)->get() as $i => $step)--}}
{{--    <li>{{ $step->text('title')->max(50) }}</li>--}}
{{--@endforeach--}}
{{--</ul>--}}

{{--<ul>--}}
{{--    @foreach($homepage->features()->get() as $feature)--}}
{{--        <li>{{ $feature->feature_title }}</li>--}}
{{--    @endforeach--}}
{{--</ul>--}}

{{--@include('view.hero')--}}
{{--@include('view.usps')--}}
{{--@include('view.demo')--}}
@include('view.compare')

@include('view.steps')

{{--@include('view.newsletter')--}}
