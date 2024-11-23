@php
    $alias = str_replace('/docs/', '', request()->uri());
    $current = \model\docs\category_list\page_list::query()->whereAliasIs($alias)->first();
    $docs = newRoot(new \model\docs)->label('Docs');
@endphp

<link rel="stylesheet" href="/view/assets/css/github-light.css"/>
<div class="relative mx-auto flex max-w-8xl justify-center sm:px-2 lg:px-8 xl:px-12">
    <div class="hidden lg:relative lg:block lg:flex-none">
        <div class="sticky top-[4.5rem] -ml-0.5 h-[calc(100vh-4.5rem)] overflow-y-auto overflow-x-hidden py-16 pl-0.5">
            <nav class="text-base lg:text-sm w-64 pr-8 xl:w-72 xl:pr-16">
                <ul class="space-y-9">
                    <li>
                        <a href="/docs" class="text-lg font-body text-blue-500">Introduction</a>
                    </li>
                    @foreach($docs->list('category')->sortable()->get() as $category)
                        <li>
                            <h2 class="text-lg font-body">{{ $category->text('title')->min(1)->max(50) }}</h2>
                            <ul class="text-lg space-y-2 font-body">
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
    @if($current !== null)
        <div class="min-w-0 max-w-2xl flex-auto px-4 py-16 lg:max-w-none lg:pl-8 lg:pr-0 xl:px-16">
            <article>
                <header class="mb-9 space-y-1">
                    <h1 class="text-3xl font-semibold text-blue-500">{{ $current->intro->getTitle() }}</h1>
                    <div class="font-body text-gray-800">{!! $current->discussion('intro')->label('Intro')->help('The URL to the GitHub Discussion')->default('')->getHtml() !!}</div>
                </header>
                <div>
                    @foreach($current->list('feature')->sortable()->columns(['content'])->get() as $feature)
                        <section class="mb-8 font-body">
                            <h2 id="{{ $feature->content->getTitle() }}" class="text-2xl font-semibold text-blue-500">{{ $feature->content->getTitle() }}</h2>
                            <div class="text-gray-800">{!! $feature->discussion('content')->label('GitHub Discussion')->help('The URL to the GitHub Discussion')->getHtml() !!}</div>
                            <label class="m-2 h-10 block">
                                <a href="{{ $feature->content->getUrl() }}" class="float-right justify-between px-3 py-2 m-2 ml-0 text-sm leading-5 cursor-pointer text-blue-500 border border-blue-500 hover:bg-blue-500 hover:text-white rounded-md">
                                    More
                                </a>
                            </label>
                        </section>
                    @endforeach
                </div>
            </article>
        </div>
        <div class="hidden xl:sticky xl:-mr-6 xl:block  xl:flex-none xl:overflow-y-auto xl:py-16 xl:pr-6">
            <nav class="w-56 font-body">
                <h2 class="pb-2">On this page</h2>
                <ul class="space-y-2">
                    @foreach($current->features()->get() as $feature)
                        <li>
                            <a href="#{{ $feature->content->getTitle() }}" class="text-blue-500">{{ $feature->content->getTitle() }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>
    @else
        <div class="min-w-0 max-w-2xl flex-auto px-4 py-16 lg:max-w-none lg:pl-8 lg:pr-0 xl:px-16">
            <h1 class="text-3xl font-semibold">{{ $docs->first_page->getTitle() }}</h1>
            <div class="mt-4 discussion text-gray-800">{!! $docs->discussion('first_page')->label('First page discussion')->help('The URL to the GitHub Discussion')->default('')->getHtml() !!}</div>
        </div>
    @endif
</div>
