(()=>{"use strict";var e,t={315:()=>{function e(t){return e="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},e(t)}function t(e){for(var t=arguments.length,n=new Array(t>1?t-1:0),a=1;a<t;a++)n[a-1]=arguments[a];var r=0;return e.replace(/%[sd]/g,(function(e){if(r>=n.length)return e;var t=n[r++];if("%s"===e)return String(t);if("%d"===e){if("number"==typeof t)return String(t);throw new Error("Argument ".concat(r," is not a number."))}return e}))}function n(t,n){var a=arguments.length>2&&void 0!==arguments[2]?arguments[2]:[];return function(t){var n,a,r,o=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{},i=arguments.length>2&&void 0!==arguments[2]?arguments[2]:[];"attr"in t?(n=function(e,n){t.attr(e,""+n)},a=function(e){t.text(e)},r=function(e){t.html(e)}):(n=function(e,n){t.setAttribute(e,""+n)},a=function(e){t.innerText=e},r=function(e){t.innerHTML=e});for(var l in o)if(!i.includes(l)){var c=o[l];switch("function"==typeof c&&(c=c.toString()),l){case"innerText":a(c);break;case"innerHTML":r(c);break;case"style":if("object"===e(c))n("style",Object.entries(c).map((function(e){return e.join(":")})).join("; "));else n("style",c);break;default:n(l,c)}}return t}(document.createElement(t),n,a)}function a(e){return"string"==typeof e}function r(e,t){for(var n=0;n<t.length;n++){var a=t[n];a.enumerable=a.enumerable||!1,a.configurable=!0,"value"in a&&(a.writable=!0),Object.defineProperty(e,a.key,a)}}var o=function(){function e(t){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),this.textarea=document.querySelector(t),this.textarea.utils=this,this.locked=!1,this.isFocused=!1,this.initFocusEvents()}var t,n,a;return t=e,n=[{key:"executeAndAddUndoStack",value:function(e){for(var t=this,n=arguments.length,a=new Array(n>1?n-1:0),r=1;r<n;r++)a[r-1]=arguments[r];var o=function(){"setContent"===e?t.setContent.apply(t,a):"insertText"===e?t.insertText.apply(t,a):"replaceSelectionText"===e&&t.replaceSelectionText.apply(t,a)};this.textarea.pagedown?this.textarea.pagedown.textOperation(o):o()}},{key:"getInstance",value:function(){return this.textarea}},{key:"on",value:function(e,t){this.textarea.addEventListener(e,t)}},{key:"off",value:function(e,t){this.textarea.removeEventListener(e,t)}},{key:"getContent",value:function(){return this.textarea.value}},{key:"setContent",value:function(e){return!this.locked&&(this.textarea.value=e,this.afterOperate(),!0)}},{key:"lock",value:function(){this.locked=!0,this.textarea.setAttribute("readonly","readonly")}},{key:"unlock",value:function(){this.locked=!1,this.textarea.removeAttribute("readonly")}},{key:"insertText",value:function(e){if(this.locked)return!1;var t=this.textarea.selectionStart,n=this.textarea.selectionEnd,a=this.textarea.value;return this.textarea.value=a.slice(0,t)+e+a.slice(n),this.textarea.selectionStart=t+e.length,this.textarea.selectionEnd=t+e.length,this.afterOperate(),!0}},{key:"replaceSelectionText",value:function(e){if(this.locked)return!1;var t=this.textarea.selectionStart,n=this.textarea.selectionEnd,a=this.textarea.value;return this.textarea.value=a.slice(0,t)+e+a.slice(n),this.textarea.selectionStart=t,this.textarea.selectionEnd=t+e.length,this.afterOperate(),!0}},{key:"getSelectedText",value:function(){return this.textarea.value.substring(this.textarea.selectionStart,this.textarea.selectionEnd)}},{key:"getSelection",value:function(){return{start:this.textarea.selectionStart,end:this.textarea.selectionEnd}}},{key:"setSelection",value:function(e,t){return!this.locked&&e>=0&&t>=0&&e<=this.textarea.value.length&&t<=this.textarea.value.length&&(this.textarea.selectionStart=e,this.textarea.selectionEnd=t,!0)}},{key:"getLineText",value:function(e){var t=this.textarea.value.split("\n");return e>=0&&e<t.length?t[e]:""}},{key:"getTextInRange",value:function(e,t){return this.textarea.value.substring(e,t)}},{key:"getCurrentLineText",value:function(){for(var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0,t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:this.textarea.selectionStart,n=this.textarea.value,a=e,r=t;a>0&&"\n"!==n[a-1];)a--;for(;r<n.length&&"\n"!==n[r];)r++;return n.substring(a,r)}},{key:"replaceCurrentLine",value:function(e){if(this.locked)return!1;for(var t=this.textarea.selectionStart,n=this.textarea.selectionEnd,a=this.textarea.value,r=t,o=n;r>0&&"\n"!==a[r-1];)r--;for(;o<a.length&&"\n"!==a[o];)o++;var i=a.substring(0,r),l=a.substring(o);return this.textarea.value=i+e+l,this.textarea.selectionStart=r,this.textarea.selectionEnd=r+e.length,this.afterOperate(),!0}},{key:"getCursorPosition",value:function(){for(var e=this.textarea.selectionStart,t=this.textarea.value,n=1,a=1,r=0;r<e;r++)"\n"===t[r]?(n++,a=1):a++;return{line:n,column:a}}},{key:"isAtLineStart",value:function(){var e=this.textarea.selectionStart,t=this.textarea.value;return 0===e||"\n"===t[e-1]}},{key:"isAtLineEnd",value:function(){var e=this.textarea.selectionEnd,t=this.textarea.value;return e===t.length||"\n"===t[e]}},{key:"isAtStart",value:function(){return 0===this.textarea.selectionStart}},{key:"isAtEnd",value:function(){return this.textarea.selectionEnd===this.textarea.value.length}},{key:"initFocusEvents",value:function(){var e=this;this.textarea.addEventListener("focus",(function(){e.isFocused=!0})),this.textarea.addEventListener("blur",(function(){e.isFocused=!1}))}},{key:"afterOperate",value:function(){$(this.textarea).trigger("input"),this.textarea.focus()}},{key:"getScrollPosition",value:function(){return{top:this.textarea.scrollTop,left:this.textarea.scrollLeft}}},{key:"setScrollPosition",value:function(e,t){this.textarea.scrollTop=e,this.textarea.scrollLeft=t}}],n&&r(t.prototype,n),a&&r(t,a),Object.defineProperty(t,"prototype",{writable:!1}),e}();function i(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);t&&(a=a.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,a)}return n}function l(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?i(Object(n),!0).forEach((function(t){c(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):i(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function c(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function s(e,t){for(var n=0;n<t.length;n++){var a=t[n];a.enumerable=a.enumerable||!1,a.configurable=!0,"value"in a&&(a.writable=!0),Object.defineProperty(e,a.key,a)}}new(function(){function e(n,r){var i=this;!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),document.querySelector(n)?(this.textarea=new o(n),this.previewArea=document.querySelector(r)):console.warn(t("element [%s] not e",n)),this.isInit=!1,this.buttons=[],$("body").on("XEditorAddButton",(function(e){for(var t=arguments.length,n=new Array(t>1?t-1:0),r=1;r<t;r++)n[r-1]=arguments[r];n.forEach((function(e){var t;t=e,"[object Object]"===Object.prototype.toString.call(t)?Object.keys(e).length?i.buttons.push(e):i.buttons.push({class:"wmd-spacer"}):a(e)&&"splitter"===e&&i.buttons.push({class:"wmd-spacer"})}))})).on("XEditorInit",(function(){i.init()})).on("XEditorRefresh",(function(){var e=i.textarea.getScrollPosition();i.setContent(i.getContent()),i.textarea.setScrollPosition(e.top,e.left)}))}var r,i,c;return r=e,i=[{key:"isMarkdown",value:function(){return"1"===$('[name="markdown"]').val()}},{key:"init",value:function(){var e=this;this.isInit||(this.isInit=!0,window.XEditor=this,$("body").append('<div id="aa-wrapper"></div>').on("XEditorPreviewEnd",(function(){return e.handlePreviewEnd()})).on("XEditorReplaceSelection",(function(t,n){console.log("replaceSelection",n),e.replaceSelection(n)})),this.initToolbar(),$("body").trigger("XEditorPreviewEnd"))}},{key:"initToolbar",value:function(){var e=this,r=function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},a=n("li",l(l({},t),{},{title:t.name}),["icon","command","onMounted"]);a.classList.add("wmd-button");var r=$(t.icon);return r.length?a.appendChild(r.get(0)):a.innerHTML=t.icon,t.shortcut&&function(t,n,a){var r=/Mac|iPod|iPhone|iPad/.test(navigator.platform);return/^([a-zA-Z0-9]|F[1-9]|10|11|12|\+)+$/.test(n)?(n=n.toLowerCase(),r&&(n=n.replace("ctrl","cmd")),document.addEventListener("keydown",(function(r){if(!e.isFocused())return!1;var o=[];r.ctrlKey&&o.push("ctrl"),r.altKey&&o.push("alt"),r.shiftKey&&o.push("shift"),/[a-zA-Z0-9]/.test(r.key)&&o.length>0&&o.push(r.key.toLowerCase()),o.join("+")===n&&(r.preventDefault(),r.stopPropagation(),r.objectTarget=t,a.call(e,r))})),!0):(console.error("无效的快捷键格式"),!1)}(a,t.shortcut,e.handleHotkey)&&(a.title="".concat(t.name," ").concat(t.shortcut.toUpperCase())),"command"in t&&("function"==typeof t.command?a.addEventListener("click",(function(){t.command.call(e,{target:a})})):a.setAttribute("onclick",t.command)),a},o=function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0,a=arguments.length>1?arguments[1]:void 0,r=n("li",l({id:t("wmd-spacer%d",e)},a));return r.classList.add("wmd-spacer"),r.classList.add(t("wmd-spacer%d",e)),r},i=function(){return $("#wmd-button-bar .wmd-spacer").length};this.toolbar=$("#wmd-button-row"),this.buttons.forEach((function(t){var n,l=!1;"id"in t?document.getElementById(t.id)?(n=document.getElementById(t.id),l=!0,"icon"in t&&(n.innerHTML=t.icon)):"name"in t?("icon"in t||(t.icon="<span>".concat(name,"</span>")),n=r(t)):n=o(i()+1,t):n=o(i()+1,t);var c,s=!1,u=function t(n,r){if(r||(r=e.toolbar),a(n)){if(!(n.indexOf("|")>-1))return $(n,r);for(var o=n.split("|"),i=0;i<o.length;i++){var l=t(o[i],r);if(l.length)return l}}};"insertBefore"in t?(c=u(t.insertBefore,e.toolbar))&&(c.before(n),s=!0):"insertAfter"in t?(c=u(t.insertAfter,e.toolbar))&&(c.after(n),s=!0):"remove"in t&&t.remove&&n.parentNode&&n.parentNode.removeChild(n),s||l||e.toolbar.append(n),"function"==typeof t.onMounted&&t.onMounted.call(e,{target:n})}))}},{key:"handleHotkey",value:function(e){e.objectTarget.click()}},{key:"handlePreviewEnd",value:function(){}},{key:"getContent",value:function(){return this.textarea.getContent()}},{key:"setContent",value:function(e){this.textarea.executeAndAddUndoStack("setContent",e)}},{key:"insertText",value:function(e){this.textarea.executeAndAddUndoStack("insertText",e)}},{key:"replaceSelection",value:function(e){this.getSelectedText()?this.textarea.executeAndAddUndoStack("replaceSelection",e):this.textarea.executeAndAddUndoStack("insertText",e)}},{key:"getSelectedText",value:function(){return this.textarea.getSelectedText()}},{key:"isFocused",value:function(){return this.textarea.isFocused}},{key:"wrapText",value:function(e,t){var n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"",a=this.textarea,r=a.getSelectedText(),o=a.getSelection();if(r){var i=r.indexOf(e),l=r.lastIndexOf(t),c=!1;if(-1!==i&&-1!==l)i<l?(o.start=o.start+i,o.end=o.start+l+t.length):c=!0;else{if(-1===i){var s=a.getTextInRange(0,o.start),u=s.lastIndexOf(t);u>-1&&(s=s.slice(u+t.length,s.length)),(i=s.indexOf(e))>-1&&(o.start=o.start-s.length+i)}if(-1===l){var d=a.getTextInRange(o.end,a.getContent().length),f=d.indexOf(e);f>-1&&(d=d.slice(0,f)),-1!==(l=d.indexOf(t))&&(o.end=o.end+l+t.length)}-1===i&&-1===l&&(c=!0)}if(c){var h=e+a.getSelectedText()+t;a.executeAndAddUndoStack("replaceSelectionText",h),a.setSelection(o.start+e.length,o.start+h.length-t.length)}else{a.setSelection(o.start,o.end);var v=a.getSelectedText();v.startsWith(e)&&(v=v.slice(e.length)),v.endsWith(t)&&(v=v.slice(0,v.length-t.length)),a.executeAndAddUndoStack("replaceSelectionText",v),a.setSelection(o.start,o.start+v.length)}}else{var p=a.isAtLineStart()?e:"\n"+e,g=a.isAtLineEnd()?t:t+"\n",m=p+n+g;a.executeAndAddUndoStack("insertText",m);var x=a.getSelection().start;a.setSelection(x-m.length+p.length,x-g.length)}}},{key:"blockPrefix",value:function(e,t){var n=this.textarea,a=n.getSelectedText();if(t||(t=""),a.length){var r=a.split("\n");console.log(r);var o=r.map((function(t,n){return e.replace("%n",n+1)+t})).join("\n");n.isAtLineStart()||(o="\n"+o),n.executeAndAddUndoStack("replaceSelectionText",o+"\n")}else{var i=e.replace("%n",1),l=i+t+"\n",c=0;n.isAtLineStart()||(l="\n"+l,c=1),n.executeAndAddUndoStack("insertText",l),n.setSelection(n.getSelection().start-l.length+i.length+c,n.getSelection().start)}}},{key:"firstSelectionLinePrefix",value:function(e,t){var n=this.textarea,a=n.getSelectedText();if(a.length){var r=a.split("\n"),o=r[0];if(o.startsWith(e))o=o.substring(e.length);else{var i=n.getSelection(),l=i.start,c=i.end;n.getTextInRange(l-e.length,l)===e?n.setSelection(l-e.length,c):o=e+o}r[0]=o;var s=r.join("\n");n.isAtLineStart()||(s="\n"+s),n.executeAndAddUndoStack("replaceSelectionText",s)}else{var u,d=e.replace("%n",1),f=0;n.isAtLineStart()?u=d+t:(u="\n"+d+t,f=1),n.executeAndAddUndoStack("insertText",u),n.setSelection(n.getSelection().start-u.length+d.length+f,n.getSelection().start)}}},{key:"openModal",value:function(){var e=this,t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},n=Object.assign({title:"标题",innerHTML:"内容",showFooter:!0,checkEmptyOnConfirm:!0,change:function(e,t){},confirm:function(e,t){return!0},cancel:function(e){},handle:function(e){},callback:function(e){}},t),a=n.checkEmptyOnConfirm?'<form class="params"></form>':"";$("#aa-modal").length<1&&$("#aa-wrapper").append('<div id="aa-modal" class="aa-modal">\n    <div class="aa-modal-frame">\n    <div class="aa-modal-header">\n        <div class="aa-modal-header-title"></div><div class="aa-modal-header-close"><i class="close-icon"></i></div>\n</div>\n    <div class="aa-modal-body">\n        '.concat(a,'\n    </div>\n    <div class="aa-modal-footer">\n        <button type="button" class="aa-modal-footer-button aa-modal-footer-cancel">取消</button><button type="button" class="aa-modal-footer-button aa-modal-footer-confirm">确定</button>\n    </div>\n</div>\n</div>')),$(".aa-modal-header-title").html(n.title),n.checkEmptyOnConfirm?$(".aa-modal-body .params").html(n.innerHTML):$(".aa-modal-body").html(n.innerHTML);var r=$("#aa-modal").get(0);n.showFooter?$(".aa-modal-footer").show():$(".aa-modal-footer").hide(),$("body").addClass("no-scroll"),n.handle.call(this,r),$(".aa-modal-footer-confirm").on("click",(function(){var t=!0;if(n.checkEmptyOnConfirm){var a=$("#aa-modal .aa-modal-body .params").serializeArray();$.each(a,(function(e,n){var a=$("#aa-modal .params [name=".concat(n.name,"]"));a.prop("required")&&""===n.value&&(t=!1,a.addClass("required-animate"),setTimeout((function(){a.removeClass("required-animate")}),800))}))}t&&n.confirm.call(e,r)&&($("#aa-modal").remove(),$("body").removeClass("no-scroll"),n.callback.call(e,r))})),$(".aa-modal-header-close").on("click",(function(t){n.cancel.call(e,t),$("#aa-modal").removeClass("active"),setTimeout((function(){$("#aa-modal").remove(),$("body").removeClass("no-scroll")}),300)})),$(".aa-modal-footer-cancel").on("click",(function(e){$("#aa-modal").removeClass("active"),n.cancel.call(r,e),setTimeout((function(){$("#aa-modal").remove(),$("body").removeClass("no-scroll")}),300)}));var o=$(".params",r);$("input,select,textarea",o).on("change input",(function(){var t=o.serializeArray(),a={};t.forEach((function(e){a[e.name]=e.value})),n.change.call(e,r,a)})),$("#aa-modal").addClass("active")}},{key:"getShortCodeRegex",value:function(e){return new RegExp("\\[(\\[?)("+e+")(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)","g")}}],i&&s(r.prototype,i),c&&s(r,c),Object.defineProperty(r,"prototype",{writable:!1}),e}())("#text","#md-preview")},770:()=>{},691:()=>{},923:()=>{},440:()=>{},166:()=>{}},n={};function a(e){var r=n[e];if(void 0!==r)return r.exports;var o=n[e]={exports:{}};return t[e](o,o.exports,a),o.exports}a.m=t,e=[],a.O=(t,n,r,o)=>{if(!n){var i=1/0;for(u=0;u<e.length;u++){for(var[n,r,o]=e[u],l=!0,c=0;c<n.length;c++)(!1&o||i>=o)&&Object.keys(a.O).every((e=>a.O[e](n[c])))?n.splice(c--,1):(l=!1,o<i&&(i=o));if(l){e.splice(u--,1);var s=r();void 0!==s&&(t=s)}}return t}o=o||0;for(var u=e.length;u>0&&e[u-1][2]>o;u--)e[u]=e[u-1];e[u]=[n,r,o]},a.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{var e={522:0,791:0,24:0,796:0,638:0,870:0};a.O.j=t=>0===e[t];var t=(t,n)=>{var r,o,[i,l,c]=n,s=0;if(i.some((t=>0!==e[t]))){for(r in l)a.o(l,r)&&(a.m[r]=l[r]);if(c)var u=c(a)}for(t&&t(n);s<i.length;s++)o=i[s],a.o(e,o)&&e[o]&&e[o][0](),e[o]=0;return a.O(u)},n=self.webpackChunkaaeditor=self.webpackChunkaaeditor||[];n.forEach(t.bind(null,0)),n.push=t.bind(null,n.push.bind(n))})(),a.O(void 0,[791,24,796,638,870],(()=>a(315))),a.O(void 0,[791,24,796,638,870],(()=>a(770))),a.O(void 0,[791,24,796,638,870],(()=>a(691))),a.O(void 0,[791,24,796,638,870],(()=>a(923))),a.O(void 0,[791,24,796,638,870],(()=>a(440)));var r=a.O(void 0,[791,24,796,638,870],(()=>a(166)));r=a.O(r)})();
//# sourceMappingURL=main.js.map