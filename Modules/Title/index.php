<?php

use TypechoPlugin\AAEditor\Module;
use TypechoPlugin\AAEditor\Util;

/**
 * 居中大字标题
 *
 * @package 居中标题
 * @author Ryan
 * @version 0.0.1
 * @link https://doufu.ru
 *
 */
class ModuleTitle implements Module
{

    public static function editorStatic(): void
    {
        ?>
        <script>
            $('body').trigger('XEditorAddButton', [{
                id: 'wmd-title-button',
                name: '<?php _e("居中标题"); ?>',
                icon: '<svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="20" height="20"><path d="M967.1 455.3H672.2c-23.5-66.1-86-113.8-160.2-113.8s-136.7 47.6-160.2 113.8H56.9C25.5 455.3 0 480.7 0 512.2c0 31.4 25.5 56.9 56.9 56.9h294.9c23.5 66.1 86 113.8 160.2 113.8s136.7-47.6 160.2-113.8h294.9c31.4 0 56.9-25.5 56.9-56.9 0-31.5-25.5-56.9-56.9-56.9zM512 569.1c-31.4 0-56.9-25.5-56.9-56.9s25.5-56.9 56.9-56.9 56.9 25.5 56.9 56.9c0 31.3-25.5 56.9-56.9 56.9z"></path></svg>',
                insertAfter: '#wmd-spacer3',
                command() {
                    this.wrapText('[x-title]', '[/x-title]', '<?php _e("居中标题") ?>')
                }
            }]).trigger('XEditorAddHtmlProcessor', [
                function (html) {
                    if (html.indexOf("[x-title")) {
                        html = html.replace(this.getShortCodeRegex("x-title"), `<div class="x-title"$3><span class="x-title-text">$5<span></div>`);
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
              href="<?php echo Util::moduleUrl('Title', 'index.css'); ?>">
        <?php
    }

    public static function parseContent($text, $archive): string
    {
        if (strpos($text, '[x-title') === false) { //提高效率，避免每篇文章都要解析
            return $text;
        }
        $pattern = Util::get_shortcode_regex(['x-title']);
        return preg_replace("/$pattern/", '<div class="x-title"$3><span class="x-title-text">$5<span></div>', $text);
    }


    public static function parseExcerpt($text, $archive): string
    {
        if (strpos($text, '[x-title') === false) { //提高效率，避免每篇文章都要解析
            return $text;
        }
        $pattern = Util::get_shortcode_regex(['x-title']);
        return preg_replace("/$pattern/", '<div class="x-title"$3><span class="x-title-text">$5<span></div>', $text);
    }
}
