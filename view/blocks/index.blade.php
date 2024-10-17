@foreach($model->get()['blocks'] ?? [] as $block)
    @include('view.blocks.' . $block['type'], ['block' => $block])
@endforeach