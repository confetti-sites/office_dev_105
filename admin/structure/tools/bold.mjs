// noinspection JSUnusedGlobalSymbols
export default class Bold {
    static get isInline() {
        return true;
    }

    static get title() {
        return "Bold";
    }

    static get sanitize() {
        return {strong: {}};
    }

    constructor({api}) {
        this.api = api;
        this.commandName = "bold";
        this.button = null;
    }

    render() {
        this.button = document.createElement('button');
        this.button.type = 'button';
        this.button.textContent = 'B';
        this.button.classList.add(this.api.styles.inlineToolButton);
        this.button.style.fontWeight = "bold";
        return this.button;
    }

    surround(range) {
        document.execCommand(this.commandName);
    }

    checkState(selection) {
        const isActive = document.queryCommandState(this.commandName);
        this.button.classList.toggle(this.api.styles.inlineToolButtonActive, isActive);
        return isActive;
    }
}