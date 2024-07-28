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
        import {IconTrash, IconUndo, IconUpload, Media, Storage} from '/admin/assets/js/admin_service.mjs';
        import {html, reactive} from 'https://esm.sh/@arrow-js/core';
        // https://fengyuanchen.github.io/cropperjs
        import Cropper from 'https://esm.sh/cropperjs';

        /**
         * @typedef {object} Value
         * @property {string} original
         * @property {object} crop
         * @property {number} crop.x
         * @property {number} crop.y
         * @property {number} crop.width
         * @property {number} crop.height
         */

        customElements.define('image-component', class extends HTMLElement {
            valueFunction = {
                // Mark the value as removed. So when
                // the page is saved, the value can be removed.
                remove: 'this.remove()',
            }

            data = {
                value: null,
                dragover: false,
                message: '',
                cropper: undefined,
            };
            cropDetails = {};
            defaultMaxWidth = 1200;

            constructor() {
                super();
                this.data = reactive(this.data);
                this.data.value = this.getCurrentValue();
                // when local_content_changed is fired, update the value
                window.addEventListener('local_content_changed', () => {
                    this.data.value = this.getCurrentValue();
                });
            }

            connectedCallback() {
                html`
                    <label class="block text-bold text-xl mt-8 mb-4">${this.dataset.label}</label>
                    <div class="_dropzone flex items-center justify-center w-full">
                        <!-- Canvas to Crop the image -->
                        <div class="${() => `${this.data.value.original === undefined ? `hidden` : ``} w-full h-64 border-2 border-gray-300 border-solid rounded-lg`}">
                            <div class="relative h-64" style="margin-left: -2px; margin-top: -2px;">
                                <img class="absolute w-full h-64 object-cover blur-sm opacity-70 rounded-lg"
                                     src="${() => this.#getFullUrl(this.data.value.original)}"
                                     alt="cropper-background">
                                <img id="image" class="block rounded-lg w-full max-h-64 hidden"
                                     src="${() => this.#getFullUrl(this.data.value.original)}"
                                     @load="${() => this.#imageLoaded(this.querySelector('#image'))}"
                                     alt="cropper-canvas">
                            </div>
                        </div>
                        <label for="${this.dataset.id}"
                               class="${() => `${this.data.value.original !== undefined ? `hidden` : ``} flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 ${this.data.dragover ? `border-solid ` : `border-dashed`} rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100`}">
                            ${() => this.data.value.original === undefined ? html`
                                <!-- Information for new image -->
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    ${IconUpload(`w-8 h-8 mb-4 text-gray-500`)}
                                    <p class="mb-2 text-sm text-gray-500"><span
                                            class="font-semibold">Click to upload</span> or drag and drop</p>
                                    ${this.#getRequirements()}
                                </div>
                            ` : ``}
                            <input @change="${e => this.uploading(e.target.files[0])}"
                                   id="${this.dataset.id}"
                                   type="file"
                                   accept="image/*"
                                   class="hidden"
                            />
                        </label>
                    </div>
                    <p class="mt-2 text-sm text-gray-600">${() => this.data.message}</p>
                `(this)

                this.#addDropZoneListeners();
                this.#registerToolbar();
            }

            #getRequirements() {
                let requirements = [{left: 'Supported formats:', right: 'jpg, jpeg, png, webp'}];
                if (this.dataset.width_px) {
                    requirements.push({left: 'Good width:', right: this.dataset.width_px + ' pixels or more'});
                    requirements.push({left: 'Perfect width:', right: this.dataset.width_px * 2 + ' pixels or more'});
                }
                return html`
                    <div class="grid grid-cols-2 gap-x-1 text-sm text-gray-500">
                        ${requirements.map(requirement => html`
                            <div class="text-right">${requirement.left}</div>
                            <div class="text-left">${requirement.right}</div>
                        `)}
                    </div>
                `;
            }

            #registerToolbar() {
                new Toolbar(this).init([
                    {
                        label: 'Remove unpublished changes',
                        icon: IconUndo(`w-8 h-8 p-1`),
                        closeOnActivate: true,
                        onActivate: async () => {
                            this.removeImage();
                            this.data.value = JSON.parse(this.dataset.value);
                            Storage.removeLocalStorageItems(this.dataset.id);
                            window.dispatchEvent(new CustomEvent('local_content_changed'));
                        }
                    },
                    {
                        label: 'Remove image',
                        icon: IconTrash(`w-8 h-8 p-1`),
                        closeOnActivate: true,
                        onActivate: async () => {
                            this.removeImage();
                            Storage.saveToLocalStorage(this.dataset.id, this.valueFunction.remove);
                            window.dispatchEvent(new CustomEvent('local_content_changed'));
                        }
                    },
                    {
                        label: 'Upload new image',
                        icon: IconUpload(`w-8 h-8 p-1`),
                        closeOnActivate: true,
                        onActivate: async () => {
                            this.removeImage();
                            this.querySelector('input').click();
                        }
                    }],
                );
            }

            /**
             * @param {number} imageWidth
             */
            #validate(imageWidth) {
                let minWidth = Number(this.dataset.width_px);
                imageWidth = Math.round(imageWidth);

                // To blurry for any device
                if (imageWidth < Math.min(640, minWidth)) {
                    this.data.message = 'The current image is ' + imageWidth + ' pixels wide. ' + minWidth + ' pixels is recommended.';
                    return;
                }
                // To blurry for desktop devices
                if (imageWidth < minWidth) {
                    this.data.message = 'The current image is ' + imageWidth + ' pixels wide. ' + minWidth + ' pixels is recommended for desktop devices (~40% of all users).';
                    return;
                }
                // To blurry for macbook devices
                if (imageWidth < (minWidth * 2)) {
                    this.data.message = 'The current image is ' + imageWidth + ' pixels wide. ' + (minWidth * 2) + ' pixels is recommended for macbook devices (~6% of all users).';
                    return;
                }

                this.data.message = '';
            }

            uploading(target) {
                const id = this.dataset.id;
                // Set local image as the original image before we can use the uploaded image
                this.data.value = {original: URL.createObjectURL(target)};
                window.dispatchEvent(new CustomEvent('state', {
                    detail: {
                        id: id + '.upload',
                        state: 'loading',
                        title: 'Uploading ' + this.dataset.label,
                    }
                }));
                // Upload the image to the server
                Media.upload(this.dataset.service_api, id, target, (response) => {
                    window.dispatchEvent(new CustomEvent('state', {
                        detail: {
                            id: id + '.upload',
                            state: 'success',
                        }
                    }));
                    // Set image as loaded to render the cropper. Set src
                    Storage.saveToLocalStorage(id, {original: response[0]['original']});
                    this.saveCropped(id, this.dataset.label);
                    window.dispatchEvent(new CustomEvent('local_content_changed'));
                });
            }

            removeImage() {
                this.data.value = {};
                this.data.message = '';
                this.cropper?.destroy();
                this.cropper = undefined;
                this.querySelector('input').value = '';
            }

            getCurrentValue() {
                let local = Storage.getFromLocalStorage(this.dataset.id);
                if (local === this.valueFunction.remove) {
                    return {};
                }
                if (local !== null) {
                    return local;
                }
                return JSON.parse(this.dataset.value);
            }

            #imageLoaded(element) {
                // Every time the crop has been changed, the image loaded event is fired,
                // but we only want to render the cropper once per page load or image change
                if (this.cropper !== undefined) {
                    return;
                }
                this.cropper = this.#renderCropper(element);
                // Fix for when resizing the window, the cropper is not triggered
                // to update the crop area, so it gets out the viewport
                let afterResize
                window.addEventListener('resize', () => {
                    clearTimeout(afterResize);
                    afterResize = setTimeout(() => {
                        this.cropper.destroy();
                        this.cropper = this.#renderCropper(element);
                    }, 10);
                })
            }

            #renderCropper(element) {
                let ratio = undefined;
                if (this.dataset.ratio_width > 0 && this.dataset.ratio_height > 0) {
                    ratio = this.dataset.ratio_width / this.dataset.ratio_height;
                }
                const parentThis = this;

                return new Cropper(element, {
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
                    data: {
                        x: this.data.value.crop?.x || null,
                        y: this.data.value.crop?.y || null,
                        width: this.data.value.crop?.width || null,
                        height: this.data.value.crop?.height || null,
                    },
                    crop(event) {
                        parentThis.#validate(event.detail.width);
                        parentThis.cropDetails = event.detail
                    },
                    cropend() {
                        parentThis.saveCropped(parentThis.dataset.id, parentThis.dataset.label);
                    },
                });
            }

            /**
             * @param {string} id
             * @param {string} label
             */
            saveCropped(id, label) {
                let value = this.getCurrentValue();
                value.crop = {
                    x: Math.round(this.cropDetails.x),
                    y: Math.round(this.cropDetails.y),
                    width: Math.round(this.cropDetails.width),
                    height: Math.round(this.cropDetails.height),
                }
                Storage.removeLocalStorageItems(id);
                if (value !== this.dataset.original) {
                    Storage.saveToLocalStorage(id, value);
                }
                window.dispatchEvent(new CustomEvent('local_content_changed'));
                Storage.saveToLocalStorage(`/listener${id}.cropped`, {
                    title: 'Process: ' + label,
                    remove_when_done: true,
                    when: {
                        event: 'saving',
                        id: id,
                    },
                    then: {
                        'method': 'POST',
                        'url': this.dataset.service_api + '/confetti-cms/media/sources',
                        'body': {
                            'id': id,
                            'original': value.original,
                            'target_width': this.dataset.width_px ?? this.defaultMaxWidth,
                            'height': value.crop.height,
                            'width': value.crop.width,
                            'x': value.crop.x,
                            'y': value.crop.y,
                        },
                    },
                    patch_in: "sources"
                })
            }

            #addDropZoneListeners() {
                this.querySelectorAll('._dropzone').forEach(input => {
                    data = this.data;
                    let parentThis = this;
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
                        parentThis.removeImage();
                        data.dragover = false;
                        e.preventDefault();
                        e.stopPropagation();
                        let file = undefined;
                        if (e.dataTransfer) {
                            file = e.dataTransfer.files[0];
                        } else if (e.target) {
                            file = e.target.files[0];
                        }
                        setTimeout(() => parentThis.uploading(file), 1);
                    });
                });
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
