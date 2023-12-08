@php($blog = section('blog'))

{{$blog->text('title')->min(1)->max(100)}}
{{$blog->text('description')->min(1)->max(600)}}
