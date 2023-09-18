/**
 * TextAreaUtils 类用于管理文本区域（Textarea）的操作和事件绑定。
 */
export default class TextAreaUtils {
    /**
     * 创建一个 TextAreaUtils 的实例。
     * @param {string} textareaSelector - 选择器，用于选择要管理的文本区域。
     */
    constructor(textareaSelector) {
        this.textarea = document.querySelector(textareaSelector);
        this.textarea.utils = this; // 将 TextAreaUtils 实例关联到 textarea 元素上
        this.locked = false; // 文本区域是否被锁定
        this.isFocused = false; // 默认未获取焦点
        this.initFocusEvents(); // 初始化焦点事件
    }

    /**
     * 运行命令并保存修改到 UndoStack。
     * @param {(string)} operate - 要执行的操作，支持 'setContent', 'insertText', 'replaceSelectionText' 等。
     * @param {...any} args - 操作所需的参数。
     */
    executeAndAddUndoStack(operate, ...args) {
        const fn = () => {
            // 找到对应的函数名并执行
            if (operate === 'setContent') {
                this.setContent(...args);
            } else if (operate === 'insertText') {
                this.insertText(...args);
            } else if (operate === 'replaceSelectionText') {
                this.replaceSelectionText(...args);
            } // 添加其他支持的命令
        };
        if (this.textarea.pagedown) {
            this.textarea.pagedown.textOperation(fn);
        } else {
            fn();
        }
    }

    /**
     * 获取文本区域实例。
     * @returns {HTMLElement} 返回文本区域的 DOM 元素。
     */
    getInstance() {
        return this.textarea;
    }

    /**
     * 绑定事件处理程序。
     * @param {string} eventType - 要绑定的事件类型，例如 'input', 'focus', 'blur' 等。
     * @param {Function} handler - 事件处理程序函数。
     */
    on(eventType, handler) {
        this.textarea.addEventListener(eventType, handler);
    }

    /**
     * 解绑事件处理程序。
     * @param {string} eventType - 要解绑的事件类型。
     * @param {Function} handler - 要解绑的事件处理程序函数。
     */
    off(eventType, handler) {
        this.textarea.removeEventListener(eventType, handler);
    }

    /**
     * 获取文本区域的内容。
     * @returns {string} 返回文本区域的当前内容。
     */
    getContent() {
        return this.textarea.value;
    }

    /**
     * 设置文本区域的内容。
     * @param {string} content - 要设置的新内容。
     */
    setContent(content) {
        if (!this.locked) {
            this.textarea.value = content;
            this.afterOperate(); // 触发 input 事件
            return true;
        }
        return false;
    }

    /**
     * 锁定文本区域，阻止内容修改
     */
    lock() {
        this.locked = true;
        this.textarea.setAttribute('readonly', 'readonly');
    }

    /**
     * 取消锁定文本区域，允许内容修改
     */
    unlock() {
        this.locked = false;
        this.textarea.removeAttribute('readonly');
    }

    /**
     * 插入文本到当前光标的位置，成功返回真，否则返回假。
     * @param {string} text - 要插入的文本。
     * @returns {boolean} 如果插入操作成功，则返回 true；否则返回 false。
     */
    insertText(text) {
        if (this.locked) return false;
        const startPos = this.textarea.selectionStart;
        const endPos = this.textarea.selectionEnd;
        const currentText = this.textarea.value;

        this.textarea.value = currentText.slice(0, startPos) + text + currentText.slice(endPos);
        this.textarea.selectionStart = startPos + text.length;
        this.textarea.selectionEnd = startPos + text.length;

        this.afterOperate(); // 触发 input 事件
        return true;
    }

    /**
     * 替换选中文本。
     * @param {string} text - 用于替换选中文本的新文本。
     * @returns {boolean} 如果替换操作成功，则返回 true；否则返回 false。
     */
    replaceSelectionText(text) {
        if (this.locked) return false;
        const startPos = this.textarea.selectionStart;
        const endPos = this.textarea.selectionEnd;
        const currentText = this.textarea.value;

        this.textarea.value = currentText.slice(0, startPos) + text + currentText.slice(endPos);
        this.textarea.selectionStart = startPos;
        this.textarea.selectionEnd = startPos + text.length;

        this.afterOperate(); // 触发 input 事件
        return true
    }

    /**
     * 获取选中文本。
     * @returns {string} 返回当前选中的文本。
     */
    getSelectedText() {
        return this.textarea.value.substring(this.textarea.selectionStart, this.textarea.selectionEnd);
    }

    /**
     * 获取当前选中文本的起始和结束位置。
     * @returns {{start: number, end: number}} 返回包含选中文本起始和结束位置的对象。
     */
    getSelection() {
        return {
            start: this.textarea.selectionStart,
            end: this.textarea.selectionEnd
        };
    }

    /**
     * 设置选中文本的起始和结束位置
     * @param {number} start - 选区的开始位置。
     * @param {number} end - 选区的结束位置。
     * @returns {boolean} 如果设置选区成功，则返回 true；否则返回 false。
     */
    setSelection(start, end) {
        if (this.locked) return false;
        if (start >= 0 && end >= 0 && start <= this.textarea.value.length && end <= this.textarea.value.length) {
            this.textarea.selectionStart = start;
            this.textarea.selectionEnd = end;
            return true;
        }
        return false;
    }

