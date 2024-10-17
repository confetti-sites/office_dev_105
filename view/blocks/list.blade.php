@switch($block['data']['style'])
    @case('unordered')
        <ul class="max-w-md mt-3 space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400">
            @foreach($block['data']['items'] as $item)
                <li>{!! $item['content'] !!}</li>
            @endforeach
        </ul>
        @break
    @case('ordered')
        <ol class="max-w-md mt-3 space-y-1 text-gray-500 list-decimal list-inside dark:text-gray-400">
            @foreach($block['data']['items'] as $item)
                <li>{!! $item['content'] !!}</li>
            @endforeach
        </ol>
        @break
@endswitch
