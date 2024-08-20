<?php

use Typecho\Config;
use TypechoPlugin\AAEditor\Module;
use TypechoPlugin\AAEditor\Util;
use Utils\Helper;

/**
 * 插入文章卡片到文章正文中
 *
 * @package 引用文章
 * @author Ryan
 * @version 0.0.1
 * @link https://doufu.ru
 *
 */
class ModulePost implements Module
{

    public static function editorStatic(): void
    {
        ?>
        <script>
            $('body').trigger('XEditorAddButton', [{
                id: 'wmd-post-button',
                name: '<?php _e("引用文章"); ?>',
                icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" width="20" height="20"><path d="M41.054688 1.816406C40.839844 1.804688 40.621094 1.863281 40.433594 1.992188C40.371094 2.03125 34.371094 6 18 6C17.621094 6 17.277344 6.214844 17.105469 6.554688L2.179688 36.40625C1.90625 36.789063 1.933594 37.308594 2.246094 37.65625C2.261719 37.675781 2.277344 37.691406 2.289063 37.703125C2.308594 37.722656 2.324219 37.738281 2.34375 37.753906C2.34375 37.757813 2.34375 37.757813 2.34375 37.757813C2.363281 37.773438 2.378906 37.785156 2.398438 37.796875C2.675781 38.035156 14.480469 48 34 48C34.382813 48 34.730469 47.78125 34.898438 47.4375L47.914063 20.839844C48.066406 20.535156 48.058594 20.171875 47.882813 19.878906C47.710938 19.585938 47.402344 19.402344 47.0625 19.386719C46.660156 19.371094 46.289063 19.59375 46.117188 19.957031L33.394531 45.964844C27.089844 45.890625 21.675781 44.753906 17.238281 43.285156C20.789063 43.785156 24.855469 43.863281 29.359375 43.078125C29.675781 43.023438 29.945313 42.820313 30.089844 42.53125L46.105469 9.796875C46.269531 9.476563 46.246094 9.09375 46.050781 8.792969C45.855469 8.492188 45.511719 8.324219 45.15625 8.34375C44.789063 8.367188 44.464844 8.585938 44.3125 8.914063L28.53125 41.15625C20.859375 42.390625 14.558594 41.085938 10.207031 39.511719C11.691406 39.695313 13.296875 39.820313 14.957031 39.820313C18.242188 39.820313 21.710938 39.34375 24.703125 37.902344C24.90625 37.800781 25.074219 37.640625 25.171875 37.4375L41.898438 3.25C42.097656 2.84375 41.996094 2.355469 41.652344 2.058594C41.480469 1.910156 41.269531 1.828125 41.054688 1.816406Z"></path></svg>',
                insertBefore: '#wmd-spacer4',
                command() {
                    this.openModal({
                        title: '<?php _e("引用文章"); ?>',
                        innerHTML: `
                    <div class="form-item">
                        <label class="required"><?php _e("文章 ID"); ?></label>
                        <input required type="text" name="cid" placeholder="<?php _e("请输入文章 CID"); ?>" value="" />
                    </div>
                `,
                        confirm(modal) {
                            let cid = $('[name="cid"]', modal).val();
                            this.replaceSelection(`[post cid="${cid}"/]`);
                            return true;
                        }
                    });
                }
            }]).trigger('XEditorAddHtmlProcessor', [
                function (html) {
                    if (html.indexOf("[post") === -1) return html;
                    // 预览回调
                    return html.replace(this.getShortCodeRegex("post"), `<div class="x-post fake">
    <span class="x-post-icon">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 50 50" width="20" height="20"><path d="M41.054688 1.816406C40.839844 1.804688 40.621094 1.863281 40.433594 1.992188C40.371094 2.03125 34.371094 6 18 6C17.621094 6 17.277344 6.214844 17.105469 6.554688L2.179688 36.40625C1.90625 36.789063 1.933594 37.308594 2.246094 37.65625C2.261719 37.675781 2.277344 37.691406 2.289063 37.703125C2.308594 37.722656 2.324219 37.738281 2.34375 37.753906C2.34375 37.757813 2.34375 37.757813 2.34375 37.757813C2.363281 37.773438 2.378906 37.785156 2.398438 37.796875C2.675781 38.035156 14.480469 48 34 48C34.382813 48 34.730469 47.78125 34.898438 47.4375L47.914063 20.839844C48.066406 20.535156 48.058594 20.171875 47.882813 19.878906C47.710938 19.585938 47.402344 19.402344 47.0625 19.386719C46.660156 19.371094 46.289063 19.59375 46.117188 19.957031L33.394531 45.964844C27.089844 45.890625 21.675781 44.753906 17.238281 43.285156C20.789063 43.785156 24.855469 43.863281 29.359375 43.078125C29.675781 43.023438 29.945313 42.820313 30.089844 42.53125L46.105469 9.796875C46.269531 9.476563 46.246094 9.09375 46.050781 8.792969C45.855469 8.492188 45.511719 8.324219 45.15625 8.34375C44.789063 8.367188 44.464844 8.585938 44.3125 8.914063L28.53125 41.15625C20.859375 42.390625 14.558594 41.085938 10.207031 39.511719C11.691406 39.695313 13.296875 39.820313 14.957031 39.820313C18.242188 39.820313 21.710938 39.34375 24.703125 37.902344C24.90625 37.800781 25.074219 37.640625 25.171875 37.4375L41.898438 3.25C42.097656 2.84375 41.996094 2.355469 41.652344 2.058594C41.480469 1.910156 41.269531 1.828125 41.054688 1.816406Z"></path></svg>
    </span>
    <span class="x-post-text">
        <?php _e("引用文章区块"); ?>
    </span>
</div>`);
                }
            ]);
        </script>
        <?php
        self::commonStyle();
    }

