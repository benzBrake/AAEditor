<?php

use Typecho\Config;
use TypechoPlugin\AAEditor\Module;
use TypechoPlugin\AAEditor\Util;
use Utils\Helper;

/**
 * 插入视频卡片到文章正文中（Firefox浏览器无法播放 H.265 视频），支持 m3u8/mp4/Bilibili视频
 *
 * @package 引用m3u8/mp4
 * @author Ryan
 * @version 0.0.1
 * @link https://doufu.ru
 *
 */
class ModuleVideo implements Module
{

    public static function editorStatic(): void
    {
        ?>
        <script>
            $('body').trigger('XEditorAddButton', [{
                id: 'wmd-video-button',
                name: '<?php _e("引用m3u8/mp4"); ?>',
                icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M3 3.9934C3 3.44476 3.44495 3 3.9934 3H20.0066C20.5552 3 21 3.44495 21 3.9934V20.0066C21 20.5552 20.5551 21 20.0066 21H3.9934C3.44476 21 3 20.5551 3 20.0066V3.9934ZM5 5V19H19V5H5ZM10.6219 8.41459L15.5008 11.6672C15.6846 11.7897 15.7343 12.0381 15.6117 12.2219C15.5824 12.2658 15.5447 12.3035 15.5008 12.3328L10.6219 15.5854C10.4381 15.708 10.1897 15.6583 10.0672 15.4745C10.0234 15.4088 10 15.3316 10 15.2526V8.74741C10 8.52649 10.1791 8.34741 10.4 8.34741C10.479 8.34741 10.5562 8.37078 10.6219 8.41459Z"></path></svg>',
                insertAfter: '#wmd-audio-album-button|#wmd-spacer3',
                command() {
                    this.openModal({
                        title: '<?php _e("引用m3u8/mp4"); ?>',
                        innerHTML: `<div class="form-item">
    <label for="src" class="required"><?php _e("视频链接") ?></label>
    <input type="text" required name="src">
</div>
<div class="form-item">
    <label for="autoplay"><?php _e("自动播放") ?></label>
    <select name="autoplay">
        <option value="off"><?php _e("关闭") ?></option>
        <option value="on"><?php _e("开启") ?></option>
    </select>
</div>`,
                        confirm(modal) {
                            let src = $('[name="src"]', modal).val(),
                                autoplay = $('[name="autoplay"]', modal).val();
                            this.replaceSelection(`[x-player src="${src}" autoplay="${autoplay}" /]`);
                            return true;
                        }
                    });
                }
            }]).trigger('XEditorAddButton', [{
                id: 'wmd-bilibili-button',
                name: '<?php _e("插入 BiliBili 视频"); ?>',
                icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M7.17157 2.75725L10.414 5.99936H13.585L16.8284 2.75725C17.219 2.36672 17.8521 2.36672 18.2426 2.75725C18.6332 3.14777 18.6332 3.78094 18.2426 4.17146L16.414 5.99936L18.5 5.99989C20.433 5.99989 22 7.56689 22 9.49989V17.4999C22 19.4329 20.433 20.9999 18.5 20.9999H5.5C3.567 20.9999 2 19.4329 2 17.4999V9.49989C2 7.56689 3.567 5.99989 5.5 5.99989L7.585 5.99936L5.75736 4.17146C5.36684 3.78094 5.36684 3.14777 5.75736 2.75725C6.14788 2.36672 6.78105 2.36672 7.17157 2.75725ZM18.5 7.99989H5.5C4.7203 7.99989 4.07955 8.59478 4.00687 9.35543L4 9.49989V17.4999C4 18.2796 4.59489 18.9203 5.35554 18.993L5.5 18.9999H18.5C19.2797 18.9999 19.9204 18.405 19.9931 17.6444L20 17.4999V9.49989C20 8.67146 19.3284 7.99989 18.5 7.99989ZM8 10.9999C8.55228 10.9999 9 11.4476 9 11.9999V13.9999C9 14.5522 8.55228 14.9999 8 14.9999C7.44772 14.9999 7 14.5522 7 13.9999V11.9999C7 11.4476 7.44772 10.9999 8 10.9999ZM16 10.9999C16.5523 10.9999 17 11.4476 17 11.9999V13.9999C17 14.5522 16.5523 14.9999 16 14.9999C15.4477 14.9999 15 14.5522 15 13.9999V11.9999C15 11.4476 15.4477 10.9999 16 10.9999Z"></path></svg>',
                insertAfter: '#wmd-video-button',
                command() {
                    this.openModal({
                        title: '<?php _e("插入 BiliBili 视频"); ?>',
                        innerHTML: `<div class="form-item">
    <label for="src" class="required"><?php _e("视频链接") ?></label>
    <input type="text" required name="src">
</div>
<div class="form-item">
    <label for="autoplay"><?php _e("自动播放") ?></label>
    <select name="autoplay">
        <option value="off"><?php _e("关闭") ?></option>
        <option value="on"><?php _e("开启") ?></option>
    </select>
</div>`,
                        confirm(modal) {
                            let src = $('[name="src"]', modal).val(),
                                autoplay = $('[name="autoplay"]', modal).val();
                            if (src.indexOf('bilibili.com')) {
                                const regex = /bilibili\.com\/video\/([^\/]*)/gm;
                                src = (regex.exec(src) || ['', ''])[1];
                            }
                            if (src) {
                                this.replaceSelection(`[x-bilibili id="${src}" autoplay="${autoplay}" /]`);
                            } else {
                                $('[name="src"]', modal).addClass('required-animate');
                                setTimeout(() => {
                                    $('[name="src"]', modal).removeClass('required-animate')
                                }, 800);
                                return false;
                            }
                            return true;
                        }
                    });
                }
            }]).trigger('XEditorAddButton', [{
                id: 'wmd-preview-video-button',
                name: '<?php _e("实时预览视频"); ?>',
                icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M11.96875 1L12 3C12.582 3 13.164516 3.0550625 13.728516 3.1640625L14.109375 1.2011719C13.419375 1.0671719 12.71 1 12 1L11.96875 1 z M 9.8222656 1.2167969C9.1172656 1.3587969 8.4237656 1.5706562 7.7597656 1.8476562L8.53125 3.6914062C9.07325 3.4654063 9.6397969 3.2927344 10.216797 3.1777344L9.8222656 1.2167969 z M 16.175781 1.8203125L15.417969 3.671875C15.959969 3.894875 16.483609 4.1720469 16.974609 4.4980469L18.080078 2.8300781C17.480078 2.4320781 16.839781 2.0923125 16.175781 1.8203125 z M 5.8632812 2.8710938C5.2682813 3.2710937 4.7091719 3.7321875 4.2011719 4.2421875L5.6191406 5.6523438C6.0341406 5.2343437 6.4925156 4.8562969 6.9785156 4.5292969L5.8632812 2.8710938 z M 19.748047 4.1933594L18.339844 5.6132812C18.758844 6.0282813 19.136844 6.4847031 19.464844 6.9707031L21.123047 5.8535156C20.722047 5.2595156 20.259047 4.7003594 19.748047 4.1933594 z M 12 5C8.1458495 5 5 8.1458524 5 12C5 15.854148 8.1458495 19 12 19C15.854151 19 19 15.854148 19 12C19 8.1458524 15.854151 5 12 5 z M 2.8378906 5.9101562C2.4398906 6.5081562 2.0982188 7.1475 1.8242188 7.8125L3.6738281 8.5742188C3.8978281 8.0302188 4.1769531 7.5075313 4.5019531 7.0195312L2.8378906 5.9101562 z M 12 7C14.773271 7 17 9.2267307 17 12C17 14.773269 14.773271 17 12 17C9.226729 17 7 14.773269 7 12C7 9.2267307 9.226729 7 12 7 z M 22.148438 7.7480469L20.304688 8.5214844C20.531688 9.0624844 20.704313 9.6290781 20.820312 10.205078L22.78125 9.8105469C22.63925 9.1045469 22.426437 8.4110469 22.148438 7.7480469 z M 12 9C11.083334 9 10.268559 9.3797556 9.7519531 9.9609375C9.2353472 10.542119 9 11.277778 9 12C9 12.722222 9.2353472 13.457881 9.7519531 14.039062C10.268559 14.620245 11.083334 15 12 15C12.916666 15 13.731441 14.620244 14.248047 14.039062C14.764653 13.457882 15 12.722222 15 12C15 11.277778 14.764653 10.542119 14.248047 9.9609375C13.731441 9.3797556 12.916666 9 12 9 z M 1.2050781 9.8789062C1.0690781 10.572906 1 11.287 1 12L1 12.021484L3 12C3 11.415 3.0559687 10.831672 3.1679688 10.263672L1.2050781 9.8789062 z M 12 11C12.416666 11 12.601893 11.120244 12.751953 11.289062C12.902014 11.457882 13 11.722222 13 12C13 12.277778 12.90201 12.542119 12.751953 12.710938C12.601893 12.879755 12.416666 13 12 13C11.583334 13 11.398107 12.879756 11.248047 12.710938C11.097986 12.542118 11 12.277778 11 12C11 11.722222 11.097986 11.457881 11.248047 11.289062C11.398107 11.120245 11.583334 11 12 11 z M 23 11.957031L21 12C21 12.59 20.941125 13.180859 20.828125 13.755859L22.791016 14.144531C22.930016 13.441531 23 12.72 23 12L23 11.957031 z M 3.1757812 13.775391L1.2148438 14.166016C1.3548438 14.870016 1.56675 15.563516 1.84375 16.228516L3.6894531 15.458984C3.4634531 14.915984 3.2907813 14.350391 3.1757812 13.775391 z M 20.318359 15.441406C20.094359 15.982406 19.814328 16.505094 19.486328 16.996094L21.150391 18.107422C21.550391 17.508422 21.893969 16.868031 22.167969 16.207031L20.318359 15.441406 z M 4.5234375 17.013672L2.8632812 18.128906C3.2622813 18.723906 3.724375 19.281063 4.234375 19.789062L5.6464844 18.373047C5.2294844 17.957047 4.8504375 17.500672 4.5234375 17.013672 z M 18.367188 18.361328C17.951187 18.778328 17.492859 19.155422 17.005859 19.482422L18.119141 21.142578C18.714141 20.743578 19.274203 20.282438 19.783203 19.773438L18.367188 18.361328 z M 7.0117188 19.492188L5.9003906 21.15625C6.4993906 21.55625 7.1397344 21.897875 7.8027344 22.171875L8.5664062 20.322266C8.0244063 20.098266 7.5017188 19.820187 7.0117188 19.492188 z M 15.449219 20.314453C14.907219 20.539453 14.341625 20.712172 13.765625 20.826172L14.154297 22.789062C14.860297 22.649062 15.55575 22.438109 16.21875 22.162109L15.449219 20.314453 z M 10.253906 20.830078L9.8671875 22.792969C10.564187 22.929969 11.282 23 12 23L12.0625 23L12.123047 22.992188L12.011719 22.001953L12.011719 21L11.955078 21C11.381078 20.997 10.808906 20.939078 10.253906 20.830078 z"></path></svg>',
                insertAfter: '#wmd-preview-button',
                command({target}) {
                    let enabled = (localStorage.getItem("editor-preview-video") || "false") === "true";
                    localStorage.setItem("editor-preview-video", "" + !enabled);
                    if (!enabled) {
                        $('body').trigger('XEditorPreviewEnd');
                    }
                    $('body').trigger('XEditorRefresh');
                    target.setAttribute('active', !enabled);
                },
                onMounted({target}) {
                    target.setAttribute('active', (localStorage.getItem("editor-preview-video") || "false") === "true");
                }
            }]).trigger('XEditorAddHtmlProcessor', [
                function (html) {
                    if (html.indexOf("[x-player")) {
                        if ((localStorage.getItem("editor-preview-video") || "false") === "true") {
                            html = html.replace(this.getShortCodeRegex("x-player"), `<div class="x-video" $3>$5</div>`);
                        } else {
                            html = html.replace(this.getShortCodeRegex("x-player"), `<div class="x-video fake">
<div class="x-video-inner">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M3 3.9934C3 3.44476 3.44495 3 3.9934 3H20.0066C20.5552 3 21 3.44495 21 3.9934V20.0066C21 20.5552 20.5551 21 20.0066 21H3.9934C3.44476 21 3 20.5551 3 20.0066V3.9934ZM5 5V19H19V5H5ZM10.6219 8.41459L15.5008 11.6672C15.6846 11.7897 15.7343 12.0381 15.6117 12.2219C15.5824 12.2658 15.5447 12.3035 15.5008 12.3328L10.6219 15.5854C10.4381 15.708 10.1897 15.6583 10.0672 15.4745C10.0234 15.4088 10 15.3316 10 15.2526V8.74741C10 8.52649 10.1791 8.34741 10.4 8.34741C10.479 8.34741 10.5562 8.37078 10.6219 8.41459Z"></path></svg>
</div>
</div>`);
                        }
                    }
                    if (html.indexOf("[x-bilibili")) {
                        html = html.replace(this.getShortCodeRegex('x-bilibili'), '<div class="x-bilibili-wrapper"><x-bilibili$3></x-bilibili></div>');
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
              href="<?php echo Util::moduleUrl('Video', 'index.css'); ?>">
        <script>
            function initVideo() {
                Array.from(document.querySelectorAll('.x-video:not([loaded])')).forEach(function (el) {
                    el.setAttribute('loaded', '');
                    let src = el.getAttribute('src'),
                        autoplay = el.getAttribute('autoplay') || "off",
                        player = el.getAttribute('player') || "<?php echo Util::pluginUrl('Modules/Video/Player.php') ?>";
                    el.innerHTML = `<iframe frameborder="0" referrerpolicy="origin-when-cross-origin" allowfullscreen="true" src="${player}?url=${encodeURIComponent(src)}&autoplay=${autoplay}"></iframe>`;
                });
            }

            <?php if (defined("__TYPECHO_ADMIN__") && __TYPECHO_ADMIN__): ?>
            $('body').on('XEditorPreviewEnd', function () {
                if ((localStorage.getItem("editor-preview-video") || "false") === "false") return;
                initVideo();
            })
            <?php else: ?>
            document.addEventListener('DOMContentLoaded', initVideo);
            document.addEventListener('pjax:complete', initVideo);
            <?php endif; ?>
        </script>
        <?php
    }

    public static function defaultPlayer()
    {
        return Util::pluginUrl('Modules/Video/Player.php');
    }

    public static function parseContent($text, $archive): string
    {
        if (strpos($text, '[x-player') !== false) { //提高效率，避免每篇文章都要解析
            $pattern = Util::get_shortcode_regex(['x-player']);
            $text = preg_replace("/$pattern/", '<div class="x-video" $3>$5</div>', $text);
        }
        if (strpos($text, '[x-bilibili') !== false) { //提高效率，避免每篇文章都要解析
            $pattern = Util::get_shortcode_regex(['x-bilibili']);
            $text = preg_replace_callback("/$pattern/", function ($matches) {
                if ($matches[1] == '[' && $matches[6] == ']') {
                    return substr($matches[0], 1, -1);
                }
                $attr = htmlspecialchars_decode($matches[3]);
                $attrs = Util::shortcode_parse_atts($attr);
                if (isset($attrs['id']) || isset($attrs['bvid'])) {
                    $vid = $attrs['id'] ?? $attrs['bvid'];
                    $idType = (strpos($vid, 'BV') === 0) ? 'bvid' : 'aid';
                    $page = $attrs['page'] ?? '1';
                    $autoplay = ($attrs['autoplay'] ?? 'off') === 'on';
                    $src = "//www.bilibili.com/blackboard/html5mobileplayer.html?" . $idType . '=' . $vid . '&page=' . $page . '&fjw=' . $autoplay;
                    return '<div class="x-bilibili-wrapper"><iframe class="x-bilibili" src="' . $src . '" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"></iframe></div>';
                } else {
                    return '<div class="x-bilibili-wrapper"><div class="x-bilibili fake"><div class="x-video-inner"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M7.17157 2.75725L10.414 5.99936H13.585L16.8284 2.75725C17.219 2.36672 17.8521 2.36672 18.2426 2.75725C18.6332 3.14777 18.6332 3.78094 18.2426 4.17146L16.414 5.99936L18.5 5.99989C20.433 5.99989 22 7.56689 22 9.49989V17.4999C22 19.4329 20.433 20.9999 18.5 20.9999H5.5C3.567 20.9999 2 19.4329 2 17.4999V9.49989C2 7.56689 3.567 5.99989 5.5 5.99989L7.585 5.99936L5.75736 4.17146C5.36684 3.78094 5.36684 3.14777 5.75736 2.75725C6.14788 2.36672 6.78105 2.36672 7.17157 2.75725ZM18.5 7.99989H5.5C4.7203 7.99989 4.07955 8.59478 4.00687 9.35543L4 9.49989V17.4999C4 18.2796 4.59489 18.9203 5.35554 18.993L5.5 18.9999H18.5C19.2797 18.9999 19.9204 18.405 19.9931 17.6444L20 17.4999V9.49989C20 8.67146 19.3284 7.99989 18.5 7.99989ZM8 10.9999C8.55228 10.9999 9 11.4476 9 11.9999V13.9999C9 14.5522 8.55228 14.9999 8 14.9999C7.44772 14.9999 7 14.5522 7 13.9999V11.9999C7 11.4476 7.44772 10.9999 8 10.9999ZM16 10.9999C16.5523 10.9999 17 11.4476 17 11.9999V13.9999C17 14.5522 16.5523 14.9999 16 14.9999C15.4477 14.9999 15 14.5522 15 13.9999V11.9999C15 11.4476 15.4477 10.9999 16 10.9999Z"></path></svg></div></div></div>';
                }
            }, $text);
        }
        return $text;
    }


    public static function parseExcerpt($text, $archive): string
    {
        if (strpos($text, '[x-player') === false && strpos($text, '[x-bilibili') === false) { //提高效率，避免每篇文章都要解析
            return $text;
        }
        $patternPlayer = Util::get_shortcode_regex(['x-player', 'x-bilibili']);
        return preg_replace("/$patternPlayer/", '【▶️视频】', $text);
    }
}
