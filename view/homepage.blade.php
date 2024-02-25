
@php($pages = model(new \model\pages))

{{ $pages->text('general_title')->default('General') }}

<br>
<br>


@php($all = $pages->list('page')
    ->whereActiveIs(request()->parameter('active') ?? 'unknown')
    ->get())

@foreach($all as $page)
    @php($page->text('active'))
    <h1>{{ $page->text('title')}}</h1>
@endforeach










{{-- @todo new query when where changes --}}

{{--@todo  component store has to many items. Remove files items I think.
{{--object(Confetti\Helpers\ComponentStore)#15 (1) {--}}
{{--["components":"Confetti\Helpers\ComponentStore":private]=>--}}
{{--array(153) {--}}
{{--["/README.md"]=>--}}
{{--object(Confetti\Helpers\ComponentEntity)#17 (5) {--}}






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
