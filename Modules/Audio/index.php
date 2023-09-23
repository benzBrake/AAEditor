<?php

use TypechoPlugin\AAEditor\Module;
use TypechoPlugin\AAEditor\Util;

/**
 * 插入音频播放器到文章中（支持播放列表）
 *
 * @package 引用音频
 * @author Ryan
 * @version 0.0.1
 * @link https://doufu.ru
 *
 */
class ModuleAudio implements Module
{

    public static function editorStatic(): void
    {
        self::commonStatic(); ?>
        <script>
            $('body').trigger('XEditorAddButton', [{
                id: 'wmd-audio-button',
                name: '<?php _e("引用音频"); ?>',
                icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M20 3V17C20 19.2091 18.2091 21 16 21C13.7909 21 12 19.2091 12 17C12 14.7909 13.7909 13 16 13C16.7286 13 17.4117 13.1948 18 13.5351V5H9V17C9 19.2091 7.20914 21 5 21C2.79086 21 1 19.2091 1 17C1 14.7909 2.79086 13 5 13C5.72857 13 6.41165 13.1948 7 13.5351V3H20ZM5 19C6.10457 19 7 18.1046 7 17C7 15.8954 6.10457 15 5 15C3.89543 15 3 15.8954 3 17C3 18.1046 3.89543 19 5 19ZM16 19C17.1046 19 18 18.1046 18 17C18 15.8954 17.1046 15 16 15C14.8954 15 14 15.8954 14 17C14 18.1046 14.8954 19 16 19Z"></path></svg>',
                insertBefore: '#wmd-spacer4',
                command() {
                    this.openModal({
                        title: '<?php _e("引用音频") ?>',
                        innerHTML: `<div class="form-item">
    <label for="src" class="required"><?php _e("音频链接") ?></label>
    <input type="text" required name="src">
</div>
<div class="form-item">
    <label for="autoplay"><?php _e("自动播放") ?></label>
    <select name="autoplay">
        <option value="off"><?php _e("关闭") ?></option>
        <option value="on"><?php _e("开启") ?></option>
    </select>
</div>
<div class="form-item">
    <label for="autoplay"><?php _e("音频格式") ?></label>
    <select name="type"><option value="audio/mpeg">mp3</option><option value="audio/wav">wav</option><option value="audio/ogg">ogg</option><option value="audio/x-mpegurl">m3u</option></select>
</div>`,
                        confirm(modal) {
                            let src = $('[name="src"]', modal).val(),
                                autoplay = $('[name="autoplay"]', modal).val() || "off";
                            this.replaceSelection(`[x-audio src="${encodeURIComponent(src)}" autoplay="${autoplay}"/]`);
                            return true;
                        }
                    });
                }
            }, {
                id: 'wmd-audio-album-button',
                name: '<?php _e("音频播放列表") ?>',
                icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20ZM12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM12 14C13.1046 14 14 13.1046 14 12C14 10.8954 13.1046 10 12 10C10.8954 10 10 10.8954 10 12C10 13.1046 10.8954 14 12 14ZM12 16C9.79086 16 8 14.2091 8 12C8 9.79086 9.79086 8 12 8C14.2091 8 16 9.79086 16 12C16 14.2091 14.2091 16 12 16Z"></path></svg>',
                insertAfter: '#wmd-audio-button',
                command() {
                    let prefix = (this.textarea.isAtLineStart() ? "" : "\n"),
                        postfix = (this.textarea.isAtLineEnd() ? "" : "\n");
                    this.replaceSelection(prefix + '[audio-album autoplay="false"]\n[x-audio src="xxx.mp3" type="audio/mpeg"/]\n[x-audio src="yyy.wav" type="audio/wav"/]\n[/audio-album]' + postfix);
                }
            }]).trigger('XEditorAddHtmlProcessor', [
                function (html) {
                    if (html.indexOf('[audio-album') !== -1) {
                        html = html.replace(this.getShortCodeRegex("audio-album"), '<div class="x-album" type="audio"$3>$5</div>');
                    }
                    if (html.indexOf('[x-album') !== -1) {
                        html = html.replace(this.getShortCodeRegex("x-album"), '<div class="x-album" type="audio"$3>$5</div>');
                    }
                    if (html.indexOf('[audio') !== -1) {
                        html = html.replace(this.getShortCodeRegex("audio"), '<div class="x-audio"$3></div>');
                    }
                    if (html.indexOf('[x-audio') !== -1) {
                        html = html.replace(this.getShortCodeRegex("x-audio"), '<div class="x-audio"$3></div>');
                    }
                    return html;
                }
            ]);
        </script>
        <?php
        self::commonStatic();
    }

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
        <script src="https://jsd.onmicrosoft.cn/npm/aplayer@1.10.1/dist/APlayer.min.js "></script>
        <link href="https://jsd.onmicrosoft.cn/npm/aplayer@1.10.1/dist/APlayer.min.css " rel="stylesheet">
        <link rel="stylesheet"
              href="<?php echo Util::moduleUrl('Audio', 'index.css'); ?>">
        <script>
            <?php if (defined("__TYPECHO_ADMIN__") && __TYPECHO_ADMIN__): ?>
            $('body').on('XEditorPreviewEnd', function () {
                parseAllAudio();
            })
            <?php else: ?>
            document.addEventListener('DOMContentLoaded', parseAllAudio);
            document.addEventListener('pjax:complete', parseAllAudio);
            <?php endif; ?>
            function parseAllAudio() {
                Array.from(document.querySelectorAll('.x-album:not([loaded])')).forEach(el => {
                    el.setAttribute('loaded', '');
                    parseAlbum(el);
                });

                Array.from(document.querySelectorAll('.x-audio:not([loaded])')).forEach(el => {
                    el.setAttribute('loaded', '');
                    let obj = parseAudioElementToSource(el);
                    if (obj) {
                        new APlayer({
                            container: el,
                            preload: 'auto',
                            autoplay: el.getAttribute("autoplay") === "on",
                            loop: el.getAttribute("loop") === "true",
                            listFolded: true,
                            listMaxHeight: 300,
                            volume: 0.7,
                            audio: [obj]
                        });
                    }
                })
            }

