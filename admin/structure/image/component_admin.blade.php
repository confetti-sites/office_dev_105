@php /** @var \Confetti\Components\Image $model */ @endphp
<!--suppress HtmlUnknownTag -->
<image-component
        data-id="{{ $model->getId() }}"
        data-label="{{ $model->getComponent()->getLabel() }}"
        data-help="{{ $model->getComponent()->getDecoration('help') }}"
        data-ratio-width="{{ $model->getComponent()->getDecoration('ratio', 'ratioWidth') }}"
        data-ratio-height="{{ $model->getComponent()->getDecoration('ratio', 'ratioHeight') }}"
        data-value='@json($model->get())'
></image-component>

@pushonce('styles_cropper')
    <link rel="stylesheet" href="/admin/structure/image/cropper.css">
@endpushonce
@pushonce('end_of_body_image_component')
    <style>image-component .cropper-modal {opacity: 0.1}</style>
    <script type="module">
        import {Toolbar} from '/admin/assets/js/lim_editor.mjs';
        import {Storage, IconUpload} from '/admin/assets/js/admin_service.mjs';
        /** @see https://github.com/codex-team/icons */
        import {IconUndo} from 'https://esm.sh/@codexteam/icons';
        import {html, reactive} from 'https://esm.sh/@arrow-js/core';
        // https://fengyuanchen.github.io/cropperjs
        import Cropper from 'https://esm.sh/cropperjs';

        customElements.define('image-component', class extends HTMLElement {
            data = {
                currentFile: null,
                toCrop: null,
                dragover: false,
            };

            constructor() {
                super();
                this.data = reactive(this.data);
            }

            connectedCallback() {
                html`
                    <label class="block text-bold text-xl mt-8 mb-4">${this.dataset.label}</label>
                    <div class="flex items-center justify-center w-full">

                        ${() => this.data.toCrop ? html`
                            <!-- Canvas to Crop the image -->
                            <div class="w-full h-64 border-2 border-gray-300 border-solid rounded-lg overflow-hidden">
                                <div class="relative h-64">
                                    <img class="absolute w-full h-64 object-cover blur-sm opacity-70"
                                         src="${() => this.data.toCrop ? URL.createObjectURL(this.data.toCrop) : ''}"
                                         alt="cropper-background">
                                    <img id="image" style="display: block;max-width: 100%;"
                                         src="${() => this.data.toCrop ? URL.createObjectURL(this.data.toCrop) : ''}"
                                         onload="${() => setTimeout(() => this.#loadCropper(this.querySelector('#image')), 1)}"
                                         alt="cropper-canvas"
                                    >
                                </div>
                            </div>
                        ` : ``}
                        <label for="${this.dataset.id}"
                               class="${() => `_dropzone ${this.data.toCrop ? `hidden` : ``} flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 ${this.data.dragover ? `border-solid` : `border-dashed`} rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100`}">
                            ${() => this.data.currentFile ? html`
                                <!-- current image -->
                                <div class="w-full h-64">
                                    <img class="w-full h-64 object-cover rounded-lg"
                                         src="${() => this.data.currentFile ? URL.createObjectURL(this.data.currentFile) : ''}"
                                         alt=""
                                    />
                                </div>
                            ` : ``}

                            ${() => !this.data.currentFile ? html`
                                <!-- Information for new image -->
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    ${IconUpload(`w-8 h-8 mb-4 text-gray-500`)}
                                    <p class="mb-2 text-sm text-gray-500"><span
                                            class="font-semibold">Click to upload</span>
                                        or drag and drop</p>
                                    <p class="text-xs text-gray-500">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
                                    <p class="text-xs text-gray-500">${this.dataset.help}</p>
                                </div>
                            ` : ``}
                            <input @change="${() => (this.data.toCrop = this.querySelector('input').files[0])}"
                                   id="${this.dataset.id}"
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

            #loadCropper(element) {
                let ratio = undefined;
                if (this.dataset.ratioWidth > 0 && this.dataset.ratioHeight > 0) {
                    ratio = this.dataset.ratioWidth / this.dataset.ratioHeight;
                }

                new Cropper(element, {
                    aspectRatio: ratio,
                    background: false,
                    modal: true,
                    guides: false,
                    restore: false,
                    dragMode: 'none',
                    center: false,
                    viewMode: 2,
                    autoCropArea: 1,
                    zoomable: false,
                    crop(event) {
                        console.log(event.detail.x);
                        console.log(event.detail.y);
                        console.log(event.detail.width);
                        console.log(event.detail.height);
                        console.log(event.detail.rotate);
                        console.log(event.detail.scaleX);
                        console.log(event.detail.scaleY);
                    },
                });
            }

            #addListeners() {
                this.querySelectorAll('._dropzone').forEach(input => {
                    data = this.data;
                    input.addEventListener('dragover', function (e) {
                        data.dragover = true;
                        console.log('dragover');
                        e.preventDefault();
                        e.stopPropagation();
                    });

                    input.addEventListener('dragleave', function (e) {
                        data.dragover = false;
                        console.log('dragleave');
                        e.preventDefault();
                        e.stopPropagation();
                    });

                    input.addEventListener('drop', function (e) {
                        data.dragover = false;
                        console.log('drop');
                        e.preventDefault();
                        e.stopPropagation();
                        if (e.dataTransfer) {
                            data.toCrop = e.dataTransfer.files[0];
                        } else if (e.target) {
                            data.toCrop = e.target.files[0];
                        }
                    });
                });
            }
        });
    </script>
@endpushonce
