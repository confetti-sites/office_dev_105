export default class {
    id;
    value;

    /**
     * @param {string} id
     * @param value
     */
    constructor(id, value) {
        this.id = id;
        this.value = value;
    }

    toHtml() {
        return `<div class="h-5 w-5 rounded-full" id="${this.id}" style="background-color:${this.value}"></div>`;
    }
}
