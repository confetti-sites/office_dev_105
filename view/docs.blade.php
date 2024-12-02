@php
    $alias = str_replace('/docs/', '', request()->uri());
    $docs = newRoot(new \model\docs)->label('Docs');
    $current = \model\docs\category_list\sub_list\page_list::query()->whereAliasIs($alias)->first();
    // Get all pages on the same subcategory
    /** @var \model\docs\category_list\sub_list $currentCategory */
    $currentCategory = modelById($current->getParentId());
@endphp

<div class="relative mx-auto md:flex max-w-8xl justify-center sm:px-2 lg:px-8 xl:px-12">
    <div class="js-left-menu hidden md:relative md:relative md:block md:flex-none">
        <div class="sticky md:top-[4.5rem] -ml-0.5 h-[calc(100vh-4.5rem)] overflow-y-auto overflow-x-hidden py-6 md:py-16 ml-4 md:pl-0.5">
            <nav class="text-base lg:text-sm w-64 pr-8 xl:w-64 xl:pr-4">
                <ul class="space-y-4">
                    @foreach($docs->list('category')->labelPlural('Categories')->sortable()->get() as $category)
                        <li>
                            <h2 class="text-lg font-body">{{ $category->text('title')->min(1)->max(50) }}</h2>
                            <ul class="text-lg mt-1 space-y-4 font-body">
                                @foreach($category->list('sub')->label('Sub category')->sortable()->get() as $sub)
                                    <li class="ml-2 my-2">
                                        <a href="/docs/{{ $sub->pages()->first()->alias }}" class="text-blue-500">{{ $sub->text('title')->min(1)->max(50) }}</a>
                                        @foreach($sub->list('page')->sortable()->columns(['content', 'banner'])->get() as $page)
                                            <ul class="lg:hidden space-y-3">
                                                <li class="ml-2 my-2">
                                                    <a href="/docs/{{ $page->text('alias')->min(1)->max(50) }}" class="text-blue-500">{{ $page->content->getTitle() }}</a>
                                                </li>
                                            </ul>
                                        @endforeach
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
        <div class="min-w-0 max-w-3xl flex-auto px-4 py-16 lg:max-w-none lg:pl-8 lg:pr-0 xl:px-16">
            <article class="text-gray-700">
                <div class="mb-9 space-y-1">
                    <h1 class="text-3xl font-semibold text-gray-800 mb-2">{{ $current->content->getTitle() }}</h1>
                    @if ($current->banner->get())
                        {!! $current->image('banner')->widthPx(900)->getPicture(alt: $current->content->getTitle(), class: 'mt-4 mb-4') !!}
                    @endif
                    <div class="mt-4 mb-4 text-gray-800 font-body">{!! $current->discussion('content')->label('GitHub Discussion')->help('The URL to the GitHub Discussion')->getHtml() !!}</div>
                    <label class="m-2 h-10 block">
                        <a href="{{ $current->content->getUrl() }}" class="float-right justify-between px-3 py-2 m-2 ml-0 text-sm leading-5 cursor-pointer text-blue-500 border border-blue-500 hover:bg-blue-500 hover:text-white rounded-md">
                            FAQ
                        </a>
                    </label>
                </div>
            </article>
        </div>
        <div class="hidden lg:relative lg:block lg:flex-none">
            <div class="sticky top-[4.5rem] ml-2 h-[calc(100vh-4.5rem)] overflow-y-auto overflow-x-hidden py-16 pl-4">
                <nav class="text-base lg:text-sm w-52 pr-8 xl:w-64 xl:pr-4">
                    @if(count($currentCategory->pages()->get()))
                        <h2 class="pb-2 text-lg font-body text-gray-700">All components:</h2>
                        <ul class="space-y-3 text-lg font-body">
                            @foreach($currentCategory->pages()->get() as $page)
                                <li class="ml-2">
                                    <a href="/docs/{{ $page->alias }}" class="text-blue-500">{{ $page->content->getTitle() }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </nav>
            </div>
        </div>
    @else
        <div class="min-w-0 max-w-2xl flex-auto px-4 py-16 lg:max-w-none lg:pl-8 lg:pr-0 xl:px-16">
            <h1 class="text-3xl font-semibold text-gray-800">{{ $docs->first_sub->getTitle() }}</h1>
            <div class="mt-4 discussion text-gray-800">{!! $docs->discussion('first_sub')->label('First sub discussion')->help('The URL to the GitHub Discussion')->default('')->getHtml() !!}</div>
        </div>
    @endif
</div>

@pushonce('script_docs')
    <link rel="stylesheet" href="/view/assets/css/github-light.css"/>
@endpushonce
@pushonce('script_docs')
    <script defer>
        const docMenuToggle = document.getElementById('menu-toggle');
        docMenuToggle.addEventListener('click', () => {
            document.getElementsByClassName('js-left-menu')[0].classList.toggle('hidden');
        });
    </script>
@endpushonce