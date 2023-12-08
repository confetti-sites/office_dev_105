@php
    /**
     * @var \Confetti\Helpers\ComponentStore $componentStore
     * @var \Confetti\Helpers\ComponentEntity $component
     * @var \Confetti\Helpers\ContentStore $contentStore
     * @var string $contentId
     */
@endphp
<div class="block text-bold text-xl mt-8 mb-4">
  {{ $component->getDecoration('label')['value'] }}
</div>
@php
  $content = rawurlencode($contentStore->find($contentId) ?? $component->getDecoration('default')['value'] ?? '')
@endphp

{{-- <div x-data="editor2(decodeURIComponent('{!! $content !!}'), '{{ $contentId }}')">
  <div x-ref="element"></div>
</div> --}}

{{-- <template x-teleport="head">
  <template x-if="modalIsOpen">
    <script type="module">
      document.addEventListener('alpine:init', () => {
        Alpine.data('editor2', (content, contentId) => {
            let editor;
            let modalIsOpen;

            return {
              modalIsOpen: false,
              updatedAt: Date.now(), // force Alpine to rerender on selection change
              init() {
                const _this = this;
                editor = new Editor({
                  element: this.$refs.element,
                  extensions: [
                    StarterKit,
                  ],
                })
              },
            }
          })
    })

    </script>
    </template>
</template> --}}

