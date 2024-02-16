@php($homepage = model(new \model\homepage)->label('Homepage'))
{{--{{ $homepage->text('homepage_title')->default('The default homepage title') }}--}}


<ul>
    @foreach($homepage->list('feature_fake')->get() as $feature)
        <h2>{{ $feature->text('feature_title')->max(10) }}</h2>
        <ul>
            @foreach($feature->list('image')->get() as $image)
                <li>{{ $image->text('image_title')->max(5) }}</li>
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
