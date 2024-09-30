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
     *   "decorations": {                     |
     *     "label": {                         |
     *      ^^^^^                             | The name of the decoration method
     *        "label": "Choose your template" |
     *         ^^^^^                          | The name of the parameter
     *                  ^^^^^^^^^^^^^^^^^^^^  | The value given to the parameter
     *     }
     *   },
     *   "key": "/model/view/features/select_file_basic/value-",
     *   "source": {"directory": "view/features", "file": "select_file_basic.blade.php", "from": 5, "line": 2, "to": 28},
     * }
     */
    constructor(id, value, component) {
        this.id = id;
        this.value = value;
    }

    toHtml() {
        return `${this.value}`;
    }
}
