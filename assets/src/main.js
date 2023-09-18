import {$C, isElement, isObject, isString, sprintf} from "./utils/functions";
import TextAreaUtils from "./utils/textarea";

class XEditor {
    constructor(sel, sel_preview) {
        if (document.querySelector(sel)) {
            this.textarea = new TextAreaUtils(sel);
            this.previewArea = document.querySelector(sel_preview);
        } else {
            console.warn(sprintf("element [%s] not e", sel));
        }

        this.isInit = false;
        this.buttons = [];
        $('body').on('XEditorAddButton', (event, ...buttons) => {
            buttons.forEach(button => {
                if (isObject(button)) {
                    if (Object.keys(button).length) {
                        this.buttons.push(button);
                    } else {
                        this.buttons.push({
                            class: 'wmd-spacer'
                        });
                    }
                } else if (isString(button) && button === "splitter") {
                    this.buttons.push({
                        class: 'wmd-spacer'
                    });
                }
            });
        }).on('XEditorInit', () => {
            this.init();
        }).on("XEditorRefresh", () => {
            let sp = this.textarea.getScrollPosition();
            this.setContent(this.getContent());
            this.textarea.setScrollPosition(sp.top, sp.left);
        });
    }

    /**
     * 判断是不是 Markdown 模式
     *
     * @returns {boolean}
     */
    isMarkdown() {
        return $('[name="markdown"]').val() === "1";
    }

    init() {
        if (this.isInit) return;
        this.isInit = true;
        window.XEditor = this;
        $("body").append(`<div id="aa-wrapper"></div>`)
            .on('XEditorPreviewEnd', () => this.handlePreviewEnd())
            .on('XEditorReplaceSelection', (e, text) => {
                console.log('replaceSelection', text);
                this.replaceSelection(text);
            });
        this.initToolbar();
        $('body').trigger('XEditorPreviewEnd');
    }

