@php
    $alias = str_replace('/blogs/', '', request()->uri());
    $blog = \model\blog_overview\blog_list::query()->whereAliasIs($alias)->first();
@endphp

<main class="max-w-3xl mx-auto">
    <article class="relative pt-12">
        <a href="/blogs" class="bg-blue-500 text-white px-4 py-2 rounded-lg mr-2">Back to overview</a>
        <div class="rounded-lg p-4 text-xl flex justify-center m-8">
            <h1>{{ $blog->title }}</h1>
            {!! $blog->image('image')->widthPx(800)->getPicture(class: 'relative w-full p-3 rounded-lg') !!}
        </div>
        <div class="font-body">
            @foreach($blog->list('content_block')->columns(['content'])->sortable()->get() as $contentRow)
                <div class="mx-4 w-full">
                    {!! $contentRow->image('image')->widthPx(800)->getPicture(class: 'relative w-full sm:w-220 p-3 rounded-lg') !!}
                    @include('view.blocks.index', ['model' => $contentRow->content('content')])
                </div>
            @endforeach
        </div>
    </article>
</main>