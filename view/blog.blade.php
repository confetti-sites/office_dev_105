{{--@php($blog = useModel(new \model\blog_overview)->blogs()->whereUrlPath('is', request()->uri())->first())--}}
@php($overview = \model\blog_overview::get())


{{--@php($blog = \model\blog_overview\blog::query()->whereUrlPathIs(request()->uri())->first())--}}

{{ $blog->title }}
{{ $blog->description }}
{{ $blog->text('content')->max(200) }}


{{-- @todo implement useRoot --}}
