@php
/** @var \Confetti\Helpers\ComponentStandard $model */
$component = $model->getComponent()
$showBlockSettings = false;
@endphp
<div class="block text-bold text-xl mt-8 mb-4">
    {{ $model->getLabel() }}
</div>

<div class="px-5 py-3 text-gray-700 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
    <span id="{{ slugId($model->getId()) }}"></span>
</div>

@push('end_of_body_'.slugId($model->getId()) )
    <style>
        @if($showBlockSettings)
            #{{ slugId($model->getId()) }} .ce-toolbar__actions--opened {
                display: block;
            }
        @endif

        #{{ slugId($model->getId()) }}
        .ce-block__content,
        .ce-toolbar__content {
            max-width: unset;
        }
    </style>
    <script type="module">
        import EditorJS from "https://esm.sh/@editorjs/editorjs";
        import List from 'https://esm.sh/@editorjs/list';

        const editor = new EditorJS({
            constructor({data, api}) {
                this.api = api;
                this.holder = document.getElementById('{{ slugId($model->getId()) }}');
            },

            /**
             * Id of Element that should contain Editor instance
             */
            holder: '{{ slugId($model->getId()) }}',
            minHeight : 0,
            defaultBlock: "paragraph",
            tools: {
                list: {
                    class: List,
                    inlineToolbar: true,
                },
            },
            inlineToolbar: true,
            placeholder: '{{ $component->getDecoration('placeholder') }}',

            data: {
                time: 1552744582955,
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

            onReady: (api, event) => {
                console.log('onReady');
            },
            onFocus: (api, event) => {
                console.log('onFocus');
            },
            onChange: (api, event) => {
                console.log('onChange');
            },
        });
    </script>
@endpush