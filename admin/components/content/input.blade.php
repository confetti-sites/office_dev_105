@php /** @var \Src\Components\ContentComponent $model */ @endphp
        <!--suppress HtmlUnknownTag -->
<content-component
        data-id="{{ $model->getId() }}"
        data-id_slug="{{ slugId($model->getId()) }}"
        data-label="{{ $model->getComponent()->getLabel() }}"
        data-decorations='{{ json_encode($model->getComponent()->getDecorations()) }}'
        data-original="{{ json_encode($model->get()) }}"
        data-component="{{ json_encode($model->getComponent()) }}"
        data-default_data="{{ json_encode($model->getDefaultData()) }}"
></content-component>

@pushonce('end_of_body_content_component')
    <style>
        @import url('/admin/components/content/editor_content.css');
    </style>
    <script type="module">
        import {html} from 'https://esm.sh/@arrow-js/core';

        /** see https://github.com/codex-team/editor.js/blob/next/types/configs/editor-config.d.ts */
        import EditorJS from 'https://esm.sh/@editorjs/editorjs@^2';
        import LimContent from '/admin/components/content/editor_content.mjs';

        /** Block tools */
        /**
         * @see https://github.com/editor-js/paragraph
         * @see https://github.com/editor-js/paragraph/blob/master/src/index.js
         **/
        import Paragraph from 'https://esm.sh/@editorjs/paragraph@^2';
        /**
         * @see https://github.com/editor-js/header
         * @see https://github.com/editor-js/header/blob/master/src/index.js
         **/
        import Header from 'https://esm.sh/@editorjs/header@^2';
        /**
         * @see https://github.com/editor-js/nested-list
         * @see https://github.com/editor-js/nested-list/blob/main/src/index.js
         */
        import NestedList from 'https://esm.sh/@editorjs/nested-list';
        /**
         * @see https://github.com/editor-js/delimiter
         * @see https://github.com/editor-js/delimiter/blob/master/src/index.js
         */
        import Delimiter from 'https://esm.sh/@editorjs/delimiter';
        /**
         * @see https://github.com/editor-js/table
         * @see https://github.com/editor-js/table/blob/master/src/table.js
         */
        import Table from 'https://esm.sh/@editorjs/table';

        /**
         * @see https://github.com/editor-js/code
         */
        import Code from 'https://esm.sh/@editorjs/code';

        /** Inline tools */
        import Underline from '/admin/components/content/tools/underline.mjs';
        import Bold from '/admin/components/content/tools/bold.mjs';
        import Italic from '/admin/components/content/tools/italic.mjs';
        import {Storage} from '/admin/assets/js/admin_service.mjs';

        // General toolbar is set in the onReady event
        let service = undefined;
        const defaultInlineToolbar = [
            'bold',
            'underline',
            'italic',
        ];

        customElements.define('content-component', class extends HTMLElement {
            id
            id_slug
            label
            data
            decorations = {
                help: {help: null},
                default: {default: null},
                placeholder: {placeholder: null},
            }
            original
            component
            default_data

            constructor() {
                super();
                this.id = this.dataset.id;
                this.id_slug = this.dataset.id_slug;
                this.label = this.dataset.label;
                this.decorations = JSON.parse(this.dataset.decorations);
                this.original = JSON.parse(this.dataset.original);
                this.component = JSON.parse(this.dataset.component);
                this.default_data = JSON.parse(this.dataset.default_data);

                // Here we set the default value if it is not set.
                if (this.original === null && !Storage.hasLocalStorageItem(this.id)) {
                    Storage.saveLocalStorageModel(this.id, this.default_data, this.dataset.component);
                }
            }

            connectedCallback() {
                html`
                    <div class="block text-bold text-xl mt-8 mb-4">
                        ${this.label}
                    </div>
                    <div class="px-5 py-4 text-gray-700 border-2 border-gray-200 rounded-lg bg-gray-50 _input">
                        <span id="_${this.id_slug}"></span>
                    </div>
                `(this)
                this.renderedCallback();
            }

            renderedCallback() {
                /**
                 * These are the settings for the editor.js
                 */
                const editor = new EditorJS({
                    id: this.id,
                    element: this,
                    // Id of Element that should contain Editor instance
                    holder: '_' + this.id_slug,
                    placeholder: this.decorations.placeholder.placeholder,
                    originalData: this.original,
                    data: localStorage.hasOwnProperty('{{ $model->getId() }}') ? JSON.parse(localStorage.getItem(this.id)) : this.original,
                    // E.g. {"label":{"label":"Title"},"default":{"default":"Confetti CMS"}};
                    decorations: this.decorations,
                    defaultData: this.default_data,
                    /** Use minHeight 100, because the default is too big. */
                    minHeight: 100,
                    defaultBlock: "paragraph",
                    inlineToolbar: true,
                    config: {
                        // componentEntity is our own component object.
                        // We can use this to get the label, default value, etc.
                        componentEntity: this.dataset.component,
                    },
                    tools: {
                        // Inline tools
                        bold: Bold,
                        underline: Underline,
                        italic: Italic,

                        // Block tools
                        header: {
                            class: Header,
                            inlineToolbar: [
                                'bold',
                                'underline',
                                'italic',
                            ],
                            config: {
                                placeholder: 'Enter a header',
                                levels: [2, 3, 4],
                                defaultLevel: 2
                            }
                        },
                        paragraph: {
                            class: Paragraph,
                            inlineToolbar: defaultInlineToolbar,
                        },
                        list: {
                            class: NestedList,
                            inlineToolbar: defaultInlineToolbar,
                            config: {
                                defaultStyle: 'unordered'
                            },
                        },
                        table: {
                            class: Table,
                            inlineToolbar: defaultInlineToolbar,
                        },
                        delimiter: Delimiter,
                        code: Code,
                    },

                    // Set generalToolbar in a variable, so we can use it in the onChange event
                    onReady: () => service = (new LimContent(editor)).init(),
                    onChange: (api, events) => service.onChange(api, events),
                });
            }
        });
    </script>
@endpushonce
