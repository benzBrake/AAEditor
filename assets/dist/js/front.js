/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/src/utils/DetailsAnimator.js":
/*!*********************************************!*\
  !*** ./assets/src/utils/DetailsAnimator.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ DetailsAnimator)
/* harmony export */ });
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var DetailsAnimator = /*#__PURE__*/function () {
  function DetailsAnimator(el) {
    var _this = this;
    _classCallCheck(this, DetailsAnimator);
    this.el = el;
    this.summary = el.querySelector('summary');
    this.content = el.querySelector('.details-content');
    this.animation = null;
    this.isClosing = false;
    this.isExpanding = false;
    this.summary.addEventListener('click', function (e) {
      return _this.onClick(e);
    });
  }
  return _createClass(DetailsAnimator, [{
    key: "onClick",
    value: function onClick(e) {
      e.preventDefault();
      if (this.isClosing || this.isExpanding) {
        return;
      }
      if (this.el.open) {
        this.shrink();
      } else {
        this.expand();
      }
    }
  }, {
    key: "shrink",
    value: function shrink() {
      var _this2 = this;
      this.isClosing = true;

      // 获取当前内容区域的高度作为动画起始点
      var startHeight = "".concat(this.content.offsetHeight, "px");
      var endHeight = '0px';
      if (this.animation) {
        this.animation.cancel();
      }

      // 只对内容区域进行动画
      this.animation = this.content.animate({
        height: [startHeight, endHeight]
      }, {
        duration: 300,
        easing: 'ease-out'
      });
      this.animation.onfinish = function () {
        // 动画结束后，关闭 <details>
        _this2.el.removeAttribute('open');
        _this2.animation = null;
        _this2.isClosing = false;
        // 移除内联 height 样式，以便下次能正确计算
        _this2.content.style.height = '';
      };
    }
  }, {
    key: "expand",
    value: function expand() {
      var _this3 = this;
      this.isExpanding = true;
      var startHeight = '0px';

      // 先把 <details> 设为 open，这样才能计算出内容区的 scrollHeight
      this.el.setAttribute('open', '');

      // 使用 scrollHeight 获取内容完全展开后的高度（包括 padding）
      var endHeight = "".concat(this.content.scrollHeight, "px");
      if (this.animation) {
        this.animation.cancel();
      }

      // 设置 overflow: hidden 防止内容在动画期间溢出
      this.content.style.overflow = 'hidden';

      // 对内容区域进行动画
      this.animation = this.content.animate({
        height: [startHeight, endHeight]
      }, {
        duration: 300,
        easing: 'ease-out'
      });
      this.animation.onfinish = function () {
        _this3.animation = null;
        _this3.isExpanding = false;
        // 动画结束后，移除内联样式，允许内容自适应高度
        _this3.content.style.height = '';
        _this3.content.style.overflow = '';
      };
    }
  }]);
}();


/***/ }),

/***/ "./assets/src/utils/fenceGallery.js":
/*!******************************************!*\
  !*** ./assets/src/utils/fenceGallery.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initFenceGrid: () => (/* binding */ initFenceGrid),
