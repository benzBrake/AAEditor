<?php

use TypechoPlugin\AAEditor\Module;
use TypechoPlugin\AAEditor\Util;

/**
 * 以卡片形式展示连接
 *
 * @package 链接卡片
 * @author Ryan
 * @version 0.0.1
 * @link https://doufu.ru
 *
 */
class ModuleLinkCard implements Module
{

    public static function editorStatic(): void
    {
        ?>
        <script>
            $('body').trigger('XEditorAddButton', [{
                id: 'wmd-link-card-button',
                name: '<?php _e("链接卡片"); ?>',
                icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M4 2C2.895 2 2 2.895 2 4L2 18L4 18L4 4L18 4L18 2L4 2 z M 8 6C6.895 6 6 6.895 6 8L6 20C6 21.105 6.895 22 8 22L20 22C21.105 22 22 21.105 22 20L22 8C22 6.895 21.105 6 20 6L8 6 z M 8 8L20 8L20 20L8 20L8 8 z M 16 9.0058594C15.230215 9.0058594 14.460443 9.2973698 13.878906 9.8789062L12.607422 11.150391L14.021484 12.564453L12.556641 14.029297L11.142578 12.615234L9.8789062 13.878906C8.7158332 15.041979 8.7158332 16.958021 9.8789062 18.121094C10.460397 18.702585 11.234094 19 12 19C12.765906 19 13.539603 18.702585 14.121094 18.121094L15.384766 16.857422L13.970703 15.443359L15.457031 13.957031L14.042969 12.542969L15.292969 11.292969C15.691896 10.894042 16.308104 10.894042 16.707031 11.292969C17.105958 11.691896 17.105958 12.308104 16.707031 12.707031L15.464844 13.949219L16.878906 15.363281L18.121094 14.121094C19.284167 12.958021 19.284167 11.041979 18.121094 9.8789062C17.539557 9.2973698 16.769785 9.0058594 16 9.0058594 z M 12.542969 14.042969L13.957031 15.457031L12.707031 16.707031C12.506522 16.90754 12.258094 17 12 17C11.741906 17 11.493478 16.90754 11.292969 16.707031C10.894042 16.308104 10.894042 15.691896 11.292969 15.292969L12.542969 14.042969 z"></path></svg>',
                insertAfter: '#wmd-link-button',
                command() {
                    this.openModal({
                        title: '<?php _e("插入链接卡片"); ?>',
                        innerHTML: `<div class="form-item">
    <label for="title"><?php _e("链接标题"); ?></label>
    <input type="text" placeholder="<?php _e("请输入链接标题"); ?>" value="" name="title">
</div>
<div class="form-item">
    <label for="url" class="required"><?php _e("链接地址"); ?></label>
    <input type="text" required="required" placeholder="<?php _e("请输入链接地址"); ?>" name="url">
</div>
<div class="form-item">
    <label for="url"><?php _e("图标链接"); ?></label>
    <input type="text" placeholder="<?php _e("留空自动获取"); ?>" name="icon">
</div>`,
                        confirm(modal) {
                            let url = $('[name="url"]', modal).val(),
                                title = $('[name="title"]', modal).val(),
                                icon = $('[name="icon"]', modal).val();
                            this.replaceSelection(`[x-link url="${url}"${title ? ' title="' + title + '"' : ""}${icon ? ' icon="' + icon + '"' : ""}/]`);
                            return true;
                        }
                    });
                }
            }]).trigger('XEditorAddHtmlProcessor', [
                function (html) {
                    if (html.indexOf("[x-link")) {
                        return html.replace(this.getShortCodeRegex("x-link"), `<div class="x-link-wrapper"><x-link$3>$5</x-link></div>`);
                    }
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
              href="<?php echo Util::moduleUrl('LinkCard', 'index.css'); ?>">
        <script>
            customElements.define(
                'x-link',
                class xLink extends HTMLElement {
                    constructor() {
                        super();
                        this.options = {
                            title: this.getAttribute("title") || this.getAttribute("url") || "",
                            url: this.getAttribute("url"),
                            icon: this.getAttribute("icon") || "<?php \Utils\Helper::options()->pluginUrl('AAEditor/index.php?url='); ?>" + this.getAttribute("url"),
                        }
                        if (this.options.url) {
                            this.outerHTML = `
                <a class="x-link" href="${this.options.url}" target="_blank">
                    <div class="x-link-backdrop"></div>
                    <div class="x-link-content">
                        <span class="x-link-title">${this.options.title}</span>
                        <span class="x-link-icon" style="background-image: url(${this.options.icon})"></span>
                    </div>
                </a>
            `;
                        }
                    }

                }
            );
        </script>
        <?php
    }

    public static function parseContent($text, $archive): string
    {
        if (strpos($text, '[x-link') === false) { //提高效率，避免每篇文章都要解析
            return $text;
        }
        $pattern = Util::get_shortcode_regex(['x-link']);
        return preg_replace_callback("/$pattern/", function ($matches) {
            $attrs = Util::shortcode_parse_atts(strip_tags(htmlspecialchars_decode($matches[3])));
            $url = array_key_exists('url', $attrs) ? strip_tags($attrs['url']) : '';
            if (empty($url)) {
                return '';
            }
            $title = array_key_exists('title', $attrs) ? $attrs['title'] : $url;
            $icon = array_key_exists('icon', $attrs) ? $attrs['icon'] : Util::pluginUrl('index.php?url=' . $url);
            return '<div class="x-link-wrapper"><a class="x-link" href="' . $url . '" target="_blank">
<div class="x-link-backdrop"></div>
    <div class="x-link-content">
        <span class="x-link-title">' . $title . '</span>
        <span class="x-link-icon" style="background-image: url(' . $icon . ')"></span>
    </div>
</a>
</div>';
        }, $text);
    }


    public static function parseExcerpt($text, $archive): string
    {
        if (strpos($text, '[x-link') === false) { //提高效率，避免每篇文章都要解析
            return $text;
        }
        $pattern = Util::get_shortcode_regex(['x-link']);
        return preg_replace_callback(/**
         * @throws \Typecho\Exception
         */ "/$pattern/", function ($m) {
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
        if (array_key_exists('title', $attrs)) {
            return sprintf("【📶%s】",  $attrs['title']);
        }
        if (array_key_exists('url', $attrs)) {
            return sprintf("【📶%s】",  $attrs['url']);
        }
        return '';
    }
}
