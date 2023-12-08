@php
    /**
     * @var \Confetti\Helpers\ComponentEntity $component
     * @var \Confetti\Helpers\ComponentStore $componentStore
     */
@endphp
<h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
    {{ $component->getDecoration('label')['value'] }}
</h2>

<div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
    @foreach($componentStore->whereParentKey($component->key) as $componentChild)
        @include("admin.structure.{$componentChild->type}_component_admin", ['componentRepository' => $componentStore, 'component' => $componentChild])
    @endforeach
</div>
