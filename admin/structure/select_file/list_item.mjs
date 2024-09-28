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
