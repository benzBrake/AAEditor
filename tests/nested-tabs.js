const assert = require('assert');
const fs = require('fs');
const path = require('path');
const vm = require('vm');
const {parseDOM} = require('htmlparser2');
const selectors = require('css-select');
const Parser = require('../assets/src/hyperdown.js');

const markdown = [
    '::: tabs',
    '::: tab Outer A',
    '::: tabs',
    '::: tab Inner A', 'Inner first', ':::',
    '::: tab Inner B', 'Inner second', ':::',
    ':::',
    ':::',
    '::: tab Outer B', 'Outer second', ':::',
    ':::',
].join('\n');

for (const file of ['assets/src/utils/tabs.js', 'assets/dist/js/front.js']) {
    const roots = parseDOM(new Parser().makeHtml(markdown));
    const cache = new WeakMap();
    class Element {
        constructor(node) {
            this.node = node;
            this.dataset = {};
            this.events = {};
            for (const [name, value] of Object.entries(node.attribs || {})) {
                if (name.startsWith('data-')) {
                    this.dataset[name.slice(5).replace(/-([a-z])/g, (_, char) => char.toUpperCase())] = value;
                }
            }
            const classes = () => (node.attribs.class || '').split(/\s+/).filter(Boolean);
            this.classList = {
                contains: name => classes().includes(name),
                toggle(name, enabled) {
                    const values = new Set(classes());
                    if (enabled) values.add(name);
                    else values.delete(name);
                    node.attribs.class = [...values].join(' ');
                },
                add(...names) { names.forEach(name => this.toggle(name, true)); },
                remove(...names) { names.forEach(name => this.toggle(name, false)); },
            };
        }
        get children() { return (this.node.children || []).filter(node => node.type === 'tag').map(wrap); }
        querySelectorAll(selector) { return selectors.selectAll(selector, this.node.children || []).map(wrap); }
        getAttribute(name) { return this.node.attribs[name] ?? null; }
        setAttribute(name, value) { this.node.attribs[name] = value; }
        get hidden() { return 'hidden' in this.node.attribs; }
        set hidden(value) {
            if (value) this.node.attribs.hidden = '';
            else delete this.node.attribs.hidden;
        }
        addEventListener(name, callback) {
            (this.events[name] || (this.events[name] = [])).push(callback);
        }
        click() { (this.events.click || []).forEach(callback => callback()); }
        focus() {}
    }
    function wrap(node) {
        if (!cache.has(node)) cache.set(node, new Element(node));
        return cache.get(node);
    }
    class Document {
        querySelectorAll(selector) { return selectors.selectAll(selector, roots).map(wrap); }
        addEventListener(name, callback) { this[name] = callback; }
    }
    const document = new Document();
    const context = vm.createContext({document, window: {}, Element, Document});
    vm.runInContext(fs.readFileSync(path.join(__dirname, '..', file), 'utf8')
        .replace('export function initFenceTabs', 'function initFenceTabs'), context);
    const init = file.endsWith('tabs.js')
        ? () => context.initFenceTabs(document)
        : () => document.DOMContentLoaded();
    init();
    const [outer, inner] = document.querySelectorAll('.fence-tabs');
    const outerButtons = outer.children[0].children;
    const outerPanels = outer.children[1].children;
    const innerButtons = inner.children[0].children;
    const innerPanels = inner.children[1].children;
    assert.strictEqual(outer.dataset.tabsInitialized, 'true', file + ': outer initialized');
    assert.strictEqual(inner.dataset.tabsInitialized, 'true', file + ': inner initialized');
    outerButtons[1].click();
    assert.strictEqual(outerPanels[0].hidden, true);
    assert.strictEqual(outerPanels[1].hidden, false);
    assert.strictEqual(innerPanels[0].hidden, false, 'outer switch preserves inner state');
    innerButtons[1].click();
    assert.strictEqual(innerPanels[0].hidden, true);
    assert.strictEqual(innerPanels[1].hidden, false);
    outerButtons[0].click();
    assert.strictEqual(outerPanels[0].hidden, false);
    assert.strictEqual(outerPanels[1].hidden, true);
    assert.strictEqual(innerButtons[1].getAttribute('aria-selected'), 'true');
    init();
    assert.strictEqual(innerPanels[1].hidden, false, 'reinitialization preserves selection');
    assert.strictEqual(outerButtons[0].events.click.length, 1, 'bind only once');
    assert.strictEqual(innerButtons[0].events.click.length, 1, 'bind nested buttons only once');
}
console.log('nested tabs tests passed');