    initToolbar() {
        // 注册快捷键
        const registerShortcut = (btn, shortcut, callback) => {
            const isMac = /Mac|iPod|iPhone|iPad/.test(navigator.platform);

            // 校验快捷键格式是否正确
            const isValidShortcut = /^([a-zA-Z0-9]|F[1-9]|10|11|12|\+)+$/.test(shortcut);
            if (!isValidShortcut) {
                console.error('无效的快捷键格式');
                return false;
            }

            // 处理大小写问题
            shortcut = shortcut.toLowerCase();

            // 检查是否为Mac系统，如果是，则将ctrl替换为cmd
            if (isMac) {
                shortcut = shortcut.replace('ctrl', 'cmd');
            }

            // 创建keydown事件处理函数
            const handleKeyDown = (event) => {
                if (!this.isFocused()) {
                    // 只有在textarea处于焦点状态时响应快捷键事件
                    return false;
                }

                const pressedKeys = [];
                if (event.ctrlKey) {
                    pressedKeys.push('ctrl');
                }
                if (event.altKey) {
                    pressedKeys.push('alt');
                }
                if (event.shiftKey) {
                    pressedKeys.push('shift');
                }

                // 只在有修饰键按下的情况下响应字母和数字键
                if (/[a-zA-Z0-9]/.test(event.key) && pressedKeys.length > 0) {
                    pressedKeys.push(event.key.toLowerCase());
                }

                const pressedShortcut = pressedKeys.join('+');

                if (pressedShortcut === shortcut) {
                    event.preventDefault(); // 阻止浏览器默认行为
                    event.stopPropagation(); // 阻止事件冒泡
                    event.objectTarget = btn;
                    callback.call(this, event);
                }
            };

            // 绑定keydown事件到document
            document.addEventListener('keydown', handleKeyDown);

            return true;
        };

        // 创建按钮
        const createButton = (attrs = {}) => {
            let btn = $C('li', {
                ...attrs,
                title: attrs.name
            }, ['icon', 'command', 'onMounted']);
            btn.classList.add('wmd-button');
            let icon = $(attrs.icon);
            if (icon.length) {
                btn.appendChild(icon.get(0));
            } else {
                btn.innerHTML = attrs.icon;
            }
            // 检查是否存在shortcut属性
            if (attrs.shortcut) {
                if (registerShortcut(btn, attrs.shortcut, this.handleHotkey)) { // 注册按键，成功才修改 title
                    btn.title = `${attrs.name} ${attrs.shortcut.toUpperCase()}`;
                }
            }

            if ("command" in attrs) {
                if (typeof attrs.command === "function") {
                    btn.addEventListener('click', () => {
                        attrs.command.call(this, {target: btn});
                    });
                } else {
                    btn.setAttribute('onclick', attrs.command)
                }
            }

            return btn;
        }

        // 创建分隔符
        const createSplitter = (num = 0, attrs) => {
            let el = $C('li', {
                id: sprintf('wmd-spacer%d', num),
                ...attrs
            });
            el.classList.add('wmd-spacer');
            el.classList.add(sprintf('wmd-spacer%d', num));
            return el;
        }

        const getSpacerCount = () => {
            return $('#wmd-button-bar .wmd-spacer').length;
        }

        this.toolbar = $('#wmd-button-row');
        this.buttons.forEach(btnCfg => {
            let el, isExists = false;
            if ("id" in btnCfg) {
                if (document.getElementById(btnCfg.id)) {
                    el = document.getElementById(btnCfg.id);
                    isExists = true;
                    if ("icon" in btnCfg) {
                        el.innerHTML = btnCfg.icon;
                    }
                } else if ("name" in btnCfg) {
                    if (!("icon" in btnCfg)) {
                        btnCfg.icon = `<span>${name}</span>`;
                    }
                    el = createButton(btnCfg);
                } else {
                    el = createSplitter(getSpacerCount() + 1, btnCfg);
                }
            } else {
                el = createSplitter(getSpacerCount() + 1, btnCfg);
            }

            let isInserted = false,
                refNode;

            /**
             * 获取插入参考节点
             *
             * @param {string} refSel 参考节点或者参考节点选择器
             * @param {*} parent
             * @return {*|jQuery|HTMLElement}
             */
            const getRefNode = (refSel, parent) => {
                if (!parent) parent = this.toolbar;
                if (isString(refSel)) {
                    if (refSel.indexOf("|") > -1) {
                        let selArr = refSel.split("|");
                        for (let i = 0; i < selArr.length; i++) {
                            let el = getRefNode(selArr[i], parent);
                            if (el.length) return el;
                        }
                    } else {
                        return $(refSel, parent);
                    }
                }
            }

            if ("insertBefore" in btnCfg) {
                refNode = getRefNode(btnCfg.insertBefore, this.toolbar);
                if (refNode) {
                    refNode.before(el);
                    isInserted = true;
                }
            } else if ("insertAfter" in btnCfg) {
                refNode = getRefNode(btnCfg.insertAfter, this.toolbar);
                if (refNode) {
                    refNode.after(el);
                    isInserted = true;
                }
            } else if ("remove" in btnCfg && btnCfg.remove && el.parentNode) {
                el.parentNode.removeChild(el);
            }

            if (!isInserted && !isExists) {
                this.toolbar.append(el);
            }
            if (typeof btnCfg.onMounted === "function") {
                btnCfg.onMounted.call(this, {
                    target: el
                })
            }
        })
    }

    handleHotkey({objectTarget}) {
        objectTarget.click();
    }

    handlePreviewEnd() {

    }

    /**
     * 获取文本框文本
     *
     * @returns {string}
     */
    getContent() {
        return this.textarea.getContent();
    }

    /**
     * 设置文本框文本（记录到 UndoStack）
     *
     * @param {string} text
     */
    setContent(text) {
        this.textarea.executeAndAddUndoStack('setContent', text);
    }

    /**
     * 往光标虽在位置插入文本（记录到 UndoStack）
     *
     * @param {string} text
     */
    insertText(text) {
        this.textarea.executeAndAddUndoStack('insertText', text);
    }

    /**
     * 替换文本框选中文本（记录到 UndoStack）
     *
     * @param {string} text
     */
    replaceSelection(text) {
        if (this.getSelectedText()) {
            this.textarea.executeAndAddUndoStack('replaceSelection', text);
        } else {
            this.textarea.executeAndAddUndoStack('insertText', text);
        }
    }

    /**
     * 获取选中文本
     *
     * @return {string}
     */
    getSelectedText() {
        return this.textarea.getSelectedText();
    }

    /**
     * 判定是否聚焦到文本框了
     *
     * @returns {boolean}
     */
    isFocused() {
        return this.textarea.isFocused;
    }

