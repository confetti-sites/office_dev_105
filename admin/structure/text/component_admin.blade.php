@php
    /** @var \Confetti\Helpers\ComponentStandard $model */
    $component = $model->getComponent();
@endphp
{{-- Trigger Tailwind: border-cyan-300, border-red-200 --}}
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

<<<<<<< HEAD
        /* With a big screen, the text is indeed to the right */
        #_{{ slugId($model->getId()) }} .ce-block__content, #_{{ slugId($model->getId()) }} .ce-toolbar__content {
            max-width: unset;
        }

        /* Remove default editor.js padding */
        #_{{ slugId($model->getId()) }} .cdx-block {
            padding: 0;
        }

        #_{{ slugId($model->getId()) }} .codex-editor--narrow .codex-editor__redactor {
            margin-right: 0;
        }
        /* Add padding to the inline tools */
        #_{{ slugId($model->getId()) }} .ce-inline-tool {
            padding: 12px;
        }
    </style>
    <!--suppress JSFileReferences -->
=======
>>>>>>> 60a5cdf49893a2c2c2f6588f0e1c23fb8be68b5a
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

                        /**
                         * Custom render settings
                         * You can add more buttons to
                         * the settings panel on the right side.
                         * By default, "Revert to saved value" button is added.
                         *
                         * @see admin/structure/text/lim_text.mjs:152 for a simple example
                         * @see https://editorjs.io/making-a-block-settings/ for a more complex example
                         *
                         * @type {Array.<{label: string, icon: *, closeOnActivate: boolean, onActivate: function(): Promise<void>}>}
                         */
                        renderSettings: [],

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