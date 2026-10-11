const assert = require('assert');
const fs = require('fs');
const path = require('path');
const { parseDOM } = require('htmlparser2');
const cases = require('./image-attributes.json');

const source = path.join(__dirname, '../assets/src/hyperdown.js');
const deployed = path.join(__dirname, '../assets/dist/js/hyperdown.js');
assert.strictEqual(fs.readFileSync(source, 'utf8'), fs.readFileSync(deployed, 'utf8'), 'deployed parser must match source');

for (const file of [source, deployed]) {
    const Parser = require(file);
    for (const test of cases) {
        const nodes = parseDOM(new Parser().makeHtml(test.markdown), { decodeEntities: true });
        const images = [];
        function visit(children) {
            for (const node of children) {
                if (node.name === 'img') images.push(node);
                if (node.children) visit(node.children);
            }
        }
        visit(nodes);
        assert.strictEqual(images.length, 1, test.name);
        // HTML parser decodes attributes once, matching browser entity handling.
        assert.deepStrictEqual(Object.keys(images[0].attribs).sort(), ['alt', 'src', 'title'], test.name);
        for (const attr of ['src', 'alt', 'title']) {
            assert.strictEqual(images[0].attribs[attr], test[attr], `${test.name}: ${attr}`);
        }
    }
}

console.log('image attribute tests passed');
