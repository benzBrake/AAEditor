<?php

use TypechoPlugin\AAEditor\Module;
use TypechoPlugin\AAEditor\Util;

/**
 *
 *
 * @package 小书签
 * @author Ryan
 * @version 0.0.1
 * @link https://doufu.ru
 *
 */
class ModuleBookmarklet implements Module
{

    public static function editorStatic(): void
    {
        ?>
        <script>
            $('body').trigger('XEditorAddButton', [{
                id: 'wmd-bookmarklet-button',
                name: '<?php _e("小书签"); ?>',
                icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M5 2H19C19.5523 2 20 2.44772 20 3V22.1433C20 22.4194 19.7761 22.6434 19.5 22.6434C19.4061 22.6434 19.314 22.6168 19.2344 22.5669L12 18.0313L4.76559 22.5669C4.53163 22.7136 4.22306 22.6429 4.07637 22.4089C4.02647 22.3293 4 22.2373 4 22.1433V3C4 2.44772 4.44772 2 5 2ZM18 4H6V19.4324L12 15.6707L18 19.4324V4Z"></path></svg>',
                insertBefore: '#wmd-spacer4',
                command() {
                    this.openModal({
                        title: '<?php _e("插入小书签")  ?>',
                        innerHTML: `<div class="form-item">
    <label for="name"><?php _e("书签名称"); ?></label>
    <input type="text" autocomplete="off" name="name" placeholder="<?php _e("请输入小书签名称"); ?>">
</div>
<div class="form-item">
    <label class="required" for="url"><?php _e("书签内容"); ?></label>
    <textarea required="required" rows="3" autocomplete="off" name="url" placeholder="javascript:"></textarea>
</div>
<div class="form-item">
    <label for="description"><?php _e("书签简介"); ?></label>
    <textarea rows="3" autocomplete="off" name="description" placeholder="<?php _e("小书签简介"); ?>"></textarea>
</div>`,
                        confirm(modal) {
                            let name = $('input[name="name"]', modal).val(),
                                url = $('textarea[name="url"]', modal).val(),
                                description = $('textarea[name="description"]', modal).val();

                            let targetText = `[bookmarklet url="${url}"${name ? ' name="' + name + '"' : ''}${description ? ' description="' + description + '"' : ''} /]`;
                            this.replaceSelection(targetText);
                            return true;
                        }
                    })
                },
            }]).trigger('XEditorAddHtmlProcessor', [
                function (html) {
                    if (html.indexOf("[bookmarklet")) {
                        html = html.replace(this.getShortCodeRegex("bookmarklet"), function (...matches) {
                            let bml = $('<div ' + matches[3] + '>' + matches[5] + '</div>')[0];
                            if (!(bml.hasAttribute('url') || bml.hasAttribute('href'))) return '';
                            let title = bml.getAttribute('name') || "<?php _e("小书签链接"); ?>";
                            let notice = "<?php _e("拖拽此链接到收藏夹或者书签工具栏：{button}") ?>";
                            notice = notice.replace('{button}', `<a class="x-bookmarklet-btn" href="${bml.getAttribute('url')}">${title}</a>`)
                            let description = bml.getAttribute('description') || "<?php _e("小书签（英语：bookmarklet），又叫书签小程序，是一种小型的程序，以网址（URL）的形式被存为浏览器中的书签，也可以是网页上的一个链接。小书签的英文名，Bookmarklet是由Bookmark和Applet组合而来。无论小书签以什么形式储存，它们都是用来给浏览器或是网页添加一些特定功能的。点击时，小书签会执行这些操作，包括执行搜索，导出数据等等。小书签一般是JavaScript程序。"); ?>";
                            return `<div class="x-bookmarklet">
<div class="x-bookmarklet-title"><div class="x-bookmarklet-icon">
<svg width="20" height="20" class="fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5 2H19C19.5523 2 20 2.44772 20 3V22.1433C20 22.4194 19.7761 22.6434 19.5 22.6434C19.4061 22.6434 19.314 22.6168 19.2344 22.5669L12 18.0313L4.76559 22.5669C4.53163 22.7136 4.22306 22.6429 4.07637 22.4089C4.02647 22.3293 4 22.2373 4 22.1433V3C4 2.44772 4.44772 2 5 2ZM18 4H6V19.4324L12 15.6707L18 19.4324V4Z"></path></svg>
</div>${notice}</div><div class="x-bookmarklet-description">${description}</div></div>`
                        });
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
              href="<?php echo Util::moduleUrl('Bookmarklet', 'index.css'); ?>">
        <?php
    }

    public static function parseContent($text, $archive): string
    {
        if (strpos($text, '[bookmarklet') === false) { //提高效率，避免每篇文章都要解析
            return $text;
        }
        $pattern = Util::get_shortcode_regex(['bookmarklet']);
        return preg_replace_callback("/$pattern/", function ($m) {
            $attrs = Util::shortcode_parse_atts(htmlspecialchars_decode($m[3] ?? ''));
            $url = $attrs['url'] ?? ($attrs['href'] ?? '');
            if (strlen($url)) {
                $name = (isset($attrs['name']) && strlen($attrs['name'])) ? $attrs['name'] : _t("小书签链接");
                $notice = _t('拖拽此链接到收藏夹或者书签工具栏：%s', sprintf('<a class="x-bookmarklet-btn" href="%s">%s</a>', $url, $name));
                $description = (isset($attrs['description']) && strlen($attrs['description'])) ? $attrs['description'] : _t("小书签（英语：bookmarklet），又叫书签小程序，是一种小型的程序，以网址（URL）的形式被存为浏览器中的书签，也可以是网页上的一个链接。小书签的英文名，Bookmarklet是由Bookmark和Applet组合而来。无论小书签以什么形式储存，它们都是用来给浏览器或是网页添加一些特定功能的。点击时，小书签会执行这些操作，包括执行搜索，导出数据等等。小书签一般是JavaScript程序。");
                return sprintf('<div class="x-bookmarklet"><div class="x-bookmarklet-title"><div class="x-bookmarklet-icon"><svg width="20" height="20" class="fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5 2H19C19.5523 2 20 2.44772 20 3V22.1433C20 22.4194 19.7761 22.6434 19.5 22.6434C19.4061 22.6434 19.314 22.6168 19.2344 22.5669L12 18.0313L4.76559 22.5669C4.53163 22.7136 4.22306 22.6429 4.07637 22.4089C4.02647 22.3293 4 22.2373 4 22.1433V3C4 2.44772 4.44772 2 5 2ZM18 4H6V19.4324L12 15.6707L18 19.4324V4Z"></path></svg></div>%s</div><div class="x-bookmarklet-description">%s</div></div>', $notice, $description);
            }
            return '';
        }, $text);
    }


    public static function parseExcerpt($text, $archive): string
    {
        if (strpos($text, '[x-span') === false) { //提高效率，避免每篇文章都要解析
            return $text;
        }
        $pattern = Util::get_shortcode_regex(['x-span']);
        return preg_replace("/$pattern/", '<span class="x-span"$3>$5</span>', $text);
    }
}
