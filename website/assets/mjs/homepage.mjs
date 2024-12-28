import {html, reactive} from 'https://esm.sh/@arrow-js/core';

export class TextDemo extends HTMLElement {
    standardSuffix = `<span><span class="text-black">&rcub;&rcub;</span><span class="text-blue-500">&lt;/h1&gt;</span></span>`;
    required = `->required()`;

    state = reactive({
        decorationContent: '',
        count: 0,
        required: false,
        requiredContent: '',
        default: false,
        defaultContent: '',
        bar: false,
        barContent: '',
        barTools: '',
        bold: false,
        italic: false,
        underline: false,
        alias: '',
        label: '',
        error: '',
        value: '',
    });

    constructor() {
        super();

        // clean the component
        this.innerHTML = '';

        this.state.$on('label', () => {
            this.state.alias = this.state.label.toLowerCase().replace(/ /g, '_');
        });

        this.state.$on('requiredContent', () => {
            this.#updateDecorationContent();
        });

        this.state.$on('defaultContent', () => {
            this.#updateDecorationContent();
            document.getElementById('brand-title').innerHTML = this.state.value;
        });

        this.state.$on('barContent', () => {
            this.#updateDecorationContent();
        });

        this.#typeLabel()
    }

    connectedCallback() {
        html`
            <div class="font-body overflow-x-hidden py-8 md:pt-12 pd:mb-4">
                <div class="${() => `flex justify-center ` + (this.state.count > 0 ? 'min-h-20' : '')}">
                    <pre><div class="text-sm md:text-base lg:text-lg xl:text-xl"><div class="${() => this.state.count > 0 ? 'flex flex-col' : 'flex'}">${() => html`
                        <span><span class="text-blue-500">&lt;h1&gt;</span><span class="text-black">&lcub;&lcub; $header->text(</span><span class="text-green-700">'${this.state.alias}'</span><span class="text-black">)&nbsp;</span></span>${this.state.decorationContent + this.standardSuffix}`}</div></div></pre>
                </div>
                <div class="flex mt-2 justify-center">
                    <button @click="${() => this.#toggleRequired()}" class="${() => `mx-2 my-2 p-2 text-sm leading-5 cursor-pointer border border-blue-500 rounded-md ${this.state.required ? 'bg-blue-500 text-white' : 'text-blue-500'}`}">
                        ->required()
                    </button>
                    <button @click="${() => this.#toggleDefault()}" class="${() => `mx-2 my-2 p-2 text-sm leading-5 cursor-pointer border border-blue-500 rounded-md ${this.state.default ? 'bg-blue-500 text-white' : 'text-blue-500'}`}">
                        ->default()
                    </button>
                    <button @click="${() => this.#toggleBar()}" class="${() => `mx-2 my-2 p-2 text-sm leading-5 cursor-pointer border border-blue-500 rounded-md ${this.state.bar ? 'bg-blue-500 text-white' : 'text-blue-500'}`}">
                        ->bar()
                    </button>
                </div>
            </div>
            <div class="mt-4 md:mt-1 mx-4 md:mx-auto md:w-2/3 min-h-32">
                <div class="text-bold text-xl mt-2 mb-4 mx-2 h-4">
                    ${() => this.state.label}
                </div>
                <div class="px-5 py-3 mx-2 text-gray-700 border-2 border-gray-400 rounded-lg bg-white font-body">
                    ${() => this.state.bar ? html`Confetti
                    <span class="${() => this.getStyle()}">CMS</span>` : this.state.value}&nbsp;</span>
                    ${() => this.state.barTools?.length >= 3 ? html`
                        <div class="absolute flex items-center space-x-1 p-1 border rounded-md w-fit bg-white">
<!--                               when active-->
<!--                             text-black hover:bg-blue-100when not active-->
                            <button class="${() => `font-bold py-1 px-2 rounded ` + (this.state.bold ? 'text-blue-600 bg-blue-100' : 'text-black hover:bg-blue-100')}" @click="${() => this.#toggleBold()}">B</button>
                            ${() => this.state.barTools?.length >= 8 ? html`
                                <button class="${() => `italic py-1 px-3 rounded ` + (this.state.italic ? 'text-blue-600 bg-blue-100' : 'text-black hover:bg-blue-100')}" @click="${() => this.#toggleItalic()}">I</button>` : ''}
                            ${() => this.state.barTools?.length >= 13 ? html`
                                <button class="${() => `underline py-1 px-2 rounded ` + (this.state.underline ? 'text-blue-600 bg-blue-100' : 'text-black hover:bg-blue-100')}" @click="${() => this.#toggleUnderline()}">U</button>` : ''}
                        </div>` : ''}
                </div>
                <p class="mx-2 mt-2 text-sm text-red-600 _error">${() => this.state.error}</p>
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
                if (i < ' Main'.length) {
                    clearInterval(interval);
                }
            }, 100);
        }, 2000);
    }

    #toggleRequired() {
        const isRequired = !this.state.required
        if (isRequired) {
            const prefix = `<span class="text-black-500 pl-4">-`;
            const suffix = `</span>`;
            const toType = `>required()`;
            this.state.requiredContent = prefix + suffix;
            let i = 0;
            const interval = setInterval(() => {
                this.state.requiredContent = prefix + toType.substring(0, i) + suffix;
                i++;
                if (i > this.required.length || !this.state.required) {
                    clearInterval(interval);
                }
                if (!this.state.required) {
                    this.state.requiredContent = '';
                }
            }, 150);
        } else {
            this.state.requiredContent = '';
            this.state.error = '';
        }
        this.state.required = isRequired
        this.state.count = this.#countDeclarations()
        setTimeout(() => {
            this.#updateError();
        }, 1800);
    }

    #toggleDefault() {
        this.state.default = !this.state.default
        if (this.state.default) {
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
                if (i > (methodPrefix + value + methodSuffix).length || !this.state.default) {
                    clearInterval(interval);
                }
                if (!this.state.default) {
                    this.state.defaultContent = '';
                }
                this.#updateError();
            }, 150);
        } else {
            this.state.defaultContent = '';
            this.state.value = '';
            this.#updateError();
        }
        this.state.count = this.#countDeclarations()
    }

    #toggleBar() {
        this.state.bar = !this.state.bar
        if (this.state.bar) {
            const prefix = `<span class="text-black-500">`;
            const suffix = `</span>`;
            const methodPrefix = `->bar('`; // black
            const methodSuffix = `')`; // black
            const methodValue = '[\'b\', \'i\', \'u\']'; // green
            this.state.barContent = '';
            let i = 0;
            const interval = setInterval(() => {
                let iMethod = i > methodPrefix.length ? methodPrefix.length : i;
                let iValue = i - methodPrefix.length;
                if (iValue <= 0) {
                    iValue = 0;
                }
                this.state.barTools = methodValue.substring(0, iValue);
                if (this.state.barTools?.length >= 3) {
                    this.state.bold = true;
                }
                this.state.barContent = `<span class="pl-4">` + prefix + methodPrefix.substring(0, iMethod) + suffix + `<span class="text-green-700">${methodValue.substring(0, iValue)}</span>` + prefix + methodSuffix.substring(0, i - methodPrefix.length - methodValue.length) + suffix + `</span>`;
                i++;
                if (i > (methodPrefix + methodValue + methodSuffix).length || !this.state.bar) {
                    clearInterval(interval);
                }
                if (!this.state.bar) {
                    this.state.barContent = '';
                }

            }, 200);
        } else {
            this.state.barContent = '';
            this.state.barTools = null;
        }
        this.state.count = this.#countDeclarations()
    }

    #toggleBold() {
        this.state.bold = !this.state.bold;
    }

    #toggleItalic() {
        this.state.italic = !this.state.italic;
    }

    #toggleUnderline() {
        this.state.underline = !this.state.underline;
    }

    #updateDecorationContent() {
        this.state.decorationContent = this.state.requiredContent + this.state.defaultContent + this.state.barContent;
    }

    #countDeclarations() {
        let count = 0;
        if (this.state.required) {
            count++;
        }
        if (this.state.default) {
            count++;
        }
        if (this.state.bar) {
            count++;
        }
        return count;
    }

    #updateError() {
        if (this.state.required && this.state.value.length === 0) {
            this.state.error = 'The title is required';
        } else {
            this.state.error = '';
        }
    }

    getStyle() {
        let result = ''

        if (this.state.bar) {
            result += ` bg-blue-200 py-1`
        }

        if (this.state.bold) {
            result += ` font-bold`
        }

        if (this.state.italic) {
            result += ` italic`
        }

        if (this.state.underline) {
            result += ` underline`
        }

        return result
    }
}
