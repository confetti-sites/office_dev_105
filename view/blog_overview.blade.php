@php($blogPage = newRoot(new \model\blog_overview))
<div class="bg-gray-50 flex items-center justify-center">
    @foreach($blogPage->list('blog')->columns(['title', 'description'])->get() as $blog)
        <div class="m-10 mt-0 relative space-y-4">
            <div class="rounded-lg p-4 bg-blue-300 text-xl flex justify-center m-8">
                <h3>{{ $blog->text('title')->min(1)->max(50)->default('Blog default title') }}</h3>
            </div>
            <div class="flex-1 flex justify-between items-center font-body">
                {{ $blog->text('description')->min(1)->max(100) }}
            </div>
            <div><a href="/blogs/{{ $blog->text('alias')->help('This will be used to generate the URL for this blog post.')->min(1)->max(50) }}" class="text-blue-500">Read more</a></div>
        </div>
    @endforeach
</div>
