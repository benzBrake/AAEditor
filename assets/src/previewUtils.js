window.XPreviewUtils = {
    htmlProcessors: [],
    init() {
        this.previewArea = document.getElementById('wmd-preview');
        $('body').on('XEditorAddHtmlProcessor', (event, processor, priority = 99) => {
            this.htmlProcessors.push({
                priority,
                processor
            });
            this.htmlProcessors = this.htmlProcessors.sort(
                (a, b) => {
                    return a.priority - b.priority;
                });
        });

        window.hljs = require("highlight.js/lib/common");
    },

    getPreviewArea() {
        return this.previewArea;
    },

    /**
     * 处理 HTML
     * @param html
     * @returns string
     */
    processHtml(html) {

        html = html.replace(/\[x]/g, '<input type="checkbox" class="x-checkbox" checked disabled/>');
        html = html.replace(/\[ ]/g, '<input type="checkbox" class="x-checkbox" disabled />');
        this.htmlProcessors.forEach(({processor, priority}) => {
            let _html = processor.call(this, html);
            if (typeof _html === "undefined") {
                console.error(processor.toString() + ' return undefined');
                return html;
            }
            html = _html;
        });
        return html;
    },

    /**
     * 来自 Wordpress
     * https://regex101.com/r/ja0b1p/1
     * $0→code $2→tag $3→attr $5→text
     * @param tag
     * @returns {RegExp}
     */
    getShortCodeRegex(tag) {
        return new RegExp('\\[(\\[?)(' + tag + ')(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)', 'g');
    },
}

window.XPreviewUtils.init();