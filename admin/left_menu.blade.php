@php use Confetti\Components\Map; @endphp
@php([$currentContentId] = variables($variables))

@php /** @var string $currentContentId */ @endphp
@php($root = newRoot(new \model)->canFake(false))

<div class="text-gray-500">
    <ul class="mt-16">
        <li class="sr-only"><a href="#main">Skip to main content</a></li>
        @foreach($root->getChildren() as $firstChild)
            <li class="relative">
                @php($component = $firstChild->getComponent())
                @php($isCurrent = $firstChild->getId() === $currentContentId || str_starts_with($currentContentId, $firstChild->getId() . '/'))
                @if($isCurrent)
                    <span class="absolute inset-y-1 left-0 w-1 rounded-tr-lg rounded-br-lg bg-primary-300"
                          aria-hidden="true"></span>
                @endif
                <a class="inline-flex items-center w-full pl-6 py-4 font-semibold"
                   href="/admin{{ $firstChild->getId() }}">
                    <span class="w-fit hover:text-gray-800 dark:hover:text-gray-200 @if($isCurrent)text-gray-800 dark:text-gray-100 @endif">{{ $component->getLabel() }}</span>
                    <span class="_left_menu_badge text-emerald-300 hidden"
                          id="_left_menu_badge-{{ $firstChild->getId() }}">&nbsp;*</span>
                </a>
                {{-- Where are only interested in the children that are maps (not a list or value field). --}}
                @php($children = method_exists($firstChild, 'getChildren') ? array_filter($firstChild->getChildren(), fn($c) => $c instanceof Map) : [])
                @if(!empty($children))
                    <ul class=" mt-2 pl-4 overflow-hidden font-medium text-gray-500"
                        aria-label="submenu">
                        @foreach($children as $secondChild)
                            @php($isCurrent = $secondChild->getId() === $currentContentId)
                            <li class="relative">
                                @if($isCurrent)
                                    <span class="absolute inset-y-1 -left-3 w-1 rounded-tr-lg rounded-br-lg bg-primary-300"
                                          aria-hidden="true"></span>
                                @endif
                                <a class="inline-flex items-center w-full py-4 font-semibold hover:text-gray-800 dark:hover:text-gray-200 @if($isCurrent)text-gray-800 dark:text-gray-100 @endif"
                                   href="/admin{{ $secondChild->getId() }}">
                                    <span class="w-fit ml-6 hover:text-gray-800 dark:hover:text-gray-200 @if($isCurrent)text-gray-800 dark:text-gray-100 @endif">{{ $secondChild->getComponent()->getLabel() }}</span>
                                    <span class="_left_menu_badge text-emerald-300 hidden"
                                          id="_left_menu_badge-{{ $secondChild->getId() }}">&nbsp;*</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</div>

@pushonce('end_of_body_left_menu')
    <script type="module">
        import {Storage} from '/admin/assets/js/admin_service.mjs';

        function updateBadges() {
            document.querySelectorAll('._left_menu_badge').forEach((el) => {
                const exists = Storage.getLocalStorageItems(el.id.replace('_left_menu_badge-', '')).length > 0;
                el.classList.toggle('hidden', !exists);
            });
        }

        updateBadges();
        window.addEventListener('local_content_changed', () => updateBadges());
    </script>
@endpushonce