    /**
     * 前台输出静态资源
     *
     * @param $archive
     * @return void
     */
    public static function archiveStatic($archive): void
    {
        self::commonStyle();
    }

    /**
     * 自定义函数，输出样式 link href
     *
     * @return void
     */
    public static function commonStyle(): void
    {
        ?>
        <link rel="stylesheet"
              href="<?php echo Util::moduleUrl('Post', 'index.css'); ?>">
        <?php
    }

    public static function parseContent($text, $archive): string
    {
        if (strpos($text, '[post') === false) { //提高效率，避免每篇文章都要解析
            return $text;
        }
        $pattern = Util::get_shortcode_regex(['post']);
        return preg_replace_callback(/**
         * @throws \Typecho\Exception
         */ "/$pattern/", function ($m) {
            return self::postCallback($m);
        }, $text);
    }

    /**
     * 引用文章回调
     * @param array $m 匹配内容
     * @return string
     * @throws \Typecho\Exception
     */
    public static function postCallback($m): string
    {
        // Allow [[foo]] syntax for escaping a tag.
        if ('[' === $m[1] && ']' === $m[6]) {
            return substr($m[0], 1, -1);
        }
        $attr = htmlspecialchars_decode($m[3]);
        $attrs = Util::shortcode_parse_atts($attr);
        $template = '<div class="x-post" style="overflow: hidden; max-height: 240px; position: relative">' .
            '<div class="text-content">' .
            '<div class="title"><a href="{permalink}">{title}</a></div>' .
            '<div class="content">{abstract}</div>' .
            '<div class="meta">' .
            '<div class="meta-item meta-item-date">{dateFormated}</div>' .
            '<div class="meta-item meta-item-comment">{commentsNum}</div>' .
            '</div></div>' .
            '<div class="media-content"><a href="{permalink}" title="{title}"><img class="no-parse" alt="{title}" src="{thumb}"/></a></div></div>';
        if (is_array($attrs)) {
            if (!array_key_exists('cid', $attrs) && array_key_exists('url', $attrs)) {
                $attrs['permalink'] = $attrs['url'];
                $attrs = Config::factory($attrs);
                $attrs->setDefault([
                    'title' => _t("请设置引用文章标题"),
                    'thumb' => Util::thumbs(null, 1, true, false)
                ]);
                return Util::toString($attrs, $template);
            } else if (array_key_exists('cid', $attrs)) {
                $post = Helper::widgetById('Contents', intval($attrs['cid']));
                if ($post->have() && !is_null($post->excerpt)) {
                    $post->abstract = Util::subStr(strip_tags($post->excerpt), 120);
                    $post->dateFormated = $post->date->format('Y-m-d');
                    if (array_key_exists('title', $attrs)) $post->title = $attrs['title'];
                    if (array_key_exists('url', $attrs)) $post->permalink = $attrs['url'];
                    $post->thumb = array_key_exists('cover', $attrs) ? $attrs['cover'] : Util::thumbs($post, 1, true);
                    return Util::toString($post, $template);
                }
            }
        }
        return '';
    }

    public static function parseExcerpt($text, $archive): string
    {
        if (strpos($text, '[post') === false) { //提高效率，避免每篇文章都要解析
            return $text;
        }
        $pattern = Util::get_shortcode_regex(['post']);
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
        if (is_array($attrs)) {
            if (!array_key_exists('cid', $attrs)) {
                if (array_key_exists('url', $attrs))
                    return _t("参见：【%s】", $attrs['title'] ?? $attrs['url']);
            } else {
                $post = Helper::widgetById('Contents', intval($attrs['cid']));
                return _t("参见：【%s】", $post->title);
            }
        }
        return '';
    }
}
