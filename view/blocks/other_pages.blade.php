@php($m = extendModel($model)->label('Other pages')))
<div class="block text-bold text-xl mt-8 mb-4">
    {{ $m->text('title') }}
</div>
@foreach($m->list('other_pages')->columns(['title', 'link'])->get() as $page)
    <a href="{{ $page->text('link') }}">{{ $page->text('title') }}</a>
@endforeach
