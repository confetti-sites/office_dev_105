@php /** @var \Confetti\Components\Select $model */ @endphp
<div class="block text-bold text-xl mt-8 mb-4">
    {{ $model->getComponent()->getLabel() }}
</div>
<select
        class="appearance-none bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-emerald-700 dark:focus:border-emerald-700"
        name="{{ $model->getId() }}"
>
    <option selected disabled>Choose an option</option>
    @foreach($model->getOptions() as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
    @endforeach
</select>
