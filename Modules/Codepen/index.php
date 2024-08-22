<?php

use TypechoPlugin\AAEditor\Module;
use TypechoPlugin\AAEditor\Util;

/**
 * 插入 Codepen 前端代码预览
 *
 * @package Codepen
 * @author Ryan
 * @version 0.0.1
 * @link https://doufu.ru
 *
 */
class ModuleCodepen implements Module
{

    public static function editorStatic(): void
    {
        ?>
        <script>
            $('body').trigger('XEditorAddButton', [{
                id: 'wmd-codepen-button',
                name: '<?php _e("插入 Codepen"); ?>',
                icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="20" height="20"><path d="M16 2.84375L15.4375 3.21875L3.4375 11.25L3 11.53125L3 20.46875L3.4375 20.75L15.4375 28.78125L16 29.15625L16.5625 28.78125L28.5625 20.75L29 20.46875L29 11.53125L28.5625 11.25L16.5625 3.21875 Z M 15 5.90625L15 11.34375L9.84375 14.8125L5.8125 12.09375 Z M 17 5.90625L26.1875 12.09375L22.15625 14.8125L17 11.34375 Z M 16 13.09375L20.34375 16L16 18.90625L11.65625 16 Z M 5 13.9375L8.0625 16L5 18.0625 Z M 27 13.9375L27 18.0625L23.9375 16 Z M 9.875 17.1875L15 20.65625L15 26.09375L5.8125 19.90625 Z M 22.125 17.1875L26.1875 19.90625L17 26.09375L17 20.65625Z"></path></svg>',
                insertBefore: '#wmd-spacer4',
                command() {
                    this.openModal({
                        title: '<?php _e("插入 Codepen"); ?>',
                        innerHTML: `<div class="form-item">
    <label class="required" for="url"><?php _e("Codepen 链接"); ?></label>
    <input type="text" placeholder="<?php _e("https://codepen.io/xxx"); ?>" value="" name="url" required>
</div>`,
                        confirm(modal) {
                            let url = $('[name="url"]', modal).val();
                            this.replaceSelection(`[x-codepen url="${url}"/]`);
                            return true;
                        }
                    });
                }
            }]).trigger('XEditorAddHtmlProcessor', [
                function (html) {
                    if (html.indexOf("[x-codepen")) {
                        html = html.replace(this.getShortCodeRegex("x-codepen"), function (...m) {
                            let pen = $(`<span${m[3]}>${m[5]}</span>`);
                            let url = pen.attr('url');
                            if (url) {
                                // 匹配 CodePen URL 的正则表达式
                                const codePenRegex = /^https?:\/\/codepen\.io\/([\w-]+)\/(?:pen|full)\/([\w-]+)/i;

                                // 尝试匹配 URL
                                url = url.trim();
                                const match = url.match(codePenRegex);

                                // 构建 iframe URL
                                if (match) {
                                    const username = match[1];
                                    const penId = match[2];
                                    const iframeUrl = `https://codepen.io/${username}/embed/${penId}?default-tab=result`;
                                    // 现在你可以使用 iframeUrl 进一步处理
                                    return `<iframe class="x-codepen" src="${iframeUrl}" frameborder="0"></iframe>`;
                                }
                                return '';
                            }
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
              href="<?php echo Util::moduleUrl('Codepen', 'index.css'); ?>">
        <?php
    }

    public static function parseContent($text, $archive): string
    {
        if (strpos($text, '[x-codepen') === false) { //提高效率，避免每篇文章都要解析
            return $text;
        }
        $pattern = Util::get_shortcode_regex(['x-codepen']);
        return preg_replace_callback("/$pattern/", function ($matches) {
            if ($matches[1] == '[' && $matches[6] == ']') {
                return substr($matches[0], 1, -1);
            }
            $attr = htmlspecialchars_decode($matches[3]);
            $attrs = Util::shortcode_parse_atts($attr);
            if (array_key_exists('url', $attrs)) {
                // 匹配 CodePen URL 的正则表达式
                $codePenRegex = '/^https?:\/\/codepen\.io\/([\w-]+)\/(?:pen|full)\/([\w-]+)/i';
                // 尝试匹配 URL
                preg_match($codePenRegex, trim($attrs['url']), $match);
                // 构建 iframe URL
                $username = $match[1];
                $penId = $match[2];
                $iframeUrl = "https://codepen.io/$username/embed/$penId?default-tab=result";

                // 如果无法匹配，则返回空字符串
                if (empty($match)) {
                    return '';
                }
                return "<iframe class=\"x-codepen\" src=\"$iframeUrl\" frameborder=\"0\"></iframe>";
            } else {
                return '';
            }

        }, $text);
    }


    public static function parseExcerpt($text, $archive): string
    {
        if (strpos($text, '[x-codepen') === false) { //提高效率，避免每篇文章都要解析
            return $text;
        }
        $pattern = Util::get_shortcode_regex(['x-codepen']);
        return preg_replace("/$pattern/", '【🔡前端代码】', $text);
    }
}
