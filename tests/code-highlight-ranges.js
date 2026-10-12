const assert = require('assert');
const fs = require('fs');
const path = require('path');
const vm = require('vm');

const source = fs.readFileSync(path.join(__dirname, '../assets/src/utils/codeBlocks.js'), 'utf8');
const context = vm.createContext({});
vm.runInContext(source.replace(/\nexport \{[\s\S]*$/, ''), context);

function highlighted(spec) {
    context.spec = spec;
    const expression = [
        'buildLineDescriptors(',
        "['one', 'two', 'three'].map(rawText => ({rawText, anchorHtml: ''})),",
        '{highlightSpec: spec, lineNumbers: false}',
        ").descriptors.map(line => line.classes.has('is-highlighted'))",
    ].join('\n');
    return Array.from(vm.runInContext(expression, context, {timeout: 1000}));
}

assert.deepStrictEqual(highlighted('1,3'), [true, false, true]);
assert.deepStrictEqual(highlighted('3-2'), [false, true, true]);
assert.deepStrictEqual(highlighted('0-2'), [true, true, false]);
assert.deepStrictEqual(highlighted('1-9007199254740991'), [true, true, true]);
assert.deepStrictEqual(highlighted('9007199254740991-9007199254740991'), [false, false, false]);
assert.deepStrictEqual(highlighted('4,9-12'), [false, false, false]);
assert.deepStrictEqual(highlighted(''), [false, false, false]);
for (const spec of [
    '9007199254740992-9007199254740992',
    '1-9007199254740992',
    '9007199254740992',
    '1-' + '9'.repeat(400),
]) {
    assert.throws(() => highlighted(spec), error =>
        error.name === 'RangeError' && /safe integers/.test(error.message));
}
console.log('code highlight range tests passed');
