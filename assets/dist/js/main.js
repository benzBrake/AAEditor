(()=>{"use strict";var e,t={415:()=>{function e(t){return e="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},e(t)}function t(e){for(var t=arguments.length,n=new Array(t>1?t-1:0),r=1;r<t;r++)n[r-1]=arguments[r];var a=0;return e.replace(/%[sd]/g,(function(e){if(a>=n.length)return e;var t=n[a++];if("%s"===e)return String(t);if("%d"===e){if("number"==typeof t)return String(t);throw new Error("Argument ".concat(a," is not a number."))}return e}))}function n(t,n){var r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:[];return function(t){var n,r,a,o=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{},i=arguments.length>2&&void 0!==arguments[2]?arguments[2]:[];"attr"in t?(n=function(e,n){t.attr(e,""+n)},r=function(e){t.text(e)},a=function(e){t.html(e)}):(n=function(e,n){t.setAttribute(e,""+n)},r=function(e){t.innerText=e},a=function(e){t.innerHTML=e});for(var l in o)if(!i.includes(l)){var c=o[l];switch("function"==typeof c&&(c=c.toString()),l){case"innerText":r(c);break;case"innerHTML":a(c);break;case"style":if("object"===e(c))n("style",Object.entries(c).map((function(e){return e.join(":")})).join("; "));else n("style",c);break;default:n(l,c)}}return t}(document.createElement(t),n,r)}function r(e){return"string"==typeof e}function a(e){return a="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},a(e)}function o(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,i(r.key),r)}}function i(e){var t=function(e,t){if("object"!=a(e)||!e)return e;var n=e[Symbol.toPrimitive];if(void 0!==n){var r=n.call(e,t||"default");if("object"!=a(r))return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===t?String:Number)(e)}(e,"string");return"symbol"==a(t)?t:t+""}var l=function(){return e=function e(t){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),this.textarea=document.querySelector(t),this.textarea.utils=this,this.locked=!1,this.isFocused=!1,this.initFocusEvents()},t=[{key:"executeAndAddUndoStack",value:function(e){for(var t=this,n=arguments.length,r=new Array(n>1?n-1:0),a=1;a<n;a++)r[a-1]=arguments[a];var o=function(){"setContent"===e?t.setContent.apply(t,r):"insertText"===e?t.insertText.apply(t,r):"replaceSelectionText"===e&&t.replaceSelectionText.apply(t,r)};this.textarea.pagedown?this.textarea.pagedown.textOperation(o):o()}},{key:"getInstance",value:function(){return this.textarea}},{key:"on",value:function(e,t){this.textarea.addEventListener(e,t)}},{key:"off",value:function(e,t){this.textarea.removeEventListener(e,t)}},{key:"getContent",value:function(){return this.textarea.value}},{key:"setContent",value:function(e){return!this.locked&&(this.textarea.value=e,this.afterOperate(),!0)}},{key:"lock",value:function(){this.locked=!0,this.textarea.setAttribute("readonly","readonly")}},{key:"unlock",value:function(){this.locked=!1,this.textarea.removeAttribute("readonly")}},{key:"insertText",value:function(e){if(this.locked)return!1;var t=this.textarea.selectionStart,n=this.textarea.selectionEnd,r=this.textarea.value;return this.textarea.value=r.slice(0,t)+e+r.slice(n),this.textarea.selectionStart=t+e.length,this.textarea.selectionEnd=t+e.length,this.afterOperate(),!0}},{key:"replaceSelectionText",value:function(e){if(this.locked)return!1;var t=this.textarea.selectionStart,n=this.textarea.selectionEnd,r=this.textarea.value;return this.textarea.value=r.slice(0,t)+e+r.slice(n),this.textarea.selectionStart=t,this.textarea.selectionEnd=t+e.length,this.afterOperate(),!0}},{key:"getSelectedText",value:function(){return this.textarea.value.substring(this.textarea.selectionStart,this.textarea.selectionEnd)}},{key:"getSelection",value:function(){return{start:this.textarea.selectionStart,end:this.textarea.selectionEnd}}},{key:"setSelection",value:function(e,t){return!this.locked&&e>=0&&t>=0&&e<=this.textarea.value.length&&t<=this.textarea.value.length&&(this.textarea.selectionStart=e,this.textarea.selectionEnd=t,!0)}},{key:"getLineText",value:function(e){var t=this.textarea.value.split("\n");return e>=0&&e<t.length?t[e]:""}},{key:"getTextInRange",value:function(e,t){return this.textarea.value.substring(e,t)}},{key:"getCurrentLineText",value:function(){for(var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0,t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:this.textarea.selectionStart,n=this.textarea.value,r=e,a=t;r>0&&"\n"!==n[r-1];)r--;for(;a<n.length&&"\n"!==n[a];)a++;return n.substring(r,a)}},{key:"replaceCurrentLine",value:function(e){if(this.locked)return!1;for(var t=this.textarea.selectionStart,n=this.textarea.selectionEnd,r=this.textarea.value,a=t,o=n;a>0&&"\n"!==r[a-1];)a--;for(;o<r.length&&"\n"!==r[o];)o++;var i=r.substring(0,a),l=r.substring(o);return this.textarea.value=i+e+l,this.textarea.selectionStart=a,this.textarea.selectionEnd=a+e.length,this.afterOperate(),!0}},{key:"getCursorPosition",value:function(){for(var e=this.textarea.selectionStart,t=this.textarea.value,n=1,r=1,a=0;a<e;a++)"\n"===t[a]?(n++,r=1):r++;return{line:n,column:r}}},{key:"isAtLineStart",value:function(){var e=this.textarea.selectionStart,t=this.textarea.value;return 0===e||"\n"===t[e-1]}},{key:"isAtLineEnd",value:function(){var e=this.textarea.selectionEnd,t=this.textarea.value;return e===t.length||"\n"===t[e]}},{key:"isAtStart",value:function(){return 0===this.textarea.selectionStart}},{key:"isAtEnd",value:function(){return this.textarea.selectionEnd===this.textarea.value.length}},{key:"initFocusEvents",value:function(){var e=this;this.textarea.addEventListener("focus",(function(){e.isFocused=!0})),this.textarea.addEventListener("blur",(function(){e.isFocused=!1}))}},{key:"afterOperate",value:function(){$(this.textarea).trigger("input"),this.textarea.focus()}},{key:"getScrollPosition",value:function(){return{top:this.textarea.scrollTop,left:this.textarea.scrollLeft}}},{key:"setScrollPosition",value:function(e,t){this.textarea.scrollTop=e,this.textarea.scrollLeft=t}}],t&&o(e.prototype,t),n&&o(e,n),Object.defineProperty(e,"prototype",{writable:!1}),e;var e,t,n}();function c(e){return c="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},c(e)}function s(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function u(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?s(Object(n),!0).forEach((function(t){d(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):s(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function d(e,t,n){return(t=h(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function f(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,h(r.key),r)}}function h(e){var t=function(e,t){if("object"!=c(e)||!e)return e;var n=e[Symbol.toPrimitive];if(void 0!==n){var r=n.call(e,t||"default");if("object"!=c(r))return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===t?String:Number)(e)}(e,"string");return"symbol"==c(t)?t:t+""}new(function(){return e=function e(n,a){var o=this;!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),document.querySelector(n)?(this.textarea=new l(n),this.previewArea=document.querySelector(a)):console.warn(t("element [%s] not e",n)),this.isInit=!1,this.buttons=[],$("body").on("XEditorAddButton",(function(e){for(var t=arguments.length,n=new Array(t>1?t-1:0),a=1;a<t;a++)n[a-1]=arguments[a];n.forEach((function(e){var t;t=e,"[object Object]"===Object.prototype.toString.call(t)?Object.keys(e).length?o.buttons.push(e):o.buttons.push({class:"wmd-spacer"}):r(e)&&"splitter"===e&&o.buttons.push({class:"wmd-spacer"})}))})).on("XEditorInit",(function(){o.init()})).on("XEditorRefresh",(function(){var e=o.textarea.getScrollPosition();o.setContent(o.getContent()),o.textarea.setScrollPosition(e.top,e.left)}))},a=[{key:"isMarkdown",value:function(){return"1"===$('[name="markdown"]').val()}},{key:"init",value:function(){var e=this;this.isInit||(this.isInit=!0,window.XEditor=this,$("body").append('<div id="aa-wrapper"></div>').on("XEditorPreviewEnd",(function(){return e.handlePreviewEnd()})).on("XEditorReplaceSelection",(function(t,n){console.log("replaceSelection",n),e.replaceSelection(n)})),this.initToolbar(),$("body").trigger("XEditorPreviewEnd"))}},{key:"initToolbar",value:function(){var e=this,a=function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},r=n("li",u(u({},t),{},{title:t.name}),["icon","command","onMounted"]);r.classList.add("wmd-button");var a=$(t.icon);return a.length?r.appendChild(a.get(0)):r.innerHTML=t.icon,t.shortcut&&function(t,n,r){var a=/Mac|iPod|iPhone|iPad/.test(navigator.platform);return/^([a-zA-Z0-9]|F[1-9]|10|11|12|\+)+$/.test(n)?(n=n.toLowerCase(),a&&(n=n.replace("ctrl","cmd")),document.addEventListener("keydown",(function(a){if(!e.isFocused())return!1;var o=[];a.ctrlKey&&o.push("ctrl"),a.altKey&&o.push("alt"),a.shiftKey&&o.push("shift"),/[a-zA-Z0-9]/.test(a.key)&&o.length>0&&o.push(a.key.toLowerCase()),o.join("+")===n&&(a.preventDefault(),a.stopPropagation(),a.objectTarget=t,r.call(e,a))})),!0):(console.error("无效的快捷键格式"),!1)}(r,t.shortcut,e.handleHotkey)&&(r.title="".concat(t.name," ").concat(t.shortcut.toUpperCase())),"command"in t&&("function"==typeof t.command?r.addEventListener("click",(function(){t.command.call(e,{target:r})})):r.setAttribute("onclick",t.command)),r},o=function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0,r=arguments.length>1?arguments[1]:void 0,a=n("li",u({id:t("wmd-spacer%d",e)},r));return a.classList.add("wmd-spacer"),a.classList.add(t("wmd-spacer%d",e)),a},i=function(){return $("#wmd-button-bar .wmd-spacer").length};this.toolbar=$("#wmd-button-row"),this.buttons.forEach((function(t){var n,l=!1;"id"in t?document.getElementById(t.id)?(n=document.getElementById(t.id),l=!0,"icon"in t&&(n.innerHTML=t.icon)):"name"in t?("icon"in t||(t.icon="<span>".concat(name,"</span>")),n=a(t)):n=o(i()+1,t):n=o(i()+1,t);var c,s=!1,u=function(t,n){if(n||(n=e.toolbar),r(t)){if(!(t.indexOf("|")>-1))return $(t,n);for(var a=t.split("|"),o=0;o<a.length;o++){var i=u(a[o],n);if(i.length)return i}}};"insertBefore"in t?(c=u(t.insertBefore,e.toolbar))&&(c.before(n),s=!0):"insertAfter"in t?(c=u(t.insertAfter,e.toolbar))&&(c.after(n),s=!0):"remove"in t&&t.remove&&n.parentNode&&n.parentNode.removeChild(n),s||l||e.toolbar.append(n),"function"==typeof t.onMounted&&t.onMounted.call(e,{target:n})}))}},{key:"handleHotkey",value:function(e){e.objectTarget.click()}},{key:"handlePreviewEnd",value:function(){}},{key:"getContent",value:function(){return this.textarea.getContent()}},{key:"setContent",value:function(e){this.textarea.executeAndAddUndoStack("setContent",e)}},{key:"insertText",value:function(e){this.textarea.executeAndAddUndoStack("insertText",e)}},{key:"replaceSelection",value:function(e){this.getSelectedText()?this.textarea.executeAndAddUndoStack("replaceSelection",e):this.textarea.executeAndAddUndoStack("insertText",e)}},{key:"getSelectedText",value:function(){return this.textarea.getSelectedText()}},{key:"isFocused",value:function(){return this.textarea.isFocused}},{key:"wrapText",value:function(e,t){var n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"",r=this.textarea,a=r.getSelectedText(),o=r.getSelection();if(a){var i=a.indexOf(e),l=a.lastIndexOf(t),c=!1;if(-1!==i&&-1!==l)i<l?(o.start=o.start+i,o.end=o.start+l+t.length):c=!0;else{if(-1===i){var s=r.getTextInRange(0,o.start),u=s.lastIndexOf(t);u>-1&&(s=s.slice(u+t.length,s.length)),(i=s.indexOf(e))>-1&&(o.start=o.start-s.length+i)}if(-1===l){var d=r.getTextInRange(o.end,r.getContent().length),f=d.indexOf(e);f>-1&&(d=d.slice(0,f)),-1!==(l=d.indexOf(t))&&(o.end=o.end+l+t.length)}-1===i&&-1===l&&(c=!0)}if(c){var h=e+r.getSelectedText()+t;r.executeAndAddUndoStack("replaceSelectionText",h),r.setSelection(o.start+e.length,o.start+h.length-t.length)}else{r.setSelection(o.start,o.end);var v=r.getSelectedText();v.startsWith(e)&&(v=v.slice(e.length)),v.endsWith(t)&&(v=v.slice(0,v.length-t.length)),r.executeAndAddUndoStack("replaceSelectionText",v),r.setSelection(o.start,o.start+v.length)}}else{var p=r.isAtLineStart()?e:"\n"+e,m=r.isAtLineEnd()?t:t+"\n",y=p+n+m;r.executeAndAddUndoStack("insertText",y);var g=r.getSelection().start;r.setSelection(g-y.length+p.length,g-m.length)}}},{key:"blockPrefix",value:function(e,t){var n=this.textarea,r=n.getSelectedText();if(t||(t=""),r.length){var a=r.split("\n");console.log(a);var o=a.map((function(t,n){return e.replace("%n",n+1)+t})).join("\n");n.isAtLineStart()||(o="\n"+o),n.executeAndAddUndoStack("replaceSelectionText",o+"\n")}else{var i=e.replace("%n",1),l=i+t+"\n",c=0;n.isAtLineStart()||(l="\n"+l,c=1),n.executeAndAddUndoStack("insertText",l),n.setSelection(n.getSelection().start-l.length+i.length+c,n.getSelection().start)}}},{key:"firstSelectionLinePrefix",value:function(e,t){var n=this.textarea,r=n.getSelectedText();if(r.length){var a=r.split("\n"),o=a[0];if(o.startsWith(e))o=o.substring(e.length);else{var i=n.getSelection(),l=i.start,c=i.end;n.getTextInRange(l-e.length,l)===e?n.setSelection(l-e.length,c):o=e+o}a[0]=o;var s=a.join("\n");n.isAtLineStart()||(s="\n"+s),n.executeAndAddUndoStack("replaceSelectionText",s)}else{var u,d=e.replace("%n",1),f=0;n.isAtLineStart()?u=d+t:(u="\n"+d+t,f=1),n.executeAndAddUndoStack("insertText",u),n.setSelection(n.getSelection().start-u.length+d.length+f,n.getSelection().start)}}},{key:"openModal",value:function(){var e=this,t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},n=Object.assign({title:"标题",innerHTML:"内容",showFooter:!0,checkEmptyOnConfirm:!0,change:function(e,t){},confirm:function(e,t){return!0},cancel:function(e){},handle:function(e){},callback:function(e){}},t),r=n.checkEmptyOnConfirm?'<form class="params"></form>':"";$("#aa-modal").length<1&&$("#aa-wrapper").append('<div id="aa-modal" class="aa-modal">\n    <div class="aa-modal-frame">\n    <div class="aa-modal-header">\n        <div class="aa-modal-header-title"></div><div class="aa-modal-header-close"><i class="close-icon"></i></div>\n</div>\n    <div class="aa-modal-body">\n        '.concat(r,'\n    </div>\n    <div class="aa-modal-footer">\n        <button type="button" class="aa-modal-footer-button aa-modal-footer-cancel">取消</button><button type="button" class="aa-modal-footer-button aa-modal-footer-confirm">确定</button>\n    </div>\n</div>\n</div>')),$(".aa-modal-header-title").html(n.title),n.checkEmptyOnConfirm?$(".aa-modal-body .params").html(n.innerHTML):$(".aa-modal-body").html(n.innerHTML);var a=$("#aa-modal").get(0);n.showFooter?$(".aa-modal-footer").show():$(".aa-modal-footer").hide(),$("body").addClass("no-scroll"),n.handle.call(this,a),$(".aa-modal-footer-confirm").on("click",(function(){var t=!0;if(n.checkEmptyOnConfirm){var r=$("#aa-modal .aa-modal-body .params").serializeArray();$.each(r,(function(e,n){var r=$("#aa-modal .params [name=".concat(n.name,"]"));r.prop("required")&&""===n.value&&(t=!1,r.addClass("required-animate"),setTimeout((function(){r.removeClass("required-animate")}),800))}))}t&&n.confirm.call(e,a)&&($("#aa-modal").remove(),$("body").removeClass("no-scroll"),n.callback.call(e,a))})),$(".aa-modal-header-close").on("click",(function(t){n.cancel.call(e,t),$("#aa-modal").removeClass("active"),setTimeout((function(){$("#aa-modal").remove(),$("body").removeClass("no-scroll")}),300)})),$(".aa-modal-footer-cancel").on("click",(function(e){$("#aa-modal").removeClass("active"),n.cancel.call(a,e),setTimeout((function(){$("#aa-modal").remove(),$("body").removeClass("no-scroll")}),300)}));var o=$(".params",a);$("input,select,textarea",o).on("change input",(function(){var t=o.serializeArray(),r={};t.forEach((function(e){r[e.name]=e.value})),n.change.call(e,a,r)})),$("#aa-modal").addClass("active")}},{key:"getShortCodeRegex",value:function(e){return new RegExp("\\[(\\[?)("+e+")(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)","g")}}],a&&f(e.prototype,a),o&&f(e,o),Object.defineProperty(e,"prototype",{writable:!1}),e;var e,a,o}())("#text","#md-preview")},307:()=>{},407:()=>{},793:()=>{},868:()=>{},3:()=>{}},n={};function r(e){var a=n[e];if(void 0!==a)return a.exports;var o=n[e]={exports:{}};return t[e](o,o.exports,r),o.exports}r.m=t,e=[],r.O=(t,n,a,o)=>{if(!n){var i=1/0;for(u=0;u<e.length;u++){for(var[n,a,o]=e[u],l=!0,c=0;c<n.length;c++)(!1&o||i>=o)&&Object.keys(r.O).every((e=>r.O[e](n[c])))?n.splice(c--,1):(l=!1,o<i&&(i=o));if(l){e.splice(u--,1);var s=a();void 0!==s&&(t=s)}}return t}o=o||0;for(var u=e.length;u>0&&e[u-1][2]>o;u--)e[u]=e[u-1];e[u]=[n,a,o]},r.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{var e={829:0,701:0,718:0,650:0,245:0,552:0};r.O.j=t=>0===e[t];var t=(t,n)=>{var a,o,[i,l,c]=n,s=0;if(i.some((t=>0!==e[t]))){for(a in l)r.o(l,a)&&(r.m[a]=l[a]);if(c)var u=c(r)}for(t&&t(n);s<i.length;s++)o=i[s],r.o(e,o)&&e[o]&&e[o][0](),e[o]=0;return r.O(u)},n=self.webpackChunkaaeditor=self.webpackChunkaaeditor||[];n.forEach(t.bind(null,0)),n.push=t.bind(null,n.push.bind(n))})(),r.O(void 0,[701,718,650,245,552],(()=>r(415))),r.O(void 0,[701,718,650,245,552],(()=>r(307))),r.O(void 0,[701,718,650,245,552],(()=>r(407))),r.O(void 0,[701,718,650,245,552],(()=>r(793))),r.O(void 0,[701,718,650,245,552],(()=>r(868)));var a=r.O(void 0,[701,718,650,245,552],(()=>r(3)));a=r.O(a)})();
//# sourceMappingURL=main.js.map