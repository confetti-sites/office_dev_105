@php use model\homepage\feature\title_first; @endphp
@php use model\homepage\feature\title_second; @endphp
@php($homepage = model(new \model\homepage)->label('Homepage'))

<ul>
    @php($features = $homepage->list('feature')
            ->min(1)->max(2)/*->columns(['title_first', 'title_second'])*/
//            ->where(new title_first, '!=', new title_second)
            ->limit(1)
            ->get())

    @foreach($features as $i => $feature)
        <li>{{ $feature->text('title_first')->max(50) }}</li>
    @endforeach
</ul>

<ul>
    @foreach($homepage->features()->get() as $feature)
        <li>SECOND FEATURES</li>
        <li>{{ $feature->title_first }}</li>
    @endforeach
</ul>


{{-- @todo new query when where changes --}}

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
