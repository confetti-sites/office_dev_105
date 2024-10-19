@php
    $alias = str_replace('/blogs/', '', request()->uri());
    $blog = \model\blog_overview\blog_list::query()->whereAliasIs($alias)->first()
@endphp

<div class="max-w-3xl mx-auto">
    <main>
        <article class="relative pt-12">
        <div class="rounded-lg p-4 bg-blue-300 text-xl flex justify-center m-8">
            <h1>{{ $blog->title }}</h1>
        </div>
        <div class="font-body">
            @foreach($blog->list('content_block')->columns(['content'])->sortable()->get() as $contentRow)
                <div class="mx-4 w-full">
                    <picture class="relative w-full sm:w-220 p-3 rounded-lg">{!! $contentRow->image('image')->widthPx(800)->getSourcesHtml() !!}</picture>
                    @include('view.blocks.index', ['model' => $contentRow->content('content')])
                </div>
            @endforeach
        </div>
        </article>
    </main>
</div>