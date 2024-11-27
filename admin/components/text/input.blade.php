@php /** @var \Src\Components\TextComponent $model */ @endphp
<!--suppress HtmlUnknownTag -->
<text-component
        data-id="{{ $model->getId() }}"
        data-id_slug="{{ slugId($model->getId()) }}"
        data-label="{{ $model->getComponent()->getLabel() }}"
        data-decorations='{{ json_encode($model->getComponent()->getDecorations()) }}'
        data-original="{{ json_encode($model->get()) }}"
        data-component="{{ json_encode($model->getComponent()) }}"
></text-component>

@pushonce('end_of_body_text_component')
    <style>
        @import url('/admin/components/text/editor_text.css');
    </style>
    <script type="module">
        import {html} from 'https://esm.sh/@arrow-js/core';
        /** see https://github.com/codex-team/editor.js/blob/next/types/configs/editor-config.d.ts */
        import EditorJS from 'https://esm.sh/@editorjs/editorjs@^2';
        import {LimText, Validators} from '/admin/components/text/editor_text.mjs'
        import Underline from '/admin/components/content/tools/underline.mjs';
        import Bold from '/admin/components/content/tools/bold.mjs';
        import Italic from '/admin/components/content/tools/italic.mjs';
        import {Storage} from '/admin/assets/js/admin_service.mjs';

        /**
         * These are the settings for the editor.js
         */
        customElements.define('text-component', class extends HTMLElement {
            id
            id_slug
            label
            data
            decorations = {
                default: {default: null},
                help: {help: null},
                max: {max: null},
                min: {min: null},
                placeholder: {placeholder: null},
                bar: {tools: []},
            }
            original
            component

            constructor() {
                super();
                this.id = this.dataset.id;
                this.id_slug = this.dataset.id_slug;
                this.label = this.dataset.label;
                this.decorations = JSON.parse(this.dataset.decorations);
                this.original = JSON.parse(this.dataset.original);
                this.component = JSON.parse(this.dataset.component);

                // Get all the ids from the array, not the labels
                this.decorations.bar.tools = this.decorations.bar?.tools?.map(tool => tool.id);

                // Here we set the default value if it is not set.
                if (this.original === null && !Storage.hasLocalStorageItem(this.id)) {
                    Storage.saveLocalStorageModel(this.id, this.decorations.default.default ? this.decorations.default.default : '', this.dataset.component);
                }
            }

            connectedCallback() {
                html`
                    <div class="block text-bold text-xl mt-8 mb-4">
                        ${this.label}
                    </div>
                    <div class="_input px-5 py-3 text-gray-700 border-2 border-gray-200 rounded-lg bg-gray-50">
                        <span id="_${this.id_slug}"></span>
                    </div>
                    <p class="mt-2 text-sm text-red-600 _error"></p>
                    ${this.decorations.help.help ? html`<p class="mt-2 text-sm text-gray-500">${this.decorations.help.help}</p>` : ''}
                `(this)
                this.renderedCallback();
            }

            renderedCallback() {
                /**
                 * These are the settings for the editor.js
                 */
                new EditorJS({
                    // Id of Element that should contain Editor instance
                    holder: '_' + this.id_slug,
                    placeholder: this.decorations.placeholder.placeholder,
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
                        b: Bold,
                        u: Underline,
                        i: Italic,
                        paragraph: {
                            class: LimText,
                            inlineToolbar: this.decorations.bar.tools,
                            config: {
                                /**
                                 * E.g. /model/homepage/title
                                 * @type {string}
                                 **/
                                contentId: this.id,
                                // This is the value stored in the database.
                                // Lim is using LocalStorage to store the data before it is saved/published.
                                originalValue: this.original,
                                /** @type {HTMLElement} */
                                component: this,
                                // E.g. {"label":{"label":"Title"},"default":{"default":"Confetti CMS"},"min":{"min":1},"max":{"max":20}};
                                /** @type {object} */
                                decorations: this.decorations,
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
