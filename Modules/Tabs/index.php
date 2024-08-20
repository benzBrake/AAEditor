<?php

use TypechoPlugin\AAEditor\Module;
use TypechoPlugin\AAEditor\Util;

/**
 * 以多标签形式排版正文
 *
 * @package 多标签卡片
 * @author Ryan
 * @version 0.0.1
 * @link https://doufu.ru
 *
 */
class ModuleTabs implements Module
{
    public static function editorStatic(): void
    {
        ?>
        <script>
            $('body').trigger('XEditorAddButton', [{
                id: 'wmd-tabs-button',
                name: '<?php _e("多标签"); ?>',
                icon: '<svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="20" height="20"><path d="M202.667 149.333c-52.651 0-96 43.35-96 96v533.334c0 52.65 43.349 96 96 96h618.666c52.651 0 96-43.35 96-96V245.333c0-52.65-43.349-96-96-96h-448a32 32 0 0 0-5.504.427c-2.816-.213-5.653-.427-8.49-.427H202.667zm0 64h156.672a53.32 53.32 0 0 1 37.696 15.616l102.997 103.019a32 32 0 0 0 22.635 9.365h298.666c18.048 0 32 13.952 32 32v405.334c0 18.048-13.952 32-32 32H202.667c-18.048 0-32-13.952-32-32V245.333c0-18.048 13.952-32 32-32zm269.248 0h349.418c18.048 0 32 13.952 32 32v37.974a94.016 94.016 0 0 0-32-5.974H535.915l-64-64z"></path></svg>',
                insertBefore: '#wmd-spacer4',
                command() {
                    this.openModal({
                        title: '<?php _e("插入多标签卡片"); ?>',
                        innerHTML: `<x-custom-tabs></x-custom-tabs>`,
                        checkEmptyOnConfirm: false,
                        confirm(modal) {
                            let items = [];
                            $(modal).find('.x-custom-tabs-nav-item').each(function () {
                                let tab = $(this);
                                let tabId = tab.attr('data-id'),
                                    content = $('.x-custom-tabs-content-item[data-id="' + tabId + '"]', modal);
                                if (content) {
                                    let tabTitle = $('input[name="title"]', content),
                                        tabContent = $('textarea[name="content"]', content),
                                        isDefault = $('input[name="is-default"]', content)[0].checked;
                                    items.push(`[tab name="${tabTitle.val()}"${isDefault ? ' active="true"' : ''}]\n${tabContent.val()}\n[/tab]`);
                                }
                            });
                            if (items.length) {
                                this.replaceSelection((this.textarea.isAtLineStart() ? '' : '\n') + '[tabs]\n' + items.join('\n') + '\n[/tabs]' + (this.textarea.isAtLineEnd() ? '' : '\n'));
                            }
                            return true;
                        },
                        handle(modal) {
                            $(modal).find('.aa-modal-frame').css({
                                'width': '100%',
                                'max-width': '640px'
                            });
                        }
                    });
                }
            }]).trigger('XEditorAddHtmlProcessor', [
                function (html) {
                    if (html.indexOf("[tabs")) {
                        html = html.replace(this.getShortCodeRegex("tabs"), `<div class="x-tabs-wrapper"><x-tabs$3>$5</x-tabs></div>`);
                    }
                    if (html.indexOf("[x-tabs")) {
                        html = html.replace(this.getShortCodeRegex("x-tabs"), `<div class="x-tabs-wrapper"><x-tabs$3>$5</x-tabs></div>`);
                    }
                    if (html.indexOf("[tab")) {
                        html = html.replace(this.getShortCodeRegex("tab"), `<div class="x-tab"$3>$5</div>`);
                    }
                    if (html.indexOf("[x-tab")) {
                        return html.replace(this.getShortCodeRegex("x-tab"), `<div class="x-tab"$3>$5</div>`);
                    }
                }
            ]);
        </script>
        <script>
            customElements.define(
                'x-custom-tabs',
                class xCustomTabs extends HTMLElement {
                    constructor() {
                        super();

                        // 修改 HTML 结构
                        let id = "x-tabs-" + Math.floor((Math.random() * 10000) + 1);
                        while (document.querySelector("#" + id)) {
                            id = "x-tabs-" + Math.floor((Math.random() * 10000) + 1);
                        }
                        this.outerHTML = `<div class="x-custom-tabs" id="${id}">
<div class="x-custom-tabs-toolbar">
<div class="x-custom-tabs-nav">

</div>
<div class="x-custom-tabs-toolbar-right">
    <button class="x-custom-tabs-add btn primary" type="button"><?php _e("增加") ?></button>
    <button disabled class="x-custom-tabs-remove-current btn danger" type="button"><?php _e("删除") ?></button>
</div>
</div>
<div class="x-custom-tabs-content">
</div>
<div class="x-custom-tabs-info">
<?php _e("点击删除可以删除当前标签页，鼠标拖动标签页即可排序。");?>
<br>
<span is-default>红色加粗</span>标签即为默认激活标签
</div>
</div>`;
                        const tabs = document.getElementById(id);

                        if (tabs) {
                            const nav = tabs.querySelector('.x-custom-tabs-nav');
                            const content = tabs.querySelector('.x-custom-tabs-content');
                            const removeCurrentBtn = tabs.querySelector('.x-custom-tabs-remove-current');

                            // 退拽排序 Start
                            let draggedItem = null;
                            let targetItem = null;
                            let observer = new MutationObserver(() => {
                                if (getTabsNum() > 1) {
                                    removeCurrentBtn.removeAttribute('disabled');
                                } else {
                                    removeCurrentBtn.setAttribute('disabled', 'disabled');
                                }
                            });

                            observer.observe(nav, {
                                childList: true
                            });

                            nav.addEventListener('dragstart', (e) => {
                                draggedItem = e.target;
                                e.dataTransfer.setData('text/plain', ''); // Required for Firefox
                            });

                            nav.addEventListener('dragover', (e) => {
                                e.preventDefault();
                            });

                            nav.addEventListener('dragenter', (e) => {
                                if (e.target !== draggedItem) {
                                    targetItem = e.target;
                                    e.target.classList.add('drag-over');
                                }
                            });

                            nav.addEventListener('dragleave', (e) => {
                                if (e.target !== draggedItem) {
                                    e.target.classList.remove('drag-over');
                                }
                            });

                            nav.addEventListener('drop', (e) => {
                                e.preventDefault();
                                if (targetItem) {
                                    const rect = targetItem.getBoundingClientRect();
                                    const offsetX = e.clientX - rect.left;
                                    const center = rect.width / 2;

                                    if (offsetX >= center) {
                                        // Dragged to the right of targetItem
                                        targetItem.after(draggedItem);
                                    } else {
                                        // Dragged to the left of targetItem
                                        targetItem.before(draggedItem);
                                    }

                                    Array.from(nav.children).forEach(tab => {
                                        let c = content.querySelector(`[data-id="${tab.getAttribute('data-id')}"`);
                                        content.appendChild(c);
                                    });
                                }
                                nav.querySelectorAll('.drag-over').forEach(item => item.classList.remove('drag-over'));
                            });
                            // 拖拽排序 End

                            tabs.querySelector('.x-custom-tabs-add').addEventListener('click', addTab);
                            removeCurrentBtn.addEventListener('click', removeCurrentTab);

                            addTab();

                            function getTabsNum() {
                                return tabs.querySelectorAll('.x-custom-tabs-nav-item').length;
                            }

                            function removeAllActive() {
                                Array.from(nav.querySelectorAll('.x-custom-tabs-nav-item-active')).forEach(el => {
                                    el.classList.remove('x-custom-tabs-nav-item-active');
                                });
                                Array.from(content.querySelectorAll('.x-custom-tabs-content-item-active')).forEach(el => {
                                    el.classList.remove('x-custom-tabs-content-item-active');
                                });
                            }

                            function switchToTab(id) {
                                removeAllActive();
                                nav.querySelector(`.x-custom-tabs-nav-item[data-id="${id}"]`).classList.add('x-custom-tabs-nav-item-active');
                                content.querySelector(`.x-custom-tabs-content-item[data-id="${id}"]`).classList.add('x-custom-tabs-content-item-active');
                            }

                            function addTab() {
                                removeAllActive();
                                let uniId = Math.floor((Math.random() * 10000) + 1);
                                while (tabs.querySelector(`[data-id="${uniId}"]`)) {
                                    uniId = Math.floor((Math.random() * 10000) + 1);
                                }
                                let navItem = document.createElement('div'),
                                    newNum = getTabsNum() + 1;
                                navItem.className = 'x-custom-tabs-nav-item x-custom-tabs-nav-item-active';
                                if (newNum === 1) {
                                    navItem.setAttribute('is-default', 'true');
                                }
                                navItem.setAttribute('data-id', uniId);
                                navItem.setAttribute('draggable', 'true');
                                navItem.innerHTML = '<?php _e("标签%s"); ?>'.replace('%s', newNum);
                                navItem.addEventListener('click', () => {
                                    switchToTab(uniId);
                                });
                                nav.appendChild(navItem);
                                let contentItem = document.createElement('div');
                                contentItem.className = 'x-custom-tabs-content-item x-custom-tabs-content-item-active';
                                contentItem.setAttribute('data-id', uniId);
                                let input = document.createElement('input');
                                input.value = '<?php _e("标签%s"); ?>'.replace('%s', newNum);
                                input.setAttribute('data-id', uniId);
                                input.name = 'title';
                                input.addEventListener('input', function (e) {
                                    let id = e.target.getAttribute('data-id');
                                    let value = e.target.value;
                                    let navItem = document.querySelector('.x-custom-tabs-nav-item[data-id="' + id + '"]');
                                    navItem.innerHTML = value;
                                });
                                contentItem.appendChild(input);
                                let textarea = document.createElement('textarea');
                                textarea.value = '<?php _e("标签%s内容") ?>'.replace('%s', newNum);
                                textarea.setAttribute('data-id', uniId);
                                textarea.name = 'content';
                                contentItem.appendChild(textarea);
                                let defaultWrapper = document.createElement('div');
                                let defaultLabel = document.createElement('label');
                                let defaultCheck = document.createElement('input');
                                defaultCheck.type = 'checkbox';
                                defaultCheck.name = 'is-default'
                                if (newNum === 1) {
                                    defaultCheck.checked = true;
                                }
                                defaultCheck.setAttribute('data-id', uniId);
                                defaultCheck.id = 'tabs-df-' + uniId;
                                defaultCheck.addEventListener('click', ({target}) => {
                                    let targetId = target.getAttribute('data-id');
                                    Array.from(content.querySelectorAll('input[name="is-default"]')).forEach(i => {
                                        i.checked = false;
                                    });
                                    target.checked = true;
                                    Array.from(nav.querySelectorAll('.x-custom-tabs-nav-item[is-default]')).forEach(i => {
                                        i.removeAttribute('is-default');
                                    });
                                    nav.querySelector('[data-id="' + targetId + '"]').setAttribute('is-default', 'true');
                                });
                                defaultLabel.setAttribute("for", 'tabs-df-' + uniId);
                                defaultLabel.innerText = '<?php _e("默认激活") ?>';
                                defaultWrapper.appendChild(defaultCheck);
                                defaultWrapper.appendChild(defaultLabel);
                                contentItem.appendChild(defaultWrapper);
                                content.appendChild(contentItem);
                            }

                            function removeCurrentTab() {
                                let currentTab = document.querySelector('.x-custom-tabs-nav-item-active'),
                                    currentContent = document.querySelector('.x-custom-tabs-content-item-active');
                                if (currentTab && currentContent && getTabsNum() > 1) {
                                    let nextSelectTab = currentTab.nextSibling || currentTab.previousSibling;
                                    let nextSelectContent = content.querySelector(`.x-custom-tabs-content-item[data-id="${nextSelectTab?.getAttribute('data-id')}"]`);
                                    nextSelectTab?.classList.add('x-custom-tabs-nav-item-active');
                                    nextSelectContent?.classList.add('x-custom-tabs-content-item-active');
                                    currentTab.parentNode?.removeChild(currentTab);
                                    currentContent.parentNode?.removeChild(currentContent);
                                }
                            }
                        }
                    }
                }
            );
        </script>
        <?php
        self::commonStatic();
    }

