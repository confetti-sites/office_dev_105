@php /** @var \Confetti\Helpers\ComponentStandard $model */ @endphp

<div class="block text-bold text-xl mt-8 mb-4">
    {{ $model->getComponent()->getLabel() }}
</div>

<img x-show="!!$store.form.previewImage" :src="$store.form.previewImage">

<div x-show="!$store.form.previewImage" class="flex items-center justify-center w-full">
    <label for="dropzone-file"
           class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
        <div class="flex flex-col items-center justify-center pt-5 pb-6">
            <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true"
                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
            </svg>
            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span>
                or drag and drop</p>
            <p class="text-xs text-gray-500">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
        </div>
        <input
                @change="$store.form.uploadImage($event)"
                id="dropzone-file"
                type="file"
                x-bind="field"
                name="{{ $model->getId() }}"
                value="{{ $model->get() ?? $model->getComponent()->getDecoration('default') }}"
                class="hidden"
        />
    </label>
</div>

@php
    $value = $model->get() ?? $model->getComponent()->getDecoration('default');
    $textPars1 = [
        'id' => $model->getId(),
        'value' => $value,
        'placeholder' => 'SVG, PNG, JPG or GIF (MAX. 800x400px)',
    ];
@endphp
{{--@component('admin.structure.input.upload', $textPars1) @endcomponent--}}


<label class="block mt-4 text-sm">
    {{-- <input
            type="file"
            x-bind="field"
            name="{{ $contentId }}"
            value="{{ $contentStore->find($model->key) }}"
    > --}}
</label>
