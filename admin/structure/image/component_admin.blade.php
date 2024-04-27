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
            dragover: false,
        })

        class ImageComponent extends HTMLElement {
            connectedCallback() {
                html`
                    <label class="block text-bold text-xl mt-8 mb-4">${this.dataset.label}</label>
                    <div class="flex items-center justify-center w-full">
                        <label for="${this.dataset.name}"
                               class="${() => `_dropzone flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 ${data.dragover ? `border-solid` : `border-dashed`} rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100`}">
                            <!-- current image -->
                            ${() => data.currentFile ? html`
                                <div class="w-full h-64">
                                    <img class="w-full h-64 object-cover rounded-lg"
                                         src="${() => data.currentFile ? URL.createObjectURL(data.currentFile) : ''}"
                                         alt=""
                                    />
                                </div>
                            ` : html`
                                <!-- Information for new image -->
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    ${IconUpload(`w-8 h-8 mb-4 text-gray-500`)}
                                    <p class="mb-2 text-sm text-gray-500"><span
                                            class="font-semibold">Click to upload</span>
                                        or drag and drop</p>
                                    <p class="text-xs text-gray-500">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
                                    <p class="text-xs text-gray-500">${this.dataset.help}</p>
                                </div>
                            `}
                            <input @change="${() => (data.currentFile = this.querySelector('input').files[0])}"
                                   id="${this.dataset.name}"
                                   type="file"
                                   accept="image/*"
                                   class="hidden"
                                   multiple="false"

                            />
                        </label>
                    </div>
                `(this)
                this.#addListeners();
            }

            #addListeners() {
                this.querySelectorAll('._dropzone').forEach(input => {
                    input.addEventListener('dragover', function(e) {
                        data.dragover = true;
                        console.log('dragover');
                        e.preventDefault();
                        e.stopPropagation();
                    });

                    input.addEventListener('dragleave', function(e) {
                        data.dragover = false;
                        console.log('dragleave');
                        e.preventDefault();
                        e.stopPropagation();
                    });

                    input.addEventListener('drop', function(e) {
                        data.dragover = false;
                        console.log('drop');
                        e.preventDefault();
                        e.stopPropagation();
                        if(e.dataTransfer) {
                            data.currentFile = e.dataTransfer.files[0];
                        } else if(e.target) {
                            data.currentFile = e.target.files[0];
                        }
                    });
                });
            }

        }

        customElements.define('image-component', ImageComponent);
    </script>
@endpushonce
