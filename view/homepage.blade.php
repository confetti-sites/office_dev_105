@php($homepage = model(new \model\homepage)->label('Homepage'))
<h3>{{ $homepage->text('title') }}</h3>
<p>{{ $homepage->text('description') }}</p>



{{--@include('view.hero')--}}
{{--@include('view.usps')--}}
{{--@include('view.demo')--}}
{{--@include('view.compare')--}}
{{--@include('view.steps')--}}
{{--@include('view.newsletter')--}}
