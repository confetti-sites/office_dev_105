// noinspection GrazieInspection

export default class {
    id;
    value;

    /**
     * @param {string} id
     * @param {any} value
     * @param decorations {object} Example:
     * {
     *     "label": {                          |
     *      ^^^^^                              | The name of the decoration method
     *         "label": "Choose your template" |
     *          ^^^^^                          | The name of the parameter
     *                   ^^^^^^^^^^^^^^^^^^^^  | The value given to the parameter
     *     },                                  |
     * }
     */
    constructor(id, value, decorations) {
        this.id = id;
        this.value = value;
    }

    toHtml() {
        return `<div class="h-5 w-5 rounded-full" id="${this.id}" style="background-color:${this.value}"></div>`;
    }
}