/* harmony export */   initFenceMasonry: () => (/* binding */ initFenceMasonry)
/* harmony export */ });
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function parseNumber(value, fallback) {
  var parsed = Number.parseInt(String(value !== null && value !== void 0 ? value : ""), 10);
  return Number.isFinite(parsed) ? parsed : fallback;
}
function isIgnorableWhitespace(node) {
  return (node === null || node === void 0 ? void 0 : node.nodeType) === Node.TEXT_NODE && node.textContent.trim() === "";
}
function isLineMarker(node) {
  return (node === null || node === void 0 ? void 0 : node.nodeType) === Node.ELEMENT_NODE && node.classList.contains("line") && node.hasAttribute("data-start") && node.hasAttribute("data-end");
}
function gridItemHasContent(nodes) {
  return nodes.some(function (node) {
    if (isLineMarker(node)) {
      return false;
    }
    if (node.nodeType === Node.TEXT_NODE) {
      return node.textContent.trim() !== "";
    }
    return node.nodeType === Node.ELEMENT_NODE;
  });
}
function createGridItems(nodes) {
  var items = [];
  var pendingMarkers = [];
  var currentNodes = [];
  var flush = function flush() {
    if (!gridItemHasContent(currentNodes)) {
      pendingMarkers = [];
      currentNodes = [];
      return;
    }
    var item = document.createElement("div");
    item.className = "grid-item";
    pendingMarkers.forEach(function (marker) {
      return item.appendChild(marker);
    });
    currentNodes.forEach(function (node) {
      return item.appendChild(node);
    });
    items.push(item);
    pendingMarkers = [];
    currentNodes = [];
  };
  nodes.forEach(function (node) {
    if (isIgnorableWhitespace(node)) {
      return;
    }
    if (node.nodeType === Node.ELEMENT_NODE && node.tagName === "BR") {
      flush();
      return;
    }
    if (isLineMarker(node)) {
      if (gridItemHasContent(currentNodes)) {
        flush();
      }
      pendingMarkers.push(node.cloneNode(true));
      return;
    }
    currentNodes.push(node.cloneNode(true));
  });
  flush();
  return items;
}
function normalizeGridFence() {
  var root = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : document;
  if (!root || typeof root.querySelectorAll !== "function") {
    return;
  }
  root.querySelectorAll(".fence-grid .fence-content").forEach(function (content) {
    var children = Array.from(content.childNodes);
    var hasWrappedParagraph = children.some(function (child) {
      return child.nodeType === Node.ELEMENT_NODE && child.tagName === "P";
    });
    var hasDirectLineMarker = children.some(function (child) {
      return isLineMarker(child);
    });
    if (!hasWrappedParagraph && !hasDirectLineMarker) {
      return;
    }
    if (!hasWrappedParagraph && hasDirectLineMarker) {
      var items = createGridItems(children);
      if (items.length) {
        content.replaceChildren.apply(content, _toConsumableArray(items));
      }
      return;
    }
    var fragment = document.createDocumentFragment();
    var changed = false;
    children.forEach(function (child) {
      if (isIgnorableWhitespace(child)) {
        return;
      }
      if (child.nodeType === Node.ELEMENT_NODE && child.tagName === "P") {
        var _items = createGridItems(Array.from(child.childNodes));
        _items.forEach(function (item) {
          return fragment.appendChild(item);
        });
        changed = true;
        return;
      }
      if (child.nodeType === Node.ELEMENT_NODE && child.tagName === "BR") {
        changed = true;
        return;
      }
      fragment.appendChild(child.cloneNode(true));
    });
    if (!changed) {
      return;
    }
    content.replaceChildren(fragment);
  });
}
function normalizeMasonryFence() {
  var root = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : document;
  if (!root || typeof root.querySelectorAll !== "function") {
    return;
  }
  root.querySelectorAll(".fence-masonry .fence-content").forEach(function (content) {
    if (content.querySelector(":scope > .masonry-wrapper")) {
      return;
    }
    var items = [];
    var buffer = [];
    var flushBuffer = function flushBuffer() {
      var hasContent = buffer.some(function (node) {
        if (node.nodeType === Node.ELEMENT_NODE) {
          return true;
        }
        return node.nodeType === Node.TEXT_NODE && node.textContent.trim() !== "";
      });
      if (hasContent) {
        items.push(buffer);
      }
      buffer = [];
    };
    Array.from(content.childNodes).forEach(function (child) {
      if (child.nodeType === Node.TEXT_NODE) {
        if (child.textContent.trim() === "") {
          flushBuffer();
          return;
        }
        buffer.push(child.cloneNode(true));
        return;
      }
      if (child.nodeType !== Node.ELEMENT_NODE) {
        return;
      }
      if (child.tagName === "BR") {
        flushBuffer();
        return;
      }
      if (child.tagName === "P") {
        flushBuffer();
        var paragraphNodes = Array.from(child.childNodes).filter(function (node) {
          return !isIgnorableWhitespace(node);
        }).map(function (node) {
          return node.cloneNode(true);
        });
        if (paragraphNodes.length) {
          items.push(paragraphNodes);
        }
        return;
      }
      flushBuffer();
      items.push([child.cloneNode(true)]);
    });
    flushBuffer();
    if (!items.length) {
      return;
    }
    var wrapper = document.createElement("div");
    wrapper.className = "masonry-wrapper";
    items.forEach(function (nodes) {
      var item = document.createElement("div");
      item.className = "masonry-item";
      var inner = document.createElement("div");
      inner.className = "masonry-item-inner";
      nodes.forEach(function (node) {
        return inner.appendChild(node);
      });
      item.appendChild(inner);
      wrapper.appendChild(item);
    });
    content.replaceChildren(wrapper);
  });
}
function initFenceGrid() {
  var root = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : document;
  normalizeGridFence(root);
  if (!root || typeof root.querySelectorAll !== "function") {
    return;
  }
  root.querySelectorAll(".fence-grid").forEach(function (el) {
    if (el.dataset.columns) {
      el.style.setProperty("--columns", "repeat(".concat(el.dataset.columns, ", 1fr)"));
    }
    if (el.dataset.gap) {
      el.style.setProperty("--gap", el.dataset.gap);
    }
    if (el.dataset.minWidth) {
      el.style.setProperty("--min-width", el.dataset.minWidth);
    }
  });
}
function initFenceMasonry() {
  var root = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : document;
  normalizeMasonryFence(root);
  var MasonryCtor = window.Masonry;
  if (typeof MasonryCtor !== "function" || !root || typeof root.querySelectorAll !== "function") {
    return;
  }
  root.querySelectorAll(".fence-masonry .masonry-wrapper").forEach(function (wrapper) {
    if (!(wrapper instanceof Element)) {
      return;
    }
    var items = Array.from(wrapper.children).filter(function (child) {
      return child.classList.contains("masonry-item");
    });
    if (!items.length) {
      return;
    }
    var fence = wrapper.closest(".fence-masonry");
    var gap = Math.max(parseNumber(fence === null || fence === void 0 ? void 0 : fence.dataset.gap, 16), 0);
    var columns = Math.max(parseNumber(fence === null || fence === void 0 ? void 0 : fence.dataset.columns, 3), 1);
    var layout = function layout() {
      var containerWidth = wrapper.clientWidth;
      if (!containerWidth) {
        return;
      }
      var columnWidth = Math.max((containerWidth - gap * (columns - 1)) / columns, 0);
      wrapper.style.setProperty("--masonry-gap", "".concat(gap, "px"));
      wrapper.style.setProperty("--masonry-columns", String(columns));
      items.forEach(function (item) {
        item.style.width = "".concat(columnWidth, "px");
        item.style.marginBottom = "".concat(gap, "px");
      });
      if (!wrapper._aaMasonry) {
        wrapper._aaMasonry = new MasonryCtor(wrapper, {
          itemSelector: ".masonry-item",
          columnWidth: columnWidth,
          gutter: gap,
          percentPosition: false,
          transitionDuration: 0
        });
        return;
      }
      wrapper._aaMasonry.options.columnWidth = columnWidth;
      wrapper._aaMasonry.options.gutter = gap;
      wrapper._aaMasonry.reloadItems();
      wrapper._aaMasonry.layout();
    };
    if (wrapper.dataset.masonryInitialized !== "true") {
      wrapper.dataset.masonryInitialized = "true";
      if (typeof ResizeObserver === "function") {
        var observer = new ResizeObserver(function () {
          layout();
        });
        observer.observe(wrapper);
        wrapper._aaMasonryResizeObserver = observer;
      } else {
        window.addEventListener("resize", layout);
      }
      wrapper.querySelectorAll("img").forEach(function (img) {
        if (img.complete) {
          return;
        }
        var refresh = function refresh() {
          return layout();
        };
        img.addEventListener("load", refresh, {
          once: true
        });
        img.addEventListener("error", refresh, {
          once: true
        });
      });
    }
    layout();
  });
}

