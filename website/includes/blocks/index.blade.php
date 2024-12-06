@foreach($model->get()['blocks'] ?? [] as $block)
    @include('website.includes.blocks.' . $block['type'], ['block' => $block])
@endforeach