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

@push('end_of_body_'.slugId($model->getId()))
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
        /* Add padding to the inline tools */
        #_{{ slugId($model->getId()) }} .ce-inline-tool {
            padding: 12px;
        }
    </style>
    <script type="module">
        import EditorJS from 'https://esm.sh/@editorjs/editorjs@^2';
        /** @see https://github.com/editor-js/paragraph/blob/master/src/index.js */
        import Paragraph from 'https://esm.sh/@editorjs/paragraph@^2';
        import {IconEtcVertical, IconUndo} from 'https://esm.sh/@codexteam/icons';
        import Underline from '/admin/structure/tools/underline.mjs';
        import Bold from '/admin/structure/tools/bold.mjs';
        import Italic from '/admin/structure/tools/italic.mjs';

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
             * E.g. {"label":{"label":"Title"},"default":{"default":"Confetti CMS"},"min":{"min":1},"max":{"max":20}};
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
                    inputHolder.classList.add('border-red-200');
                    Component.element.getElementsByClassName('_error')[0].innerHTML = message;
                    return;
                }
                // Remove the error message
                Component.element.getElementsByClassName('_error')[0].innerText = '';
                inputHolder.classList.remove('border-red-200');
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
             * @param {string} value
             */
            static validateWithMessage(value) {
                // Convert html entities in one function. Otherwise, the value length is wrong.
                // For example &nbsp; is one character, but the length is 6.
                value = new DOMParser().parseFromString(value, 'text/html').body.textContent;
                const validators = [
                    Component.validateMinLength,
                    Component.validateMaxLength,
                ];
                for (const validator of validators) {
                    const message = validator(value);
                    if (message != null) {
                        return message;
                    }
                }
            }

            /**
             * @param {string} value
             * @return {string}
             */
            static validateMinLength(value) {
                if (value.length >= Component.decorations.min.min) {
                    return null;
                }
                return `The value must be at least ${Component.decorations.min.min} characters long.`;
            }

            /**
             * @param {string} value
             * @return {string}
             */
            static validateMaxLength(value) {
                if (value.length <= Component.decorations.max.max) {
                    return null;
                }
                // Cut the value to the max length, and get the rest
                let toMuch = value.substring(Component.decorations.max.max);
                let suffix = '';
                if (toMuch.length > 26) {
                    toMuch = toMuch.substring(0, 26);
                    suffix = '(...)';
                }
                return `The value must be at most ${Component.decorations.max.max} characters long.<br>Therefore you cannot use: <span class="text-red-500 underline">${toMuch}</span> ${suffix}`
            }

        }

        /**
         * In this text component, we only allow the paragraph tool.
         */
        class Text extends Paragraph {
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
            inlineToolbar: true,
            tools: {
                bold: Bold,
                underline: Underline,
                italic: Italic,
                paragraph: {
                    class: Paragraph,
                    inlineToolbar: [
                        'bold',
                        'underline',
                        'italic',
                    ]
                },
            },
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
                const component = await api.saver.save()

                // Ensure that the value is updated when the user types
                OnChangeHandler.changed(api, events, component);

                // Ensure that there is only one block
                OnChangeHandler.ensureOneBlock(api, events, component);
            },
        });

        class OnChangeHandler {
            /**
             * Every time the user types, this function is called.
             *
             * @param {EditorJS} api
             * @param {Array} events
             * @param {object} component
             */
            static changed(api, events, component) {
                for (const event of events) {
                    if (event.type !== 'block-changed') {
                        continue;
                    }
                    // If there are more than one block, first the
                    // block-added code needs to flatten the blocks.
                    if (component.blocks.length > 1) {
                        break;
                    }
                    let value = '';
                    if (component.blocks.length > 0) {
                        value = component.blocks[0].data.text;
                    }
                    Component.storageValue = value;
                    // Update the style
                    Component.updateValueChangedStyle(value);
                }
            }

            /**
             * This is a workaround to ensure that there is only one block.
             * Because Editor.js doesn't allow easy configuration to have only one block.
             *
             * @param {EditorJS} api
             * @param {Array} events
             * @param {object} component
             */
            static ensureOneBlock(api, events, component) {
                // Compose the text of all blocks
                let text = '';
                component.blocks.forEach((block, index) => {
                    text += ' ' + block.data.text;
                });

                let blockAdded = false;
                for (const event of events) {
                    // We are only interested in the blocks that are added.
                    // And we only want to remove the block if it's not the first block.
                    if (event.type !== 'block-added' || event.detail.index === 0) {
                        continue;
                    }
                    api.blocks.delete();
                    blockAdded = true;
                }
                if (blockAdded) {
                    const firstBlock = component.blocks[0]
                    api.blocks.update(firstBlock.id, {text: text})
                    setTimeout(() => {
                        api.caret.setToBlock(0, 'end')
                    }, 20);
                }
            }
        }
    </script>
@endpush