@php($playground = newRoot(new \model\playground)->label('Playground'))
<div class="pt-8">
    <div class="container py-4 mt-8">
        <div class="md:w-1/2 opacity-1 p-2 py-2">
            <h2 class="text-2xl dark:text-white text-gray-900">One line text</h2>
            <p>{!! $playground->text('one_line_text')->min(1)->max(100) !!}</p>
        </div>
    </div>
    <div class="container py-4 mt-8">
        <div class="md:w-1/2 opacity-1 p-2 py-2">
            <h2 class="text-2xl dark:text-white text-gray-900">Image</h2>
            @php($alt = $playground->text('image_alt')->min(1)->max(100)->default('Image'))
            <p>{!! $playground->image('image')->widthPx(300)->getSourcesHtml($alt) !!}</p>
        </div>
    </div>
</div>
