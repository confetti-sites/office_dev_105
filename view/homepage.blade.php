@php use model\homepage\feature\title; @endphp
@php($homepage = model(new \model\homepage)->label('Homepage'))

<ul>
    @foreach($homepage->list('feature')->where('category', '!=', 'dog')->orderDescBy(new title)->get() as $i => $feature)
        <li>{{ $feature->text('title')->max(50) }}</li>
        <li>{{ $feature->text('category')->max(5) }}</li>
    @endforeach
</ul>

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
{{--@include('view.compare')--}}

{{--@include('view.steps')--}}

{{--@include('view.newsletter')--}}
