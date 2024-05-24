@php /** @var \Confetti\Components\Image $model */ @endphp
        <!--suppress HtmlUnknownTag, HtmlUnknownAttribute -->
<image-component
        data-id="{{ $model->getId() }}"
        data-label="{{ $model->getComponent()->getLabel() }}"
        data-help="{{ $model->getComponent()->getDecoration('help') }}"
        data-width_px="{{ $model->getComponent()->getDecoration('widthPx') }}"
        data-ratio_width="{{ $model->getComponent()->getDecoration('ratio', 'ratioWidth') }}"
        data-ratio_height="{{ $model->getComponent()->getDecoration('ratio', 'ratioHeight') }}"
        data-value='@json($model->get())'
        data-service_api="{{ getServiceApi() }}"
        data-value='@json($model->get())'
></image-component>

@pushonce('styles_cropper')
    <link rel="stylesheet" href="/admin/structure/image/cropper.css">
@endpushonce
@pushonce('end_of_body_image_component')
    <script type="module">
        import {Toolbar} from '/admin/assets/js/lim_editor.mjs';
        import {Storage, Media, IconUpload, IconTrash, IconUndo} from '/admin/assets/js/admin_service.mjs';
        import {html, reactive} from 'https://esm.sh/@arrow-js/core';
        // https://fengyuanchen.github.io/cropperjs
        import Cropper from 'https://esm.sh/cropperjs';

        customElements.define('image-component', class extends HTMLElement {
            data = {
                value: null,
                dragover: false,
                warningMessage: '',
            };

            constructor() {
                super();
                this.data.value = JSON.parse(this.dataset.value);
                this.data = reactive(this.data);
            }

            connectedCallback() {
                html`
                    <label class="block text-bold text-xl mt-8 mb-4">${this.dataset.label}</label>
                    <div class="flex items-center justify-center w-full">
                        ${() => this.data.value.original !== undefined ? html`
                            <!-- Canvas to Crop the image -->
                            <div class="w-full h-64 border-2 border-gray-300 border-solid rounded-lg">
                                <div class="relative h-64" style="margin-left: -2px; margin-top: -2px;">
                                    <img class="absolute w-full h-64 object-cover blur-sm opacity-70 rounded-lg"
                                         src="${() => this.#getFullUrl(this.data.value.original)}"
                                         alt="cropper-background">
                                    <img id="image" class="block rounded-lg w-full"
                                         src="${() => this.#getFullUrl(this.data.value.original)}"
                                         @load="${() => this.#imageLoaded(this.querySelector('#image'))}"
                                         alt="cropper-canvas">
                                </div>
                            </div>
                        ` : ``}
                        <label for="${this.dataset.id}"
                               class="${() => `_dropzone ${this.data.value.original !== undefined ? `hidden` : ``} flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 ${this.data.dragover ? `border-solid` : `border-dashed`} rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100`}">
                            ${() => this.data.value.original === undefined ? html`
                                <!-- Information for new image -->
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    ${IconUpload(`w-8 h-8 mb-4 text-gray-500`)}
                                    <p class="mb-2 text-sm text-gray-500"><span
                                    class="font-semibold">Click to upload</span> or drag and drop.</p>
                                    ${() => this.dataset.width_px ? html`
                                        <p class="text-sm text-gray-500">Good width: ${this.dataset.width_px} pixels or more</p>
                                        <p class="text-sm text-gray-500">Perfect width: ${this.dataset.width_px * 2} pixels or more</p>
                                    ` : ``}
                                </div>
                            ` : ``}
                            <input @change="${e => this.#uploading(e)}"
                                   id="${this.dataset.id}"
                                   type="file"
                                   accept="image/*"
                            />
<!--                                   class="hidden"-->
                        </label>
                    </div>
                    <p class="mt-2 text-sm text-gray-600">${() => this.data.warningMessage}</p>
                `(this)

                this.#addListeners();

                new Toolbar(this).init([
                    {
                        label: 'Remove unpublished changes',
                        icon: IconUndo(`w-8 h-8 p-1`),
                        closeOnActivate: true,
                        onActivate: async () => {
                            /** @todo remove unpublished changes */
                            console.log('@todo Remove unpublished changes');
                        }
                    },
                    {
                        label: 'Remove image',
                        icon: IconTrash(`w-8 h-8 p-1`),
                        closeOnActivate: true,
                        onActivate: async () => {
                            this.#removeImage();
                        }
                    },
                    {
                        label: 'Upload new image',
                        icon: IconUpload(`w-8 h-8 p-1`),
                        closeOnActivate: true,
                        onActivate: async () => {
                            this.#removeImage();
                            this.querySelector('input').click();
                        }
                    }],
                );
            }

            #uploading(e) {
                this.data.value.original = URL.createObjectURL(e.target.files[0]);

                Media.upload(this.dataset.service_api, this.dataset.id, e.target.files[0], (response) => {
                    this.data.value.original = response[0]['original'];
                });
            }

            #removeImage() {
                this.data.value = {};
                this.data.warningMessage = '';
                this.querySelector('input').value = '';
            }

            #imageLoaded(element) {
                let ratio = undefined;
                if (this.dataset.ratio_width > 0 && this.dataset.ratio_height > 0) {
                    ratio = this.dataset.ratio_width / this.dataset.ratio_height;
                }
                const parentThis = this;

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
                        // console.log(event);
                        // console.log(event.detail);

                        parentThis.#validate(event.detail.width);

                        
                        // console.log(event.detail.x);
                        // console.log(event.detail.y);
                        // console.log(event.detail.width);
                        // console.log(event.detail.height);
                        // console.log(event.detail.rotate);
                        // console.log(event.detail.scaleX);
                        // console.log(event.detail.scaleY);
                    },
                });
            }

            #addListeners() {
                this.querySelectorAll('._dropzone').forEach(input => {
                    data = this.data;
                    input.addEventListener('dragover', function (e) {
                        data.dragover = true;
                        e.preventDefault();
                        e.stopPropagation();
                    });

                    input.addEventListener('dragleave', function (e) {
                        data.dragover = false;
                        e.preventDefault();
                        e.stopPropagation();
                    });

                    input.addEventListener('drop', function (e) {
                        data.dragover = false;
                        e.preventDefault();
                        e.stopPropagation();
                        if (e.dataTransfer) {
                            data.value.original = e.dataTransfer.files[0];
                        } else if (e.target) {
                            data.value.original = e.target.files[0];
                        }
                    });
                });
            }

            /**
             * @param {number} imageWidth
             */
            #validate(imageWidth) {
                let minWidth = Number(this.dataset.width_px);
                imageWidth = Math.round(imageWidth);

                // To blurry for any device
                if (imageWidth < Math.min(640, minWidth)) {
                    this.data.warningMessage = 'The current image is ' + imageWidth + ' pixels wide. ' + minWidth + ' pixels is recommended.';
                    return;
                }
                // To blurry for desktop devices
                if (imageWidth < minWidth) {
                    this.data.warningMessage = 'The current image is ' + imageWidth + ' pixels wide. ' + minWidth + ' pixels is recommended for desktop devices (~40% of all users).';
                    return;
                }
                // To blurry for macbook devices
                if (imageWidth < (minWidth * 2)) {
                    this.data.warningMessage = 'The current image is ' + imageWidth + ' pixels wide. ' + (minWidth * 2) + ' pixels is recommended for macbook devices (~6% of all users).';
                    return;
                }

                this.data.warningMessage = '';
            }

            #getFullUrl(path) {
                if (path === undefined) {
                    return '';
                }
                // If `path` starts with blob, then it is a local file and we need to return it as is
                if (path.startsWith('blob:')) {
                    return path;
                }
                return `${this.dataset.service_api}/confetti-cms/media/images${path}`;
            }
        });
    </script>
@endpushonce
