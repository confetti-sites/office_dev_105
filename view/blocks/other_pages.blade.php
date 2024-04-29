@php($m = extendModel($model)->label('Other pages')))
<div class="block text-bold text-xl mt-8 mb-4">
    {{ $m->text('title_todo_fix_is_same_key') }}
</div>
@foreach($m->list('other_pages')->columns(['title', 'link'])->get() as $page)
    <a href="{{ $page->text('link') }}">{{ $page->text('title') }}</a>
@endforeach
