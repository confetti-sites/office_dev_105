@php
    /** @var \Confetti\Helpers\ComponentStandard $model */
    $component = $model->getComponent();
@endphp
<div class="block text-bold text-xl mt-8 mb-4">
    {{ $model->getLabel() }}
</div>

<div class="px-5 py-3 text-gray-700 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
    <span id="_{{ slugId($model->getId()) }}"></span>

</div>

@push('end_of_body_'.slugId($model->getId()) )
    <style>
        /* Hide the toolbar so the user can't add new blocks */
        #_{{ slugId($model->getId()) }} .ce-toolbar__plus {
            display: none;
        }
        #_{{ slugId($model->getId()) }} .ce-toolbar__settings-btn {
            display: none;
        }

         /* With a big screen, the text is indeed to the right */
        #_{{ slugId($model->getId()) }}
        .ce-block__content,
        .ce-toolbar__content {
            max-width: unset;
        }
    </style>
    <script type="module">
        import EditorJS from "https://esm.sh/@editorjs/editorjs@^2";
        import Paragraph from 'https://esm.sh/@editorjs/paragraph@^2';

        class ParagraphChild extends Paragraph {

            render() {
                let toRender = super.render();
                setTimeout(() => {
                    ParagraphChild.hideToolButton('.ce-toolbar__plus');
                    ParagraphChild.hideToolButton('.ce-toolbar__settings-btn');
                    ParagraphChild.appendToolButton(
                        'revert',
                        new DOMParser().parseFromString('<span class="codex-icon" data-icon-name="IconUndo" title="IconUndo"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.33333 13.6667L6 10.3333L9.33333 7M6 10.3333H15.1667C16.0507 10.3333 16.8986 10.6845 17.5237 11.3096C18.1488 11.9348 18.5 12.7826 18.5 13.6667C18.5 14.5507 18.1488 15.3986 17.5237 16.0237C16.8986 16.6488 16.0507 17 15.1667 17H14.3333" data-darkreader-inline-stroke="" style="--darkreader-inline-stroke: currentColor;"></path></svg></span>', 'image/svg+xml').documentElement,
                    );
                }, 100)
                return toRender;
            }

            /**
             * @param {string} selector
             */
            static hideToolButton(selector) {
                document.querySelector(`#_{{ slugId($model->getId()) }} ` + selector).remove();
            }

            /**
             * @param {string} name
             * @param {Element} icon
             */
            static appendToolButton(name, icon) {
                const toolbar = document.querySelector(`#_{{ slugId($model->getId()) }} .ce-toolbar__actions`);
                console.log(`#_{{ slugId($model->getId()) }} .ce-toolbar__actions`);

                const popover = document.createElement('div');
                popover.classList.add('ce-popover-item__title');

                const wrapper = document.createElement('div');
                wrapper.classList.add('ce-popover-item');
                wrapper.setAttribute('data-item-name', name);
                wrapper.append(icon);
                wrapper.append(popover);

                toolbar.appendChild(wrapper);
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
                console.log(api, events)
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
                            text = JSON.stringify(text);
                            localStorage.setItem('{{ $model->getId() }}', text);
                            console.log('block-change', text);
                            break;
                    }
                }
            },
        });


    </script>
@endpush