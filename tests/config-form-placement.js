const assert = require('assert');
const fs = require('fs');
const path = require('path');
const vm = require('vm');
const crypto = require('crypto');
const { execFileSync } = require('child_process');
const { parseDOM } = require('htmlparser2');
const selectors = require('css-select');
const domutils = require('domutils');

const html = execFileSync('php', [path.join(__dirname, 'backup-csrf.php'), '--html-fixture'], { encoding: 'utf8' });

for (const file of ['assets/src/config/config.js', 'assets/dist/js/config.js']) {
    // Execute the actual script against the PHP callback's generated HTML.
    const roots = parseDOM(html, { decodeEntities: true });
    const wrappers = new WeakMap();
    function select(selector, root) {
        if (selector.startsWith(':scope > ')) {
            return (root.children || []).filter(node => selectors.is(node, selector.slice(9)));
        }
        return selectors.selectAll(selector, root.children || root);
    }
    function wrap(node) {
        if (!node) return null;
        if (wrappers.has(node)) return wrappers.get(node);
        const result = {
            get parentNode() { return wrap(node.parent); },
            get classList() {
                return {
                    add(name) { node.attribs.class = `${node.attribs.class || ''} ${name}`.trim(); },
                    contains(name) { return (node.attribs.class || '').split(/\s+/).includes(name); },
                };
            },
            querySelectorAll(selector) { return select(selector, node).map(wrap); },
            querySelector(selector) { return wrap(select(selector, node)[0]); },
            addEventListener() {},
            removeChild(child) { domutils.removeElement(child.node); },
            appendChild(child) { domutils.appendChild(node, child.node); },
            node,
        };
        wrappers.set(node, result);
        return result;
    }
    let ready;
    const document = {
        querySelectorAll(selector) { return selectors.selectAll(selector, roots).map(wrap); },
        querySelector(selector) { return wrap(selectors.selectOne(selector, roots)); },
        addEventListener(event, callback) { if (event === 'DOMContentLoaded') ready = callback; },
    };
    const context = {
        document, window: {}, location: { hash: '' },
        sessionStorage: { getItem() { return null; } }, console,
    };
    vm.runInNewContext(fs.readFileSync(path.join(__dirname, '..', file), 'utf8'), context, { filename: file });
    assert.strictEqual(typeof ready, 'function');
    ready();
    const normal = selectors.selectOne('#normal-config-form', roots);
    const content = selectors.selectOne('.x-content', roots);
    assert.strictEqual(normal.parent, content, `${file}: move normal save form`);
    const backup = selectors.selectOne('.x-backup', roots);
    for (const type of ['backup', 'restore', 'delete']) {
        assert.strictEqual(selectors.selectOne(`#aaeditor-backup-${type}`, roots).parent, backup, `${file}: preserve ${type} form`);
    }
    assert.strictEqual(selectors.selectAll('form form', roots).length, 0, `${file}: no nested forms`);
}

const deployed = fs.readFileSync(path.join(__dirname, '../assets/dist/js/config.js'), 'utf8');
const hash = crypto.createHash('md5').update(deployed).digest('hex');
const manifest = require('../assets/dist/mix-manifest.json');
assert.strictEqual(manifest['/js/config.js'], `/js/config.js?id=${hash}`, 'config asset cache version');

console.log('configuration form placement tests passed');
