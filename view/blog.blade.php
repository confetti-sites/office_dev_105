@php($blog = useModel(new \model\blog_overview)->blogs()->whereUrlPath('is', request()->uri())->first())

{{ $blog->title }}
{{ $blog->description }}
{{ $blog->text('content')->max(200) }}
