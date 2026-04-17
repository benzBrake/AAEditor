/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/src/main.js":
/*!****************************!*\
  !*** ./assets/src/main.js ***!
  \****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _utils_functions__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./utils/functions */ "./assets/src/utils/functions.js");
/* harmony import */ var _utils_textarea__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./utils/textarea */ "./assets/src/utils/textarea.js");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _createForOfIteratorHelper(r, e) { var t = "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (!t) { if (Array.isArray(r) || (t = _unsupportedIterableToArray(r)) || e && r && "number" == typeof r.length) { t && (r = t); var _n = 0, F = function F() {}; return { s: F, n: function n() { return _n >= r.length ? { done: !0 } : { done: !1, value: r[_n++] }; }, e: function e(r) { throw r; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var o, a = !0, u = !1; return { s: function s() { t = t.call(r); }, n: function n() { var r = t.next(); return a = r.done, r; }, e: function e(r) { u = !0, o = r; }, f: function f() { try { a || null == t["return"] || t["return"](); } finally { if (u) throw o; } } }; }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }


var XEditor = /*#__PURE__*/function () {
  function XEditor(sel, sel_preview) {
    var _this = this;
    _classCallCheck(this, XEditor);
    if (document.querySelector(sel)) {
      var _this$previewArea;
      this.textarea = new _utils_textarea__WEBPACK_IMPORTED_MODULE_1__["default"](sel);
      this.previewArea = document.querySelector(sel_preview);
      this.previewWrapper = ((_this$previewArea = this.previewArea) === null || _this$previewArea === void 0 ? void 0 : _this$previewArea.closest("#wmd-preview-wrapper-aaeditor")) || null;
    } else {
      console.warn((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.sprintf)("element [%s] not e", sel));
    }
    this.isInit = false;
    this.buttons = [];
    this.insertProcessors = [];
    this.scrollSyncFrame = 0;
    this.scrollSyncLock = false;
    this.scrollSyncBound = false;
    this.previewLineAnchors = [];
    this.pendingPreviewSyncTimers = [];
    $("body").on("XEditorAddButton", function (event) {
      for (var _len = arguments.length, buttons = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
        buttons[_key - 1] = arguments[_key];
      }
      buttons.forEach(function (button) {
        if ((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.isObject)(button)) {
          if (Object.keys(button).length) {
            _this.buttons.push(button);
          } else {
            _this.buttons.push({
              "class": "wmd-spacer"
            });
          }
        } else if ((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.isString)(button) && button === "splitter") {
          _this.buttons.push({
            "class": "wmd-spacer"
          });
        }
      });
    }).on("XEditorInit", function () {
      _this.init();
    }).on("XEditorRefresh", function () {
      var sp = _this.textarea.getScrollPosition();
      _this.setContent(_this.getContent());
      _this.textarea.setScrollPosition(sp.top, sp.left);
    }).on("XEditorAddInsertProcessor", function (event) {
      for (var _len2 = arguments.length, processors = new Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) {
        processors[_key2 - 1] = arguments[_key2];
      }
      processors.forEach(function (processor) {
        if (typeof processor === "function") {
          _this.insertProcessors.push(processor);
        }
      });
    });
  }

  /**
   * 判断是不是 Markdown 模式
   *
   * @returns {boolean}
   */
  return _createClass(XEditor, [{
    key: "isMarkdown",
    value: function isMarkdown() {
      return $('[name="markdown"]').val() === "1";
    }
  }, {
    key: "init",
    value: function init() {
      var _this2 = this;
      if (this.isInit) return;
      this.isInit = true;
      window.XEditor = this;
      $("body").append("<div id=\"aa-wrapper\"></div>").on("XEditorPreviewEnd", function () {
        return _this2.handlePreviewEnd();
      }).on("XEditorReplaceSelection", function (e, text) {
        console.log("replaceSelection", text);
        _this2.replaceSelection(text);
      });
      this.initToolbar();
      this.initScrollSync();
      $("body").trigger("XEditorPreviewEnd");
    }
  }, {
    key: "initScrollSync",
    value: function initScrollSync() {
      var _this3 = this;
      if (this.scrollSyncBound || !this.previewArea || !this.previewWrapper) {
        return;
      }
      this.scrollSyncBound = true;
      var textareaElement = this.textarea.getInstance();
      textareaElement.addEventListener("scroll", function () {
        _this3.schedulePreviewScrollSync();
      });
      window.addEventListener("scroll", function () {
        _this3.schedulePreviewScrollSync();
      });
      window.addEventListener("resize", function () {
        _this3.schedulePreviewScrollSync(true);
      });
    }
  }, {
    key: "clearPendingPreviewSyncTimers",
    value: function clearPendingPreviewSyncTimers() {
      this.pendingPreviewSyncTimers.forEach(function (timerId) {
        window.clearTimeout(timerId);
      });
      this.pendingPreviewSyncTimers = [];
    }
  }, {
    key: "schedulePreviewScrollSync",
    value: function schedulePreviewScrollSync() {
      var _this4 = this;
      var force = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      if (!this.previewArea || !this.previewWrapper) {
        return;
      }
      if (this.scrollSyncFrame) {
        window.cancelAnimationFrame(this.scrollSyncFrame);
      }
      this.scrollSyncFrame = window.requestAnimationFrame(function () {
        _this4.scrollSyncFrame = 0;
        _this4.syncPreviewScroll(force);
      });
    }
  }, {
    key: "rebuildPreviewLineAnchors",
    value: function rebuildPreviewLineAnchors() {
      if (!this.previewArea) {
        this.previewLineAnchors = [];
        return;
      }
      this.previewLineAnchors = Array.from(this.previewArea.querySelectorAll(".line[data-start][data-end]")).map(function (element) {
        var start = Number.parseInt(element.getAttribute("data-start") || "", 10);
        var end = Number.parseInt(element.getAttribute("data-end") || "", 10);
        if (Number.isNaN(start) || Number.isNaN(end)) {
          return null;
        }
        return {
          element: element,
          start: start,
          end: end
        };
      }).filter(Boolean);
    }
  }, {
    key: "isPreviewVisible",
    value: function isPreviewVisible() {
      if (!this.previewArea || !this.previewWrapper) {
        return false;
      }
      var style = window.getComputedStyle(this.previewWrapper);
      return style.display !== "none" && style.visibility !== "hidden";
    }
  }, {
    key: "getTextareaLineHeight",
    value: function getTextareaLineHeight() {
      var textareaElement = this.textarea.getInstance();
      var style = window.getComputedStyle(textareaElement);
      var lineHeight = Number.parseFloat(style.lineHeight);
      if (Number.isNaN(lineHeight)) {
        var fontSize = Number.parseFloat(style.fontSize) || 16;
        lineHeight = fontSize * 1.5;
      }
      return lineHeight > 0 ? lineHeight : 24;
    }
  }, {
    key: "getPreviewScrollContainer",
    value: function getPreviewScrollContainer() {
      if (!this.previewWrapper && !this.previewArea) {
        return null;
      }
      var candidates = [this.previewWrapper, this.previewArea].filter(Boolean);
      var selected = candidates[0] || null;
      var maxScrollableHeight = -1;
      candidates.forEach(function (element) {
        var scrollableHeight = element.scrollHeight - element.clientHeight;
        if (scrollableHeight > maxScrollableHeight) {
          maxScrollableHeight = scrollableHeight;
          selected = element;
        }
      });
      return selected;
    }
  }, {
    key: "getSourceScrollState",
    value: function getSourceScrollState() {
      var textareaElement = this.textarea.getInstance();
      var innerScrollMax = Math.max(textareaElement.scrollHeight - textareaElement.clientHeight, 0);
      if (innerScrollMax > 0) {
        return {
          top: textareaElement.scrollTop,
          max: innerScrollMax
        };
      }
      var rect = textareaElement.getBoundingClientRect();
      var docTop = window.scrollY + rect.top;
      var pageScrollTop = Math.max(window.scrollY - docTop, 0);
      var pageScrollMax = Math.max(textareaElement.offsetHeight - window.innerHeight, 0);
      return {
        top: Math.min(pageScrollTop, pageScrollMax),
        max: pageScrollMax
      };
    }
  }, {
    key: "getSourceLineState",
    value: function getSourceLineState() {
      var sourceScrollState = this.getSourceScrollState();
      var lineHeight = this.getTextareaLineHeight();
      var rawLine = sourceScrollState.top / lineHeight;
      var lineNumber = Math.max(0, Math.floor(rawLine));
      return {
        lineHeight: lineHeight,
        lineNumber: lineNumber,
        lineOffset: sourceScrollState.top - lineNumber * lineHeight,
        sourceMaxScroll: sourceScrollState.max
      };
    }
  }, {
    key: "getPreviewContainerOffsetTop",
    value: function getPreviewContainerOffsetTop(anchorElement, scrollContainer) {
      if (!anchorElement || !scrollContainer) {
        return 0;
      }
      var offsetTop = anchorElement.offsetTop;
      var current = anchorElement.offsetParent;
      while (current && current !== scrollContainer) {
        offsetTop += current.offsetTop;
        current = current.offsetParent;
      }
      return offsetTop;
    }
  }, {
    key: "getPreviewScrollableHeight",
    value: function getPreviewScrollableHeight() {
      var scrollContainer = this.getPreviewScrollContainer();
      if (!scrollContainer) {
        return 0;
      }
      return Math.max(scrollContainer.scrollHeight - scrollContainer.clientHeight, 0);
    }
  }, {
    key: "findAnchorForLine",
    value: function findAnchorForLine(lineNumber) {
      if (!this.previewLineAnchors.length) {
        return null;
      }
      var nearest = this.previewLineAnchors[0];
      var _iterator = _createForOfIteratorHelper(this.previewLineAnchors),
        _step;
      try {
        for (_iterator.s(); !(_step = _iterator.n()).done;) {
          var anchor = _step.value;
          if (lineNumber >= anchor.start && lineNumber <= anchor.end) {
            return anchor;
          }
          if (anchor.start > lineNumber) {
            return nearest;
          }
          nearest = anchor;
        }
      } catch (err) {
        _iterator.e(err);
      } finally {
        _iterator.f();
      }
      return nearest;
    }
  }, {
    key: "setPreviewScrollTop",
    value: function setPreviewScrollTop(scrollTop) {
      var _this5 = this;
      var scrollContainer = this.getPreviewScrollContainer();
      if (!scrollContainer) {
        return;
      }
      var maxScrollTop = this.getPreviewScrollableHeight();
      var nextScrollTop = Math.max(0, Math.min(scrollTop, maxScrollTop));
      this.scrollSyncLock = true;
      scrollContainer.scrollTop = nextScrollTop;
      if (this.previewWrapper && this.previewWrapper !== scrollContainer) {
        this.previewWrapper.scrollTop = nextScrollTop;
      }
      if (this.previewArea && this.previewArea !== scrollContainer) {
        this.previewArea.scrollTop = nextScrollTop;
      }
      window.requestAnimationFrame(function () {
        _this5.scrollSyncLock = false;
      });
    }
  }, {
    key: "syncPreviewByRatio",
    value: function syncPreviewByRatio() {
      var sourceScrollState = this.getSourceScrollState();
      var targetMaxScroll = this.getPreviewScrollableHeight();
      if (targetMaxScroll <= 0) {
        this.setPreviewScrollTop(0);
        return;
      }
      var ratio = sourceScrollState.max <= 0 ? 0 : sourceScrollState.top / sourceScrollState.max;
      this.setPreviewScrollTop(targetMaxScroll * ratio);
    }
  }, {
    key: "syncPreviewScroll",
    value: function syncPreviewScroll() {
      var force = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      if (!this.previewArea || !this.previewWrapper || !this.isPreviewVisible() || this.scrollSyncLock) {
        return;
      }
      var scrollContainer = this.getPreviewScrollContainer();
      if (!scrollContainer) {
        return;
      }
      var sourceLineState = this.getSourceLineState();
      var sourceMaxScroll = sourceLineState.sourceMaxScroll;
      var targetMaxScroll = this.getPreviewScrollableHeight();
      if (targetMaxScroll <= 0) {
        this.setPreviewScrollTop(0);
        return;
      }
      if (!this.previewLineAnchors.length || force) {
        this.rebuildPreviewLineAnchors();
      }
      var lineHeight = sourceLineState.lineHeight,
        lineNumber = sourceLineState.lineNumber,
        lineOffset = sourceLineState.lineOffset;
      var anchor = this.findAnchorForLine(lineNumber);
      if (!anchor) {
        this.syncPreviewByRatio();
        return;
      }
      var span = Math.max(anchor.end - anchor.start, 0);
      var anchorHeight = anchor.element.offsetHeight || Number.parseFloat(window.getComputedStyle(anchor.element).height) || lineHeight;
      var anchorTop = this.getPreviewContainerOffsetTop(anchor.element, scrollContainer);
      var lineRatio = span > 0 ? (lineNumber - anchor.start) / (span + 1) : 0;
      var intraAnchorOffset = anchorHeight * lineRatio;
      var lineOffsetRatio = lineHeight > 0 ? lineOffset / lineHeight : 0;
      var targetScrollTop = anchorTop + intraAnchorOffset + anchorHeight * lineOffsetRatio;
      if (!Number.isFinite(targetScrollTop)) {
        this.syncPreviewByRatio();
        return;
      }
      this.setPreviewScrollTop(targetScrollTop);
    }
  }, {
    key: "initToolbar",
    value: function initToolbar() {
      var _this6 = this;
      // 注册快捷键
      var registerShortcut = function registerShortcut(btn, shortcut, callback) {
        var isMac = /Mac|iPod|iPhone|iPad/.test(navigator.platform);

        // 校验快捷键格式是否正确
        var isValidShortcut = /^([a-zA-Z0-9]|F[1-9]|10|11|12|\+)+$/.test(shortcut);
        if (!isValidShortcut) {
          console.error("无效的快捷键格式");
          return false;
        }

        // 处理大小写问题
        shortcut = shortcut.toLowerCase();

        // 检查是否为Mac系统，如果是，则将ctrl替换为cmd
        if (isMac) {
          shortcut = shortcut.replace("ctrl", "cmd");
        }

        // 创建keydown事件处理函数
        var handleKeyDown = function handleKeyDown(event) {
          if (!_this6.isFocused()) {
            // 只有在textarea处于焦点状态时响应快捷键事件
            return false;
          }
          var pressedKeys = [];
          if (event.ctrlKey) {
            pressedKeys.push("ctrl");
          }
          if (event.altKey) {
            pressedKeys.push("alt");
          }
          if (event.shiftKey) {
            pressedKeys.push("shift");
          }

          // 只在有修饰键按下的情况下响应字母和数字键
          if (/[a-zA-Z0-9]/.test(event.key) && pressedKeys.length > 0) {
            pressedKeys.push(event.key.toLowerCase());
          }
          var pressedShortcut = pressedKeys.join("+");
          if (pressedShortcut === shortcut) {
            event.preventDefault(); // 阻止浏览器默认行为
            event.stopPropagation(); // 阻止事件冒泡
            event.objectTarget = btn;
            callback.call(_this6, event);
          }
        };

        // 绑定keydown事件到document
        document.addEventListener("keydown", handleKeyDown);
        return true;
      };

      // 创建按钮
      var createButton = function createButton() {
        var attrs = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
        var btn = (0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.$C)("li", _objectSpread(_objectSpread({}, attrs), {}, {
          title: attrs.name
        }), ["icon", "command", "onMounted"]);
        btn.classList.add("wmd-button");
        var icon = $(attrs.icon);
        if (icon.length) {
          btn.appendChild(icon.get(0));
        } else {
          btn.innerHTML = attrs.icon;
        }
        // 检查是否存在shortcut属性
        if (attrs.shortcut) {
          if (registerShortcut(btn, attrs.shortcut, _this6.handleHotkey)) {
            // 注册按键，成功才修改 title
            btn.title = "".concat(attrs.name, " ").concat(attrs.shortcut.toUpperCase());
          }
        }
        if ("command" in attrs) {
          if (typeof attrs.command === "function") {
            btn.addEventListener("click", function () {
              attrs.command.call(_this6, {
                target: btn
              });
            });
          } else {
            btn.setAttribute("onclick", attrs.command);
          }
        }
        return btn;
      };

      // 创建分隔符
      var createSplitter = function createSplitter() {
        var num = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
        var attrs = arguments.length > 1 ? arguments[1] : undefined;
        var el = (0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.$C)("li", _objectSpread({
          id: (0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.sprintf)("wmd-spacer%d-aaeditor", num)
        }, attrs));
        el.classList.add("wmd-spacer");
        el.classList.add((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.sprintf)("wmd-spacer%d", num));
        return el;
      };
      var getSpacerCount = function getSpacerCount() {
        return $("#wmd-button-bar-aaeditor .wmd-spacer").length;
      };
      this.toolbar = $("#wmd-button-row-aaeditor");
      this.buttons.forEach(function (btnCfg) {
        var el,
          isExists = false;
        if ("id" in btnCfg) {
          if (document.getElementById(btnCfg.id)) {
            el = document.getElementById(btnCfg.id);
            isExists = true;
            if ("icon" in btnCfg) {
              el.innerHTML = btnCfg.icon;
            }
          } else if ("name" in btnCfg) {
            if (!("icon" in btnCfg)) {
              btnCfg.icon = "<span>".concat(name, "</span>");
            }
            el = createButton(btnCfg);
          } else {
            el = createSplitter(getSpacerCount() + 1, btnCfg);
          }
        } else {
          el = createSplitter(getSpacerCount() + 1, btnCfg);
        }
        var isInserted = false,
          refNode;

        /**
         * 获取插入参考节点
         *
         * @param {string} refSel 参考节点或者参考节点选择器
         * @param {*} parent
         * @return {*|jQuery|HTMLElement}
         */
        var _getRefNode = function getRefNode(refSel, parent) {
          if (!parent) parent = _this6.toolbar;
          if ((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.isString)(refSel)) {
            if (refSel.indexOf("|") > -1) {
              var selArr = refSel.split("|");
              for (var i = 0; i < selArr.length; i++) {
                var _el = _getRefNode(selArr[i], parent);
                if (_el.length) return _el;
              }
            } else {
              return $(refSel, parent);
            }
          }
        };
        if ("insertBefore" in btnCfg) {
          refNode = _getRefNode(btnCfg.insertBefore, _this6.toolbar);
          if (refNode) {
            refNode.before(el);
            isInserted = true;
          }
        } else if ("insertAfter" in btnCfg) {
          refNode = _getRefNode(btnCfg.insertAfter, _this6.toolbar);
          if (refNode) {
            refNode.after(el);
            isInserted = true;
          }
        } else if ("remove" in btnCfg && btnCfg.remove && el.parentNode) {
          el.parentNode.removeChild(el);
        }
        if (!isInserted && !isExists) {
          _this6.toolbar.append(el);
        }
        if (typeof btnCfg.onMounted === "function") {
          btnCfg.onMounted.call(_this6, {
            target: el
          });
        }
      });
    }
  }, {
    key: "handleHotkey",
    value: function handleHotkey(_ref) {
      var objectTarget = _ref.objectTarget;
      objectTarget.click();
    }
  }, {
    key: "handlePreviewEnd",
    value: function handlePreviewEnd() {
      var _this7 = this;
      this.rebuildPreviewLineAnchors();
      this.schedulePreviewScrollSync(true);
      this.clearPendingPreviewSyncTimers();
      [60, 180, 360].forEach(function (delay) {
        var timerId = window.setTimeout(function () {
          _this7.schedulePreviewScrollSync(true);
        }, delay);
        _this7.pendingPreviewSyncTimers.push(timerId);
      });
    }

    /**
     * 获取文本框文本
     *
     * @returns {string}
     */
  }, {
    key: "getContent",
    value: function getContent() {
      return this.textarea.getContent();
    }

    /**
     * 设置文本框文本（记录到 UndoStack）
     *
     * @param {string} text
     */
  }, {
    key: "setContent",
    value: function setContent(text) {
      this.textarea.executeAndAddUndoStack("setContent", text);
    }

    /**
     * 往光标虽在位置插入文本（记录到 UndoStack）
     *
     * @param {string} text
     */
  }, {
    key: "insertText",
    value: function insertText(text) {
      this.textarea.executeAndAddUndoStack("insertText", text);
    }

    /**
     * 替换文本框选中文本（记录到 UndoStack）
     *
     * @param {string} text
     */
  }, {
    key: "replaceSelection",
    value: function replaceSelection(text) {
      if (this.getSelectedText()) {
        this.textarea.executeAndAddUndoStack("replaceSelection", text);
      } else {
        this.textarea.executeAndAddUndoStack("insertText", text);
      }
    }

    /**
     * 获取选中文本
     *
     * @return {string}
     */
  }, {
    key: "getSelectedText",
    value: function getSelectedText() {
      return this.textarea.getSelectedText();
    }

    /**
     * 获取选中区域的起止位置
     * @returns {{start: number, end: number}}
     */
  }, {
    key: "getSelection",
    value: function getSelection() {
      return this.textarea.getSelection();
    }

    /**
     * 设置选中区域的起止位置
     * @param {number} start 起始位置
     * @param {number} end 结束位置
     */
  }, {
    key: "setSelection",
    value: function setSelection(start, end) {
      this.textarea.setSelection(start, end);
    }

    /**
     * 判定是否聚焦到文本框了
     *
     * @returns {boolean}
     */
  }, {
    key: "isFocused",
    value: function isFocused() {
      return this.textarea.isFocused;
    }

    /**
     * 增加或移除选中文本的前后缀。
     * @param {string} prefix - 前缀。
     * @param {string} postfix - 后缀。
     * @param {string} defaultText - 没有选中文本时插入到前缀和后缀之间的文本。
     */
  }, {
    key: "wrapSelection",
    value: function wrapSelection(prefix, postfix) {
      var defaultText = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : "";
      var textarea = this.textarea;
      var selection = this.getSelection();
      var selectedText = this.getSelectedText();
      if (selectedText) {
        // 情况 1: 选中的文本已经是一个完整的包裹块
        if (selectedText.startsWith(prefix) && selectedText.endsWith(postfix)) {
          var _newText = selectedText.slice(prefix.length, selectedText.length - postfix.length);
          textarea.executeAndAddUndoStack("replaceSelectionText", _newText);
          // 更新光标，选中被解开包装的文本
          textarea.setSelection(selection.start, selection.start + _newText.length);
          return;
        }
        // 情况 2: 选中的文本是包裹块的“内容”部分
        // 获取紧邻选中区域前后的文本
        var textBefore = textarea.getTextInRange(0, selection.start);
        var textAfter = textarea.getTextInRange(selection.end, textarea.getContent().length);
        if (textBefore.endsWith(prefix) && textAfter.startsWith(postfix)) {
          // 确定完整的块在整个文本中的起始和结束位置
          var fullBlockStart = selection.start - prefix.length;
          var fullBlockEnd = selection.end + postfix.length;
          // 先将选择区域扩展为整个块
          textarea.setSelection(fullBlockStart, fullBlockEnd);
          // 然后用原始选择的文本（即无前后缀的内容）替换整个块
          textarea.executeAndAddUndoStack("replaceSelectionText", selectedText);
          // 更新光标，选中被解开包装的文本
          textarea.setSelection(fullBlockStart, fullBlockStart + selectedText.length);
          return;
        }
        // 情况 3: 以上都不是，执行包裹操作
        var newText = prefix + selectedText + postfix;
        textarea.executeAndAddUndoStack("replaceSelectionText", newText);
        // 更新光标，选中被包裹的原始文本
        textarea.setSelection(selection.start + prefix.length, selection.end + prefix.length);
      } else {
        // 没有选中文本时，插入带有默认文本的包裹块
        var newPrefix = textarea.isAtLineStart() ? prefix : "\n" + prefix;
        var newPostfix = textarea.isAtLineEnd() ? postfix : postfix + "\n";
        var _newText2 = newPrefix + defaultText + newPostfix;
        textarea.executeAndAddUndoStack("insertText", _newText2);

        // 更新光标，选中插入的默认文本
        var insertionStart = textarea.getSelection().start - _newText2.length;
        textarea.setSelection(insertionStart + newPrefix.length, insertionStart + newPrefix.length + defaultText.length);
      }
    }

    /**
     * 区块增加前缀。
     * @param {string} prefix - 前缀。
     * @param {string} defaultText - 没有选中文本时插入到前缀后面的文本。
     */
  }, {
    key: "blockPrefix",
    value: function blockPrefix(prefix, defaultText) {
      var textarea = this.textarea;
      var realSelectionText = textarea.getSelectedText();
      defaultText || (defaultText = "");
      if (realSelectionText.length) {
        var realSelectionArr = realSelectionText.split("\n");
        console.log(realSelectionArr);
        var newText = realSelectionArr.map(function (line, i) {
          return prefix.replace("%n", i + 1) + line;
        }).join("\n");
        // 检查选中文本的第一行是否在行首，如果不是，在 newText 前添加换行符
        if (!textarea.isAtLineStart()) {
          newText = "\n" + newText;
        }
        textarea.executeAndAddUndoStack("replaceSelectionText", newText + "\n");
      } else {
        var newPrefix = prefix.replace("%n", 1),
          _newText3 = newPrefix + defaultText + "\n",
          startPosFix = 0;
        if (!textarea.isAtLineStart()) {
          _newText3 = "\n" + _newText3;
          startPosFix = 1;
        }
        textarea.executeAndAddUndoStack("insertText", _newText3);
        textarea.setSelection(textarea.getSelection().start - _newText3.length + newPrefix.length + startPosFix, textarea.getSelection().start);
      }
    }

    /**
     * 选中文本首行前缀处理。
     * @param {string} prefix - 前缀。
     * @param {string} defaultText - 没有选中文本时插入到前缀后面的文本。
     */
  }, {
    key: "firstSelectionLinePrefix",
    value: function firstSelectionLinePrefix(prefix, defaultText) {
      var textarea = this.textarea;
      var realSelectionText = textarea.getSelectedText();
      if (realSelectionText.length) {
        var realSelectionArr = realSelectionText.split("\n");
        var firstLine = realSelectionArr[0];

        // 判断选中的第一行是否以指定前缀开头
        if (firstLine.startsWith(prefix)) {
          // 去除前缀
          firstLine = firstLine.substring(prefix.length);
        } else {
          var _textarea$getSelectio = textarea.getSelection(),
            startPos = _textarea$getSelectio.start,
            endPos = _textarea$getSelectio.end;
          if (textarea.getTextInRange(startPos - prefix.length, startPos) === prefix) {
            textarea.setSelection(startPos - prefix.length, endPos);
          } else {
            // 增加前缀
            firstLine = prefix + firstLine;
          }
        }

        // 重新组合文本
        realSelectionArr[0] = firstLine;
        var newText = realSelectionArr.join("\n");

        // 检查选中文本的第一行是否在行首，如果不是，在 newText 前添加换行符
        if (!textarea.isAtLineStart()) {
          newText = "\n" + newText;
        }
        textarea.executeAndAddUndoStack("replaceSelectionText", newText);
      } else {
        var newPrefix = prefix.replace("%n", 1);
        var _newText4,
          startPosFix = 0;
        if (textarea.isAtLineStart()) {
          _newText4 = newPrefix + defaultText;
        } else {
          _newText4 = "\n" + newPrefix + defaultText;
          startPosFix = 1;
        }
        textarea.executeAndAddUndoStack("insertText", _newText4);

        // 设置光标位置
        textarea.setSelection(textarea.getSelection().start - _newText4.length + newPrefix.length + startPosFix, textarea.getSelection().start);
      }
    }
  }, {
    key: "getInitialFormValues",
    value: function getInitialFormValues() {
      var fields = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
      var initialValues = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      var values = {};
      fields.forEach(function (field) {
        var _value;
        if (!(field !== null && field !== void 0 && field.name)) {
          return;
        }
        var value = initialValues[field.name];
        if (value === undefined && "value" in field) {
          value = field.value;
        }
        if (field.type === "checkbox") {
          values[field.name] = Boolean(value);
          return;
        }
        values[field.name] = (_value = value) !== null && _value !== void 0 ? _value : "";
      });
      return values;
    }
  }, {
    key: "resolveFieldHidden",
    value: function resolveFieldHidden() {
      var field = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var values = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      if (typeof field.hidden === "function") {
        return Boolean(field.hidden(values));
      }
      return Boolean(field.hidden);
    }
  }, {
    key: "renderFormField",
    value: function renderFormField() {
      var field = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var rawValue = arguments.length > 1 ? arguments[1] : undefined;
      if (!(field !== null && field !== void 0 && field.name)) {
        return "";
      }
      var value = rawValue !== null && rawValue !== void 0 ? rawValue : "";
      var fieldName = (0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.escapeAttribute)(field.name);
      var fieldId = (0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.escapeAttribute)(field.id || "aa-modal-field-".concat(field.name.replace(/[^a-zA-Z0-9_-]/g, "-")));
      var wrapperClasses = ["form-item"];
      if (field.type === "textarea") {
        wrapperClasses.push("for-textarea");
      }
      if (field.className) {
        wrapperClasses.push(field.className);
      }
      var labelClasses = [];
      if (field.labelClass) {
        labelClasses.push(field.labelClass);
      }
      if (field.required) {
        labelClasses.push("required");
      }
      var hiddenAttr = field.hidden ? ' hidden="true"' : "";
      var requiredAttr = field.required ? " required" : "";
      var placeholderAttr = field.placeholder ? " placeholder=\"".concat((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.escapeAttribute)(field.placeholder), "\"") : "";
      var minAttr = field.type === "number" && field.min !== undefined ? " min=\"".concat((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.escapeAttribute)(field.min), "\"") : "";
      var inputClassAttr = field.inputClass ? " class=\"".concat((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.escapeAttribute)(field.inputClass), "\"") : "";
      var label = field.label === false ? "" : "<label for=\"".concat(fieldId, "\"").concat(labelClasses.length ? " class=\"".concat((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.escapeAttribute)(labelClasses.join(" ")), "\"") : "", ">").concat((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.escapeHtml)(field.label || ""), "</label>");
      if (field.type === "select") {
        var options = (field.options || []).map(function (option) {
          var _option$label;
          var optionValue = (0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.isObject)(option) ? option.value : option;
          var optionLabel = (0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.isObject)(option) ? (_option$label = option.label) !== null && _option$label !== void 0 ? _option$label : option.value : option;
          var selectedAttr = String(optionValue) === String(value) ? " selected" : "";
          return "<option value=\"".concat((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.escapeAttribute)(optionValue), "\"").concat(selectedAttr, ">").concat((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.escapeHtml)(optionLabel), "</option>");
        }).join("");
        return "<div class=\"".concat((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.escapeAttribute)(wrapperClasses.join(" ")), "\" data-field-name=\"").concat(fieldName, "\"").concat(hiddenAttr, ">").concat(label, "<select id=\"").concat(fieldId, "\" name=\"").concat(fieldName, "\"").concat(requiredAttr).concat(inputClassAttr, ">").concat(options, "</select></div>");
      }
      if (field.type === "textarea") {
        var rowsAttr = field.rows ? " rows=\"".concat((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.escapeAttribute)(field.rows), "\"") : "";
        return "<div class=\"".concat((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.escapeAttribute)(wrapperClasses.join(" ")), "\" data-field-name=\"").concat(fieldName, "\"").concat(hiddenAttr, ">").concat(label, "<textarea id=\"").concat(fieldId, "\" name=\"").concat(fieldName, "\"").concat(requiredAttr).concat(placeholderAttr).concat(rowsAttr).concat(inputClassAttr, ">").concat((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.escapeHtml)(value), "</textarea></div>");
      }
      if (field.type === "checkbox") {
        var checkedAttr = value ? " checked" : "";
        if (field["switch"]) {
          return "<div class=\"".concat((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.escapeAttribute)(wrapperClasses.join(" ")), "\" data-field-name=\"").concat(fieldName, "\"").concat(hiddenAttr, ">").concat(label, "<input type=\"checkbox\" id=\"").concat(fieldId, "\" name=\"").concat(fieldName, "\" switch").concat(checkedAttr).concat(requiredAttr).concat(inputClassAttr, "><label class=\"form-switcher\" for=\"").concat(fieldId, "\"></label></div>");
        }
        return "<div class=\"".concat((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.escapeAttribute)(wrapperClasses.join(" ")), "\" data-field-name=\"").concat(fieldName, "\"").concat(hiddenAttr, ">").concat(label, "<input type=\"checkbox\" id=\"").concat(fieldId, "\" name=\"").concat(fieldName, "\"").concat(checkedAttr).concat(requiredAttr).concat(inputClassAttr, "></div>");
      }
      var inputType = field.type || "text";
      var valueAttr = " value=\"".concat((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.escapeAttribute)(value), "\"");
      return "<div class=\"".concat((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.escapeAttribute)(wrapperClasses.join(" ")), "\" data-field-name=\"").concat(fieldName, "\"").concat(hiddenAttr, ">").concat(label, "<input type=\"").concat((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.escapeAttribute)(inputType), "\" id=\"").concat(fieldId, "\" name=\"").concat(fieldName, "\"").concat(valueAttr).concat(requiredAttr).concat(placeholderAttr).concat(minAttr).concat(inputClassAttr, "></div>");
    }
  }, {
    key: "collectFormValues",
    value: function collectFormValues(formElm) {
      var values = {};
      if (!formElm) {
        return values;
      }
      var elements = Array.from(formElm.querySelectorAll("[name]"));
      var processedNames = new Set();
      elements.forEach(function (element) {
        var name = element.name;
        if (!name || processedNames.has(name)) {
          return;
        }
        processedNames.add(name);
        if (element instanceof HTMLInputElement && element.type === "checkbox") {
          values[name] = element.checked;
          return;
        }
        if (element instanceof HTMLInputElement && element.type === "number") {
          values[name] = element.value === "" ? "" : Number(element.value);
          return;
        }
        values[name] = element.value;
      });
      return values;
    }
  }, {
    key: "animateRequiredField",
    value: function animateRequiredField(element) {
      if (!element) {
        return;
      }
      element.classList.add("required-animate");
      window.setTimeout(function () {
        element.classList.remove("required-animate");
      }, 800);
    }
  }, {
    key: "validateForm",
    value: function validateForm(formElm) {
      var _this8 = this;
      if (!formElm) {
        return true;
      }
      var isValid = true;
      var elements = Array.from(formElm.querySelectorAll("[name]"));
      elements.forEach(function (element) {
        if (!element.required || element.closest('.form-item[hidden="true"]')) {
          return;
        }
        var isCheckbox = element instanceof HTMLInputElement && element.type === "checkbox";
        var hasValue = isCheckbox ? element.checked : String(element.value || "").trim() !== "";
        if (!hasValue) {
          isValid = false;
          _this8.animateRequiredField(element);
        }
      });
      return isValid;
    }
  }, {
    key: "syncFormFieldVisibility",
    value: function syncFormFieldVisibility(modalElm) {
      var _this9 = this;
      var fields = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
      var values = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
      fields.forEach(function (field) {
        if (!(field !== null && field !== void 0 && field.name)) {
          return;
        }
        var wrapper = Array.from(modalElm.querySelectorAll(".form-item")).find(function (item) {
          return item.dataset.fieldName === field.name;
        });
        if (!wrapper) {
          return;
        }
        var isHidden = _this9.resolveFieldHidden(field, values);
        wrapper.setAttribute("hidden", isHidden ? "true" : "false");
        wrapper.classList.toggle("hidden", isHidden);
      });
    }
  }, {
    key: "openFormModal",
    value: function openFormModal() {
      var _this10 = this;
      var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var modalOptions = Object.assign({
        title: "标题",
        fields: [],
        initialValues: {},
        onSubmit: function onSubmit() {
          return true;
        },
        onChange: function onChange() {},
        validate: function validate() {
          return true;
        },
        showFooter: true
      }, options);
      var initialValues = this.getInitialFormValues(modalOptions.fields, modalOptions.initialValues);
      var innerHTML = modalOptions.fields.map(function (field) {
        return _this10.renderFormField(_objectSpread(_objectSpread({}, field), {}, {
          hidden: _this10.resolveFieldHidden(field, initialValues)
        }), initialValues[field.name]);
      }).join("");
      this.openModal({
        title: modalOptions.title,
        innerHTML: innerHTML,
        showFooter: modalOptions.showFooter,
        checkEmptyOnConfirm: true,
        handle: function handle(modalElm) {
          _this10.syncFormFieldVisibility(modalElm, modalOptions.fields, initialValues);
          modalOptions.onChange.call(_this10, modalElm, _objectSpread({}, initialValues));
        },
        change: function change(modalElm, values) {
          _this10.syncFormFieldVisibility(modalElm, modalOptions.fields, values);
          modalOptions.onChange.call(_this10, modalElm, values);
        },
        confirm: function confirm(modalElm, values) {
          if (modalOptions.validate.call(_this10, modalElm, values) === false) {
            return false;
          }
          return modalOptions.onSubmit.call(_this10, modalElm, values);
        }
      });
    }

    /**
     * 打开模态框 (参考 Joe 主题)
     * @param options
     */
  }, {
    key: "openModal",
    value: function openModal() {
      var _this11 = this;
      var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var _modalOptions = {
        title: "标题",
        innerHTML: "内容",
        showFooter: true,
        checkEmptyOnConfirm: true,
        change: function change(modal, params) {},
        confirm: function confirm(modal, params) {
          return true;
        },
        cancel: function cancel(event) {},
        handle: function handle(modal) {},
        callback: function callback(modal) {}
      };
      var modalOptions = Object.assign(_modalOptions, options);
      var contentWrap = modalOptions.checkEmptyOnConfirm ? '<form class="params"></form>' : "";
      if ($("#aa-modal").length < 1) {
        $("#aa-wrapper").append("<div id=\"aa-modal\" class=\"aa-modal\">\n    <div class=\"aa-modal-frame\">\n    <div class=\"aa-modal-header\">\n        <div class=\"aa-modal-header-title\"></div><div class=\"aa-modal-header-close\"><i class=\"close-icon\"></i></div>\n</div>\n    <div class=\"aa-modal-body\">\n        ".concat(contentWrap, "\n    </div>\n    <div class=\"aa-modal-footer\">\n        <button type=\"button\" class=\"aa-modal-footer-button aa-modal-footer-cancel\">\u53D6\u6D88</button><button type=\"button\" class=\"aa-modal-footer-button aa-modal-footer-confirm\">\u786E\u5B9A</button>\n    </div>\n</div>\n</div>"));
      }
      $(".aa-modal-header-title").text(modalOptions.title);
      if (modalOptions.checkEmptyOnConfirm) {
        var _form = $(".aa-modal-body .params").empty();
        if ((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.isElement)(modalOptions.innerHTML)) {
          _form.append(modalOptions.innerHTML);
        } else {
          _form.html(modalOptions.innerHTML);
        }
      } else {
        var body = $(".aa-modal-body").empty();
        if ((0,_utils_functions__WEBPACK_IMPORTED_MODULE_0__.isElement)(modalOptions.innerHTML)) {
          body.append(modalOptions.innerHTML);
        } else {
          body.html(modalOptions.innerHTML);
        }
      }
      var modalElm = $("#aa-modal").get(0);
      modalOptions.showFooter ? $(".aa-modal-footer").show() : $(".aa-modal-footer").hide();
      $("body").addClass("no-scroll");
      modalOptions.handle.call(this, modalElm);
      $(".aa-modal-footer-confirm").on("click", function () {
        var params = {};
        var flag = true;
        if (modalOptions.checkEmptyOnConfirm) {
          var formElm = $("#aa-modal .aa-modal-body .params").get(0);
          params = _this11.collectFormValues(formElm);
          flag = _this11.validateForm(formElm);
        }
        if (flag && modalOptions.confirm.call(_this11, modalElm, params)) {
          $("#aa-modal").remove();
          $("body").removeClass("no-scroll");
          modalOptions.callback.call(_this11, modalElm);
        }
      });
      $(".aa-modal-header-close").on("click", function (event) {
        modalOptions.cancel.call(_this11, event);
        $("#aa-modal").removeClass("active");
        setTimeout(function () {
          $("#aa-modal").remove();
          $("body").removeClass("no-scroll");
        }, 300);
      });
      $(".aa-modal-footer-cancel").on("click", function (event) {
        $("#aa-modal").removeClass("active");
        modalOptions.cancel.call(modalElm, event);
        setTimeout(function () {
          $("#aa-modal").remove();
          $("body").removeClass("no-scroll");
        }, 300);
      });
      var form = $(".params", modalElm);
      $("input,select,textarea", form).on("change input", function () {
        var params = _this11.collectFormValues(form.get(0));
        modalOptions.change.call(_this11, modalElm, params);
      });
      $("#aa-modal").addClass("active");
    }

    /**
     * 来自 Wordpress
     * https://regex101.com/r/ja0b1p/1
     * $0→code $2→tag $3→attr $5→text
     * @param tag
     * @returns {RegExp}
     */
  }, {
    key: "getShortCodeRegex",
    value: function getShortCodeRegex(tag) {
      return new RegExp("\\[(\\[?)(" + tag + ")(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)", "g");
    }
  }]);
}();
new XEditor("#text", "#wmd-preview-aaeditor");

/***/ }),

