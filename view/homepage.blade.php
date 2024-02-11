@php($homepage = model(new \model\homepage)->label('Homepage'))

<ul>
    @foreach($homepage->list('feature')->get() as $feature)
        <li>{{ $feature->text('feature_title') }}</li>
    @endforeach
</ul>


{{--@include('view.hero')--}}
{{--@include('view.usps')--}}
{{--@include('view.demo')--}}
{{--@include('view.compare')--}}
{{--@include('view.steps')--}}
{{--@include('view.newsletter')--}}