    /**
     * 前台输出静态资源
     *
     * @param $archive
     * @return void
     */
    public static function archiveStatic($archive): void
    {
        self::commonStatic();
    }

    /**
     * 自定义函数，输出样式 link href
     *
     * @return void
     */
    public static function commonStatic(): void
    {
        ?>
        <link rel="stylesheet"
              href="<?php echo Util::moduleUrl('Tabs', 'index.css'); ?>">
        <script>
            customElements.define(
                'x-tabs',
                class xTabs extends HTMLElement {
                    constructor() {
                        super();
                        this.classList.add('x-tabs');
                        Array.from(this.querySelectorAll(':scope>br')).forEach(b => this.removeChild(b));
                        let tabs = Array.from(this.querySelectorAll(':scope>.x-tab'));
                        if (tabs.length) {
                            let tabNav = document.createElement('ul'),
                                tabContent = document.createElement('div');
                            tabNav.className = 'x-tabs-nav';
                            tabContent.className = 'x-tabs-content';
                            for (let i = 0; i < tabs.length; i++) {
                                let tab = tabs[i],
                                    tabNavItem = document.createElement('li'),
                                    tabContentItem = document.createElement('div');
                                if (tab.getAttribute('active') === "true") {
                                    this.setAttribute("active", i + 1);
                                }
                                tabNavItem.className = 'x-tabs-nav-item';
                                tabNavItem.setAttribute('tabindex', i + 1);
                                let name = tab.getAttribute('name');
                                let title = tab.getAttribute('title');
                                tabNavItem.innerHTML = '<span>' + (name ? name : (title ? title : '<?php _e("标签") ?>'.replace("%d", i + 1))) + '<span>';
                                tabNavItem.addEventListener('click', () => {
                                    this.setAttribute('active', tabNavItem.getAttribute('tabindex'));
                                })
                                tabNav.appendChild(tabNavItem);
                                tabContentItem.className = 'x-tabs-content-item';
                                tabContentItem.setAttribute('tabindex', i + 1);
                                if (tab.firstElementChild && tab.firstElementChild.tagName === "BR") {
                                    tab.removeChild(tab.firstElementChild);
                                }
                                if (tab.lastElementChild && tab.lastElementChild.tagName === "BR") {
                                    tab.removeChild(tab.lastElementChild);
                                }
                                tabContentItem.innerHTML = tab.innerHTML;
                                if (tab.previousSibling && tab.previousSibling.tagName === "SPAN" && tab.previousSibling.className === 'line') {
                                    if (tabContentItem.firstElementChild) {
                                        tabContentItem.insertBefore(tab.previousSibling, tabContentItem.firstElementChild);
                                    } else {
                                        tabContentItem.appendChild(tab.previousSibling);
                                    }
                                }
                                if (tabContentItem.firstElementChild && tabContentItem.firstElementChild.tagName.toString() === "P" && tabContentItem.firstElementChild.innerHTML == "") {
                                    tabContentItem.removeChild(tabContentItem.firstElementChild);
                                }
                                if (tabContentItem.lastElementChild && tabContentItem.lastElementChild.tagName.toString() === "P" && tabContentItem.lastElementChild.innerHTML == "") {
                                    tabContentItem.removeChild(tabContentItem.lastElementChild);
                                }
                                let {children: chlren} = tabContentItem;
                                let chl = chlren.length;
                                if (chl > 2 && chlren[chl - 2].matches('br') && chlren[chl - 1].matches('span.line[data-start]')) {
                                    chlren[chl - 2].remove();
                                }
                                tabContent.appendChild(tabContentItem);
                                this.removeChild(tab);
                            }
                            this.appendChild(tabNav);
                            this.appendChild(tabContent);

                            Array.from(this.querySelectorAll(':scope>span.line')).forEach(span => {
                                if (span.nextElementSibling && span.nextElementSibling.classList.contains('x-tabs-content-item')) {
                                    span.nextElementSibling.insertBefore(span, span.nextElementSibling.firstElementChild);
                                } else {
                                    span.parentNode.removeChild(span);
                                }
                            });

                            if (!this.hasAttribute("active") && tabNav.childElementCount) {
                                this.setAttribute('active', 1);
                            }

                            switchToTab(this.getAttribute('active'));

                            let observer = new MutationObserver((mutations) => {
                                mutations.forEach(mutation => {
                                    if (mutation.attributeName === "active") {
                                        switchToTab(mutation.target.getAttribute('active'));
                                    }
                                })
                            });

                            observer.observe(this, {
                                attributes: true
                            })

                            function switchToTab(tabindex) {
                                Array.from(tabNav.querySelectorAll('.x-tabs-nav-item-active')).forEach(navItem => {
                                    navItem.classList.remove('x-tabs-nav-item-active');
                                });
                                Array.from(tabContent.querySelectorAll('.x-tabs-content-item-active')).forEach(contentItem => {
                                    contentItem.classList.remove('x-tabs-content-item-active');
                                });
                                tabNav.querySelector(`:scope>[tabindex="${tabindex}"]`).classList.add('x-tabs-nav-item-active');
                                tabContent.querySelector(`:scope>[tabindex="${tabindex}"]`).classList.add('x-tabs-content-item-active');
                            }
                        }
                    }
                }
            );
        </script>
        <?php
    }

