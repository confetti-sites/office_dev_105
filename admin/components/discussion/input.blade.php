@php /** @var \Src\Components\DiscussionComponent $model */ @endphp
        <!--suppress HtmlUnknownTag -->
<discussion-component
        data-id="{{ $model->getId() }}"
        data-label="{{ $model->getComponent()->getLabel() }}"
        data-decorations='{{ json_encode($model->getComponent()->getDecorations()) }}'
        data-original='{{ json_encode($model->get()) }}'
        data-component='{{ json_encode($model->getComponent()) }}'
></discussion-component>

@pushonce('style_discussion_component')
    <link rel="stylesheet" href="/website/assets/css/github-light.css"/>
@endpushonce
@pushonce('end_of_body_discussion_component')
    <script type="module">
        import {Toolbar} from '/admin/assets/js/editor.mjs';
        import {Storage} from '/admin/assets/js/admin_service.mjs';
        import {IconUndo} from 'https://esm.sh/@codexteam/icons';
        import {html, reactive} from 'https://esm.sh/@arrow-js/core';

        customElements.define('discussion-component', class extends HTMLElement {
            id
            label
            original
            data
            decorations = {
                help: {help: null},
            }

            constructor() {
                super();
                this.id = this.dataset.id;
                this.label = this.dataset.label;
                this.original = JSON.parse(this.dataset.original);
                this.decorations = JSON.parse(this.dataset.decorations);
                const currentValue = Storage.getFromLocalStorage(this.id) || this.original || {};
                this.data = reactive({
                    // If no value is given, we will save defaultWhenNoDefaultColor when the element is loaded
                    value: currentValue,
                    inputValue: currentValue.url || '',
                });
            }

            connectedCallback() {
                this.data.$on('inputValue', async value => {
                    // check if value contains github.com
                    if (value.includes('github.com')) {
                        value = {
                            url: value,
                            discussion: await this.fetchDiscussion(value),
                        };
                    } else {
                        value = {'error': 'Invalid url', 'url': null};
                    }
                    this.data.value = value;
                    Storage.removeLocalStorageModels(this.id);
                    if (value !== this.original) {
                        Storage.saveLocalStorageModel(this.id, value, this.dataset.component);
                    }
                    this.#checkStyle();
                    window.dispatchEvent(new CustomEvent('local_content_changed'));
                });

                html`
                    <label class="block text-bold text-xl mt-8 mb-4">${this.label}</label>
                    <input class="${() => `w-full px-5 py-3 text-gray-700 border-2 rounded-lg bg-gray-50 ${this.data.value === this.original ? `border-gray-300` : `border-emerald-300`}`}"
                           name="${this.id}"
                           value="${() => this.data.inputValue}"
                           @input="${(e) => this.data.inputValue = e.target.value}">
                    ${this.decorations.help.help ? html`
                        <p class="mt-2 text-sm text-gray-500">${this.decorations.help.help}</p>` : ''}
                    ${() => this.data.value.error ? html`
                        <p class="mt-2 text-sm text-red-500">${this.data.value.error}</p>` : ''}

                    ${() => this.data.value.url ? html`
                        <label class="m-2 h-10 block">
                            <button type="button" class="float-right justify-between px-2 py-1 m-2 ml-0 text-sm font-medium leading-5 cursor-pointer text-white bg-emerald-700 hover:bg-emerald-800 border border-transparent rounded-md"
                                    onclick="navigator.clipboard.writeText('${this.data.value.url}').then(() => this.innerHTML = 'Copied!').catch(() => this.innerHTML = 'Failed!');">
                                Copy URL
                            </button>
                        </label>
                    ` : ''}
                    ${() => this.data.value.discussion ? html`
                        <label class="block text-bold text-l mt-8 mb-4">The discussion content:</label>
                        <div class="mt-2 p-4 border border-gray-400 rounded-lg">
                            <discussion>${this.data.value.discussion.body}</discussion>
                        </div>
                    ` : ''}
                `(this)

                new Toolbar(this).init([{
                        label: 'Remove unpublished changes',
                        icon: IconUndo,
                        closeOnActivate: true,
                        onActivate: async () => {
                            this.querySelector('input').value = this.original;
                            this.querySelector('input').dispatchEvent(new Event('change'));
                            this.data.value = this.original;
                        }
                    }],
                );

                // We need to save some value (with component) to show it in the list
                if (this.data.value === null) {
                    this.data.value = {'error': 'No url given', 'url': null};
                }
            }

            #checkStyle() {
                const input = this.querySelector('input');
                if (this.data.value === this.original) {
                    input.classList.remove('border-emerald-300');
                    input.classList.add('border-gray-200');
                } else {
                    // Mark the input element as dirty
                    input.classList.remove('border-gray-200');
                    input.classList.add('border-emerald-300');
                }
            }

            async fetchDiscussion(url) {
                // Extract repository owner, repo name, and discussion ID from the URL
                let match = url.match(/https:\/\/github.com\/([^/]+)\/([^/]+)\/discussions\/(\d+)/);
                if (!match) {
                    return {error: 'Invalid Github URL', url: null};
                }

                // Construct the API URL
                const apiUrl = `https://api.github.com/repos/${match[1]}/${match[2]}/discussions/${match[3]}`;

                // Fetch the data from the GitHub API
                const response = await fetch(apiUrl);

                // Check if the response is OK
                if (!response.ok) {
                    if (response.status === 404) {
                        return {error: 'Discussion not found by url : ' + url, url: null};
                    }
                    return {error: `HTTP error! status: ${response.status}`, url: null};
                }

                // Parse and get the response body
                let responseBody = await async function () {
                    return await response.json()
                }();

                // Convert the markdown to HTML
                let markdown = responseBody.body;
                responseBody.body = await async function () {
                    return await fetch('https://api.github.com/markdown', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({text: markdown}),
                    }).then(response => response.text());
                }();
                return responseBody
            }
        });
    </script>
@endpushonce