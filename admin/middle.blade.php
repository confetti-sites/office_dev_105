@php
    [$currentContentId] = variables($variables);
    // Get parent content id
    // \w|~ remove word characters (with ulid)
    // /-/ remove target ids
    $parentContentId = preg_replace('#/(\w|~|/-/)+$#', '', $currentContentId);
    $hasParent = str_contains($currentContentId, '~');
    $model = modelById($currentContentId);
    $children = $model->getChildren();
    $total = 0;
@endphp
{{--shadow left side--}}
<div class="container pt-6 px-6 mx-auto grid">
    @include('admin.breadcrumbs', ['currentId' => $currentContentId])
    @foreach($children as $child)
        @php($component = $child->getComponent())
        @if($component->type === 'root')
            <a href="/admin{{ $child->getId() }}">
                <div class="flex items-center justify-between w-full px-4 py-2 mt-8 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-gray-600 border border-transparent rounded-lg active:bg-gray-600 hover:bg-gray-700 focus:outline-none focus:shadow-outline-gray">
                    {{ $component->getLabel() }} type {{ $component->type }}
                </div>
            </a>
            @continue
        @endif
        @include("admin.structure.{$component->type}.component_admin", ['model' => $child])
        @php($total++)
    @endforeach
    @if($total > 0)
{{--         Ensure that we have the parent id for every item--}}
{{--        <input type="hidden" name="{{ $currentContentId }}" value="__is_parent" x-bind="field"--}}
{{--               x-init="$dispatch('saveThisField')">--}}
        <button
                class="flex items-center justify-between w-full px-5 py-3 mt-8 text-sm font-medium leading-5 text-white bg-cyan-500 hover:bg-cyan-600 border border-transparent rounded-md"
                parent-content-id="{{ $parentContentId }}"
                has-parent="{{ $hasParent }}"
        >
            <span>Save</span>
        </button>
    @endif
    @if(count($children) === 0)
        <div class="flex items-center justify-center w-full px-4 py-2 mt-8 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-cyan-600 border border-transparent rounded-lg active:bg-cyan-600 hover:bg-cyan-700 focus:outline-none focus:shadow-outline-cyan">
            ? link to something ?
                        <a href="/admin/{{ $model->getId() . '/~' . newId() }}">Create your first page</a>
        </div>
    @endif
</div>