/******/ (() => { // webpackBootstrap
/*!*************************************!*\
  !*** ./assets/src/config/config.js ***!
  \*************************************/
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
document.addEventListener('DOMContentLoaded', function () {
  /**
   * Apply a function to an array of DOM elements selected by a selector.
   * @param {string} selector - The CSS selector.
   * @param {function} func - The function to apply to selected elements.
   * @param {HTMLElement} parent - The parent element to search within (default is document).
   */
  function applyFunctionToSelector(selector, func) {
    var parent = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : document;
    if (!parent) {
      parent = document;
    }
    var selectedElements = parent.querySelectorAll(selector);
    Array.from(selectedElements).forEach(func);
  }

  /**
   * Shortcut for document.querySelector.
   * @param {string} selector - The CSS selector.
   * @param {HTMLElement} parent - The parent element to search within (default is document).
   * @returns {HTMLElement|null} - The first matching element or null if not found.
   */
  function querySelector(selector) {
    var parent = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : document;
    if (!parent) {
      parent = document;
    }
    return parent.querySelector(selector);
  }

  /**
   * Compare two version numbers.
   * @param {string} serverVersion - The server version number.
   * @param {string} currentVersion - The current version number.
   * @returns {number} - 1 if server version is greater, -1 if current version is greater, 0 if equal.
   */
  function compareVersions(serverVersion, currentVersion) {
    var serverVersionArray = serverVersion.split('.');
    var currentVersionArray = currentVersion.split('.');
    var minLength = Math.min(serverVersionArray.length, currentVersionArray.length);
    for (var i = 0; i < minLength; i++) {
      var a = parseInt(serverVersionArray[i]);
      var b = parseInt(currentVersionArray[i]);
      if (a > b) {
        return 1;
      } else if (a < b) {
        return -1;
      }
    }
    if (serverVersionArray.length > currentVersionArray.length) {
      for (var j = minLength; j < serverVersionArray.length; j++) {
        if (parseInt(serverVersionArray[j]) !== 0) {
          return 1;
        }
      }
      return 0;
    } else if (serverVersionArray.length < currentVersionArray.length) {
      for (var _j = minLength; _j < currentVersionArray.length; _j++) {
        if (parseInt(currentVersionArray[_j]) !== 0) {
          return -1;
        }
      }
      return 0;
    }
    return 0;
  }
  var xConfig = querySelector('.x-config');
  var xContent = querySelector('.x-content', xConfig);

  // Move the form element
  var form = xConfig.parentNode.querySelector('form');
  form.parentNode.removeChild(form);
  xContent.appendChild(form);

  // Restore the active state
  var xTabs = querySelector('.x-tabs', xConfig);
  var active = sessionStorage.getItem('x-active') || '.x-notice';
  applyFunctionToSelector(active, function (el) {
    el.classList.add('active');
  }, xContent);
  applyFunctionToSelector("[data-class=\"".concat(active.replace('.', ''), "\"]"), function (el) {
    el.classList.add('active');
  }, xTabs);

  // Handle tab clicks
  Array.from(xTabs.querySelectorAll(':scope > li')).forEach(function (el) {
    el.addEventListener('click', function (event) {
      event.preventDefault();
      event.stopPropagation();
      window.scrollTo(0, 0);
      applyFunctionToSelector('.x-tabs li', function (el) {
        el.classList.remove('active');
      });
      event.target.classList.add('active');
      var className = '.' + event.target.dataset["class"];
      sessionStorage.setItem('x-active', className);
      applyFunctionToSelector('.x-item', function (el) {
        el.classList.remove('active');
      }, xContent);
      applyFunctionToSelector(className, function (el) {
        el.classList.add('active');
      }, xContent);
    });
  });

  // Load the log
  if (typeof window.XEditorUpdateURL === 'string' && window.XEditorUpdateURL.startsWith('http')) {
    fetch(window.XEditorUpdateURL).then(function (response) {
      return response.text();
    }).then(function (text) {
      var _fragment$querySelect;
      var fragment = document.createElement('div');
      fragment.innerHTML = text;
      text = (_fragment$querySelect = fragment.querySelector('.post-content,.entry-content')) === null || _fragment$querySelect === void 0 ? void 0 : _fragment$querySelect.innerHTML;
      var firstNode = fragment.querySelector('h2, h3');
      var serverVersion = firstNode.innerText.substring(0, 5);
      var xNotice = querySelector('.x-notice', xConfig);
      var title = querySelector('.title', xConfig);
      querySelector('.loading', title).remove();
      querySelector('.latest.version', title).innerHTML = firstNode.innerText;
      querySelector('.message', xNotice).innerHTML = text;
      querySelector('.latest.version', title).classList.add('active');
      if (compareVersions(serverVersion, title.dataset.version) > 0) {
        querySelector('.latest.found', title).classList.add('active');
      } else {
        querySelector('.latest', title).classList.add('active');
      }
    })["catch"](function (err) {
      return console.log('Request Failed', err);
    });
  }
  if (location.hash) {
    // 跳转到相应 Tab
    var hashNode = document.querySelector(location.hash);
    if (hashNode && hashNode.classList.contains('x-item')) {
      document.querySelectorAll('.x-item.active').forEach(function (i) {
        return i.classList.remove('active');
      });
      var classList = JSON.parse(JSON.stringify(_toConsumableArray(hashNode.classList)));
      classList = classList.filter(function (c) {
        return c.startsWith('x-') && c !== "x-item";
      });
      document.querySelector("[data-class=\"".concat(classList[0], "\"]")).click();
    }
  }
});
/******/ })()
;