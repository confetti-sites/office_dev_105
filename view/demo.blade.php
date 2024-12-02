@php($demo = newRoot(new \model\homepage\demo)->label('Demo'))

<div class="dark:bg-gray-900 pt-8">
    @php($blocks = $demo->list('block')->label('Demo')->columns(['title', 'description', 'image'])->sortable()->min(1)->max(6)->get())
    @foreach($blocks as $block)
        <div class="container py-4 md:flex gap-6">
            @php($position = $block->select('image_position')->options(['left', 'right'])->default('right')->required()->get())
            <div class="md:w-1/2 opacity-1 p-2 py-2">
                <h2 class="text-2xl dark:text-white text-gray-900">{{ $block->text('title')->min(1)->max(100) }}</h2>
                <p class="mx-auto mb-8 mt-4 max-w-2xl font-light text-gray-500 md:mb-12 sm:text-xl font-body">
                    @include('view.blocks.index', ['model' => $block->content('description')])
                </p>
            </div>
            <div class="md:w-1/2 mt-8 md:mt-0 opacity-1 py-2 @if($position == 'left') -order-1 @endif">
                <img src="{{ $block->image('image')->widthPx(300) }}" alt="">
            </div>
        </div>
    @endforeach
</div>
