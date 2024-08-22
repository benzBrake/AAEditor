<?php

use TypechoPlugin\AAEditor\Module;
use TypechoPlugin\AAEditor\Util;

/**
 * 以折叠卡片形式排版正文
 *
 * @package 折叠卡片
 * @author Ryan
 * @version 0.0.1
 * @link https://doufu.ru
 *
 */
class ModuleCard implements Module
{

    public static function editorStatic(): void
    {
        ?>
        <script>
            $('body').trigger('XEditorAddButton', [{
                id: 'wmd-card-button',
                name: '<?php _e("折叠卡片"); ?>',
                icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M3.00488 3H21.0049C21.5572 3 22.0049 3.44772 22.0049 4V20C22.0049 20.5523 21.5572 21 21.0049 21H3.00488C2.4526 21 2.00488 20.5523 2.00488 20V4C2.00488 3.44772 2.4526 3 3.00488 3ZM20.0049 11H4.00488V19H20.0049V11ZM20.0049 9V5H4.00488V9H20.0049ZM14.0049 15H18.0049V17H14.0049V15Z"></path></svg>',
                insertBefore: '#wmd-spacer4',
                command() {
                    let lastSelection = this.textarea.getSelection();

                    this.openModal({
                        title: '<?php _e("插入折叠卡片"); ?>',
                        innerHTML: `<div class="form-item">
            <label for="fold"><?php _e("默认状态"); ?></label>
            <select name="fold">
                <option value="on"><?php _e("折叠"); ?></option>
                <option value="off"><?php _e("展开"); ?></option>
                <option value="frozen"><?php _e("固定") ?></option>
            </select>
        </div>
        <div class="form-item">
            <label for="title"><?php _e("标题内容"); ?></label>
            <input type="text" placeholder="<?php _e("请输入标题"); ?>" name="title">
        </div>
        <div class="form-item for-textarea">
            <label for="text"><?php _e("正文内容"); ?></label>
            <textarea placeholder="<?php _e("请输入正文"); ?>" name="text" rows="10"></textarea>
        </div>`,
                        confirm(modal) {
                            let text = $('[name="text"]', modal).val() || "<?php _e("折叠卡片内容") ?>";
                            let fold = $('[name="fold"]', modal).val() || "on";
                            let title = $('[name="title"]', modal).val() || "<?php _e("折叠卡片标题") ?>";
                            if (fold === "frozen") {
                                this.replaceSelection(`[card title="${title}"]${text}[/card]`);
                            } else {
                                this.replaceSelection(`[x-card title="${title}" fold="${fold}"]${text}[/x-card]`);
                            }
                            return true;
                        }
                    });
                }
            }]).trigger('XEditorAddHtmlProcessor', [
                function (html) {
                    let fn = createCardCallback.bind(this);
                    if (html.indexOf("[x-cards")) {
                        html = html.replace(this.getShortCodeRegex("x-cards"), (...matches) => {
                            let content = matches[5];
                            content = content.replace(this.getShortCodeRegex("x-card"), fn);
                            content = content.replace(this.getShortCodeRegex("collapse"), fn);
                            return '<div class="x-cards-wrapper"><div class="x-card" ' + matches[3] + '>' + content + '</div></div>';
                        });
                    }
                    if (html.indexOf("[collapses")) {
                        html = html.replace(this.getShortCodeRegex("collapses"), (...matches) => {
                            let content = matches[5];
                            content = content.replace(this.getShortCodeRegex("x-card"), fn);
                            content = content.replace(this.getShortCodeRegex("collapse"), fn);
                            return '<div class="x-cards-wrapper"><div class="x-card" ' + matches[3] + '>' + content + '</div></div>';
                        });
                    }
                    if (html.indexOf("[collapse")) {
                        html = html.replace(this.getShortCodeRegex("collapse"), fn);
                    }
                    if (html.indexOf("[x-card")) {
                        html = html.replace(this.getShortCodeRegex("x-card"), fn);
                    }
                    if (html.indexOf("[card")) {
                        html = html.replace(this.getShortCodeRegex("card"), (...matches) => {
                            let div = document.createElement('div');
                            div.innerHTML = `<div class="x-card-wrapper"${matches[3]}></div>`;
                            let title = div.firstElementChild.getAttribute('title') ?? '<?php _e("无标题") ?>';
                            return `<div class="x-card-static"><div class="x-card-title">${title}</div><div class="x-card-content">${matches[5]}</div><div>`;
                        });
                    }

                    function createCardCallback() {
                        let attr = arguments[3] || "", content = arguments[5], classList = ['x-card'],
                            title = '<?php _e("无标题") ?>';
                        let {named} = this.parseShortCodeAttrs(attr);
                        if (named['class']) {
                            classList.push(named['class'].split(" "));
                        }
                        if (named['title']) {
                            title = named['title'];
                        }
                        if (named['fold'] === 'true' || named['fold'] === 'on' || named['fold'] === '1') {
                            classList.push('fold');
                        }
                        return `<div class="x-cards-wrapper" style="overflow: hidden"><div class="${classList.join(" ")}"><div class="x-card-title">${title}<span class="x-card-icon"></span></div><div class="x-card-content">${content}</div></div></div>`;
                    }

                    return html;
                }
            ]);
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
              href="<?php echo Util::moduleUrl('Card', 'index.css'); ?>">
        <script>
            function xCardInit() {
                const xCards = document.querySelectorAll('.x-cards-wrapper:not([x-card-inited])');
                for (let i = 0; i < xCards.length; i++) {
                    xCards[i].setAttribute("x-card-inited", "true");
                    xCards[i].querySelectorAll(":scope>br").forEach(br => br.parentNode.removeChild(br));
                    xCards[i].querySelectorAll(":scope>.x-card").forEach((card) => {
                        card.firstElementChild.addEventListener('click', (e) => {
                            let wrapper = e.currentTarget.closest(".x-cards-wrapper");
                            let currentCard = e.currentTarget.closest(".x-card");
                            let isFold = currentCard.classList.contains("fold");
                            if (wrapper.getAttribute("type") === "blinds") {
                                wrapper.querySelectorAll(".x-card").forEach((card) => {
                                    card.classList.add("fold");
                                });
                            }
                            if (isFold) {
                                currentCard.classList.remove("fold");
                            } else {
                                currentCard.classList.add("fold");
                            }
                        });
                    })
                }
            }

            document.addEventListener('DOMContentLoaded', xCardInit);
            document.addEventListener('pjax:complete', xCardInit);
            document.addEventListener('XMPreviewEnd', xCardInit);
        </script>
        <?php
    }

