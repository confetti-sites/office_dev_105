// noinspection GrazieInspection

export default class {
    id;
    value;

    /**
     * @param {string} id
     * @param {any} value
     * For example:
     * {
     *     "original": "/model/page/feature~2H5VGB5169/type-/value/olifant_1300.original.jpeg",
     *     "crop": {"x": 0, "y": 0, "width": 1300, "height": 731},
     *     "sources": [
     *         {
     *             "name": "/model/page/feature~2H5VGB5169/type-/value/olifant_1300.mobile.jpeg",
     *             "size": {"height": 359, "width": 640},
     *             "media": "mobile"
     *         }
     *     ]
     * }
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
        this.component = component;
    }

    toHtml() {
        if (this.value.crop === undefined) {
            return '';
        }

        let prefix = '/conf_api/confetti-cms/media/images/';
        let cl = '';
        let height = this.value.crop.height;
        let width = this.value.crop.width;
        let pictureSource1x = null;
        let pictureSource2x = null;

        // if (this.value.sources. with  media": "mobile
        if (this.value.sources !== undefined) {
            this.value.sources.forEach((source) => {
                if (source.media === 'miniature') {
                    height = source.size.height;
                    width = source.size.width;
                    pictureSource1x = source;
                }
                if (source.media === 'miniature2x') {
                    pictureSource2x = source;
                }
            });
        }

        if ((this.value.crop.width / this.value.crop.height) < 1.05) {
            // If the image is more square than landscape, we want to make it square
            cl = `h-16 w-16 rounded-md shadow-sm border border-gray-200 object-cover`;
        } else {
            // If the image is more landscape than square, we want to make the ratio 2:3
            cl = `h-16 w-24 rounded-md shadow-sm border border-gray-200 object-cover`;
        }

        // if this.value.crop.width < 100 blur very little bit
        if (this.value.crop.width < 70) {
            cl += ' filter blur-[0.5px]';
        }

        if (pictureSource1x) {
            pictureSource2x  = pictureSource2x ?? pictureSource1x;
            return `<picture>
                        <source class="" srcset="${prefix}${pictureSource1x.name} 1x, /conf_api/confetti-cms/media/images/${pictureSource2x.name} 2x">
                        <img class="${cl}" src="${prefix}${pictureSource2x.name}" alt="${this.component.decorations.label.label}">
                    </picture>`;
        }
        return `<img class="${cl}" src="${prefix}${this.value.original}" alt="${this.component.decorations.label.label}">`;
    }
}
