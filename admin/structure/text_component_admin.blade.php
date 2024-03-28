@php
/** @var \Confetti\Helpers\ComponentStandard $model */
$component = $model->getComponent();
@endphp
<div class="block text-bold text-xl mt-8 mb-4">
    {{ $model->getLabel() }}
</div>

<div class="px-5 py-3 text-gray-700 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
    <span id="{{ slugId($model->getId()) }}"></span>
</div>

@push('end_of_body_'.slugId($model->getId()) )
    <style>
        /* Hide the toolbar so the user can't add new blocks */
        #{{ slugId($model->getId()) }} .ce-toolbar__actions--opened {
            display: none;
        }

        /* With a big screen, the text is indeed to the right */
        #{{ slugId($model->getId()) }}
        .ce-block__content,
        .ce-toolbar__content {
            max-width: unset;
        }
    </style>
    <script type="module">
        import EditorJS from "https://esm.sh/@editorjs/editorjs";
        import Paragraph from 'https://esm.sh/@editorjs/paragraph';

        const editor = new EditorJS({
            constructor({data, api}) {
                this.api = api;
                this.holder = document.getElementById('{{ slugId($model->getId()) }}');
            },

            // Id of Element that should contain Editor instance
            holder: '{{ slugId($model->getId()) }}',
            minHeight : 0,
            defaultBlock: "paragraph",
            tools: {
                paragraph: {
                    class: Paragraph,
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
                            text: "{{ $model->get() }}"
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
                    // In the text component, we allow only one block If a new block
                    // is added, we remove it and append the text to the first block.
                    if (event.type === 'block-added') {
                        let currentText = api.blocks.getBlockByIndex(api.blocks.getCurrentBlockIndex()).holder.innerText;
                        let first = api.blocks.getBlockByIndex(0);
                        let firstText = first.holder.getElementsByClassName('cdx-block')[0].innerText;
                        first.holder.getElementsByClassName('cdx-block')[0].innerText = firstText + " " + currentText;
                        api.blocks.delete();
                    }
                }
            }
        });
    </script>
@endpush