/***/ }),

/***/ "./assets/src/utils/tabs.js":
/*!**********************************!*\
  !*** ./assets/src/utils/tabs.js ***!
  \**********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initFenceTabs: () => (/* binding */ initFenceTabs)
/* harmony export */ });
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function splitClasses() {
  var value = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "";
  return String(value).split(/\s+/).map(function (item) {
    return item.trim();
  }).filter(Boolean);
}
function getDirectChildByClass(element, className) {
  if (!(element instanceof Element)) {
    return null;
  }
  return Array.from(element.children).find(function (child) {
    return child.classList.contains(className);
  }) || null;
}
function resolveTabs() {
  var root = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : document;
  var tabs = [];
  if (root instanceof Element && root.classList.contains("fence-tabs")) {
    tabs.push(root);
  }
  if (typeof root.querySelectorAll === "function") {
    tabs.push.apply(tabs, _toConsumableArray(root.querySelectorAll(".fence-tabs")));
  }
  return Array.from(new Set(tabs));
}
function applyStateClasses(element) {
  var activeClasses = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
  var inactiveClasses = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : [];
  var isActive = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
  if (!(element instanceof Element)) {
    return;
  }
  activeClasses.forEach(function (className) {
    element.classList.toggle(className, isActive);
  });
  inactiveClasses.forEach(function (className) {
    element.classList.toggle(className, !isActive);
  });
}
function initTabsInstance(tabs) {
  var _window$matchMedia, _window;
  if (!(tabs instanceof Element)) {
    return;
  }
  var nav = getDirectChildByClass(tabs, "fence-tabs-nav");
  var panelsWrapper = getDirectChildByClass(tabs, "fence-tabs-panels");
  if (!nav || !panelsWrapper) {
    return;
  }
  var buttons = Array.from(nav.querySelectorAll(".fence-tabs-tab"));
  var panels = Array.from(panelsWrapper.querySelectorAll(".fence-tabs-panel"));
  if (!buttons.length || buttons.length !== panels.length) {
    return;
  }
  var activeClasses = splitClasses(tabs.dataset.tabActiveClasses);
  var inactiveClasses = splitClasses(tabs.dataset.tabInactiveClasses);
  var panelActiveClasses = ["fence-tabs-panel-active", "tab-panel-active"];
  var panelInactiveClasses = ["fence-tabs-panel-inactive", "tab-panel-inactive"];
  var reduceMotion = (_window$matchMedia = (_window = window).matchMedia) === null || _window$matchMedia === void 0 || (_window$matchMedia = _window$matchMedia.call(_window, "(prefers-reduced-motion: reduce)")) === null || _window$matchMedia === void 0 ? void 0 : _window$matchMedia.matches;
  var activate = function activate(index) {
    var focusButton = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
    var animatePanel = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;
    var previousIndex = Number.parseInt(tabs.dataset.tabsActiveIndex || "", 10);
    var shouldAnimate = animatePanel && !reduceMotion && previousIndex !== index && tabs.dataset.tabsReady === "true";
    buttons.forEach(function (button, buttonIndex) {
      var isActive = buttonIndex === index;
      button.setAttribute("aria-selected", isActive ? "true" : "false");
      button.setAttribute("tabindex", isActive ? "0" : "-1");
      applyStateClasses(button, activeClasses, inactiveClasses, isActive);
      if (focusButton && isActive) {
        button.focus();
      }
    });
    panels.forEach(function (panel, panelIndex) {
      var isActive = panelIndex === index;
      panel.classList.remove("fence-tabs-panel-no-animation");
      panel.hidden = !isActive;
      panel.setAttribute("aria-hidden", isActive ? "false" : "true");
      applyStateClasses(panel, panelActiveClasses, panelInactiveClasses, isActive);
      if (isActive && !shouldAnimate) {
        panel.classList.add("fence-tabs-panel-no-animation");
      }
    });
    tabs.dataset.tabsActiveIndex = String(index);
  };
  buttons.forEach(function (button, index) {
    if (button.dataset.tabsBound === "true") {
      return;
    }
    button.dataset.tabsBound = "true";
    button.addEventListener("click", function () {
      activate(index, false);
    });
  });
  var activeIndex = Number.parseInt(tabs.dataset.tabsActiveIndex || "", 10);
  if (Number.isNaN(activeIndex) || activeIndex < 0 || activeIndex >= buttons.length) {
    activeIndex = buttons.findIndex(function (button) {
      return button.getAttribute("aria-selected") === "true";
    });
  }
  if (activeIndex < 0) {
    activeIndex = 0;
  }
  activate(activeIndex, false, false);
  tabs.dataset.tabsReady = "true";
  tabs.dataset.tabsInitialized = "true";
}
function initFenceTabs() {
  var root = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : document;
  resolveTabs(root).forEach(function (tabs) {
    initTabsInstance(tabs);
  });
}

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
/************************************************************************/
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
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!*****************************!*\
  !*** ./assets/src/front.js ***!
  \*****************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _utils_DetailsAnimator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./utils/DetailsAnimator */ "./assets/src/utils/DetailsAnimator.js");
/* harmony import */ var _utils_tabs__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./utils/tabs */ "./assets/src/utils/tabs.js");
/* harmony import */ var _utils_fenceGallery__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils/fenceGallery */ "./assets/src/utils/fenceGallery.js");



function initFenceDetails() {
  var root = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : document;
  root.querySelectorAll(".fence-details").forEach(function (el) {
    if (el.dataset.detailsInitialized === "true") {
      return;
    }
    el.dataset.detailsInitialized = "true";
    new _utils_DetailsAnimator__WEBPACK_IMPORTED_MODULE_0__["default"](el);
  });
}
function initFrontFeatures() {
  var root = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : document;
  initFenceDetails(root);
  (0,_utils_tabs__WEBPACK_IMPORTED_MODULE_1__.initFenceTabs)(root);
  (0,_utils_fenceGallery__WEBPACK_IMPORTED_MODULE_2__.initFenceGrid)(root);
  (0,_utils_fenceGallery__WEBPACK_IMPORTED_MODULE_2__.initFenceMasonry)(root);
}
document.addEventListener("DOMContentLoaded", function () {
  initFrontFeatures(document);
});
})();

/******/ })()
;