@php
    [$id] = variables($variables);
    $model = modelById($id);
    $children = $model->getChildren();
    // If model is part of a list (has ~ in the id), it can be deleted
    $canBeDeleted = str_contains($id, '~');
@endphp

<div class="container pt-6 px-6 mx-auto max-w-4xl grid">
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
    <div class="mt-8"
         id="save-middle">
        <script type="module">
            import {storage, IconLoader} from '/admin/assets/js/admin_service.mjs';
            import {html, reactive} from 'https://esm.sh/@arrow-js/core';

            const toSave = () => storage.getLocalStorageItems('{{ $id }}').length;
            const id = '{{ $id }}';
            let state = {count: toSave(), confirmDelete: false, waiting: false};
            state = reactive(state);
            window.addEventListener('local_content_changed', () => {
                state.count = toSave();
            });

            html`
            <div class="flex flex-row w-full space-x-4">
                @if($canBeDeleted)
                <button class="${() => `basis-1/4 px-5 ` + (state.waiting ? `` : `py-3 `) + `flex items-center justify-center text-sm font-medium leading-5 text-white ${state.confirmDelete ? `bg-cyan-500 hover:bg-red-600` : `bg-cyan-500 hover:bg-cyan-600`} border border-transparent rounded-md`}"
                        @click="${() => state.confirmDelete ? (state.waiting = true) && storage.delete('{{ getServiceApi() }}', id, ()=> storage.redirectAway(id)) : state.confirmDelete = true}">
                    <span class="${() => state.waiting ? `hidden` : ``}">${() => state.confirmDelete ? `Confirm` : `Delete`}</span>
                    <span class="${() => state.waiting ? `` : `hidden`}">${IconLoader(20)}</span>
                </button>
                @endif
            <button class="{{ $canBeDeleted ? 'basis-3/4' : 'w-full' }} px-5 py-3 flex items-center justify-center text-sm font-medium leading-5 text-white bg-cyan-500 hover:bg-cyan-600 border border-transparent rounded-md"
                        @click="${() => (state.waiting = true) && storage.saveFromLocalStorage('{{ getServiceApi() }}', id)}">
                    <span class="${() => state.waiting ? `hidden` : ``}">Publish</span>
                    <span class="${() => state.waiting ? `` : `hidden`}">${IconLoader(20)}</span>
                </button>
            </div>

            `(document.getElementById('save-middle'));
        </script>
    </div>
    @if(count($children) === 0)
        <div class="flex items-center justify-center w-full px-4 py-2 mt-8 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-cyan-600 border border-transparent rounded-lg active:bg-cyan-600 hover:bg-cyan-700 focus:outline-none focus:shadow-outline-cyan">
            <a href="/admin/{{ $id . '/~' . newId() }}">Create your first page</a>
        </div>
    @endif
</div>
