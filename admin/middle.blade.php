@php
    /**
     * @var \Confetti\Helpers\ContentStore $contentStore
     * @var \Confetti\Helpers\ComponentStore $componentStore
     * @var string $currentContentId
     */
    $componentKey = Confetti\Helpers\ComponentStandard::keyFromId($currentContentId);
    // Get parent content id
    // \w|~ remove word characters (with ulid)
    // /-/ remove target ids
    $parentContentId = preg_replace('#/(\w|~|/-/)+$#', '', $currentContentId);
    $hasParent = str_contains($currentContentId, '~');
    $components = $componentStore->whereParentKey($componentKey);
    $total = 0;
@endphp
<div class="container pt-6 px-6 mx-auto grid">
    @if($parentContentId && $parentContentId !== '/section')
        <div class="gap-6 mb-4">
            <a
                    class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"
                    href="/admin{{ $parentContentId }}"
            >
                &#8592; Back to overview
            </a>
        </div>
    @endif
    @foreach($components as $component)
        @if($component->type == 'section')
            <a href="/admin{{ $component->key }}">
                <div class="flex items-center justify-between w-full px-4 py-2 mt-8 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-gray-600 border border-transparent rounded-lg active:bg-gray-600 hover:bg-gray-700 focus:outline-none focus:shadow-outline-gray">
                    {{ $component->getDecoration('label')['value'] }}
                </div>
            </a>
            @continue
        @endif
        @php($suffix = str_replace($componentKey, '', $component->key))
        @include("admin.structure.{$component->type}_component_admin", ['componentStore' => $componentStore, 'component' => $component, 'contentStore' => $contentStore, 'contentId' => $currentContentId . $suffix])
        @php($total++)
    @endforeach
    @if($total > 0)
        {{-- Ensure that we have the parent id for every item --}}
        <input type="hidden" name="{{ $currentContentId }}" value="__is_parent" x-bind="field"
               x-init="$dispatch('saveThisField')">
        <button
                class="flex items-center justify-between w-full px-4 py-2 mt-8 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"
                parent-content-id="{{ $parentContentId }}"
                has-parent="{{ $hasParent }}"
                x-bind="submit"
        >
            {{-- x-show="countFields() > 1" --}}
            Save
        </button>
    @endif
    @if(count($components) === 0)
        {{--        show welcome first page in dashboard, documentation, handy links, ... --}}
        <div class="flex items-center justify-center w-full px-4 py-2 mt-8 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
            <a href="/admin/blog">Create your first page</a>
        </div>
    @endif
</div>
@pushonce('script_middle')
    <script>
        function countFields() {
            return document.querySelectorAll("[x-bind='field']").length;
        }
    </script>
@endpushonce
