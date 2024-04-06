// noinspection JSUnusedGlobalSymbols
export default class Underline {
    static get isInline() {
        return true;
    }

    static get title() {
        return "Underline";
    }

    static get sanitize() {
        return {u: {}};
    }

    constructor({api}) {
        this.api = api;
        this.commandName = "underline";
        this.button = null;
    }

    render() {
        this.button = document.createElement('button');
        this.button.type = 'button';
        this.button.textContent = 'U';
        this.button.classList.add(this.api.styles.inlineToolButton);
        this.button.style.textDecoration = "underline";
        return this.button;
    }

    surround(range) {
        // Unfortunately, there is no alternative
        // noinspection JSDeprecatedSymbols
        document.execCommand(this.commandName);
    }

    checkState(selection) {
        const isActive = document.queryCommandState(this.commandName);
        this.button.classList.toggle(this.api.styles.inlineToolButtonActive, isActive);
        return isActive;
    }
}