import { Editor } from 'https://esm.sh/@tiptap/core'
import StarterKit from 'https://esm.sh/@tiptap/starter-kit'
import LinkExtention from 'https://esm.sh/@tiptap/extension-link'
import UnderlineExtention from 'https://esm.sh/@tiptap/extension-underline'
import extensionListItem from 'https://esm.sh/@tiptap/extension-list-item'
import BulletList from 'https://esm.sh/@tiptap/extension-bullet-list'
import OrderedList from 'https://esm.sh/@tiptap/extension-ordered-list'

const options = {

}

const setEditor = (options) => {
    return new Editor({

    })
}

document.addEventListener('alpine:init', () => {
    Alpine.data('editor', (content, contentId) => {
        let editor;

        return {
          updatedAt: Date.now(), // force Alpine to rerender on selection change
          init() {
            const _this = this;
            editor = new Editor({
              element: this.$refs.element,
              editorProps: {
                attributes: {
                  class: 'min-h-[201px] prose prose-sm sm:prose lg:prose-lg xl:prose-2xl mx-auto focus:outline-none',
                },
              },
              extensions: [
                StarterKit,
                LinkExtention.configure({
                    openOnClick: false,
                    HTMLAttributes: {
                      rel: 'noopener noreferrer',
                    },
                  }),
                UnderlineExtention,
                extensionListItem,
                BulletList.configure({
                  keepMarks: true,
                }),
                OrderedList.configure({
                  keepMarks: true,
                })
              ],
              content: content,
                onUpdate({ editor }) {
                    _this.updatedAt = Date.now();
                    const html = editor.getHTML();
                    Alpine.store('form').upsert(contentId, html)
                },
                onCreate() {
                  _this.updatedAt = Date.now()
                },
                onSelectionUpdate() {
                  _this.updatedAt = Date.now()
                }
            })
          },
          isLoaded() {
            return !!editor
          },

          isActive(type, opts = {}) {
            return editor.isActive(type, opts)
          },
          toggleHeading(opts) {
            editor.chain().toggleHeading(opts).focus().run()
          },
          toggleBold() {
            editor.chain().toggleBold().focus().run()
          },
          toggleItalic() {
            editor.chain().toggleItalic().focus().run()
          },
          setUnderline() {
            editor.chain().focus().toggleUnderline().run()
          },
          setBulletList() {
            editor.chain().focus().toggleBulletList().run()
          },
          setOrderedList() {
            editor.chain().focus().toggleOrderedList().run()
          },
          setParagraph() {
            editor.chain().setParagraph().run()
          },
          openLinkModal() {
            this.openModal2();
            this.$nextTick(() => {
                if (this.$refs.urlInput) {
                    this.$refs.urlInput.focus();
                }
            });
            const currentUrl = editor.getAttributes('link').href;
            if (currentUrl) {
              const target = editor.getAttributes('link').target;
              this.$nextTick(() => {
                this.$refs.urlInput.text = 'Update link';
                  this.$refs.urlInput.value = currentUrl;
                  if (target === '_blank') {
                      this.$refs.newTabCheckbox.checked = true;
                  }
              })
            }
          },
          setLink(urlData = {}) {
            let {url = '', newTab = null} = urlData;
              // cancelled
              if (url === null) {
                  return
                }
            // empty
            if (url === '') {
              editor
                .chain()
                .focus()
                .extendMarkRange('link')
                .unsetLink()
                .run()

              return
            }

            const target = newTab ? '_blank' : '_self';
            const setLinkData = { href: url, target };

            // update link
            editor
              .chain()
              .focus()
              .extendMarkRange('link')
              .setLink(setLinkData)
              .run()
          },
        }
      })
})
