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
        // Get all after the last slash
        let value = this.value.split('/').pop();

        // And all before the first dot
        value = value.split('.')[0];

        // Replace all underscores and dashes with spaces
        value = value.replace(/_/g, ' ').replace(/-/g, ' ');

        // And make a title of this
        value = value.charAt(0).toUpperCase() + value.slice(1);

        return `${value}`;
    }
}
