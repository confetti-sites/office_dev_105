
@php($pages = model(new \model\pages))

{{ $pages->text('general_title')->default('General') }}

<ul>
@foreach($pages->list('page')->whereActiveIs(request()->parameter('active'))->get() as $page)
    @php($page->text('active'))
    <li>- {{ $page->text('title') }}</li>
    @foreach($page->list('subpage')->get() as $subpage)
        <li>-- {{ $subpage->text('title') }}</li>
    @endforeach
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
