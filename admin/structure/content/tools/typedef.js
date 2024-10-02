/**
 * This file contains all the typedefs used in the editor.
 */

/**
 * @typedef {object} Editor
 * @property {boolean} isReady
 * @property {RootConfiguration} configuration
 * @property {HTMLElement} element
 * @property {string} holder
 * @property {any} placeholder
 * @property {Data} data
 * @property {string} defaultBlock
 * @property {boolean} inlineToolbar
 * @property {object} tools
 * @property {string} logLevel
 * @property {number} minHeight
 * @property {object} sanitizer
 * @property {boolean} hideToolbar
 * @property {object} i18n
 * @property {boolean} readOnly
 *
 * @mixin Api
 *
 * @property {object} blocks
 * @property {function} blocks.getBlockByIndex
 * @property {function} blocks.delete
 * @property {function} blocks.update
 * @property {function} blocks.render
 * @property {object} caret
 * @property {function} caret.setToBlock
 * @property {object} events
 * @property {object} listeners
 * @property {object} notifier
 * @property {object} sanitizer
 * @property {object} saver
 * @property {function} saver.save
 * @property {object} selection
 * @property {object} styles
 * @property {object} toolbar
 * @property {object} inlineToolbar
 * @property {object} tooltip
 * @property {object} i18n
 * @property {object} readOnly
 * @property {object} ui
 */

/**
 * @typedef {object} RootConfiguration
 * @property {HTMLElement} element
 * @property {string} holder
 * @property {any} placeholder
 * @property {Data} originalData
 * @property {Data} data
 * E.g. {"label":{"label":"Title"},"default":{"default":"Confetti CMS"},"min":{"min":1},"max":{"max":20}};
 * @property {object} decorations
 * @property {object} defaultData
 * @property {string} defaultBlock
 * @property {boolean} inlineToolbar
 * @property {object} tools
 * @property {string} logLevel
 * @property {number} minHeight
 * @property {object} sanitizer
 * @property {boolean} hideToolbar
 * @property {object} i18n
 * @property {boolean} readOnly
 */

/**
 * @typedef {object} Config
 * @property {string} contentId
 * @property {string} originalValue
 * @property {HTMLElement} component
 * @property {object} decorations
 * @property {function[]} validators
 * @property {array} renderSettings
 * @property {string} componentEntity
 */

/**
 * @typedef {object} ToolbarItem
 * @property {string} label
 * @property {string} icon
 * @property {boolean} closeOnActivate
 * @property {function} onActivate
 */

/**
 * @typedef {object} Api
 * @property {object} blocks
 * @property {function} blocks.getBlockByIndex
 * @property {function} blocks.delete
 * @property {function} blocks.update
 * @property {function} blocks.render
 * @property {object} caret
 * @property {function} caret.setToBlock
 * @property {object} events
 * @property {object} listeners
 * @property {object} notifier
 * @property {object} sanitizer
 * @property {object} saver
 * @property {function} saver.save
 * @property {object} selection
 * @property {object} styles
 * @property {object} toolbar
 * @property {object} inlineToolbar
 * @property {object} tooltip
 * @property {object} i18n
 * @property {object} readOnly
 * @property {object} ui
 */

/**
 * @typedef {object} Data
 * @property {number} time
 * @property {Array} blocks
 * @property {string} version
 */