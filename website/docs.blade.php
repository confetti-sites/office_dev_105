@php
    $alias = str_replace('/docs/', '', request()->uri());
    $docs = newRoot(new \model\docs)->label('Docs');
    $current = \model\docs\category_list\sub_list\page_list::query()->whereAliasIs($alias)->first();
    // Get all pages on the same subcategory
    /** @var \model\docs\category_list\sub_list $currentCategory */
    if ($current !== null) {
        $currentCategory = modelById($current->getParentId());
    }
@endphp
@extends('website.layouts.main')

@section('content')
<!-- Left menu -->
<div class="relative container mx-auto md:flex max-w-8xl justify-center sm:px-2 lg:px-4">
    <div class="absolute top-[35rem] -left-4 w-64 h-64 bg-yellow-100 rounded-full mix-blend-multiply filter blur-xl opacity-70 -z-10"></div>
    <div class="absolute top-[30rem] right-0 w-72 h-72 bg-green-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 -z-10"></div>
    <div class="absolute bottom-[7rem] left-20 w-72 h-72 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 -z-10"></div>
    <div class="js-left-menu hidden md:relative md:relative md:block md:flex-none">
        <div class="sticky md:top-[4.5rem] -ml-0.5 overflow-y-auto overflow-x-hidden py-6 md:py-16 ml-4 md:pl-0.5">
            <nav class="text-base lg:text-sm w-52 pr-8">
                <ul class="space-y-4">
                    @foreach($docs->list('category')->labelPlural('Categories')->sortable()->get() as $category)
                        <li>
                            <h2 class="text-lg font-body">{{ $category->text('title')->min(1)->max(50) }}</h2>
                            <ul class="text-lg mt-1 space-y-4 font-body">
                                @foreach($category->list('sub')->label('Sub category')->sortable()->get() as $sub)
                                    <li class="ml-2 my-2">
                                        <a href="/docs/{{ $sub->pages()->first()->alias }}" class="text-blue-500">{{ $sub->text('title')->min(1)->max(50) }}</a>
                                        @if(count($sub->pages()->get()) > 1)
                                            @foreach($sub->list('page')->sortable()->columns(['content', 'banner'])->get() as $page)
                                                <ul class="lg:hidden space-y-3">
                                                    <li class="ml-2 my-2">
                                                        <a href="/docs/{{ $page->text('alias')->min(1)->max(50) }}" class="text-blue-500">{{ $page->content->getTitle() }}</a>
                                                    </li>
                                                </ul>
                                            @endforeach
                                        @endif
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
        <!-- Article -->
        <div class="min-w-0 max-w-3xl flex-auto px-4 py-16 lg:max-w-none lg:pl-8 lg:pr-0">
            <article class="text-gray-700">
                <div class="mb-9 space-y-1">
                    <h1 class="text-3xl font-semibold text-gray-800 mb-2">{{ $current->content->getTitle() }}</h1>
                    @if ($current->banner->get())
                        {!! $current->image('banner')->widthPx(900)->getPicture(class: 'mt-4 mb-4', alt: 'Example of of result of the ' . $current->content->getTitle() . ' Component') !!}
                    @endif
                    <div class="mt-4 mb-4 text-gray-800 font-body">{!! $current->discussion('content')->label('GitHub Discussion')->help('The URL to the GitHub Discussion')->getHtml() !!}</div>
                    @if(count($current->relatedLinks()->get()) > 0)
                        <div class="mb-4 py-8 text-gray-800 font-body">
                            <ul class="list-disc list-inside">
                                @foreach($current->list('related_link')->sortable()->label('Related Link')->get() as $link)
                                    <li><a href="{{ $link->text('link') }}" class="text-blue-500">{{ $link->text('title') }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <label class="m-2 h-10 block">
                        <a href="{{ $current->content->getUrl() }}" class="float-right justify-between px-3 py-2 m-2 ml-0 text-sm leading-5 cursor-pointer text-blue-500 border border-blue-500 hover:bg-blue-500 hover:text-white rounded-md">
                            FAQ
                        </a>
                    </label>
                </div>
            </article>

            @guest
                <a href="/waiting-list" class="relative ml-auto flex h-10 w-full mt-20 items-center justify-center before:absolute before:inset-0 before:rounded-full before:bg-primary before:transition-transform before:duration-300 hover:before:scale-105 active:duration-75 active:before:scale-95 px-4">
                    <span class="relative text-sm font-semibold text-white">Stay updated by joining the waitlist</span>
                </a>
            @endguest
        </div>
        <!-- Right menu -->
        <div class="hidden lg:relative lg:block lg:flex-none ml-6 w-40">
            <div class="sticky top-[4.5rem] ml-2 h-[calc(100vh-4.5rem)] overflow-y-auto overflow-x-hidden py-16 pl-4">
                <nav class="text-base lg:text-sm">
                    @if(count($currentCategory->pages()->get()) > 1)
                        @if($currentCategory->title != '')
                            <h2 class="pb-2 text-lg font-body text-gray-700">{{ $currentCategory->title }}</h2>
                        @endif
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
        <!-- 404/Start page -->
        <div class="min-w-0 max-w-2xl flex-auto px-4 py-16 lg:max-w-none lg:pl-8 lg:pr-0 xl:px-16">
            <h1 class="text-3xl font-semibold text-gray-800">{{ $docs->text('start_page_title')->label('Start page title')->get() }}</h1>
            <div class="mt-4 discussion text-gray-800">@include('website.includes.blocks.index', ['model' => $docs->content('start_page_content')->label('Start page content')])</div>
        </div>
    @endif
</div>

@pushonce('style_docs')
    <link rel="stylesheet" href="/website/assets/css/github-light.css"/>
@endpushonce
@pushonce('end_of_body_docs')
    <script defer>
        document.getElementById('menu-toggle').addEventListener('click', () => {
            document.getElementsByClassName('js-left-menu')[0].classList.toggle('hidden');
        });
    </script>
@endpushonce
@endsection