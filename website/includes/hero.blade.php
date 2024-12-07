@php($hero = newRoot(new \model\homepage\hero)->label('Hero'))

<example-text></example-text>

@pushonce('end_of_body_hero')
    <script type="module" defer>
        import {html, reactive} from 'https://esm.sh/@arrow-js/core';

        customElements.define('example-text', class extends HTMLElement {
            standardSuffix = `<span><span class="text-black">&rcub;&rcub;</span><span class="text-blue-500">&lt;/h1&gt;</span></span>`;
            required = `->required()`;

            state = reactive({
                decorationContent: '',
                count: 0,
                required: false,
                requiredContent: '',
                default: false,
                defaultContent: '',
                help: false,
                helpContent: '',
                helpText: '',
                alias: '',
                label: '',
                error: '',
                value: '',
            });

            constructor() {
                super();

                this.state.$on('label', () => {
                    this.state.alias = this.state.label.toLowerCase().replace(/ /g, '_');
                });

                this.state.$on('requiredContent', () => {
                    this.#updateDecorationContent();
                });

                this.state.$on('defaultContent', () => {
                    this.#updateDecorationContent();
                });

                this.state.$on('helpContent', () => {
                    this.#updateDecorationContent();
                });

                this.#typeLabel()
            }

            connectedCallback() {
                html`
                    <div class="block px-2 font-body min-h-48">
                        <div>
                            <pre><code><div class="${() => this.state.count > 0 ? 'flex flex-col' : 'flex'}">${() => html`
                                <span><span class="text-blue-500">&lt;h1&gt;</span><span class="text-black">&lcub;&lcub; $hero->text(</span><span class="text-green-700">'${this.state.alias}'</span><span class="text-black">) </span></span>${this.state.decorationContent + this.standardSuffix}`}</div></code></pre>
                        </div>
                        <div class="block text-bold text-xl mt-2 mb-4 h-4">
                            ${() => this.state.label}
                        </div>
                        <div class="px-5 py-3 text-gray-700 border-2 border-gray-200 rounded-lg bg-gray-50">
                            ${() => this.state.value}&nbsp;
                        </div>
                        ${() => this.state.help ? html`<p class="mt-2 text-sm text-gray-600">${() => this.state.helpText}</p>` : ''}
                        <p class="mt-2 text-sm text-red-600 _error">${() => this.state.error}</p>
                    </div>
                    <div>
                        <button @click="${() => this.#toggleRequired()}" class="${() => `px-3 py-2 m-2 ml-0 text-sm leading-5 cursor-pointer text-blue-500 border border-blue-500 rounded-md ${this.state.required ? 'bg-blue-500 text-white' : ''}`}">
                            ->required()
                        </button>
                        <button @click="${() => this.#toggleDefault()}" class="${() => `px-3 py-2 m-2 ml-0 text-sm leading-5 cursor-pointer text-blue-500 border border-blue-500 rounded-md ${this.state.default ? 'bg-blue-500 text-white' : ''}`}">
                            ->default('Confetti CMS')
                        </button>
                        <button @click="${() => this.#toggleHelp()}" class="${() => `px-3 py-2 m-2 ml-0 text-sm leading-5 cursor-pointer text-blue-500 border border-blue-500 rounded-md ${this.state.help ? 'bg-blue-500 text-white' : ''}`}">
                            ->help('The title of the page')
                        </button>
                    </div>
                `(this);
            }

            #typeLabel() {
                // Slowly build the label as if we type it "Title Main"
                const label = 'Title Main';
                let i = 0;
                const interval = setInterval(() => {
                    this.state.label = label.substring(0, i);
                    i++;
                    if (i > label.length) {
                        clearInterval(interval);
                    }
                }, 100);

                // Slowly only remove the " Main" part of the label
                setTimeout(() => {
                    let i = label.length;
                    const interval = setInterval(() => {
                        this.state.label = label.substring(0, i);
                        i--;
                        if (i < 5) {
                            clearInterval(interval);
                        }
                    }, 100);
                }, 2000);
            }

            #toggleRequired() {
                const isRequired = !this.state.required
                if (isRequired) {
                    const prefix = `<span class="text-black-500 pl-4">`;
                    const suffix = `</span>`;
                    const toType = `->required()`;
                    this.state.requiredContent = '';
                    let i = 0;
                    const interval = setInterval(() => {
                        this.state.requiredContent = prefix + toType.substring(0, i) + suffix;
                        i++;
                        if (i > this.required.length) {
                            clearInterval(interval);
                        }
                    }, 100);
                } else {
                    this.state.requiredContent = '';
                    this.state.error = '';
                }
                this.state.required = isRequired
                this.state.count = this.#countDeclarations()
                setTimeout(() => {
                    this.#updateError();
                }, 1500);
            }

            #toggleDefault() {
                this.state.default = !this.state.default
                if (this.state.default) {
                    // type <span class="text-black-500 pl-4">->default('</span><span class="text-green-700">Confetti CMS</span><span class="text-black-500">')</span>
                    const prefix = `<span class="text-black-500">`;
                    const suffix = `</span>`;
                    const methodPrefix = `->default('`; // black
                    const methodSuffix = `')`; // black
                    const value = 'Confetti CMS'; // green
                    this.state.defaultContent = '';
                    let i = 0;
                    const interval = setInterval(() => {
                        let iMethod = i > methodPrefix.length ? methodPrefix.length : i;
                        let iValue = i - methodPrefix.length;
                        if (iValue <= 0) {
                            iValue = 0;
                        }
                        this.state.value = value.substring(0, iValue);
                        let iSuffix = i - methodPrefix.length - value.length;
                        if (iSuffix <= 0) {
                            iSuffix = 0;
                        }

                        this.state.defaultContent = `<span class="pl-4">` + prefix + methodPrefix.substring(0, iMethod) + suffix + `<span class="text-green-700">${value.substring(0, iValue)}</span>` + prefix + methodSuffix.substring(0, iSuffix) + suffix + `</span>`;
                        i++;
                        if (i > (methodPrefix + value + methodSuffix).length) {
                            clearInterval(interval);
                        }
                        this.#updateError();
                    }, 100);
                } else {
                    this.state.defaultContent = '';
                    this.#updateError();
                }
                this.state.count = this.#countDeclarations()
            }

            #toggleHelp() {
                this.state.help = !this.state.help
                if (this.state.help) {
// type <span class="text-black-500 pl-4">->help('</span><span class="text-green-700">The title of the page</span><span class="text-black-500">')</span>
                    const prefix = `<span class="text-black-500">`;
                    const suffix = `</span>`;
                    const methodPrefix = `->help('`; // black
                    const methodSuffix = `')`; // black
                    const value = 'The title of the page'; // green
                    this.state.helpContent = '';
                    let i = 0;
                    const interval = setInterval(() => {
                        let iMethod = i > methodPrefix.length ? methodPrefix.length : i;
                        let iValue = i - methodPrefix.length;
                        this.state.helpText = value.substring(0, iValue);
                        if (iValue <= 0) {
                            iValue = 0;
                        }
                        this.state.helpContent = `<span class="pl-4">` + prefix + methodPrefix.substring(0, iMethod) + suffix + `<span class="text-green-700">${value.substring(0, iValue)}</span>` + prefix + methodSuffix.substring(0, i - methodPrefix.length - value.length) + suffix + `</span>`;
                        i++;
                        if (i > (methodPrefix + value + methodSuffix).length) {
                            clearInterval(interval);
                        }

                    }, 100);
                } else {
                    this.state.helpContent = '';
                }
                this.state.count = this.#countDeclarations()
            }

            #updateDecorationContent() {
                this.state.decorationContent = this.state.requiredContent + this.state.defaultContent + this.state.helpContent;
            }

            #countDeclarations() {
                let count = 0;
                if (this.state.required) {
                    count++;
                }
                if (this.state.default) {
                    count++;
                }
                if (this.state.help) {
                    count++;
                }
                return count;
            }

            #updateError() {
                if (this.state.required && this.state.value === '') {
                    this.state.error = 'This field is required';
                } else {
                    this.state.error = '';
                }
            }
        });
    </script>
@endpushonce


















