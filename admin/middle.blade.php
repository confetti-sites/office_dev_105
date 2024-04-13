@php
    [$id] = variables($variables);
    $model = modelById($id);
    $children = $model->getChildren();
@endphp

<div class="container pt-6 px-6 mx-auto xl:px-24 grid">
    @include('admin.breadcrumbs', ['currentId' => $id])
    @foreach($children as $child)
        @php($component = $child->getComponent())
        @if($component->type === 'root')
            <a href="/admin{{ $child->getId() }}">
                <div class="flex items-center justify-between w-full px-4 py-2 mt-8 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-gray-600 border border-transparent rounded-lg active:bg-gray-600 hover:bg-gray-700 focus:outline-none focus:shadow-outline-gray">
                    {{ $component->getLabel() }}
                </div>
            </a>
            @continue
        @endif
        @include("admin.structure.{$component->type}.component_admin", ['model' => $child])
    @endforeach
    <div id="save-middle">
        <script type="module">
            import {storage} from '/admin/assets/js/admin_service.mjs';
            import {html, reactive} from 'https://esm.sh/@arrow-js/core';

            const getSubmitText = () => storage.getSubmitText('{{ $id }}', '{{ $model->getComponent()->getLabel() }}');
            const toSave = () => storage.getLocalStorageItems('{{ $id }}').length;
            let data = reactive({label: getSubmitText(), count: toSave()});
            window.addEventListener('local_content_changed', () => {
                data.label = getSubmitText();
                data.count = toSave();
            });
            html`
            <button class="${() => `flex items-center justify-center w-full px-5 py-3 mt-8 text-sm font-medium leading-5 ${data.count > 0 ? 'text-white bg-cyan-500 hover:bg-cyan-600 border border-transparent' : ''} rounded-md`}"
                @click="${() => {
                storage.saveFromLocalStorage('{{ getServiceApiUrl() }}', '{{ $id }}')
            }}">${() => data.label}</button>`(document.getElementById('save-middle'));
        </script>
    </div>
    @if(count($children) === 0)
        <div class="flex items-center justify-center w-full px-4 py-2 mt-8 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-cyan-600 border border-transparent rounded-lg active:bg-cyan-600 hover:bg-cyan-700 focus:outline-none focus:shadow-outline-cyan">
            <a href="/admin/{{ $id . '/~' . newId() }}">Create your first page</a>
        </div>
    @endif
</div>
