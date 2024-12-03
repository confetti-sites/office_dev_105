@switch($block['data']['level'])
    @case(1)
        <h1 id="{{ $data['id'] }}" class="my-3 text-5xl font-extrabold dark:text-white">{!! $block['data']['text'] !!}</h1>
        @break
    @case(2)
        <h2 id="{{ $data['id'] }}" class="my-3 text-4xl font-bold dark:text-white">{!! $block['data']['text'] !!}</h2>
        @break
    @case(3)
        <h3 id="{{ $data['id'] }}" class="my-3 text-3xl font-semibold dark:text-white">{!! $block['data']['text'] !!}</h3>
        @break
    @case(4)
        <h4 id="{{ $data['id'] }}" class="mt-3 text-2xl font-medium dark:text-white">{!! $block['data']['text'] !!}</h4>
        @break
    @case(5)
        <h5 id="{{ $data['id'] }}" class="mt-3 text-xl font-normal dark:text-white">{!! $block['data']['text'] !!}</h5>
        @break
    @case(6)
        <h6 id="{{ $data['id'] }}" class="mt-3 text-lg font-light dark:text-white">{!! $block['data']['text'] !!}</h6>
        @break
@endswitch