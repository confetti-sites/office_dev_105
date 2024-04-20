@php
    /** @var \Confetti\Helpers\ComponentStandard $model */
    $component = $model->getComponent();
@endphp
{{-- Trigger Tailwind: border-emerald-700, border-red-200 --}}
<div id="_{{ slugId($model->getId()) }}_component">
    <div class="block text-bold text-xl mt-8 mb-4">
        {{ $component->getLabel() }}
    </div>

    <div class="px-5 py-3 text-gray-700 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700 _input">
        <span id="{{ slugId($model->getId()) }}"></span>
    </div>
    <p class="mt-2 text-sm text-red-600 dark:text-red-500 _error"></p>
</div>

@push('end_of_body_'.slugId($model->getId()))
    <!--suppress JSFileReferences -->
    <script type="module">
        import EditorJS from 'https://esm.sh/@editorjs/editorjs@^2';
        import {LimText, Validators} from '/admin/structure/text/lim_text.mjs'
        import Underline from '/admin/structure/tools/underline.mjs';
        import Bold from '/admin/structure/tools/bold.mjs';
        import Italic from '/admin/structure/tools/italic.mjs';

        /**
         * These are the settings for the editor.js
         */
        new EditorJS({

            /**
             * Id of Element that should contain Editor instance
             * @type {string}
             */
            holder: '{{ slugId($model->getId()) }}',

            /**
             * @type {string}
             **/
            placeholder: '{{ $component->getDecoration('placeholder') }}',

            /** Use minHeight 0, because the default is too big. */
            minHeight: 0,

            /**
             * We keep using the therm "paragraph",
             * so we can override it. Prevent error:
             * "Paste handler for «text» Tool on «P» tag is
             * skipped because it is already used by «paragraph» Tool."
             */
            defaultBlock: "paragraph",

            /** To hide the toolbar, you can set it to false */
            inlineToolbar: true,

            /**
             * 1. Map tool names to the actual tools
             * 2. Add the tool to the inlineToolbar
             */
            tools: {
                bold: Bold,
                underline: Underline,
                italic: Italic,
                paragraph: {
                    class: LimText,
                    inlineToolbar: [
                        'bold',
                        'underline',
                        'italic',
                    ],

                    config: {
                        /**
                         * E.g. /model/homepage/title
                         * @type {string}
                         **/
                        contentId: '{{ $model->getId() }}',

                        /**
                         * This is the value stored in the database.
                         * Lim is using LocalStorage to store the data before it is saved/published.
                         * @type {string}
                         **/
                        originalValue: '{{ $model->get() }}',

                        /**
                         * @type {HTMLElement}
                         * */
                        component: document.getElementById('_{{ slugId($model->getId()) }}_component'),

                        /**
                         * E.g. {"label":{"label":"Title"},"default":{"default":"Confetti CMS"},"min":{"min":1},"max":{"max":20}};
                         * @type {object}
                         */
                        decorations: @json($component->getDecorations()),

                        /**
                         * Feel free to add more validators
                         * The config object is the object on this level.
                         * The value is the value of the input field.
                         *
                         * @type {Array.<function(config: object, value: string): string[]>}
                         **/
                        validators: [
                            Validators.validateMinLength,
                            Validators.validateMaxLength,
                        ],
                    }
                },
            },

            /**
             * Lim_text need to hook into this events.
             * Feel free to extend/override these functions.
             **/
            onChange: LimText.onChange,
        });
    </script>
@endpush