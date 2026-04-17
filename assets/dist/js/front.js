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

document.addEventListener("DOMContentLoaded", function (_) {
  document.querySelectorAll('.fence-details').forEach(function (el) {
    new _utils_DetailsAnimator__WEBPACK_IMPORTED_MODULE_0__["default"](el);
  });
});
})();

/******/ })()
;