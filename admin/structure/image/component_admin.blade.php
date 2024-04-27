@php /** @var \Confetti\Components\Image $model */ @endphp

<image-component
    data-name="{{ $model->getId() }}"
    data-label="{{ $model->getComponent()->getLabel() }}"
    data-help="{{ $model->getComponent()->getDecoration('help') }}"
    data-value=@json($model->get())
></image-component>


@pushonce('end_of_body_image_component')
    <script type="module">
        import {Toolbar} from '/admin/assets/js/lim_editor.mjs';
        import {Storage, IconUpload} from '/admin/assets/js/admin_service.mjs';
        /** @see https://github.com/codex-team/icons */
        import {IconUndo} from 'https://esm.sh/@codexteam/icons';
        import {html, reactive} from 'https://esm.sh/@arrow-js/core';

        const data = reactive({
            currentFile: null,
        })

        class ImageComponent extends HTMLElement {
            connectedCallback() {
                html`
                    <label class="block text-bold text-xl mt-8 mb-4">${this.dataset.label}</label>
                    <div class="flex items-center justify-center w-full">
                        <label for="${this.dataset.name}"
                               class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                            <!-- current image -->
                            ${() => data.currentFile ? html`
                                <div class="w-full h-64">
                                    <img class="w-full h-64 object-cover rounded-lg"
                                         src="${() => data.currentFile ? URL.createObjectURL(data.currentFile) : 'none'}"
                                         alt=""
                                    />
                                </div>
                            ` : html`
                                <!-- dropzone for new image -->
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    ${IconUpload(`w-8 h-8 mb-4 text-gray-500`)}
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span>
                                        or drag and drop</p>
                                    <p class="text-xs text-gray-500">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
                                    <p class="text-xs text-gray-500">${this.dataset.help}</p>
                                </div>
                            `}
                            <input @change="${() => data.currentFile = this.querySelector('input').files[0]}"
                                   id="${this.dataset.name}"
                                   type="file"
                            />
                        </label>
                    </div>
                `(this)
            }
        }

        customElements.define('image-component', ImageComponent);
    </script>
@endpushonce
