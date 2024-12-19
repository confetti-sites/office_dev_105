@php($compare = newRoot(new \model\homepage\compare)->label('Compare'))
@php($cases = $compare->list('case')->sortable()->min(1)->max(4)->get())
<div class="relative mb-12">
    <div class="absolute top-0 right-0 w-14 md:w-72 h-14 md:h-72 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70"></div>
    <div class="absolute top-20 -left-4 w-14 md:w-72 h-14 md:h-72 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-70"></div>
    <div class="absolute -bottom-32 left-20 w-72 h-72 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-70"></div>
    <div class="bg-gray-50 flex items-center justify-center">
        <div class="relative w-full">
            <div class="relative">
                <button type="button" class="text-3xl font-bold text-center mt-10 lg:mt-20 w-full">{{ $compare->text('title')->min(1)->max(50) }}</button>
                <homepage-compare>
                    <!-- skeleton loader -->
                    <div class="flex items-center justify-center mt-4 mb-4 space-x-4 text-xl border-b border-gray-300">
                        @foreach($cases as $tapNr => $case)
                            <div class="{{ $tapNr === 0 ? 'text-blue-600 border-b border-blue-600' : 'hover:text-blue-600' }} px-2 py-2 cursor-pointer">
                                <span>{{ $case->title }}</span>
                                <span>{{ $case->description }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="min-h-80"></div>
                </homepage-compare>
            </div>
        </div>
    </div>
</div>

@pushonce('end_of_body_homepage_compare')
    <script type="module" defer>
        import {html, reactive} from 'https://esm.sh/@arrow-js/core';

        customElements.define('homepage-compare', class extends HTMLElement {
            constructor() {
                super();
                this.state = reactive({
                    tab: 0,
                });
                this.innerHTML = '';
            }
            connectedCallback() {
                html`
            <div class="flex items-center justify-center mt-4 mb-4 space-x-4 text-xl border-b border-gray-300">
                @foreach($cases as $tapNr => $case)
                    <div class="${() => 'px-2 py-2 cursor-pointer ' + (this.state.tab === {{ $tapNr }} ? 'text-blue-600 border-b border-blue-600' : 'hover:text-blue-600')}"
                         @click="${() => this.state.tab={{ $tapNr }}}">
                        <span>{{ $case->text('title')->min(1)->max(20) }}</span>
                        <span>{{ $case->text('description')->min(1)->max(30) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="md:container mx-auto">
                @foreach($cases as $tapNr => $case)
                    <div class="${() => 'grid grid-cols-1 md:grid-cols-2 ' + (this.state.tab === {{ $tapNr }} ? 'block' : 'hidden')}">
                        @foreach($case->list('column')->sortable()->columns(['title'])->min(2)->max(2)->get() as $column)
                            <div class="my-4 xl:m-6 mt-0 relative space-x-4 space-y-4">
                                <div class="flex justify-center mx-8 md:mx-14 lg:mx-24  my-4 lg:my-8 p-4 bg-blue-300 text-xl rounded-lg">
                                    <h3>{{ $column->text('title')->min(1)->max(50) }}</h3>
                                </div>
                                @foreach($column->list('step')->sortable()->columns(['description'])->min(1)->max(10)->get() as $nr => $step)
                                    <div class="bg-white rounded-lg">
                                        <div class="p-4 flex items-center justify-between space-x-4">
                                            <div class="rounded-lg p-2 bg-blue-300 text-white">
                                                Step {{ $nr + 1 }}
                                            </div>
                                            <div class="flex-1 flex justify-between items-center font-body">
                                                {{ $step->text('description')->bar(['b', 'i', 'u'])->min(3)->max(100) }}
                                            </div>
                                        </div>
                                        <!-- Let tailwind trigger: md:text-base lg:text-lg xl:text-xl w-full hidden lg:inline-block xl:inline-block text-black lg:inline-block -->
                                        @if(!$step->example->isEmpty())
                                            <div class="pb-3 text-center text-gray-500 font-body">
                                                @include('website.includes.blocks.index', ['model' => $step->content('example')])
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        `(this);
            }
        });

    </script>
@endpushonce


