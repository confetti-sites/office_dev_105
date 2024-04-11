@php use Confetti\Components\Map; @endphp
@php([$currentContentId] = variables($variables))

@php /** @var string $currentContentId */ @endphp
@php($root = newRoot(new \model))

<div class="text-gray-500 dark:text-gray-400">
    <ul class="mt-16">
        @foreach($root->getChildren() as $firstChild)
            <li class="relative">
                @php($component = $firstChild->getComponent())
                @php($isCurrent = $firstChild->getId() === $currentContentId || str_starts_with($currentContentId, $firstChild->getId() . '/'))
                @if($isCurrent)
                    <span class="absolute inset-y-0 left-0 w-2 rounded-tr-lg rounded-br-lg bg-primary-300"
                          aria-hidden="true"></span>
                @endif
                <a
                        class="inline-flex items-center w-full pl-3 py-4 font-semibold hover:text-gray-800 dark:hover:text-gray-200 @if($isCurrent)text-gray-800 dark:text-gray-100 @endif"
                        href="/admin{{ $firstChild->getId() }}"
                        id="left-menu-badge-{{ slugId($firstChild->getId()) }}"
                >
                    <script type="module">
                        import {content} from '/admin/assets/js/admin_service.mjs';
                        import {html, reactive} from 'https://esm.sh/@arrow-js/core';

                        let data = reactive({length: content.getLocalStorageItems('{{ $firstChild->getId() }}')}).length;
                        window.addEventListener('local_content_changed', (event) => {
                            data.length = content.getLocalStorageItems('{{ $firstChild->getId() }}').length
                        });
                        html`<span class="${() => `ml-4 w-fit ${data.length > 0 ? ' border-dashed border-b-4 border-cyan-100 ' : ''}`}">{{ $firstChild->getComponent()->getLabel() }}</span>`(document.getElementById('left-menu-badge-{{ slugId($firstChild->getId()) }}'));
                    </script>
                </a>
                {{-- Where are only interested in the children that are maps (not a list or value field). --}}
                @php($children = method_exists($firstChild, 'getChildren') ? array_filter($firstChild->getChildren(), fn($c) => $c instanceof Map) : [])
                @if(!empty($children))
                    <ul
                            class=" mt-2 pl-1 space-y-2 overflow-hidden font-medium text-gray-500"
                            aria-label="submenu"
                    >
                        @foreach($children as $secondChild)
                            @if($secondChild instanceof Map)
                                @php($isCurrent = $secondChild->getId() === $currentContentId)
                                <li class="relative hover:text-gray-800 dark:hover:text-gray-200">
                                    @if($isCurrent)
                                        <span class="absolute inset-y-0 -left-0.5 w-3 rounded-tr-lg rounded-br-lg bg-primary-300"
                                              aria-hidden="true">
                                        </span>
                                    @endif
                                    <a class="inline-flex items-center w-full pl-10 py-4 font-semibold hover:text-gray-800 dark:hover:text-gray-200 @if($isCurrent)text-gray-800 dark:text-gray-100 @endif"
                                       href="/admin{{ $secondChild->getId() }}"
                                       id="left-menu-badge-{{ slugId($secondChild->getId()) }}">
                                        <script type="module">
                                            import {content} from '/admin/assets/js/admin_service.mjs';
                                            import {html, reactive} from 'https://esm.sh/@arrow-js/core';

                                            let data = reactive({length: content.getLocalStorageItems('{{ $secondChild->getId() }}')}).length;
                                            window.addEventListener('local_content_changed', (event) => {
                                                data.length = content.getLocalStorageItems('{{ $secondChild->getId() }}').length
                                            });
                                            html`<span class="${() => `w-fit ${data.length > 0 ? ' border-dashed border-b-4 border-cyan-100 ' : ''}`}">{{ $secondChild->getComponent()->getLabel() }}</span>`(document.getElementById('left-menu-badge-{{ slugId($secondChild->getId()) }}'));
                                        </script>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</div>