    public static function parseContent($text, $archive): string
    {
        if (strpos($text, '[x-cards') !== false || strpos($text, '[collapses') !== false) {
            $patternWrapper = Util::get_shortcode_regex(['x-cards', 'collapses']);
            $text = preg_replace_callback("/$patternWrapper/", function ($m) {
                // Allow [[foo]] syntax for escaping a tag.
                if ('[' === $m[1] && ']' === $m[6]) {
                    return substr($m[0], 1, -1);
                }
                $pattern = Util::get_shortcode_regex(['x-card', 'collapse']);
                $index = 1;
                $content = preg_replace_callback("/$pattern/", function ($matches) use (&$index) {
                    $attrs = Util::shortcode_parse_atts(htmlspecialchars_decode($matches[3]));
                    $classList = ['x-card'];
                    if (array_key_exists("fold", $attrs) && $attrs['fold'] === "on") {
                        array_push($classList, "fold");
                    }
                    $title = _t("标题 %s", $index);
                    if (array_key_exists("title", $attrs)) {
                        $title = $attrs['title'];
                    }
                    $html = sprintf('<div class="%s" data-index="%d"><div class="x-card-title">%s<span class="x-card-icon"></span></div><div class="x-card-content">%s</div></div>', implode(" ", $classList), $index, $title, $matches[5]);
                    $index++;
                    return $html;
                }, $m[5]);
                return '<div class="x-cards-wrapper"' . $m[3] . '>' . $content . '</div>';
            }, $text);
        }

        if (strpos($text, '[collapse') !== false || strpos($text, '[x-card') !== false) { //提高效率，避免每篇文章都要解析
            $pattern = Util::get_shortcode_regex(['collapse', 'x-card']);
            $text = preg_replace_callback("/$pattern/", function ($matches) {
                $attrs = Util::shortcode_parse_atts(htmlspecialchars_decode($matches[3]));
                $classList = ['x-card'];
                if (array_key_exists("fold", $attrs) && $attrs['fold'] === "on") {
                    array_push($classList, "fold");
                }
                $title = _t("标题");
                if (array_key_exists("title", $attrs)) {
                    $title = $attrs['title'];
                }
                return sprintf('<div class="x-cards-wrapper" style="overflow: hidden"><div class="%s"><div class="x-card-title">%s<span class="x-card-icon"></span></div><div class="x-card-content">%s</div></div></div>', implode(" ", $classList), $title, $matches[5]);
            }, $text);
        }

        if (strpos($text, '[card') !== false) {
            $pattern = Util::get_shortcode_regex(['card']);
            $text = preg_replace_callback("/$pattern/", function ($matches) {
                $attrs = Util::shortcode_parse_atts(htmlspecialchars_decode($matches[3]));
                $title = _t("无标题");
                if (array_key_exists("title", $attrs)) {
                    $title = $attrs['title'];
                }
                return sprintf('<div class="x-card-static" style="overflow: hidden"><div class="x-card-title">%s</div><div class="x-card-content">%s</div></div>', $title, $matches[5]);
            }, $text);
        }
        return $text;
    }

    public static function parseExcerpt($text, $archive): string
    {
        if (strpos($text, '[x-cards') !== false) {
            $pattern = Util::get_shortcode_regex(['x-cards']);
            $text = preg_replace("/$pattern/", '$5', $text);
        }
        if (strpos($text, '[collapse') !== false || strpos($text, '[x-card') !== false || strpos($text, '[card') !== false) { //提高效率，避免每篇文章都要解析
            $pattern = Util::get_shortcode_regex(['collapse', 'x-card', 'card']);
            $text = preg_replace_callback(/**
             * @throws \Typecho\Exception
             */ "/$pattern/", function ($m) {
                return self::excerptCallback($m);
            }, $text);
        }
        return $text;
    }

    public static function excerptCallback($m)
    {
        // Allow [[foo]] syntax for escaping a tag.
        if ('[' === $m[1] && ']' === $m[6]) {
            return substr($m[0], 1, -1);
        }
        $attrs = Util::shortcode_parse_atts($m[3]) ?? [];
        $text = '【🗂️';
        if (array_key_exists('title', $attrs)) {
            $text .= $attrs['title'];
        }
        return $text . ' · ' . $m[5] . '】';
    }
}
