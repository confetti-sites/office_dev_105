export class Storage {
    static saveToLocalStorage(id, data) {
        const value = JSON.stringify(data);
        // Don't save if the value is the same
        if (localStorage.hasOwnProperty(id) && localStorage.getItem(id) === value) {
            return;
        }
        localStorage.setItem(id, value);
    }

    /**
     * Get one item from local storage
     * @param {string} id
     * @returns {string|null}
     */
    static getFromLocalStorage(id) {
        if (localStorage.hasOwnProperty(id)) {
            return JSON.parse(localStorage.getItem(id));
        }
        return null;
    }

    /**
     * Check if an item exists in local storage
     * @param {string} id
     * @returns {boolean}
     */
    static hasLocalStorageItem(id) {
        return localStorage.hasOwnProperty(id);
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
     * @param {string} serviceApiUrl
     * @param {string} id
     * @param {boolean} specific
     * @returns {Promise<boolean>}
     */
    static saveFromLocalStorage(serviceApiUrl, id, specific = false) {
        const prefixQ = id + '/'
        // Get all items from local storage (exact match and prefix + '/')
        let items = Object.keys(localStorage)
            // We want to update the children, and we need to update the parents as well
            .filter(key => specific ? key === id : (prefixQ.startsWith(key) || key.startsWith(prefixQ)))
            .map(key => {
                // We want to decode, so we can save numbers and booleans
                let value = JSON.parse(localStorage.getItem(key));
                // We can't save objects to the server, so we need to convert them to strings
                if (typeof value === 'object') {
                    value = JSON.stringify(value);
                }
                return {
                    "id": key,
                    "value": value
                };
            });

        if (items.length === 0) {
            return Promise.resolve(true);
        }
        document.dispatchEvent(new CustomEvent('status-created', {
            detail: {
                id: id + '-save-from-local-storage',
                state: 'loading',
                title: 'Saving content',
            }
        }));

        // Save all items to the server
        return this.save(serviceApiUrl, items).then(r => {
            // if not successful, console.error
            if (r instanceof Error) {
                console.error('Error saving to server');
                document.dispatchEvent(new CustomEvent('status-created', {
                    detail: {
                        id: id + '-save-from-local-storage',
                        state: 'error',
                        title: r.message,
                    }
                }));
                return false;
            }

            // Remove saved items from local storage
            items.forEach(item => {
                localStorage.removeItem(item.id);
            });
            window.dispatchEvent(new Event('local_content_changed'));
            document.dispatchEvent(new CustomEvent('status-created', {
                detail: {
                    id: id + '-save-from-local-storage',
                    state: 'success',
                    title: 'Saved'
                }
            }));

            return true;
        });
    }

    // static redirectAway(items) {
    //     let needsRedirectBack = true;
    //     items.forEach(item => {
    //         // item ends with - we don't want to redirect back,
    //         // the user may want to continue editing the children
    //         if (item.id.endsWith('-')) {
    //             needsRedirectBack = false;
    //         }
    //     });
    //     if (needsRedirectBack) {
    //         this.redirectAway(id);
    //     } else {
    //         window.location.reload();
    //     }
    // }

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
                if (response.status >= 500) {
                    return new Error('Cannot save content. Error status: ' + response.status);
                }
                if (response.status >= 401) {
                    return new Error('Cannot save content. You may need to login again to save this changes.');
                }
                if (response.status >= 400) {
                    return new Error('Cannot save content. You may change the content and try again.');
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
     * @param {string} parentId
     */
    static redirectAway(parentId) {
        window.location.href = `/admin${parentId}`;
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

export class Media {
    static upload(serviceApiUrl, id, file, then) {
        const formData = new FormData();
        formData.append(id, file);

        fetch(`${serviceApiUrl}/confetti-cms/media/images`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + document.cookie.split('access_token=')[1].split(';')[0],
            },
            body: formData
        }).then(response => {
            if (response.status >= 300) {
                console.error("Error status: " + response.status);
                return null;
            }
            if (!response.headers.get('Content-Type').includes('application/json')) {
                // response cut by 400 characters
                response = response.clone();
                response.text().then(text => {
                    console.error("No json returned: " + text.slice(0, 400));
                });
                return null;
            }

            response.json().then(json => {
                then(json);
            });
        });
    }
}

export const IconUpload = (classes) => {
    return `<svg class="${classes}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/></svg>`;
}

export const IconUndo = (classes) => {
    return `<svg class="${classes}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"><path d="M9.33333 13.6667L6 10.3333L9.33333 7M6 10.3333H15.1667C16.0507 10.3333 16.8986 10.6845 17.5237 11.3096C18.1488 11.9348 18.5 12.7826 18.5 13.6667C18.5 14.5507 18.1488 15.3986 17.5237 16.0237C16.8986 16.6488 16.0507 17 15.1667 17H14.3333" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>`;
}

export const IconTrash = (classes) => {
    return `<svg class="${classes}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"><path d="M18.1328 7.7234C18.423 7.7634 18.7115 7.80571 19 7.85109M18.1328 7.7234L17.2267 17.4023C17.1897 17.8371 16.973 18.2432 16.62 18.5394C16.267 18.8356 15.8037 19.0001 15.3227 19H8.67733C8.19632 19.0001 7.73299 18.8356 7.37998 18.5394C7.02698 18.2432 6.81032 17.8371 6.77333 17.4023L5.86715 7.7234M18.1328 7.7234C17.1536 7.58919 16.1693 7.48733 15.1818 7.41803M5.86715 7.7234C5.57697 7.76263 5.28848 7.80494 5 7.85032M5.86715 7.7234C6.84642 7.58919 7.83074 7.48733 8.81818 7.41803M15.1818 7.41803C13.0638 7.26963 10.9362 7.26963 8.81818 7.41803M15.1818 7.41803C15.1818 5.30368 13.7266 4.34834 12 4.34834C10.2734 4.34834 8.81818 5.43945 8.81818 7.41803" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.5 15.5L10 11" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 11L13.5 15.5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>`;
}