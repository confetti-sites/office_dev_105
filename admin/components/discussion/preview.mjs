// noinspection GrazieInspection

export default class {
    id;
    value;

    /**
     * @param {string} id
     * @param {any} value
     * @param component {object}
     * For example:
     * {
     *     "url": "https://github.com/confetti-cms/community/discussions/1",
     *     "discussion": {
     *         "repository_url": "https://api.github.com/repos/confetti-cms/community",
     *         "html_url": "https://github.com/confetti-cms/community/discussions/1",
     *         "id": 7493776,
     *         "node_id": "D_kwDONQNvK84AcliQ",
     *         "number": 1,
     *         "title": "Image",
     *         "body": "<p>The content</p>\n",
     *         "reactions": {...},
     *     }
     * }
     */
    constructor(id, value, component) {
        this.id = id;
        this.value = value;
    }

    toHtml() {
        if (this.value.error) {
            return `<span id="${this.id}">${this.value.error}</span>`;
        }
        if (!this.value.discussion && !this.value.discussion.title) {
            return `<span id="${this.id}">No title</span>`;
        }
        return `<span id="${this.id}">${this.value.discussion.title}</span>`;
    }
}