            function parseAlbum(el) {
                let autoplay = el.getAttribute('autoplay') === "on",
                    loop = el.getAttribute('loop') === "on",
                    order = el.getAttribute('order') || "list",
                    audioList = [];
                Array.from(el.childNodes).forEach(child => {
                    switch (child.tagName) {
                        case "SPAN":
                            el.before(child);
                            break;
                        default:
                            if (child.classList.contains('x-audio')) {
                                let obj = parseAudioElementToSource(child);
                                if (obj) {
                                    audioList.push(obj);
                                }
                            }
                            el.removeChild(child);
                            break;
                    }
                });

                if (audioList.length) {
                    new APlayer({
                        container: el,
                        preload: 'auto',
                        autoplay: autoplay,
                        loop: loop,
                        order: order,
                        listFolded: audioList.length < 2,
                        listMaxHeight: 300,
                        volume: 0.7,
                        audio: audioList
                    });
                } else {
                    el.innerHTML = errorHTML("<?php _e("播放列表是空的！") ?>");
                }
            }

            function parseAudioElementToSource(el) {
                let url = el.getAttribute("url") || el.getAttribute("src");
                if (url) {
                    return {
                        name: el.getAttribute('name') || decodeURIComponent(getFileNameFromUrl(url)),
                        url: url,
                        artist: el.getAttribute('artist') || '<?php _e("未知艺术家"); ?>',
                        cover: el.getAttribute('cover') || "https://cdn-us.imgs.moe/2023/09/08/64fb20d21209f.png",
                        lrc: el.getAttribute('lrc')
                    }
                }
            }

            function getFileNameFromUrl(url) {
                if (typeof url === 'string') {
                    // 创建一个<a>元素来模拟请求
                    const a = document.createElement('a');
                    a.href = url;

                    // 获取URL的文件名
                    return a.pathname.split('/').pop();
                }
            }

            function getSuffix(url) {
                if (typeof url === 'string') {
                    // 创建一个<a>元素来模拟请求
                    const a = document.createElement('a');
                    a.href = url;

                    // 获取URL的文件扩展名
                    const pathArray = a.pathname.split('.');
                    return pathArray[pathArray.length - 1];
                }
                return '';
            }

            function getMimeTypeFromSuffix(suffix) {
                // 定义支持的MIME类型与文件扩展名的映射关系
                const mimeTypes = {
                    'mp3': 'audio/mpeg',
                    'wav': 'audio/wav',
                    'ogg': 'audio/ogg',
                    'm3u': 'audio/x-mpegurl'
                };

                // 如果文件扩展名存在于映射关系中，则返回对应的MIME类型；否则返回null
                return mimeTypes[suffix] || null;
            }

            function errorHTML(msg) {
                return `<div class="audio-error-msg">
                                <span class="error-icon" width="20" height="20"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM11 15V17H13V15H11ZM11 7V13H13V7H11Z"></path></svg></span>
                                <span class="error-text">${msg || "<?php _e("错误"); ?>"}</span>
                            </div>`;
            }
        </script>
        <?php
    }

    public static function parseContent($text, $archive): string
    {
        if (strpos($text, '[audio-album') !== false || strpos($text, '[x-album') !== false) {
            $pattern = Util::get_shortcode_regex(['audio-album', 'x-album']);
            $text = preg_replace("/$pattern/",
                '<div class="x-album"$3>$5</div>'
                , $text);
        }

        if (strpos($text, '[audio') !== false || strpos($text, '[x-audio') !== false) {
            $pattern = Util::get_shortcode_regex(['audio', 'x-audio']);
            $text = preg_replace("/$pattern/",
                '<div class="x-audio"$3>$5</div>'
                , $text);
        }
        return $text;
    }

    public static function parseExcerpt($text, $archive): string
    {
        $pattern = Util::get_shortcode_regex(['audio', 'x-audio']);
        return preg_replace("/$pattern/",
            _t('【▶️音频】')
            , $text);
    }
}
