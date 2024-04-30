{{--@php($blog = useModel(new \model\blog_overview)->blogs()->whereUrlPath('is', request()->uri())->first())--}}
@php($blog = \model\blog_overview\blog_list::query()->first())


{{--@php($blog = \model\blog_overview\blog::query()->whereUrlPathIs(request()->uri())->first())--}}

{{ $blog->title }}

{{ $blog->content('content')->default('The cool blog title') }}