/***/ "./assets/src/utils/functions.js":
/*!***************************************!*\
  !*** ./assets/src/utils/functions.js ***!
  \***************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   $A: () => (/* binding */ $A),
/* harmony export */   $C: () => (/* binding */ $C),
/* harmony export */   escapeAttribute: () => (/* binding */ escapeAttribute),
/* harmony export */   escapeHtml: () => (/* binding */ escapeHtml),
/* harmony export */   isElement: () => (/* binding */ isElement),
/* harmony export */   isObject: () => (/* binding */ isObject),
/* harmony export */   isString: () => (/* binding */ isString),
/* harmony export */   sprintf: () => (/* binding */ sprintf)
/* harmony export */ });
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
/**
 * 格式化字符串
 *
 * @param {string} format 需要格式化额字符串
 * @param {*} args 替换内容
 * @returns {*}
 */
function sprintf(format) {
  for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
    args[_key - 1] = arguments[_key];
  }
  var index = 0;
  return format.replace(/%[sd]/g, function (match) {
    if (index >= args.length) {
      return match;
    }
    var arg = args[index++];
    if (match === '%s') {
      return String(arg);
    } else if (match === '%d') {
      if (typeof arg === 'number') {
        return String(arg);
      } else {
        throw new Error("Argument ".concat(index, " is not a number."));
      }
    }
    return match;
  });
}
function escapeHtml() {
  var value = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "";
  return String(value).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#39;");
}
function escapeAttribute() {
  var value = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "";
  return escapeHtml(value).replace(/`/g, "&#96;");
}
function $C(tag, attrs) {
  var skipAttrs = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : [];
  var el = document.createElement(tag);
  return $A(el, attrs, skipAttrs);
}
function $A(el) {
  var attrs = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var skipAttrs = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : [];
  var setAttr, setText, setHTML;
  if ("attr" in el) {
    setAttr = function setAttr(name, val) {
      el.attr(name, "" + val);
    };
    setText = function setText(val) {
      el.text(val);
    };
    setHTML = function setHTML(val) {
      el.html(val);
    };
  } else {
    setAttr = function setAttr(name, val) {
      el.setAttribute(name, "" + val);
    };
    setText = function setText(val) {
      el.innerText = val;
    };
    setHTML = function setHTML(val) {
      el.innerHTML = val;
    };
  }
  for (var p in attrs) {
    if (!skipAttrs.includes(p)) {
      // @ts-ignore
      var attr = attrs[p];
      if (typeof attr === "function") {
        attr = attr.toString();
      }
      switch (p) {
        case 'innerText':
          setText(attr);
          break;
        case 'innerHTML':
          setHTML(attr);
          break;
        case 'style':
          if (_typeof(attr) === "object") {
            var style = Object.entries(attr).map(function (en) {
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
function isObject(obj) {
  return Object.prototype.toString.call(obj) === '[object Object]';
}
function isString(str) {
  return typeof str === "string";
}
function isElement(obj) {
  return obj instanceof HTMLElement;
}

/***/ }),

/***/ "./assets/src/utils/textarea.js":
/*!**************************************!*\
  !*** ./assets/src/utils/textarea.js ***!
  \**************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ TextAreaUtils)
/* harmony export */ });
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
/**
 * TextAreaUtils 类用于管理文本区域（Textarea）的操作和事件绑定。
 */
var TextAreaUtils = /*#__PURE__*/function () {
  /**
   * 创建一个 TextAreaUtils 的实例。
   * @param {string} textareaSelector - 选择器，用于选择要管理的文本区域。
   */
  function TextAreaUtils(textareaSelector) {
    _classCallCheck(this, TextAreaUtils);
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
  return _createClass(TextAreaUtils, [{
    key: "executeAndAddUndoStack",
    value: function executeAndAddUndoStack(operate) {
      var _this = this;
      for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
        args[_key - 1] = arguments[_key];
      }
      var fn = function fn() {
        // 找到对应的函数名并执行
        if (operate === 'setContent') {
          _this.setContent.apply(_this, args);
        } else if (operate === 'insertText') {
          _this.insertText.apply(_this, args);
        } else if (operate === 'replaceSelectionText') {
          _this.replaceSelectionText.apply(_this, args);
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
  }, {
    key: "getInstance",
    value: function getInstance() {
      return this.textarea;
    }

    /**
     * 绑定事件处理程序。
     * @param {string} eventType - 要绑定的事件类型，例如 'input', 'focus', 'blur' 等。
     * @param {Function} handler - 事件处理程序函数。
     */
  }, {
    key: "on",
    value: function on(eventType, handler) {
      this.textarea.addEventListener(eventType, handler);
    }

    /**
     * 解绑事件处理程序。
     * @param {string} eventType - 要解绑的事件类型。
     * @param {Function} handler - 要解绑的事件处理程序函数。
     */
  }, {
    key: "off",
    value: function off(eventType, handler) {
      this.textarea.removeEventListener(eventType, handler);
    }

    /**
     * 获取文本区域的内容。
     * @returns {string} 返回文本区域的当前内容。
     */
  }, {
    key: "getContent",
    value: function getContent() {
      return this.textarea.value;
    }

    /**
     * 设置文本区域的内容。
     * @param {string} content - 要设置的新内容。
     */
  }, {
    key: "setContent",
    value: function setContent(content) {
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
  }, {
    key: "lock",
    value: function lock() {
      this.locked = true;
      this.textarea.setAttribute('readonly', 'readonly');
    }

    /**
     * 取消锁定文本区域，允许内容修改
     */
  }, {
    key: "unlock",
    value: function unlock() {
      this.locked = false;
      this.textarea.removeAttribute('readonly');
    }

    /**
     * 插入文本到当前光标的位置，成功返回真，否则返回假。
     * @param {string} text - 要插入的文本。
     * @returns {boolean} 如果插入操作成功，则返回 true；否则返回 false。
     */
  }, {
    key: "insertText",
    value: function insertText(text) {
      if (this.locked) return false;
      var startPos = this.textarea.selectionStart;
      var endPos = this.textarea.selectionEnd;
      var currentText = this.textarea.value;
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
  }, {
    key: "replaceSelectionText",
    value: function replaceSelectionText(text) {
      if (this.locked) return false;
      var startPos = this.textarea.selectionStart;
      var endPos = this.textarea.selectionEnd;
      var currentText = this.textarea.value;
      this.textarea.value = currentText.slice(0, startPos) + text + currentText.slice(endPos);
      this.textarea.selectionStart = startPos;
      this.textarea.selectionEnd = startPos + text.length;
      this.afterOperate(); // 触发 input 事件
      return true;
    }

    /**
     * 获取选中文本。
     * @returns {string} 返回当前选中的文本。
     */
  }, {
    key: "getSelectedText",
    value: function getSelectedText() {
      return this.textarea.value.substring(this.textarea.selectionStart, this.textarea.selectionEnd);
    }

    /**
     * 获取当前选中文本的起始和结束位置。
     * @returns {{start: number, end: number}} 返回包含选中文本起始和结束位置的对象。
     */
  }, {
    key: "getSelection",
    value: function getSelection() {
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
  }, {
    key: "setSelection",
    value: function setSelection(start, end) {
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
  }, {
    key: "getLineText",
    value: function getLineText(lineNumber) {
      var text = this.textarea.value;
      var lines = text.split('\n');
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
  }, {
    key: "getTextInRange",
    value: function getTextInRange(start, end) {
      var text = this.textarea.value;
      return text.substring(start, end);
    }

    /**
     * 获取当前行的文本。
     * @param {number} start - 开始位置，默认为行首，即 0。
     * @param {number} end - 结束位置，默认为该行的长度。
     * @returns {string} 返回从指定位置开始到结束位置的文本。
     */
  }, {
    key: "getCurrentLineText",
    value: function getCurrentLineText() {
      var start = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
      var end = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : this.textarea.selectionStart;
      var text = this.textarea.value;
      var lineStart = start;
      var lineEnd = end;
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
  }, {
    key: "replaceCurrentLine",
    value: function replaceCurrentLine(newText) {
      if (this.locked) return false;
      var startPos = this.textarea.selectionStart;
      var endPos = this.textarea.selectionEnd;
      var text = this.textarea.value;
      var lineStart = startPos;
      var lineEnd = endPos;
      while (lineStart > 0 && text[lineStart - 1] !== '\n') {
        lineStart--;
      }
      while (lineEnd < text.length && text[lineEnd] !== '\n') {
        lineEnd++;
      }
      var newTextBefore = text.substring(0, lineStart);
      var newTextAfter = text.substring(lineEnd);
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
  }, {
    key: "getCursorPosition",
    value: function getCursorPosition() {
      var startPos = this.textarea.selectionStart;
      var text = this.textarea.value;
      var lineCount = 1;
      var columnCount = 1;
      for (var i = 0; i < startPos; i++) {
        if (text[i] === '\n') {
          lineCount++;
          columnCount = 1;
        } else {
          columnCount++;
        }
      }
      return {
        line: lineCount,
        column: columnCount
      };
    }

    /**
     * 是否在行首。
     * @returns {boolean} 如果光标在行首，则返回 true；否则返回 false。
     */
  }, {
    key: "isAtLineStart",
    value: function isAtLineStart() {
      var startPos = this.textarea.selectionStart;
      var text = this.textarea.value;
      return startPos === 0 || text[startPos - 1] === '\n';
    }

    /**
     * 是否在行尾。
     * @returns {boolean} 如果光标在行尾，则返回 true；否则返回 false。
     */
  }, {
    key: "isAtLineEnd",
    value: function isAtLineEnd() {
      var endPos = this.textarea.selectionEnd;
      var text = this.textarea.value;
      return endPos === text.length || text[endPos] === '\n';
    }

    /**
     * 是否在文本开头。
     * @returns {boolean} 如果光标在文本开头，则返回 true；否则返回 false。
     */
  }, {
    key: "isAtStart",
    value: function isAtStart() {
      return this.textarea.selectionStart === 0;
    }

    /**
     * 是否在文本结尾。
     * @returns {boolean} 如果光标在文本结尾，则返回 true；否则返回 false。
     */
  }, {
    key: "isAtEnd",
    value: function isAtEnd() {
      return this.textarea.selectionEnd === this.textarea.value.length;
    }

    // 初始化焦点事件
  }, {
    key: "initFocusEvents",
    value: function initFocusEvents() {
      var _this2 = this;
      this.textarea.addEventListener('focus', function () {
        _this2.isFocused = true;
      });
      this.textarea.addEventListener('blur', function () {
        _this2.isFocused = false;
      });
    }

    // 触发 input 事件
  }, {
    key: "afterOperate",
    value: function afterOperate() {
      $(this.textarea).trigger('input');
      this.textarea.focus();
    }

    /**
     * 获取文本区域的滚动条位置。
     * @returns {{top: number, left: number}} 返回包含滚动条垂直位置和水平位置的对象。
     */
  }, {
    key: "getScrollPosition",
    value: function getScrollPosition() {
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
  }, {
    key: "setScrollPosition",
    value: function setScrollPosition(top, left) {
      this.textarea.scrollTop = top;
      this.textarea.scrollLeft = left;
    }
  }]);
}();


/***/ }),

/***/ "./assets/src/main.scss":
/*!******************************!*\
  !*** ./assets/src/main.scss ***!
  \******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./assets/src/front.scss":
/*!*******************************!*\
  !*** ./assets/src/front.scss ***!
  \*******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./assets/src/editor/hljs.scss":
/*!*************************************!*\
  !*** ./assets/src/editor/hljs.scss ***!
  \*************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./assets/src/content.scss":
/*!*********************************!*\
  !*** ./assets/src/content.scss ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./assets/src/config/config.scss":
/*!***************************************!*\
  !*** ./assets/src/config/config.scss ***!
  \***************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"/js/main": 0,
/******/ 			"css/config": 0,
/******/ 			"css/content": 0,
/******/ 			"css/hljs": 0,
/******/ 			"css/front": 0,
/******/ 			"css/main": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunkaaeditor"] = self["webpackChunkaaeditor"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	__webpack_require__.O(undefined, ["css/config","css/content","css/hljs","css/front","css/main"], () => (__webpack_require__("./assets/src/main.js")))
/******/ 	__webpack_require__.O(undefined, ["css/config","css/content","css/hljs","css/front","css/main"], () => (__webpack_require__("./assets/src/main.scss")))
/******/ 	__webpack_require__.O(undefined, ["css/config","css/content","css/hljs","css/front","css/main"], () => (__webpack_require__("./assets/src/front.scss")))
/******/ 	__webpack_require__.O(undefined, ["css/config","css/content","css/hljs","css/front","css/main"], () => (__webpack_require__("./assets/src/editor/hljs.scss")))
/******/ 	__webpack_require__.O(undefined, ["css/config","css/content","css/hljs","css/front","css/main"], () => (__webpack_require__("./assets/src/content.scss")))
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["css/config","css/content","css/hljs","css/front","css/main"], () => (__webpack_require__("./assets/src/config/config.scss")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;