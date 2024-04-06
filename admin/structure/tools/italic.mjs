// noinspection JSUnusedGlobalSymbols
export default class Italic {
    static get isInline() {
        return true;
    }

    static get title() {
        return "Italic";
    }

    static get sanitize() {
        return {i: {}};
    }

    constructor({api}) {
        this.api = api;
        this.commandName = "italic";
        this.button = null;
    }

    render() {
        this.button = document.createElement('button');
        this.button.type = 'button';
        this.button.textContent = 'I';
        this.button.classList.add(this.api.styles.inlineToolButton);
        this.button.style.fontStyle = "italic";
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