    /**
     * 增加前缀后缀。
     * @param {string} prefix - 前缀。
     * @param {string} postfix - 后缀。
     * @param {string} defaultText - 没有选中文本时插入到前缀和后缀之间的文本。
     */
    wrapText(prefix, postfix, defaultText = "") {
        const {textarea} = this;
        const selectedText = textarea.getSelectedText();
        const currentPos = textarea.getSelection();

        if (selectedText) {
            if (selectedText.startsWith(prefix) && selectedText.endsWith(postfix)) {
                // 去除 prefix/postfix
                const newText = selectedText.slice(prefix.length, -postfix.length);
                textarea.executeAndAddUndoStack('replaceSelection', newText);
            } else if (
                textarea.getTextInRange(currentPos.start - prefix.length, currentPos.start) === prefix &&
                textarea.getTextInRange(currentPos.end, currentPos.end + postfix.length) === postfix
            ) {
                // 光标前后已经有 prefix 和 postfix，直接替换选中文本
                textarea.setSelection(currentPos.start - prefix.length, currentPos.end + prefix.length);
                textarea.executeAndAddUndoStack('replaceSelection', selectedText);
                textarea.setSelection(currentPos.start - prefix.length, currentPos.end - prefix.length - postfix.length + 1);
            } else {
                // 添加 prefix 和 postfix 并替换选中文本
                const newText = prefix + selectedText + postfix;
                textarea.executeAndAddUndoStack('replaceSelection', newText);
                const lastSelection = textarea.getSelection();
                textarea.setSelection(lastSelection.start + prefix.length, lastSelection.end - postfix.length);
            }
        } else {
            if (
                textarea.getTextInRange(currentPos.start - prefix.length, currentPos.start) === prefix &&
                textarea.getTextInRange(currentPos.end, currentPos.end + postfix.length) === postfix
            ) {
                // 光标前后已经有 prefix 和 postfix，直接删除它们
                textarea.setSelection(currentPos.start - prefix.length, currentPos.end + prefix.length);
                textarea.executeAndAddUndoStack('replaceSelection');
                textarea.setSelection(currentPos.start - prefix.length, currentPos.start - prefix.length);
            } else {
                // 插入带有 prefix 和 postfix 的默认文本
                const newText = prefix + defaultText + postfix;
                textarea.executeAndAddUndoStack('insertText', newText);
                const start = textarea.getSelection().start;
                textarea.setSelection(start - newText.length + prefix.length, start - postfix.length);
            }
        }
    }

    /**
     * 区块增加前缀。
     * @param {string} prefix - 前缀。
     * @param {string} defaultText - 没有选中文本时插入到前缀后面的文本。
     */
    blockPrefix(prefix, defaultText) {
        const {textarea} = this;
        let realSelectionText = textarea.getSelectedText();
        if (realSelectionText.length) {
            let realSelectionArr = realSelectionText.split('\n');
            let newText = realSelectionArr.map((line, i) => {
                return prefix.replace("%n", i + 1) + line;
            }).join("\n");
            // 检查选中文本的第一行是否在行首，如果不是，在 newText 前添加换行符
            if (!textarea.isAtLineStart()) {
                newText = "\n" + newText;
            }
            textarea.executeAndAddUndoStack('replaceSelection', newText + "\n");
        } else {
            let newPrefix = prefix.replace("%n", 1),
                newText = newPrefix + defaultText + "\n",
                startPosFix = 0;
            if (!textarea.isAtLineStart()) {
                newText = "\n" + newText;
                startPosFix = 1;
            }
            textarea.executeAndAddUndoStack('insertText', newText);
            textarea.setSelection(textarea.getSelection().start - newText.length + newPrefix.length + startPosFix, textarea.getSelection().start);
        }
    }

    /**
     * 选中文本首行前缀处理。
     * @param {string} prefix - 前缀。
     * @param {string} defaultText - 没有选中文本时插入到前缀后面的文本。
     */
    firstSelectionLinePrefix(prefix, defaultText) {
        const {textarea} = this;
        let realSelectionText = textarea.getSelectedText();
        if (realSelectionText.length) {
            let realSelectionArr = realSelectionText.split('\n');
            let firstLine = realSelectionArr[0];

            // 判断选中的第一行是否以指定前缀开头
            if (firstLine.startsWith(prefix)) {
                // 去除前缀
                firstLine = firstLine.substring(prefix.length);
            } else {
                let {start: startPos, end: endPos} = textarea.getSelection();
                if (textarea.getTextInRange(startPos - prefix.length, startPos) === prefix) {
                    textarea.setSelection(startPos - prefix.length, endPos);
                } else {
                    // 增加前缀
                    firstLine = prefix + firstLine;
                }
            }

            // 重新组合文本
            realSelectionArr[0] = firstLine;
            let newText = realSelectionArr.join("\n");

            // 检查选中文本的第一行是否在行首，如果不是，在 newText 前添加换行符
            if (!textarea.isAtLineStart()) {
                newText = "\n" + newText;
            }

            textarea.executeAndAddUndoStack('replaceSelectionText', newText);
        } else {
            let newPrefix = prefix.replace("%n", 1);
            let newText, startPosFix = 0;

            if (textarea.isAtLineStart()) {
                newText = newPrefix + defaultText;
            } else {
                newText = "\n" + newPrefix + defaultText;
                startPosFix = 1;
            }

            textarea.executeAndAddUndoStack('insertText', newText);

            // 设置光标位置
            textarea.setSelection(textarea.getSelection().start - newText.length + newPrefix.length + startPosFix, textarea.getSelection().start);
        }
    }

