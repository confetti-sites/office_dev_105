@php
    /** @var \Confetti\Helpers\ComponentStandard $model */
    $component = $model->getComponent();
@endphp
<div id="_{{ slugId($model->getId()) }}_component">
    <div class="block text-bold text-xl mt-8 mb-4">
        {{ $model->getLabel() }}
    </div>

    <div class="px-5 py-3 text-gray-700 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700 _input">
        <span id="_{{ slugId($model->getId()) }}"></span>
    </div>
    <p class="mt-2 text-sm text-red-600 dark:text-red-500 _error"></p>
</div>

@push('end_of_body_'.slugId($model->getId()) )
    <style>
        /* Hide the toolbar items so the user can't add new blocks */
        #_{{ slugId($model->getId()) }} .ce-toolbar__plus, #_{{ slugId($model->getId()) }} .cdx-search-field, #_{{ slugId($model->getId()) }} .ce-popover-item[data-item-name="move-up"], #_{{ slugId($model->getId()) }} .ce-popover-item[data-item-name="delete"], #_{{ slugId($model->getId()) }} .ce-popover-item[data-item-name="move-down"] {
            display: none;
        }

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
    </style>
    <script type="module">
        import EditorJS from "https://esm.sh/@editorjs/editorjs@^2";
        import Paragraph from 'https://esm.sh/@editorjs/paragraph@^2';
        import {IconEtcVertical, IconUndo} from 'https://esm.sh/@codexteam/icons'

        class Component {
            /**
             * @type {string}
             */
            static id = '{{ $model->getId() }}';
            /**
             * @type {string}
             */
            static originalValue = '{{ $model->get() }}';
            /**
             * @type {HTMLElement}
             */
            static element = document.getElementById('_{{ slugId($model->getId()) }}_component');

            /**
             * e.g.  static decorations = {"label":{"label":"Title"},"default":{"default":"Confetti CMS"},"min":{"min":1},"max":{"max":20}};
             * @type {object}
             */
            static decorations = @json($component->getDecorations());

            /**
             * @return {string}
             */
            static get storageValue() {
                return localStorage.getItem(Component.id);
            }

            /**
             * @param {string} value
             */
            static set storageValue(value) {
                // Use JSON.stringify to encode special characters
                localStorage.setItem(Component.id, value);
            }

            /**
             * @param {string} value
             */
            static updateValueChangedStyle(value) {
                const inputHolder = Component.element.querySelector('._input');
                const message = this.validateWithMessage(value);
                if (message != null) {
                    inputHolder.classList.add('border-red-500');
                    Component.element.getElementsByClassName('_error')[0].innerText = message;
                    return;
                }
                // Remove the error message
                Component.element.getElementsByClassName('_error')[0].innerText = '';
                inputHolder.classList.remove('border-red-500');
                // Value can be null, when it's not set in local storage.
                if (value !== null && value !== Component.originalValue) {
                    inputHolder.classList.remove('border-gray-200');
                    inputHolder.classList.add('border-cyan-300');
                } else {
                    inputHolder.classList.remove('border-cyan-300');
                    inputHolder.classList.add('border-gray-200');
                }
            }

            /**
             * We don't use the default validation, because we want to interact with the ui.
             * @param {string|null} value
             */
            static validateWithMessage(value) {
                // Validate min length
                if (value.length < Component.decorations.min.min) {
                    return `The value must be at least ${Component.decorations.min.min} characters long.`;
                }
                // Validate max length
                if (value.length > Component.decorations.max.max) {
                    return `The value must be at most ${Component.decorations.max.max} characters long.`;
                }
                return null;
            }
        }

        /**
         * In this text component, we only allow the paragraph tool.
         */
        class ParagraphChild extends Paragraph {
            renderSettings() {
                return [
                    {
                        icon: IconUndo,
                        label: 'Revert to saved value',
                        closeOnActivate: true,
                        onActivate: async () => {
                            const contentAdded = await this.api.saver.save()
                            this.api.blocks.update(contentAdded.blocks[0].id, {
                                text: Component.originalValue,
                            })
                        }
                    },
                ];
            }
        }

        /**
         * These are the settings for the editor.js
         */
        new EditorJS({
            // Id of Element that should contain Editor instance
            holder: '_{{ slugId($model->getId()) }}',
            // Use minHeight 0, because the default is too big.
            minHeight: 0,
            defaultBlock: "paragraph",
            tools: {
                paragraph: {
                    class: ParagraphChild,
                    inlineToolbar: true,
                },
            },
            inlineToolbar: true,
            placeholder: '{{ $component->getDecoration('placeholder') }}',
            data: {
                time: 0,
                blocks: [
                    {
                        type: "paragraph",
                        data: {
                            text: localStorage.getItem('{{ $model->getId() }}') ?? '{{ $model->get() }}',
                        }
                    }
                ],
                version: "2.11.10"
            },

            onReady: () => {
                // Ensure that the value is updated when the page is loaded
                Component.updateValueChangedStyle(Component.storageValue);
                // Icons are loaded yet, so we need to wait a bit.
                setTimeout(() => {
                    /* Replace the default editor.js 6 dots settings icon with an 3 dots icon */
                    Component.element.querySelector('.ce-toolbar__settings-btn').innerHTML = IconEtcVertical;
                }, 100);
            },

            onChange: async (api, events) => {
                // if not array, make an array
                if (!Array.isArray(events)) {
                    events = [events];
                }
                for (const event of events) {
                    switch (event.type) {
                        // In the text component, we allow only one block If a new block
                        // is added, we remove it and append the text to the first block.
                        case 'block-added':
                            const contentAdded = await api.saver.save()
                            const firstBlock = contentAdded.blocks[0]
                            api.blocks.update(firstBlock.id, {
                                text: firstBlock.data.text + " " + contentAdded.blocks[1].data.text
                            })
                            api.blocks.delete();
                            // Move caret to the end
                            setTimeout(() => {api.caret.setToBlock(0, 'end')}, 20);
                            break;
                        // Save the value to local storage, so we can save it later when the user clicks on save.
                        case 'block-changed':
                            const contentChanged = await api.saver.save()
                            let value = '';
                            if (contentChanged.blocks.length > 0) {
                                value = contentChanged.blocks[0].data.text
                            }
                            Component.storageValue = value;
                            // Update the style
                            Component.updateValueChangedStyle(value);
                            break;
                    }
                }
            },
        });
    </script>
@endpush