/**
 * This file contains all the typedefs used in the editor.
 */

/**
 * @typedef {object} Config
 * @property {string} contentId
 * @property {string} originalValue
 * @property {HTMLElement} component
 * @property {object} decorations
 * @property {function[]} validators
 * @property {array} renderSettings
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
 * @typedef {object} ContentValue
 * @property {number} time
 * @property {Array} blocks
 * @property {string} version
 */