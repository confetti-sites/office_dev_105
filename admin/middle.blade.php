@php
    [$currentContentId] = variables($variables);
    $model = modelById($currentContentId);
    $children = $model->getChildren();
    $total = 0;
@endphp

<div class="container pt-6 px-6 mx-auto xl:px-24 grid">
    @include('admin.breadcrumbs', ['currentId' => $currentContentId])
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
        @php($total++)
    @endforeach
    @if($total > 0)
        <div id="save-middle">
            <script type="module">
                import {content} from '/admin/assets/js/admin_service.mjs';
                import {html} from 'https://esm.sh/@arrow-js/core';

                html`
                <button class="flex items-center justify-between w-full px-5 py-3 mt-8 text-sm font-medium leading-5 text-white bg-cyan-500 hover:bg-cyan-600 border border-transparent rounded-md"
                    @click="${() => {
                    content.saveLocalStorage('{{ getServiceApiUrl() }}', '{{ $currentContentId }}')
                }}">
                    <span>Save</span>
                </button>`(document.getElementById('save-middle'));
            </script>
        </div>
    @endif
    @if(count($children) === 0)
        <div class="flex items-center justify-center w-full px-4 py-2 mt-8 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-cyan-600 border border-transparent rounded-lg active:bg-cyan-600 hover:bg-cyan-700 focus:outline-none focus:shadow-outline-cyan">
            <a href="/admin/{{ $model->getId() . '/~' . newId() }}">Create your first page</a>
        </div>
    @endif
</div>
{{--                parent-content-id="{{ $parentContentId }}"--}}
{{--                has-parent="{{ $hasParent }}"--}}
