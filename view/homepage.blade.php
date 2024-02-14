@php($homepage = model(new \model\homepage)->label('Homepage'))
{{--{{ $homepage->text('title') }}--}}
<ul>
    @foreach($homepage->list('feature')->get() as $feature)
        <li>{{ $feature->text('title')->default('default feature title') }}</li>
        <ul>
            @foreach($feature->list('image')->get() as $image)
                <li>{{ $image->text('alt') }}</li>
            @endforeach
        </ul>
    @endforeach
</ul>


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