    /**
     * 获取指定行号的文本。
     * @param {number} lineNumber - 要获取的行号。
     * @returns {string} 返回指定行号的文本，如果行号无效则返回空字符串。
     */
    getLineText(lineNumber) {
        const text = this.textarea.value;
        const lines = text.split('\n');

        if (lineNumber >= 0 && lineNumber < lines.length) {
            return lines[lineNumber];
        }

        return '';
    }

    /**
     * 截取文本。
     * @param {number} start - 起始位置。
     * @param {number} end - 结束位置。
     * @returns {string} 返回从起始位置到结束位置的文本。
     */
    getTextInRange(start, end) {
        const text = this.textarea.value;
        return text.substring(start, end);
    }

    /**
     * 获取当前行的文本。
     * @param {number} start - 开始位置，默认为行首，即 0。
     * @param {number} end - 结束位置，默认为该行的长度。
     * @returns {string} 返回从指定位置开始到结束位置的文本。
     */
    getCurrentLineText(start = 0, end = this.textarea.selectionStart) {
        const text = this.textarea.value;
        let lineStart = start;
        let lineEnd = end;

        while (lineStart > 0 && text[lineStart - 1] !== '\n') {
            lineStart--;
        }

        while (lineEnd < text.length && text[lineEnd] !== '\n') {
            lineEnd++;
        }

        return text.substring(lineStart, lineEnd);
    }

    /**
     * 替换当前行的文本。
     * @param {string} newText - 用于替换当前行的新文本。
     * @returns {boolean} 如果替换成功，则返回 true；否则返回 false。
     */
    replaceCurrentLine(newText) {
        if (this.locked) return false;
        const startPos = this.textarea.selectionStart;
        const endPos = this.textarea.selectionEnd;
        const text = this.textarea.value;

        let lineStart = startPos;
        let lineEnd = endPos;

        while (lineStart > 0 && text[lineStart - 1] !== '\n') {
            lineStart--;
        }

        while (lineEnd < text.length && text[lineEnd] !== '\n') {
            lineEnd++;
        }

        const newTextBefore = text.substring(0, lineStart);
        const newTextAfter = text.substring(lineEnd);

        this.textarea.value = newTextBefore + newText + newTextAfter;
        this.textarea.selectionStart = lineStart;
        this.textarea.selectionEnd = lineStart + newText.length;
        this.afterOperate(); // 触发 input 事件
        return true;
    }

    /**
     * 获取光标位置的行号和列号。
     * @returns {{line: number, column: number}} 返回包含光标所在行号和列号的对象。
     */
    getCursorPosition() {
        const startPos = this.textarea.selectionStart;
        const text = this.textarea.value;
        let lineCount = 1;
        let columnCount = 1;

        for (let i = 0; i < startPos; i++) {
            if (text[i] === '\n') {
                lineCount++;
                columnCount = 1;
            } else {
                columnCount++;
            }
        }

        return {line: lineCount, column: columnCount};
    }

    /**
     * 是否在行首。
     * @returns {boolean} 如果光标在行首，则返回 true；否则返回 false。
     */
    isAtLineStart() {
        const startPos = this.textarea.selectionStart;
        const text = this.textarea.value;
        return startPos === 0 || text[startPos - 1] === '\n';
    }

    /**
     * 是否在行尾。
     * @returns {boolean} 如果光标在行尾，则返回 true；否则返回 false。
     */
    isAtLineEnd() {
        const endPos = this.textarea.selectionEnd;
        const text = this.textarea.value;
        return endPos === text.length || text[endPos] === '\n';
    }

    /**
     * 是否在文本开头。
     * @returns {boolean} 如果光标在文本开头，则返回 true；否则返回 false。
     */
    isAtStart() {
        return this.textarea.selectionStart === 0;
    }

    /**
     * 是否在文本结尾。
     * @returns {boolean} 如果光标在文本结尾，则返回 true；否则返回 false。
     */
    isAtEnd() {
        return this.textarea.selectionEnd === this.textarea.value.length;
    }

    // 初始化焦点事件
    initFocusEvents() {
        this.textarea.addEventListener('focus', () => {
            this.isFocused = true;
        });

        this.textarea.addEventListener('blur', () => {
            this.isFocused = false;
        });
    }

    // 触发 input 事件
    afterOperate() {
        $(this.textarea).trigger('input');
        this.textarea.focus();
    }

    /**
     * 获取文本区域的滚动条位置。
     * @returns {{top: number, left: number}} 返回包含滚动条垂直位置和水平位置的对象。
     */
    getScrollPosition() {
        return {
            top: this.textarea.scrollTop,
            left: this.textarea.scrollLeft
        };
    }

    /**
     * 设置文本区域的滚动条位置。
     * @param {number} top - 垂直滚动位置。
     * @param {number} left - 水平滚动位置。
     */
    setScrollPosition(top, left) {
        this.textarea.scrollTop = top;
        this.textarea.scrollLeft = left;
    }

}