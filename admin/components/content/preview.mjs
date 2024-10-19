// noinspection GrazieInspection

export default class {
    id;
    value;

    /**
     * @param {string} id
     *
     *  For example:
     * {
     *   blocks: [{
     *     data: {
     *        text: "The&nbsp;value"
     *     },
     *     id: "jfJmb5kz8l",
     *     type: "paragraph",
     *     length: 1,
     *   }],
     * }
     * @param {any} value
     * @param component {object}
     */
    constructor(id, value, component) {
        this.id = id;
        this.value = '';

        // Loop through all the blocks until a valid text is found
        for (let i = 0; i < value.blocks.length; i++) {
            if (value.blocks[i].data.text !== undefined) {
                this.value = value.blocks[i].data.text;
                break; // Stop the loop once a valid value is found
            }
        }

        // If the value is to long it will be truncated
        // so the data in the html is not to long
        if (this.value.length > 200) {
            this.value = this.value.substring(0, 200) + "...";
        }
    }

    toHtml() {
        return `<span class="line-clamp-2">${this.value}</span>`;
    }
}
