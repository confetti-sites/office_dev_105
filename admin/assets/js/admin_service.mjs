export class Storage {
    static saveLocalStorageModel(id, data, component = null) {
        const value = JSON.stringify(data);

        if (component) {
            // We need to save the component: When we load the list, we need to know what the component details are.
            localStorage.setItem('/component' + id, component);
        }

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
            let raw = localStorage.getItem(id);
            if (raw === 'undefined') {
                console.warn('Local storage item id ' +id+ ' has string: "undefined". Skipping.');
                return null;
            }
            return JSON.parse(raw);
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

    static hasLocalStorageItems(prefix) {
        return this.getLocalStorageItems(prefix).length > 0;
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

    static removeLocalStorageModels(model) {
        let prefixes = [model, '/component' + model, '/listener' + model];

        for (let prefix of prefixes) {
            // Remove from local storage
            // Get all items from local storage (exact match and prefix + `/`)
            let items = Object.keys(localStorage).filter(key => key === prefix || key.startsWith(prefix + '/'));
            // Remove items from local storage
            items.forEach(item => {
                localStorage.removeItem(item);
            });
        }
    }

    /**
     * @param {string} serviceApiUrl
     * @param {string} id
     * @param {boolean} specific
     * @returns {Promise<boolean>}
     */
    static saveFromLocalStorage(serviceApiUrl, id, specific = false) {
        return EventService.handleEvent('saving', id).then(r => {
            // Loop over every item in r, if it contains instanceof Error, filter it out
            if (r.some(item => item instanceof Error)) {
                return false;
            }
            const prefixQ = id + '/'
            // Get all items from local storage (exact match and prefix + '/')
            let items = Object.keys(localStorage)
                // We want to update the children, and we need to update the parents as well
                .filter(key => specific ? key === id : (prefixQ.startsWith(key) || key.startsWith(prefixQ)))
                .filter(key => localStorage.getItem(key) !== 'undefined')
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
            window.dispatchEvent(new CustomEvent('state', {
                detail: {
                    id: id + '.save_from_local_storage',
                    state: 'loading',
                    title: 'Saving content',
                }
            }));

            // Save all items to the server
            return this.save(serviceApiUrl, items).then(r => {
                // if not successful, console.error
                if (r instanceof Error) {
                    console.error('Error saving to server');
                    window.dispatchEvent(new CustomEvent('state', {
                        detail: {
                            id: id + '.save_from_local_storage',
                            state: 'error',
                            title: r.message,
                        }
                    }));
                    return false;
                }

                // Remove saved items from local storage
                items.forEach(item => {
                    localStorage.removeItem(item.id);
                    localStorage.removeItem('/component' + item.id);
                    localStorage.removeItem('/listener' + item.id);
                });
                window.dispatchEvent(new Event('local_content_changed'));
                window.dispatchEvent(new CustomEvent('state', {
                    detail: {
                        id: id + '.save_from_local_storage',
                        state: 'success',
                        title: 'Saved'
                    }
                }));
                return true;
            });
        });
    }

    /**
     * @param {string} serviceApiUrl
     * @param {array<{id: string, value: string}>} data
     * @returns {Promise<any>}
     */
    static save(serviceApiUrl, data) {
        // Remove all items from the database where the value is 'this.remove()'
        const toRemove = data.filter(item => item && item.value === 'this.remove()');
        // If the cookie is not set, we can't save to the server
        if (document.cookie.indexOf('access_token=') === -1) {
            window.location.reload();
        }
        // Loop over every item and remove. Example: DELETE /contents?id=/model/title
        toRemove.forEach(item => {
            fetch(`${serviceApiUrl}/confetti-cms/content/contents?id_prefix=${item.id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + document.cookie.split('access_token=')[1].split(';')[0],
                },
            }).then(response => {
                if (response.status >= 300) {
                    return new Error('Cannot remove content. Error status: ' + response.status);
                }
            })
                .catch(error => {
                    console.error('Error:', error);
                });
        });

        const toPublish = data.filter(item => item && item.value !== 'this.remove()');

        return fetch(`${serviceApiUrl}/confetti-cms/content/contents`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + document.cookie.split('access_token=')[1].split(';')[0],
            },
            body: JSON.stringify({"data": toPublish})
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
        return fetch(`${serviceApiUrl}/confetti-cms/content/contents?id_prefix=${id}`, {
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
                this.removeLocalStorageModels(id);
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

export class EventService {
    static handleEvent(event, key) {
        let promises = [];

        // loop over local storage with prefix `/listener/`
        Storage.getLocalStorageItems(`/listener`).forEach(listener => {
            const data = JSON.parse(listener.value);
            if (data.when.event === event && (data.when.id === key || data.when.id.startsWith(key + '/'))) {
                let response = this.call(data.when.id, data.title, data.then.method, data.then.url, data.then.body);
                // Get response body and save to local storage
                promises.push(response.then(r => {
                    if (r instanceof Error) {
                        return r;
                    }
                    return r.json();
                }).then(body => {
                    if (body instanceof Error) {
                        return body;
                    }
                    if (data.patch_in !== undefined) {
                        let value = Storage.getFromLocalStorage(data.when.id);
                        if (value === null) {
                            console.error(`Cannot add patch_in to local storage. No value found. ${data.when.id} not found in local storage.`);
                        }
                        value[data.patch_in] = body
                        Storage.saveLocalStorageModel(data.when.id, value);
                    }
                    if (data.remove_when_done) {
                        localStorage.removeItem(listener.id);
                    }
                }));
            }
        });
        return Promise.all(promises);
    }

    /**
     * @param {string} id
     * @param {string} title
     * @param {string} method
     * @param {string} url
     * @param {object} body
     */
    static async call(id, title, method, url, body) {
        window.dispatchEvent(new CustomEvent('state', {
            detail: {
                id: id + '.call',
                state: 'loading',
                title: title,
            }
        }));

        const r = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + document.cookie.split('access_token=')[1].split(';')[0],
            },
            body: JSON.stringify(body)
        })
        if (r.status >= 400) {
            // get body
            r.json().then(json => {
                window.dispatchEvent(new CustomEvent('state', {
                    detail: {
                        id: id + '.call',
                        state: 'error',
                        title: json.error || json.message,
                    }
                }));
            });
            return new Error('Error status: ' + r.status);
        }
        window.dispatchEvent(new CustomEvent('state', {
            detail: {
                id: id + '.call',
                state: 'success',
                title: title,
            }
        }));
        return r;
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
        }).then(async response => {
            if (response.status >= 300) {
                console.error("Error status: " + response.status);
                let message = "Error uploading image: ";
                await response.json().then(json => {
                    message = `${json.message} ${response.statusText}`;
                });

                window.dispatchEvent(new CustomEvent('state', {
                    detail: {
                        id: id + '.upload',
                        state: 'error',
                        title: message,
                    }
                }));
                return;
            }

            if (response.headers.get('Content-Length') === '0') {
                console.error("Content-Length is 0");
                return;
            }

            if (!response.headers.get('Content-Type').includes('application/json')) {
                // response cut by 400 characters
                response = response.clone();
                response.text().then(text => {
                    console.error("Response header is not application/json. Response: " + text.slice(0, 400));
                });
                return
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