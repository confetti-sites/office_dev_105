@php /** @var \Confetti\Helpers\ComponentStandard $model */ @endphp


<content-component
        data-name="{{ $model->getId() }}"
        data-name_slug="{{ slugId($model->getId()) }}"
        data-label="{{ $model->getComponent()->getLabel() }}"
        data-placeholder="{{ $model->getComponent()->getDecoration('placeholder') }}"
        data-decorations='@json($model->getComponent()->getDecorations())'
        data-original='@json($model->get())'
></content-component>

@pushonce('end_of_body_content_component')
    <style>
        @import url('/admin/structure/content/lim_content.css');
    </style>
    <script type="module">
        import {html} from 'https://esm.sh/@arrow-js/core';

        /** see https://github.com/codex-team/editor.js/blob/next/types/configs/editor-config.d.ts */
        import EditorJS from 'https://esm.sh/@editorjs/editorjs@^2';
        import LimContent from '/admin/structure/content/lim_content.mjs';

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

        /** Inline tools */
        import Underline from '/admin/structure/tools/underline.mjs';
        import Bold from '/admin/structure/tools/bold.mjs';
        import Italic from '/admin/structure/tools/italic.mjs';

        // General toolbar is
        let service = undefined;
        const defaultInlineToolbar = [
            'bold',
            'underline',
            'italic',
            'link',
        ];

        customElements.define('content-component', class extends HTMLElement {
            connectedCallback() {
                html`
                    <div class="block text-bold text-xl mt-8 mb-4">
                        ${this.dataset.label}
                    </div>
                    <div class="px-5 py-4 text-gray-700 border-2 border-gray-200 rounded-lg bg-gray-50 _input">
                        <span id="_${this.dataset.name_slug}"></span>
                    </div>
                `(this)
                this.renderedCallback();
            }

            renderedCallback() {
                /**
                 * These are the settings for the editor.js
                 */
                const editor = new EditorJS({
                    id: this.dataset.name,
                    element: this,
                    // Id of Element that should contain Editor instance
                    holder: '_' + this.dataset.name_slug,
                    placeholder: this.dataset.placeholder,
                    originalData: JSON.parse(this.dataset.original),
                    data: localStorage.hasOwnProperty('{{ $model->getId() }}') ? JSON.parse(localStorage.getItem(this.dataset.name)) : JSON.parse(this.dataset.original),
                    // E.g. {"label":{"label":"Title"},"default":{"default":"Confetti CMS"},"min":{"min":1},"max":{"max":20}};
                    decorations: JSON.parse(this.dataset.decorations),
                    /** Use minHeight 100, because the default is too big. */
                    minHeight: 100,
                    defaultBlock: "paragraph",
                    inlineToolbar: true,
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
                    },

                    // Set generalToolbar in a variable, so we can use it in the onChange event
                    onReady: () => service = (new LimContent(editor)).init(),
                    onChange: (api, events) => service.onChange(api, events),
                });
            }
        });
    </script>
@endpushonce