    /**
     * 打开模态框 (参考 Joe 主题)
     * @param options
     */
    openModal(options = {}) {
        const _modalOptions = {
            title: "标题",
            innerHTML: '内容',
            showFooter: true,
            checkEmptyOnConfirm: true,
            change: function (modal, params) {
            },
            confirm: function (modal, params) {
                return true;
            },
            cancel: function (event) {
            },
            handle: function (modal) {
            },
            callback: function (modal) {
            }
        };
        let modalOptions = Object.assign(_modalOptions, options);
        let contentWrap = modalOptions.checkEmptyOnConfirm ? '<form class="params"></form>' : "";
        if ($("#aa-modal").length < 1) {
            $('#aa-wrapper').append(`<div id="aa-modal" class="aa-modal">
    <div class="aa-modal-frame">
    <div class="aa-modal-header">
        <div class="aa-modal-header-title"></div><div class="aa-modal-header-close"><i class="close-icon"></i></div>
</div>
    <div class="aa-modal-body">
        ${contentWrap}
    </div>
    <div class="aa-modal-footer">
        <button type="button" class="aa-modal-footer-button aa-modal-footer-cancel">取消</button><button type="button" class="aa-modal-footer-button aa-modal-footer-confirm">确定</button>
    </div>
</div>
</div>`);
        }
        $('.aa-modal-header-title').html(modalOptions.title);
        if (modalOptions.checkEmptyOnConfirm) {
            $('.aa-modal-body .params').html(modalOptions.innerHTML);
        } else {
            $('.aa-modal-body').html(modalOptions.innerHTML);
        }
        let modalElm = $("#aa-modal").get(0);
        modalOptions.showFooter ? $(`.aa-modal-footer`).show() : $('.aa-modal-footer').hide();
        $('body').addClass('no-scroll');
        modalOptions.handle.call(this, modalElm);
        $('.aa-modal-footer-confirm').on('click', () => {
            let flag = true;
            if (modalOptions.checkEmptyOnConfirm) {
                // 检查必填输入框
                const form = $('#aa-modal .aa-modal-body .params');
                let params = form.serializeArray();
                $.each(params, function (i, param) {
                    let element = $(`#aa-modal .params [name=${param.name}]`);
                    if (element.prop('required') && param.value === "") {
                        flag = false;
                        element.addClass('required-animate');
                        setTimeout(function () {
                            element.removeClass('required-animate');
                        }, 800);
                    }
                });
            }
            if (flag && modalOptions.confirm.call(this, modalElm)) {
                $('#aa-modal').remove();
                $('body').removeClass('no-scroll');
                modalOptions.callback.call(this, modalElm);
            }
        });
        $('.aa-modal-header-close').on('click', (event) => {
            modalOptions.cancel.call(this, event);
            $('#aa-modal').removeClass("active");
            setTimeout(function () {
                $('#aa-modal').remove();
                $('body').removeClass('no-scroll');
            }, 300);
        });
        $('.aa-modal-footer-cancel').on('click', (event) => {
            $('#aa-modal').removeClass("active");
            modalOptions.cancel.call(modalElm, event);
            setTimeout(function () {
                $('#aa-modal').remove();
                $('body').removeClass('no-scroll');
            }, 300);
        });
        let form = $(".params", modalElm);
        $("input,select,textarea", form).on('change input', () => {
            let data = form.serializeArray(),
                params = {};
            data.forEach((item) => {
                params[item.name] = item.value;
            })
            modalOptions.change.call(this, modalElm, params);
        });
        $('#aa-modal').addClass('active');
    }

    /**
     * 来自 Wordpress
     * https://regex101.com/r/ja0b1p/1
     * $0→code $2→tag $3→attr $5→text
     * @param tag
     * @returns {RegExp}
     */
    getShortCodeRegex(tag) {
        return new RegExp('\\[(\\[?)(' + tag + ')(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)', 'g');
    }
}

new XEditor("#text", "#md-preview");