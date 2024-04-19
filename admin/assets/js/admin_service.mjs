export class storage {
    /**
     * @param {string} serviceApiUrl
     * @param {string} prefix
     * @returns {boolean}
     */
    static saveFromLocalStorage(serviceApiUrl, prefix) {
        // Get all items from local storage (exact match and prefix + '/')
        let items = Object.keys(localStorage)
            .filter(key => key === prefix || key.startsWith(prefix + '/'))
            .map(key => {
                return {
                    "id": key,
                    "value": JSON.parse(localStorage.getItem(key))
                };
            });

        // Save all items to the server
        this.save(serviceApiUrl, items).then(r => {
            // if not successful, console.error
            if (!r) {
                return false;
            }

            // Remove saved items from local storage
            items.forEach(item => {
                localStorage.removeItem(item.id);
            });

            this.redirectAway(prefix);
            return true;
        });
    }

    /**
     * @param {string} serviceApiUrl
     * @param {array<{id: string, value: string}>} data
     * @returns {Promise<any>}
     */
    static save(serviceApiUrl, data) {
        return fetch(`${serviceApiUrl}/confetti-cms/content/contents`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + document.cookie.split('access_token=')[1].split(';')[0],
            },
            body: JSON.stringify({"data": data})
        })
            .then(response => {
                if (response.status >= 300) {
                    console.log("Error: " + response.responseText);
                    return;
                }
                return response.json();
            })
            .catch(error => {
                console.error('Error:', error);
            });

    }

    /**
     * @param {string} serviceApiUrl
     * @param {string} id
     * @param {function} then
     * @returns {Promise<any>}
     */
    static delete(serviceApiUrl, id, then = null) {
        // Remove from database
        return fetch(`${serviceApiUrl}/confetti-cms/content/contents?id=${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + document.cookie.split('access_token=')[1].split(';')[0],
            },
        }).then(response => {
            if (response.status >= 300) {
                console.error("Error status: " + response.status);
            } else {
                this.removeLocalStorageItems(id);
                window.dispatchEvent(new Event('local_content_changed'));
                if (then) {
                    then();
                }
            }
        });
    }

    /**
     * @param {string} prefix
     * @returns {{id: string, value: string}[]}
     */
    static getLocalStorageItems(prefix) {
        return Object.keys(localStorage)
            .filter(key => {
                // We want to include /model/overview/blog~1Z4BJ9J5D9
                // when key is /model/overview/blog~
                if (prefix.endsWith('~') || prefix.endsWith('/-')) {
                    return key.startsWith(prefix);
                }
                return key === prefix || key.startsWith(prefix + '/');
            })
            .map(key => {
                return {
                    "id": key,
                    "value": localStorage.getItem(key)
                };
            });
    }

    static hasLocalStorageItems(prefix) {
        return this.getLocalStorageItems(prefix).length > 0;
    }

    static removeLocalStorageItems(prefix) {
        // Remove from local storage
        // Get all items from local storage (exact match and prefix + `/`)
        let items = Object.keys(localStorage).filter(key => key === prefix || key.startsWith(prefix + '/'));
        // Remove items from local storage
        items.forEach(item => {
            localStorage.removeItem(item);
        });
    }

    /**
     * Get all new created items from local storage
     * Search for items that end with full ulid `/model/banner~1ZK6J9J5D9`
     * but the search query only has `/model/banner~`
     * Note that we don't want to include `/model/banner~1ZK6J9J5D9/title`
     */
    static getMapItems(key) {
        // trim the right side of the localstorage id with 10 characters and compare
        return Object.keys(localStorage)
            .filter(id => id.slice(0, -10) === key)
            .map(id => ({
                "id": id,
                "data": {
                    ".": JSON.parse(localStorage.getItem(id)),
                }
            }));
    }

    /**
     * @param {string} prefix
     */
    static redirectAway(prefix) {
        // Get parent content id to redirect to
        // \w|~ remove word characters (with ulid)
        // /-/ remove target ids
        if (prefix.includes('~')) {
            const parentContentId = prefix.replace(/\/(\w|~|\/-\/)+$/, '');
            window.location.href = `/admin${parentContentId}`;
        } else {
            window.location.reload();
        }
    }

    /**
     * @returns {string}
     */
    static newId() {
        const char = '123456789ABCDEFGHJKMNPQRSTVWXYZ';
        const encodingLength = char.length;
        const desiredLengthTotal = 10;
        const desiredLengthTime = 6;

        // Encode time
        // We use the time since a fixed point in the past.
        // This gives us a more space to use in the feature.
        let time = Math.floor(Date.now() / 1000) - 1684441872;
        let out = '';
        while (out.length < desiredLengthTime) {
            const mod = time % encodingLength;
            out = char[mod] + out;
            time = (time - mod) / encodingLength;
        }

        // Encode random
        while (out.length < desiredLengthTotal) {
            const rand = Math.floor(Math.random() * encodingLength);
            out += char[rand];
        }

        return out;
    }
}

export const IconLoader = (width) => {
    return `<svg xmlns="http://www.w3.org/2000/svg" width="${width}" height="${width}" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M12 6.99998C9.1747 6.99987 6.99997 9.24998 7 12C7.00003 14.55 9.02119 17 12 17C14.7712 17 17 14.75 17 12"><animateTransform attributeName="transform" attributeType="XML" dur="560ms" from="0,12,12" repeatCount="indefinite" to="360,12,12" type="rotate"/></path></svg>`;
}