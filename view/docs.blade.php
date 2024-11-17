@php
    $alias = str_replace('/docs/', '', request()->uri());
    $current = \model\docs\category_list\page_list::query()->whereAliasIs($alias)->first();
    $docs = newRoot(new \model\docs)->label('Docs');
@endphp
<div class="relative mx-auto flex max-w-8xl justify-center sm:px-2 lg:px-8 xl:px-12">
    <div class="hidden lg:relative lg:block lg:flex-none">
        <div class="sticky top-[4.5rem] -ml-0.5 h-[calc(100vh-4.5rem)] overflow-y-auto overflow-x-hidden py-16 pl-0.5">
            <nav class="text-base lg:text-sm w-64 pr-8 xl:w-72 xl:pr-16">
                <ul class="space-y-9">
                    @foreach($docs->list('category')->sortable()->get() as $category)
                        <li>
                            <h2 class="text-lg">{{ $category->text('title')->min(1)->max(50) }}</h2>
                            <ul class="space-y-2">
                                @foreach($category->list('page')->sortable()->get() as $page)
                                    <li class="ml-2">
                                        <a href="/docs/{{ $page->text('alias')->min(1)->max(50) }}" class="text-blue-500">{{ $page->text('title')->min(1)->max(50) }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>
    </div>
    <div class="min-w-0 max-w-2xl flex-auto px-4 py-16 lg:max-w-none lg:pl-8 lg:pr-0 xl:px-16">
        <article>
            <header class="mb-9 space-y-1">
                <h1 class="text-3xl font-semibold">{{ $current->text('title')->min(1)->max(50) }}</h1>
                <p class="text-gray-500">@include('view.blocks.index', ['model' => $current->content('content')])</p>
            </header>
            <div>
                @foreach($current->list('feature')->sortable()->columns(['feature'])->get() as $feature)
                    <section class="mb-8">
                        <p class="text-gray-500">{!! $feature->discussion('feature')->label('The URL to the GitHub Discussion')->getHtml() !!}</p>
                    </section>
                @endforeach
            </div>
        </article>
    </div>
    <div class="hidden xl:sticky xl:-mr-6 xl:block  xl:flex-none xl:overflow-y-auto xl:py-16 xl:pr-6">
        <nav class="w-56">
            <h2>On this page</h2>
        </nav>
    </div>
</div>
