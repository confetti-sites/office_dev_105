@php
    /** @var \Confetti\Helpers\ComponentStandard $model */
    $component = $model->getComponent();
@endphp
<div id="_{{ slugId($model->getId()) }}_component">
    <div class="block text-bold text-xl mt-8 mb-4">
        {{ $model->getLabel() }}
    </div>

    <div class="px-5 py-3 text-gray-700 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700 _input_holder">
        <span id="_{{ slugId($model->getId()) }}"></span>
    </div>
</div>

@pushonce('end_of_body_'.slugId($model->getId()) )
    <style>
        /* Hide the toolbar items so the user can't add new blocks */
        #_{{ slugId($model->getId()) }} .ce-toolbar__plus {
            display: none;
        }
        #_{{ slugId($model->getId()) }} .cdx-search-field {
            display: none;
        }
        #_{{ slugId($model->getId()) }} .ce-popover-item[data-item-name="move-up"] {
            display: none;
        }
        #_{{ slugId($model->getId()) }} .ce-popover-item[data-item-name="delete"] {
            display: none;
        }
        #_{{ slugId($model->getId()) }} .ce-popover-item[data-item-name="move-down"] {
            display: none;
        }

        /* With a big screen, the text is indeed to the right */
        #_{{ slugId($model->getId()) }} .ce-block__content,
        #_{{ slugId($model->getId()) }} .ce-toolbar__content {
            max-width: unset;
        }
        /* Remove default editor.js padding */
        #_{{ slugId($model->getId()) }} .cdx-block {
             padding: 0;
        }
        .codex-editor--narrow .codex-editor__redactor {
            margin-right: 0;
        }
    </style>
    <script type="module">
        import EditorJS from "https://esm.sh/@editorjs/editorjs@^2";
        import Paragraph from 'https://esm.sh/@editorjs/paragraph@^2';

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
             * @return {string}
             */
            static get value() {
                return JSON.parse(localStorage.getItem(Component.id));
            }

            /**
             * @param {string} value
             */
            static set value(value) {
                // Save the value to local storage
                this.setLocalStorageValue(value);

                // Update the style
                Component.updateValueChangedStyle();

                // Change the value of the editor
                // Check if the innerText is changed to prevent infinite event loop
                if (this.element.querySelector('[contenteditable="true"]').innerText !== value) {
                    this.element.querySelector('[contenteditable="true"]').innerText = value;
                }
            }

            /**
             * @param {string} value
             */
            static setLocalStorageValue(value) {
                localStorage.setItem(Component.id, JSON.stringify(value));
            }

            static updateValueChangedStyle() {
                const inputHolder = Component.element.querySelector('._input_holder');
                if (Component.value !== null && Component.value !== Component.originalValue) {
                    inputHolder.classList.remove('border-gray-200');
                    inputHolder.classList.add('border-red-300');
                } else {
                    inputHolder.classList.remove('border-red-300');
                    inputHolder.classList.add('border-gray-200');
                }
            }
        }

        // Ensure that the value is updated when the page is loaded
        Component.updateValueChangedStyle();

        class ParagraphChild extends Paragraph {
            renderSettings() {
                return [
                    {
                        icon: '<span class="codex-icon" data-icon-name="IconUndo" title="IconUndo"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.33333 13.6667L6 10.3333L9.33333 7M6 10.3333H15.1667C16.0507 10.3333 16.8986 10.6845 17.5237 11.3096C18.1488 11.9348 18.5 12.7826 18.5 13.6667C18.5 14.5507 18.1488 15.3986 17.5237 16.0237C16.8986 16.6488 16.0507 17 15.1667 17H14.3333" data-darkreader-inline-stroke="" style="--darkreader-inline-stroke: currentColor;"></path></svg></span>',
                        label: 'Revert to saved value',
                        closeOnActivate: true,
                        onActivate: () => {
                            Component.value = Component.originalValue;
                        }
                    },
                ];
            }
        }

        new EditorJS({
            // Id of Element that should contain Editor instance
            holder: '_{{ slugId($model->getId()) }}',
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
                            text: JSON.parse(localStorage.getItem('{{ $model->getId() }}')) ?? '{{ $model->get() }}',
                        }
                    }
                ],
                version: "2.11.10"
            },

            onChange: (api, events) => {
                // if not array, make an array
                if (!Array.isArray(events)) {
                    events = [events];
                }
                for (const event of events) {
                    switch (event.type) {
                        // In the text component, we allow only one block If a new block
                        // is added, we remove it and append the text to the first block.
                        case 'block-added':
                            let currentText = api.blocks.getBlockByIndex(api.blocks.getCurrentBlockIndex()).holder.innerText;
                            let first = api.blocks.getBlockByIndex(0);
                            let firstText = first.holder.getElementsByClassName('cdx-block')[0].innerText;
                            first.holder.getElementsByClassName('cdx-block')[0].innerText = firstText + " " + currentText;
                            api.blocks.delete();
                            break;
                        // Save the value to local storage, so we can save it later when the user clicks on save.
                        case 'block-changed':
                            let text = api.blocks.getBlockByIndex(0).holder.innerText;
                            // Because localStorage can only store strings, we need to store it as json.
                            Component.value = text;
                            break;
                    }
                }
            },
        });


    </script>
@endpushonce