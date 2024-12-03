@foreach($model->get()['blocks'] ?? [] as $block)
    @include('website.blocks.' . $block['type'], ['block' => $block])
@endforeach