<div x-data="editor(decodeURIComponent('{!! $content !!}'), '{{ $contentId }}')">
  <div class="w-full mb-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
    <template x-if="isLoaded()">
        <div id="menu" class="flex items-center justify-between px-3 py-2 border-b dark:border-gray-600">
            <div class="flex flex-wrap items-center divide-gray-200 sm:divide-x dark:divide-gray-600">
                <div class="flex items-center space-x-1 sm:pr-4">
                    <button type="button" @click="toggleHeading({ level: 1 })" :class="{ 'bg-slate-500 text-white' : isActive('heading', { level: 1 }, updatedAt) }" class="p-2 text-gray-500 rounded cursor-pointer hover:bg-slate-500 hover:text-white dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                      <svg class="w-4 h-4" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) Copyright 2023 Fonticons, Inc. --><path d="M0 64C0 46.3 14.3 32 32 32H80h48c17.7 0 32 14.3 32 32s-14.3 32-32 32H112V208H336V96H320c-17.7 0-32-14.3-32-32s14.3-32 32-32h48 48c17.7 0 32 14.3 32 32s-14.3 32-32 32H400V240 416h16c17.7 0 32 14.3 32 32s-14.3 32-32 32H368 320c-17.7 0-32-14.3-32-32s14.3-32 32-32h16V272H112V416h16c17.7 0 32 14.3 32 32s-14.3 32-32 32H80 32c-17.7 0-32-14.3-32-32s14.3-32 32-32H48V240 96H32C14.3 96 0 81.7 0 64z"/></svg>
                        <span class="sr-only">H1</span>
                    </button>
                    <button type="button" @click="toggleBold()" :class="{ 'bg-slate-500 text-white' : isActive('bold', updatedAt) }" class="p-2 text-gray-500 rounded cursor-pointer hover:bg-slate-500 hover:text-white dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                      <svg class="w-4 h-4" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) Copyright 2023 Fonticons, Inc. --><path d="M0 64C0 46.3 14.3 32 32 32H80 96 224c70.7 0 128 57.3 128 128c0 31.3-11.3 60.1-30 82.3c37.1 22.4 62 63.1 62 109.7c0 70.7-57.3 128-128 128H96 80 32c-17.7 0-32-14.3-32-32s14.3-32 32-32H48V256 96H32C14.3 96 0 81.7 0 64zM224 224c35.3 0 64-28.7 64-64s-28.7-64-64-64H112V224H224zM112 288V416H256c35.3 0 64-28.7 64-64s-28.7-64-64-64H224 112z"/></svg>
                        <span class="sr-only">Bold</span>
                    </button>
                    <button type="button" @click="toggleItalic()" :class="{ 'bg-slate-500 text-white' : isActive('italic', updatedAt) }" class="p-2 text-gray-500 rounded cursor-pointer hover:bg-slate-500 hover:text-white dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                      <svg class="w-4 h-4" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) Copyright 2023 Fonticons, Inc. --><path d="M128 64c0-17.7 14.3-32 32-32H352c17.7 0 32 14.3 32 32s-14.3 32-32 32H293.3L160 416h64c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H90.7L224 96H160c-17.7 0-32-14.3-32-32z"/></svg>
                        <span class="sr-only">Italic</span>
                    </button>
                    <button type="button" @click="setUnderline()" :class="{ 'bg-slate-500 text-white' : isActive('underline', updatedAt) }" class="p-2 text-gray-500 rounded cursor-pointer hover:bg-slate-500 hover:text-white dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                      <svg class="w-4 h-4" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) Copyright 2023 Fonticons, Inc. --><path d="M16 64c0-17.7 14.3-32 32-32h96c17.7 0 32 14.3 32 32s-14.3 32-32 32H128V224c0 53 43 96 96 96s96-43 96-96V96H304c-17.7 0-32-14.3-32-32s14.3-32 32-32h96c17.7 0 32 14.3 32 32s-14.3 32-32 32H384V224c0 88.4-71.6 160-160 160s-160-71.6-160-160V96H48C30.3 96 16 81.7 16 64zM0 448c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32z"/></svg>
                        <span class="sr-only">Underline</span>
                    </button>
                    <button type="button" @click="openLinkModal()" :class="{ 'bg-slate-500 text-white' : isActive('link', updatedAt) }" class="p-2 text-gray-500 rounded cursor-pointer hover:bg-slate-500 hover:text-white dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                      <svg class="w-4 h-4" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) Copyright 2023 Fonticons, Inc. --><path d="M579.8 267.7c56.5-56.5 56.5-148 0-204.5c-50-50-128.8-56.5-186.3-15.4l-1.6 1.1c-14.4 10.3-17.7 30.3-7.4 44.6s30.3 17.7 44.6 7.4l1.6-1.1c32.1-22.9 76-19.3 103.8 8.6c31.5 31.5 31.5 82.5 0 114L422.3 334.8c-31.5 31.5-82.5 31.5-114 0c-27.9-27.9-31.5-71.8-8.6-103.8l1.1-1.6c10.3-14.4 6.9-34.4-7.4-44.6s-34.4-6.9-44.6 7.4l-1.1 1.6C206.5 251.2 213 330 263 380c56.5 56.5 148 56.5 204.5 0L579.8 267.7zM60.2 244.3c-56.5 56.5-56.5 148 0 204.5c50 50 128.8 56.5 186.3 15.4l1.6-1.1c14.4-10.3 17.7-30.3 7.4-44.6s-30.3-17.7-44.6-7.4l-1.6 1.1c-32.1 22.9-76 19.3-103.8-8.6C74 372 74 321 105.5 289.5L217.7 177.2c31.5-31.5 82.5-31.5 114 0c27.9 27.9 31.5 71.8 8.6 103.9l-1.1 1.6c-10.3 14.4-6.9 34.4 7.4 44.6s34.4 6.9 44.6-7.4l1.1-1.6C433.5 260.8 427 182 377 132c-56.5-56.5-148-56.5-204.5 0L60.2 244.3z"/></svg>
                        <span class="sr-only">Link</span>
                    </button>
                    <button type="button" @click="setBulletList()" :class="{ 'bg-slate-500 text-white' : isActive('bulletList', updatedAt) }" class="p-2 text-gray-500 rounded cursor-pointer hover:bg-slate-500 hover:text-white dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                      <svg class="w-4 h-4" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) Copyright 2023 Fonticons, Inc. --><path d="M64 144a48 48 0 1 0 0-96 48 48 0 1 0 0 96zM192 64c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zm0 160c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zm0 160c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zM64 464a48 48 0 1 0 0-96 48 48 0 1 0 0 96zm48-208a48 48 0 1 0 -96 0 48 48 0 1 0 96 0z"/></svg>
                        <span class="sr-only">list</span>
                    </button>
                    <button type="button" @click="setOrderedList()" :class="{ 'bg-slate-500 text-white' : isActive('orderedList', updatedAt) }" class="p-2 text-gray-500 rounded cursor-pointer hover:bg-slate-500 hover:text-white dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                      <svg class="w-4 h-4" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) Copyright 2023 Fonticons, Inc. --><path d="M24 56c0-13.3 10.7-24 24-24H80c13.3 0 24 10.7 24 24V176h16c13.3 0 24 10.7 24 24s-10.7 24-24 24H40c-13.3 0-24-10.7-24-24s10.7-24 24-24H56V80H48C34.7 80 24 69.3 24 56zM86.7 341.2c-6.5-7.4-18.3-6.9-24 1.2L51.5 357.9c-7.7 10.8-22.7 13.3-33.5 5.6s-13.3-22.7-5.6-33.5l11.1-15.6c23.7-33.2 72.3-35.6 99.2-4.9c21.3 24.4 20.8 60.9-1.1 84.7L86.8 432H120c13.3 0 24 10.7 24 24s-10.7 24-24 24H32c-9.5 0-18.2-5.6-22-14.4s-2.1-18.9 4.3-25.9l72-78c5.3-5.8 5.4-14.6 .3-20.5zM224 64H480c17.7 0 32 14.3 32 32s-14.3 32-32 32H224c-17.7 0-32-14.3-32-32s14.3-32 32-32zm0 160H480c17.7 0 32 14.3 32 32s-14.3 32-32 32H224c-17.7 0-32-14.3-32-32s14.3-32 32-32zm0 160H480c17.7 0 32 14.3 32 32s-14.3 32-32 32H224c-17.7 0-32-14.3-32-32s14.3-32 32-32z"/></svg>
                        <span class="sr-only">list ol</span>
                    </button>
                </div>
            </div>
            <button type="button" data-tooltip-target="tooltip-fullscreen" class="p-2 text-gray-500 rounded cursor-pointer sm:ml-auto hover:bg-slate-500 hover:text-white dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 19 19">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 1h5m0 0v5m0-5-5 5M1.979 6V1H7m0 16.042H1.979V12M18 12v5.042h-5M13 12l5 5M2 1l5 5m0 6-5 5"></path>
                </svg>
                <span class="sr-only">Full screen</span>
            </button>
            <div id="tooltip-fullscreen" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700" style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate3d(695.5px, 69px, 0px);" data-popper-placement="bottom">
                Show full screen
                <div class="tooltip-arrow" data-popper-arrow="" style="position: absolute; left: 0px; transform: translate3d(81px, 0px, 0px);"></div>
            </div>
        </div>
    </template>

    @component('admin.structure.components.modal')
      <div>
        @php($textPars =[
          'ref' => 'urlInput',
          'type' => 'search',
          'label' => 'Link',
          'placeholder' => 'https://www.example.com',
          'value' => '',
          'classes' => 'pl-0'
        ])
        @include('admin.structure.input.text',$textPars)
        <div class="mb-4 mt-4">
          @php($checkboxPars =[
            'ref' => 'newTabCheckbox',
            'type' => 'checkbox',
            'label' => 'Open link in nieuw tab',
            'id' => "checkbox-1",
          ])
          @include('admin.structure.input.checkbox', $checkboxPars)
        </div>
      </div>

      <button
        @click="setLink({url: $refs.urlInput.value, newTab: $refs.newTabCheckbox.checked}); closeModal2()"
        type="button"
        class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2"
        x-text="$refs.urlInput.value ? 'Update link' : 'Voeg link toe'"
        ></button>
        <button @click="closeModal2()" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
          Cancel
        </button>
    @endcomponent
    <div class="px-4 py-2 bg-white rounded-b-lg dark:bg-gray-800">
        <div x-ref="element" class="prose min-h-[200px] block w-full px-0 text-sm text-gray-800 bg-white border-0 dark:bg-gray-800 focus:ring-0 dark:text-white dark:placeholder-gray-400"></div>
    </div>
  </div>
</div>

@pushonce('script_list')
{{-- <script>
  alert('sdds')
</script> --}}
{{-- <script type="module">
  document.addEventListener('alpine:init', () => {
    Alpine.data('editor2', (content, contentId) => {
        let editor;
        let modalIsOpen;

        return {
          modalIsOpen: false,
          updatedAt: Date.now(), // force Alpine to rerender on selection change
          init() {
            const _this = this;
            editor = new Editor({
              element: this.$refs.element,
              extensions: [
                StarterKit,
              ],
            })
          },
        }
      })
})

</script> --}}
@endpushonce