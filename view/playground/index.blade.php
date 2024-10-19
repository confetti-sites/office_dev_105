@php($playground = newRoot(new \model\playground)->label('Playground'))
<div class="pt-8">
    <div class="container py-4 mt-8 p-2 py-2">
        <h2 class="text-2xl dark:text-white text-gray-900">One line text</h2>
        <p>{!! $playground->text('one_line_text')->min(1)->max(100) !!}</p>
    </div>
    <div class="container py-4 mt-8 p-2 py-2">
        <h2 class="text-2xl dark:text-white text-gray-900">Color</h2>
        <div class="flex items-center space-x-4 h-5 w-full" style="background-color: {{ $playground->color('color_picker') }}">
        </div>
    </div>
    <div class="container py-4 mt-8 p-2 py-2">
        <h2 class="text-2xl dark:text-white text-gray-900">List</h2>
        <ul class="max-w-md mt-3 space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400">
            @foreach($playground->list('list')->min(1)->max(3)->get() as $item)
                <li class="text-sm">{!! $item->text('the_text') !!}</li>
            @endforeach
        </ul>
    </div>
    <div class="container py-4 mt-8 p-2 py-2">
        <h2 class="text-2xl dark:text-white text-gray-900">Select</h2>
        Selected: {{ $playground->select('select')->options(['Option 1', 'Option 2', 'Option 3']) }}
    </div>
    <div class="container py-4 mt-8 p-2 py-2">
        <h2 class="text-2xl dark:text-white text-gray-900">Rich text</h2>
        @include('view.blocks.index', ['model' => $playground->content('rich_text')])
    </div>
    <div class="container py-4 mt-8 p-2 py-2">
        <h2 class="text-2xl dark:text-white text-gray-900">Image</h2>
        @php($alt = $playground->text('image_alt')->min(1)->max(100)->default('Image'))
        <picture class="w-full h-full object-cover rounded-lg shadow-md">
            {!! $playground->image('image')->widthPx(1200)->getSourcesHtml($alt) !!}
        </picture>
    </div>
</div>
