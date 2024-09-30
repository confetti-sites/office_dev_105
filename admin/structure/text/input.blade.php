@php /** @var \Confetti\Helpers\ComponentStandard $model */ @endphp
<!--suppress HtmlUnknownTag -->
<text-component
        data-id="{{ $model->getId() }}"
        data-id_slug="{{ slugId($model->getId()) }}"
        data-label="{{ $model->getComponent()->getLabel() }}"
        data-placeholder="{{ $model->getComponent()->getDecoration('placeholder') }}"
        data-help="{{ $model->getComponent()->getDecoration('help') }}"
        data-decorations='@json($model->getComponent()->getDecorations())'
        data-original='@json($model->get())'
        data-component="{{ json_encode($model->getComponent()) }}"
></text-component>

@pushonce('end_of_body_text_component')
    <style>
        @import url('/admin/structure/text/lim_text.css');
    </style>
    <script type="module">
        import {html} from 'https://esm.sh/@arrow-js/core';
        /** see https://github.com/codex-team/editor.js/blob/next/types/configs/editor-config.d.ts */
        import EditorJS from 'https://esm.sh/@editorjs/editorjs@^2';
        import {LimText, Validators} from '/admin/structure/text/lim_text.mjs'
        import Underline from '/admin/structure/content/tools/underline.mjs';
        import Bold from '/admin/structure/content/tools/bold.mjs';
        import Italic from '/admin/structure/content/tools/italic.mjs';

        /**
         * These are the settings for the editor.js
         */
        customElements.define('text-component', class extends HTMLElement {
            connectedCallback() {
                html`
                    <div class="block text-bold text-xl mt-8 mb-4">
                        ${this.dataset.label}
                    </div>
                    <div class="_input px-5 py-3 text-gray-700 border-2 border-gray-200 rounded-lg bg-gray-50">
                        <span id="_${this.dataset.id_slug}"></span>
                    </div>
                    <p class="mt-2 text-sm text-red-600 _error"></p>
                    <p class="mt-2 text-sm text-gray-500">${this.dataset.help}</p>
                `(this)
                this.renderedCallback();
            }

            renderedCallback() {
                /**
                 * These are the settings for the editor.js
                 */
                new EditorJS({
                    // Id of Element that should contain Editor instance
                    holder: '_' + this.dataset.id_slug,
                    placeholder: this.dataset.placeholder,
                    // Use minHeight 0, because the default is too big
                    minHeight: 0,
                    // We keep using the therm "paragraph",
                    // so we can override it. Prevent error:
                    // "Paste handler for «text» Tool on «P» tag is
                    // skipped because it is already used by «paragraph» Tool."
                    defaultBlock: "paragraph",
                    // To hide the toolbar, you can set it to false
                    inlineToolbar: true,
                    // 1. Map tool names to the actual tools
                    // 2. Add the tool to the inlineToolbar
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
                                contentId: this.dataset.id,
                                // This is the value stored in the database.
                                // Lim is using LocalStorage to store the data before it is saved/published.
                                originalValue: JSON.parse(this.dataset.original),
                                /** @type {HTMLElement} */
                                component: this,
                                // E.g. {"label":{"label":"Title"},"default":{"default":"Confetti CMS"},"min":{"min":1},"max":{"max":20}};
                                /** @type {object} */
                                decorations: JSON.parse(this.dataset.decorations),
                                // Feel free to add more validators
                                // The config object is the object on this level.
                                // The value is the value of the input field.
                                /** @type {Array.<function(config: object, value: string): string[]>} */
                                validators: [
                                    Validators.validateMinLength,
                                    Validators.validateMaxLength,
                                ],
                                // componentEntity is our own component object.
                                // We can use this to get the label, default value, etc.
                                componentEntity: this.dataset.component,
                            }
                        },
                    },

                    /**
                     * Lim_text need to hook into this events.
                     * Feel free to extend/override these functions.
                     **/
                    onChange: LimText.onChange,
                });
            }
        });
    </script>
@endpushonce
