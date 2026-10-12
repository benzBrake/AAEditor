const assert = require('assert');
const fs = require('fs');
const path = require('path');
const vm = require('vm');
const {webcrypto} = require('crypto');
const Parser = require('../assets/src/hyperdown.js');

const context = vm.createContext({
    crypto: webcrypto,
    window: {},
    document: {getElementById() { return null; }},
    $: Object.assign(() => ({on() {}}), {trim: value => value.trim()}),
    require() { return {}; },
});
const previewSource = fs.readFileSync(path.join(__dirname, '../assets/src/previewUtils.js'), 'utf8');
vm.runInContext('const enhanceCodeBlocks = () => {}; const DEFAULT_CODE_BLOCK_MESSAGES = {};\n'
    + previewSource.replace(/^import .+;\r?\n/, ''), context);
const util = fs.readFileSync(path.join(__dirname, '../Util.php'), 'utf8');
const hook = util.match(/converter\.hook\('makeHtml', function \(html\) \{([\s\S]*?)\r?\n {24}\}\);/);
assert.ok(hook, 'find the actual editor preview hook');
const render = vm.runInContext('(function(html) {' + hook[1] + '\n})', context);

for (const code of [
    '<pre class="aaeditor-code-block"><code class="language-text">[x] [ ]</code></pre>',
    '<p><code title="example">[x] [ ]</code></p>',
    '<pre>__BLOCK__</pre><code>[x]</code>',
    new Parser().makeHtml('~~~text\n[x] [ ]\n~~~'),
]) {
    assert.strictEqual(render(code), code, 'preserve literal code and attributes');
    const result = render(code + '<p>[x] [ ]</p>');
    assert.ok(result.startsWith(code), 'preserve code beside task markers');
    assert.strictEqual((result.match(/type="checkbox"/g) || []).length, 2, 'render task markers outside code');
}
console.log('preview code protection tests passed');
