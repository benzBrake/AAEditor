const assert = require('assert');
const { parseDOM } = require('htmlparser2');
const cases = require('./url-attributes.json');

for (const file of ['../assets/src/hyperdown.js', '../assets/dist/js/hyperdown.js']) {
    const Parser = require(file);
    for (const test of cases) {
        const url = test.url;
        const paths = [
            [`[label](${url} "caption")`, 'a', 'href', 'caption'],
            [`![label](${url} "caption")`, 'img', 'src', 'caption'],
            [`[label][target]\n\n[target]: ${url}`, 'a', 'href', null],
            [`![label][target]\n\n[target]: ${url}`, 'img', 'src', 'label'],
        ];
        if (test.angle || test.autolink) paths.push([`<${url}>`, 'a', 'href', null]);
        if (test.autolink) paths.push([url, 'a', 'href', null]);
        for (const [markdown, tag, attribute, title] of paths) {
            const nodes = parseDOM(new Parser().makeHtml(markdown), { decodeEntities: true });
            const elements = [];
            function visit(children) {
                for (const node of children) {
                    if (node.name === tag) elements.push(node);
                    if (node.children) visit(node.children);
                }
            }
            visit(nodes);
            assert.strictEqual(elements.length, 1, test.name);
            const attrs = elements[0].attribs;
            assert.strictEqual(attrs[attribute], test.expected, `${test.name}: ${markdown}`);
            const protocol = new URL(attrs[attribute], 'https://example.test/base/').protocol;
            assert.ok(['http:', 'https:', 'mailto:'].includes(protocol), `${test.name}: unsafe URL protocol`);
            for (const name of Object.keys(attrs)) {
                assert.ok([attribute, 'alt', 'title'].includes(name), `${test.name}: unexpected attribute`);
            }
            if (title !== null) assert.strictEqual(attrs.title, title, `${test.name}: title`);
            if (tag === 'img') assert.strictEqual(attrs.alt, 'label', `${test.name}: alt`);
        }
    }
    // Markdown normalizes line breaks/tabs before URL parsing; verify this boundary directly.
    for (const url of ['java\tscript:alert', 'java\nscript:alert', 'java\rscript:alert', '\0javascript:alert']) {
        const [safeUrl, title] = new Parser().cleanUrl(`${url} "caption"`, true);
        const image = parseDOM(`<img src="${safeUrl}">`, { decodeEntities: true })[0];
        assert.strictEqual(image.attribs.src, '#');
        assert.strictEqual(title, 'caption');
    }
}

console.log('URL attribute tests passed');
