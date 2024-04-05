@php
    /** @var \Confetti\Helpers\ComponentStandard $model */
    $component = $model->getComponent();
@endphp
<div id="_{{ slugId($model->getId()) }}_component" class="relative">
    <div class="block text-bold text-xl mt-8 mb-4">
        {{ $model->getComponent()->getLabel() }}
    </div>
    <div class="px-5 py-3 text-gray-700 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700 _input">
        <span id="_{{ slugId($model->getId()) }}"></span>
    </div>
    <p class="mt-2 text-sm text-red-600 dark:text-red-500 _error"></p>
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

        {{--#_{{ slugId($model->getId()) }} .codex-editor--narrow .codex-editor__redactor {--}}
        /*    margin-right: 0;*/
        /*}*/
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
        import {IconEtcVertical} from 'https://esm.sh/@codexteam/icons';
        import Toolbar from '/admin/structure/tools/toolbar.mjs';

        // Block tools
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

        // Inline tools
        import Underline from '/admin/structure/tools/underline.mjs';
        import Bold from '/admin/structure/tools/bold.mjs';
        import Italic from '/admin/structure/tools/italic.mjs';

        class Component {
            /**
             * @type {string}
             */
            static id = '{{ $model->getId() }}';
            /**
             * @type {ContentValue}
             */
            static originalValue = @json($model->get());
            /**
             * @type {HTMLElement}
             */
            static element = document.getElementById('_{{ slugId($model->getId()) }}_component');

            /**
             * E.g. {"label":{"label":"Title"},"default":{"default":"Confetti CMS"},"min":{"min":1},"max":{"max":20}};
             * @type {object}
             */
            static decorations = @json($component->getDecorations());

            /**
             * E.g. {"time":1712349766517,"blocks":[{"id":"1Z7S3FP926","type":"paragraph","data":{"text":"The cool blog title"}}],"version":"2.29.1"}
             * @returns {ContentValue}
             */
            static get storageValue() {
                return JSON.parse(localStorage.getItem(Component.id));
            }

            /**
             * If the value is null, it will be null in local storage
             * and removed from the database.
             * @param {ContentValue|null} value
             */
            static set storageValue(value) {
                let toSave = null;
                // if blocks is empty, we need to set it to null
                if (value.blocks.length !== 0) {
                    // Use JSON.stringify to encode special characters
                    toSave = JSON.stringify(value);
                }
                localStorage.setItem(Component.id, toSave);
            }

            static updateValueChangedStyle() {
                const inputHolder = Component.element.querySelector('._input');
                // Value can be null, when it's not set in local storage.
                if (this._isChanged()) {
                    inputHolder.classList.remove('border-gray-200');
                    inputHolder.classList.add('border-cyan-300');
                } else {
                    inputHolder.classList.remove('border-cyan-300');
                    inputHolder.classList.add('border-gray-200');
                }
            }

            static _isChanged() {
                let original = '';
                let changed = '';
                // foreach over blocks.*.data and add to string, for original and changed
                if (Component.storageValue !== null) {
                    for (const block of Component.storageValue.blocks) {
                        original += JSON.stringify(block.data);
                    }
                }
                for (const block of Component.originalValue.blocks) {
                    changed += JSON.stringify(block.data);
                }
                return original !== changed;
            }
        }

        /**
         * In this text component, we only allow the paragraph tool.
         */
        // class Text extends Paragraph {
        //     renderSettings() {
        //         return [
        //             {
        //                 icon: IconUndo,
        //                 label: 'Revert to saved value',
        //                 closeOnActivate: true,
        //                 onActivate: async () => {
        //                     console.log('Todo; Revert to saved value');
        //                     // const contentAdded = await this.api.saver.save()
        //                     // this.api.blocks.update(contentAdded.blocks[0].id, {
        //                     //     text: Component.originalValue,
        //                     // })
        //                 }
        //             },
        //         ];
        //     }
        // }

        /**
         * These are the settings for the editor.js
         */
        new EditorJS({
            // Id of Element that should contain Editor instance
            holder: '_{{ slugId($model->getId()) }}',
            placeholder: '{{ $component->getDecoration('placeholder') }}',
            data: localStorage.hasOwnProperty('{{ $model->getId() }}') ? JSON.parse(localStorage.getItem('{{ $model->getId() }}')) : @json($model->get()),
            defaultBlock: "paragraph",
            inlineToolbar: true,
            tools: {
                general_toolbar: Toolbar,

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
                    inlineToolbar: [
                        'bold',
                        'underline',
                        'italic',
                    ]
                },
            },

            onReady: () => {
                // Ensure that the value is updated when the page is loaded
                Component.updateValueChangedStyle();
                // Icons are loaded yet, so we need to wait a bit.
                setTimeout(() => {
                    /* Replace the default editor.js 6 dots settings icon with a 3-dot icon */
                    Component.element.querySelector('.ce-toolbar__settings-btn').innerHTML = IconEtcVertical;
                }, 100);
            },

            onChange: async (api, events) => {
                // if not array, make an array
                if (!Array.isArray(events)) {
                    events = [events];
                }

                // Ensure that the value is updated when the user types
                await OnChangeHandler.changed(api, events);
            },
        });

        class OnChangeHandler {
            /**
             * Every time the user types, this function is called.
             *
             * @param {Api} api
             * @param {Array} events
             */
            static async changed(api, events) {
                for (const event of events) {
                    if (event.type !== 'block-changed') {
                        continue;
                    }
                    Component.storageValue = await api.saver.save();
                    // Update the style
                    Component.updateValueChangedStyle();
                }
            }
        }
    </script>
@endpush