    public static function parseContent($text, $archive): string
    {
        $patternTabs = Util::get_shortcode_regex(['tabs', 'x-tabs']);
        $patternTab = Util::get_shortcode_regex(['tab', 'x-tab']);
        $regex_useless = '/^\<br>|\<br>$/';
        return preg_replace_callback("/$patternTabs/", function ($m) use ($patternTab, $regex_useless) {
            // Allow [[foo]] syntax for escaping a tag.
            if ('[' === $m[1] && ']' === $m[6]) {
                return substr($m[0], 1, -1);
            }
            $attr = htmlspecialchars_decode($m[3]);
            $attrs = Util::shortcode_parse_atts($attr);
            $active = is_array($attrs) && array_key_exists('active', $attrs) ? $attrs['active'] : 0;
            preg_match_all("/$patternTab/", $m[5], $matches);
            $tabs_html = [];
            for ($i = 0; $i < count($matches[0]); $i++) {
                $a = htmlspecialchars_decode($matches[3][$i]);
                $attrs = Util::shortcode_parse_atts($a);
                if (!is_array($attrs)) $attrs = [];
                $title = $attrs['name'] ?? $attrs['title'] ?? _t("标签 %d", $i + 1);
                if ($attrs['active'] ?? '' === "true") $active = $i + 1;
                $t = trim($matches[5][$i]);
                $t = preg_replace($regex_useless, '', $t);
                $tabs_html[$i] = "<div title='{$title}' class='x-tab'>{$t}</div>";
            }
            if ($active < 1) $active = 1;
            $content = implode('', $tabs_html);
            return "<div class='x-tabs-wrapper' style='position: relative'><x-tabs active='{$active}' style='display: block; position: relative'>{$content}</x-tabs></div>";
        }, $text);
    }


    public static function parseExcerpt($text, $archive): string
    {
        $patternTabs = Util::get_shortcode_regex(['tabs', 'x-tabs']);
        $patternTab = Util::get_shortcode_regex(['tab', 'x-tab']);
        $text = preg_replace("/$patternTabs/", '$5', $text);
        return preg_replace_callback(/**
         * @throws \Typecho\Exception
         */ "/$patternTab/", function ($m) {
            return self::excerptCallback($m);
        }, $text);
    }

    public static function excerptCallback($m)
    {
        // Allow [[foo]] syntax for escaping a tag.
        if ('[' === $m[1] && ']' === $m[6]) {
            return substr($m[0], 1, -1);
        }
        $attrs = Util::shortcode_parse_atts($m[3]);
        $text = '';
        if (array_key_exists('title', $attrs)) {
            $text .= $attrs['title'];
        }
        if (array_key_exists('name', $attrs)) {
            $text .= $attrs['name'];
        }
        return $text . $m[5];
    }
}
