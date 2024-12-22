@php(newRoot(new \model\homepage))

@extends('website.layouts.main')

@section('content')
    @include('website.includes.hero')
    @include('website.includes.usps')
{{--    -- examples of admin --}}
{{--    -- voorbeelden soorten websites. Blog / static websites --}}
    @include('website.includes.compare')
    @include('website.includes.steps')
    @include('website.includes.cta')
@endsection
