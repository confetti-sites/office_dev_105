@php /** @var \Confetti\Components\SelectFile $model */ @endphp
<div class="block text-bold text-xl mt-8 mb-4">
    {{ $model->getLabel() }}
</div>
<select class="appearance-none bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        name="{{ $model->getId() }}"
>
    <option selected disabled>Choose an option</option>
    @foreach($model->getOptions() as $child)
        <option value="{{ $child->getComponent()->source->getPath() }}">{{ $child->getLabel() }}</option>
    @endforeach
</select>
@foreach($model->getOptions() as $pointerChild)
    @foreach($pointerChild->getChildren() as $grandChild)
        @include("admin.structure.{$grandChild->getComponent()->type}_component_admin", ['model' => $grandChild])
    @endforeach
@endforeach
@pushonce('end_of_body_select')
    <script>
        console.log('select');
    </script>
@endpushonce
