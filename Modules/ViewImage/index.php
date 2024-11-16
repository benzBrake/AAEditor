<?php

use TypechoPlugin\AAEditor\Module;
use TypechoPlugin\AAEditor\Util;

/**
 * 增加图片暗箱功能
 *
 * @package 图片暗箱
 * @author Ryan
 * @version 0.0.1
 * @link https://doufu.ru
 *
 */
class ModuleViewImage implements Module
{
    /**
     * 处理器权重，正整数，越小优先级越高
     *
     * @var int
     */
    static $priority = 98;

    /**
     * 编辑器页面输出静态资源，新增按钮，增加预览处理，增加预览样式
     *
     * @return void
     */
    public static function editorStatic(): void
    {
        ?>
        <script>
            $('body').trigger('XEditorAddButton', [{
                id: 'wmd-photos-button', // 按钮 id 建议为 wmd-自定义-button
                name: '<?php _e("相册排版"); ?>',
                icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M3 3C1.9069372 3 1 3.9069372 1 5L1 15C1 16.093063 1.9069372 17 3 17L16 17C17.093063 17 18 16.093063 18 15L18 5C18 3.9069372 17.093063 3 16 3L3 3 z M 3 5L16 5L16 12L13.5 9.5L9.34375 13.648438L7.9238281 12.240234L9.4804688 10.677734L7.4335938 8.5214844L3 12.992188L3 5 z M 11 6 A 1 1 0 0 0 10 7 A 1 1 0 0 0 11 8 A 1 1 0 0 0 12 7 A 1 1 0 0 0 11 6 z M 20 7L20 19L5 19L5 21L20 21C21.093063 21 22 20.093063 22 19L22 7L20 7 z"></path></svg>',
                insertAfter: '#wmd-image-button', // 在 #wmd-image-button 后插入，选择器，document.querySelector 支持的选择器格式
                shortcut: 'ctrl+alt+g', // 快捷键
                command() {
                    // 点击按钮就的操作
                    this.wrapText('[photos]\n', '\n[/photos]');
                }
            }]).trigger('XEditorAddHtmlProcessor', [function (html) {
                if (html.indexOf("[photos") > -1) {
                    html = html.replace(this.getShortCodeRegex('photos'), '<div class="x-photos ' + (localStorage.getItem('editor-album-style') || 'google') + '">$5</div>');
                } else {
                    html = html.replace(this.getShortCodeRegex('album'), '<div class="x-photos ' + (localStorage.getItem('editor-album-style') || 'google') + '">$5</div>');
                }
                return html;
            }])
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
        self::commonStatic(); ?>
        <?php
    }

    /**
     * 自定义函数，输出样式 link href
     *
     * @return void
     */
    public static function commonStatic(): void
    {
        ?>
        <script src="<?php echo Util::moduleUrl('ViewImage', 'fslightbox.js'); ?>"></script>
        <script>
            (function () {
                const fn = _ => {
                    Array.from(document.querySelectorAll('img[view-image]')).forEach(img => {
                        img.removeAttribute('view-image')
                        if (img.parentNode.tagName === 'A') {
                            img.setAttribute('data-fslightbox', 'gallery')
                            img.setAttribute('data-type', 'image')
                        } else {
                            img.outerHTML = '<a href="' + img.src + '" data-fslightbox="gallery" data-type="image">' + img.outerHTML + '</a>';
                        }
                    });
                    refreshFsLightbox();
                }
                fn();
                $('body').on('XEditorPreviewEnd', fn)
                document.addEventListener('pjax:complete', fn);
            })();
        </script>
        <style>
            a[data-fslightbox] > img[style*="aspect-ratio"] {
                height: auto;
            }
        </style>
        <?php
    }

    /**
     * 把 [photos] 短代码渲染成相册 html 结构
     *
     * @param {string} $text 正文内容
     * @param {Widget_Archive|Widget_Abstract_Contents} $archive 页面对象
     * @return string
     */
    public static function parseContent($text, $archive): string
    {
        return preg_replace_callback("/<img[^>]+>/i", function ($matches) {
            return '<img view-image' .substr($matches[0], 4);
        }, $text);
    }

    public static function parseExcerpt($text, $archive): string
    {
        return $text;
    }
}