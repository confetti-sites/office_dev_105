@php
    /** @var \Confetti\Helpers\ComponentStandard $model */
    $component = $model->getComponent();
@endphp
{{-- Trigger Tailwind: border-cyan-300 --}}
<div id="_{{ slugId($model->getId()) }}_component">
    <div class="block text-bold text-xl mt-8 mb-4">
        {{ $model->getComponent()->getLabel() }}
    </div>
    <div class="px-5 py-4 text-gray-700 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700 _input">
        <span id="_{{ slugId($model->getId()) }}"></span>
    </div>
</div>

@push('end_of_body_'.slugId($model->getId()))
    <style>
        /* With a big screen, the text is indeed to the right */
        #_{{ slugId($model->getId()) }} .ce-block__content, #_{{ slugId($model->getId()) }} .ce-toolbar__content {
            max-width: unset;
        }

        /* Remove default editor.js padding */
        #_{{ slugId($model->getId()) }} .cdx-block {
            padding: 0;
        }

        /* Add padding to the inline tools */
        #_{{ slugId($model->getId()) }} .ce-inline-tool {
            padding: 12px;
        }

        /* Add padding to ce-paragraph */
        #_{{ slugId($model->getId()) }} .ce-block {
            padding-bottom: 12px;
        }

        /* Add font size to h2, h3 */
        #_{{ slugId($model->getId()) }} h2 {
            font-size: 1.875rem;
        }
        #_{{ slugId($model->getId()) }} h3 {
            font-size: 1.5rem;
        }
        #_{{ slugId($model->getId()) }} h4 {
            font-size: 1.25rem;
        }
    </style>
    <script type="module">

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

        /**
         * These are the settings for the editor.js
         */
        const editor = new EditorJS({
            id: '{{ $model->getId() }}',
            element: document.getElementById('_{{ slugId($model->getId()) }}_component'),
            // Id of Element that should contain Editor instance
            holder: '_{{ slugId($model->getId()) }}',
            placeholder: '{{ $component->getDecoration('placeholder') }}',
            originalData: @json($model->get()),
            data: localStorage.hasOwnProperty('{{ $model->getId() }}') ? JSON.parse(localStorage.getItem('{{ $model->getId() }}')) : @json($model->get()),
            // E.g. {"label":{"label":"Title"},"default":{"default":"Confetti CMS"},"min":{"min":1},"max":{"max":20}};
            decorations: @json($component->getDecorations()),
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

    </script>
@endpush