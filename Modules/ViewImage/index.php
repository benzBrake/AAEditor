<?php

use TypechoPlugin\AAEditor\Module;
use TypechoPlugin\AAEditor\Util;

/**
 * 点击图片网页全屏预览
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
                    const images = document.querySelector('#wmd-preview') ? document.querySelectorAll('#wmd-preview img') : document.querySelectorAll('img[view-image]');
                    Array.from(images).forEach(img => {
                        if (!img.closest('.x-photos')) {
                            if (img.parentNode.tagName === 'A') {
                                img.parentNode.setAttribute('data-fslightbox', 'gallery')
                                img.parentNode.setAttribute('data-type', 'image')
                            } else {
                                img.outerHTML = '<a href="' + img.src + '" data-fslightbox="gallery" data-type="image">' + img.outerHTML + '</a>';
                            }
                        }
                        img.removeAttribute('view-image')
                    });
                    if (typeof refreshFsLightbox === 'function') refreshFsLightbox();
                }
                <?php if (defined("__TYPECHO_ADMIN__") && __TYPECHO_ADMIN__): ?>
                $('body').on('XEditorPreviewEnd', function () {
                    fn();
                });
                <?php else: ?>
                fn();
                document.addEventListener('pjax:complete', fn);
                <?php endif; ?>
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
            return '<img view-image' . substr($matches[0], 4);
        }, $text);
    }

    public static function parseExcerpt($text, $archive): string
    {
        return $text;
    }
}
