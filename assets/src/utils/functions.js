/**
 * 格式化字符串
 *
 * @param {string} format 需要格式化额字符串
 * @param {*} args 替换内容
 * @returns {*}
 */
export function sprintf(format, ...args) {
    let index = 0;

    return format.replace(/%[sd]/g, (match) => {
        if (index >= args.length) {
            return match;
        }

        const arg = args[index++];

        if (match === '%s') {
            return String(arg);
        } else if (match === '%d') {
            if (typeof arg === 'number') {
                return String(arg);
            } else {
                throw new Error(`Argument ${index} is not a number.`);
            }
        }

        return match;
    });
}

export function $C(tag, attrs, skipAttrs = []) {
    let el = document.createElement(tag);
    return $A(el, attrs, skipAttrs);
}

export function $A(el, attrs = {}, skipAttrs = []) {
    let setAttr, setText, setHTML;
    if ("attr" in el) {
        setAttr = function (name, val) {
            el.attr(name, "" + val);
        }
        setText = function (val) {
            el.text(val);
        }
        setHTML = function (val) {
            el.html(val);
        }
    } else {
        setAttr = function (name, val) {
            el.setAttribute(name, "" + val);
        }
        setText = function (val) {
            el.innerText = val;
        }
        setHTML = function (val) {
            el.innerHTML = val;
        }
    }
    for (let p in attrs) {
        if (!skipAttrs.includes(p)) {
            // @ts-ignore
            let attr = attrs[p];
            if (typeof attr === "function") {
                attr = attr.toString();
            }
            switch (p) {
                case 'innerText':
                    setText(attr)
                    break;
                case 'innerHTML':
                    setHTML(attr);
                    break;
                case 'style':
                    if (typeof attr === "object") {
                        let style = Object.entries(attr).map((en) => {
                            return en.join(":");
                        }).join("; ");
                        setAttr('style', style);
                    } else {
                        setAttr('style', attr);
                    }
                    break;
                default:
                    setAttr(p, attr);
                    break;
            }

        }
    }
    return el;
}

export function isObject(obj) {
    return Object.prototype.toString.call(obj) === '[object Object]'
}

export function isString(str) {
    return typeof str === "string";
}

export function isElement(obj) {
    return obj instanceof HTMLElement;
}