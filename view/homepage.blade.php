@php($homepage = model(new \model\homepage)->label('Homepage'))
{{--<ul>--}}
    @foreach($homepage->list('feature')->get() as $feature)
{{--        <ul>--}}
            @foreach($feature->list('image')->get() as $image)
{{--                <li>--}}
                <br>{{ $image->text('title') }}<br>
{{--                </li>--}}
            @endforeach
{{--        </ul>--}}
    @endforeach
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
