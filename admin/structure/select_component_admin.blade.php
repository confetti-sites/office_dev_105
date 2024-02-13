@php
    /**
     * @var \Confetti\Helpers\ComponentEntity $component
     * @var \Confetti\Helpers\ComponentStore $componentStore
     * @var \Confetti\Helpers\ContentStore $contentStore
     * @var string $contentId
     */
    use Confetti\Components\Select;

    $currentValue = $contentStore->findOneData($contentId) ?? $component->getDecoration('default')['value'] ?? '';
    $options = Select::getAllOptions($componentStore, $component);
    // Use hashId because alpinejs can't handel the / in the key
@endphp
<div x-data="{ {{ hashId($component->key) }}: '{{ $currentValue }}' }">
    <label class="block text-bold text-xl mb-4">
        {{ $component->getDecoration('label')['value'] }}
    </label>
    <select
            class="appearance-none bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            x-model="{{ hashId($component->key) }}"
            x-bind="field"
            name="{{ $contentId }}"
    >
        <option selected disabled>Choose an option</option>
        @foreach($options as $value => $optionLabel)
            <option value="{{ $value }}">{{ $optionLabel }}</option>
        @endforeach
    </select>
</div>
