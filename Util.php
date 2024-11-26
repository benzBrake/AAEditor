<?php

namespace TypechoPlugin\AAEditor;

use ReflectionClass;
use Typecho\Common;
use Typecho\Db;
use Typecho\Plugin;
use Typecho\Plugin\Exception;
use Utils\Helper;
use Widget\Contents;
use Widget\Options;
use Widget\User;

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

class Util
{
    static $manifest;
    private static $contentParsers;
    private static $excerptParsers;
    private static $archiveStatics;

    private static $staticMap = [
        'https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css' => 'css/font-awesome.min.css',
        'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js' => 'js/tex-mml-chtml.js',
        'https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11/build/highlight.min.js' => 'js/highlight.min.js',
        'https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11/build/styles/' => 'css/highlight.js',
        'https://cdn.jsdelivr.net/npm/html-to-md@0.8.5/dist/index.min.js' => 'js/html-to-md.min.js',
        'https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css' => 'css/toastify.min.css',
        'https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.js' => 'js/toastify.min.js',
        'https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.js' => 'js/toastify.min.js',
        'https://cdn.jsdelivr.net/npm/aplayer@1/dist/APlayer.min.js' => 'js/APlayer.min.js',
        'https://cdn.jsdelivr.net/npm/aplayer@1/dist/APlayer.min.css' => 'css/APlayer.min.css',
        'https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js' => 'js/clipboard.min.js'

    ];

    /**
     * 激活插件
     * @return string
     */
    public static function activate(): string
    {
        // 添加公共内容
        Plugin::factory('\Widget\Archive')->footer = [__CLASS__, 'archiveFooter'];

        // 添加文章编辑选项
        Plugin::factory('admin/write-post.php')->richEditor = [__CLASS__, 'richEditor'];
        Plugin::factory('admin/write-page.php')->richEditor = [__CLASS__, 'richEditor'];
        Plugin::factory('admin/write-post.php')->option = [__CLASS__, 'fakeToolbar'];
        Plugin::factory('admin/write-page.php')->option = [__CLASS__, 'fakeToolbar'];
        Plugin::factory('admin/write-post.php')->bottom = [__CLASS__, 'editorFooter'];
        Plugin::factory('admin/write-page.php')->bottom = [__CLASS__, 'editorFooter'];

        // 短代码
        Plugin::factory('admin/common.php')->begin = [__CLASS__, 'shortCodeInit'];
        Plugin::factory('\Widget\Archive')->handleInit = [__CLASS__, 'shortCodeInit'];

        // 内容替换处理
        Plugin::factory('\Widget\Base\Contents')->contentEx_99 = [__CLASS__, 'contentEx'];
        Plugin::factory('\Widget\Base\Contents')->excerptEx_99 = [__CLASS__, 'excerptEx'];

        // 增加路由
        Helper::addAction('editor', __NAMESPACE__ . '\Action');
        $url = Common::url('options-plugin.php?config=AAEditor#typecho-option-item-XModules-8', Helper::options()->adminUrl);
        return _t(/** @lang text */ '插件已启用，<a href="%s">点此进入插件设置</a>启用你需要的模块！', $url);
    }

    /**
     * 禁用插件
     *
     * @return string
     * @throws Exception|Db\Exception
     */
    public static function deactivate(): string
    {
        $db = Db::get();
        // 清理数据库
        Helper::removeAction('editor');
        if (Util::pluginOption('XCleanDatabase', 'none') === 'clean') {
            $db->query($db->delete('table.options')->where('name = ?', "plugin:AAEditorBackup"));
            return _t("数据清理成功，插件已禁用！");
        } else {
            return _t("插件已禁用，但备份数据未清理。");
        }
    }

    /**
     * 前台附加 CSS JS
     * @return void
     * @throws Exception
     */
    public static function archiveFooter($archive)
    {
        self::collectManifest();
        if (Util::pluginOption('XEditorContentStyle', 'off') === 'on') {
            ?>
            <link rel="stylesheet"
                  href="<?php echo Util::pluginStatic('css', 'content.css'); ?>">
            <?php
        }
        $lfa = Util::pluginOption('XLoadFontAwesome', 'on');
        if ($lfa === 'on') {
            // 引入字体图标
            ?>
            <link rel="stylesheet"
                  href="<?php echo Util::parseJSD('https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css'); ?>">
            <?php
        } else if ($lfa === 'auto') {
            ?>
            <script>
                (function () {
                    function loadFontAwesome() {
                        if (document.querySelector('.fa')) {
                            let rel = document.createElement('link');
                            rel.rel = 'stylesheet';
                            rel.href = '<?php echo Util::parseJSD('https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css'); ?>';
                            document.body.appendChild(rel);
                        }
                    }

                    loadFontAwesome();
                    document.addEventListener('pjax:complete', () => {
                        loadFontAwesome();
                    });
                })();
            </script>
            <?php
        }
        if (Util::pluginOption('XMathJaxSupport', 'on') === 'on') {
            // 引入 MathJax
            ?>
            <script id="MathJax-script" async
                    src="<?php echo Util::parseJSD('https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js'); ?>"></script>
            <script>
                // 配置 MathJax
                MathJax = {
                    tex: {
                        inlineMath: [['$', '$']],
                        displayMath: [['$$', '$$']],
                        processEscapes: true,
                        processEnvironments: true,
                    },
                    options: {
                        skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre'],
                    },
                };
                // 刷新预时重新渲染
                document.addEventListener('pjax:complete', () => {
                    try {
                        MathJax.typesetPromise();
                    } catch (e) {

                    }
                });
            </script>
            <?php
        }
        ?>
        <style>
            <?php echo file_get_contents(Util::pluginDir('assets/dist/css/x.css')); ?>
        </style>
        <?php
        $cssFiles = Util::listHljsCss();
        $filename = Util::pluginOption('XHljs', 'off');
        if ($filename !== "off" && array_key_exists($filename, $cssFiles)) {
            ?>
            <script
                    src="<?php echo Util::parseJSD('https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11/build/highlight.min.js') ?>"></script>
            <link rel="stylesheet"
                  href="<?php echo Common::url(preg_replace("/(?<!\.min)\.css$/", ".min.css", $filename), Util::parseJSD('https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11/build/styles/')) ?>">
            <link rel="stylesheet" href="<?php echo Util::pluginStatic('css', 'hljs.css') ?>"/>
            <script>
                (function () {
                    const copyIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16"><path d="M6.9998 6V3C6.9998 2.44772 7.44752 2 7.9998 2H19.9998C20.5521 2 20.9998 2.44772 20.9998 3V17C20.9998 17.5523 20.5521 18 19.9998 18H16.9998V20.9991C16.9998 21.5519 16.5499 22 15.993 22H4.00666C3.45059 22 3 21.5554 3 20.9991L3.0026 7.00087C3.0027 6.44811 3.45264 6 4.00942 6H6.9998ZM5.00242 8L5.00019 20H14.9998V8H5.00242ZM8.9998 6H16.9998V16H18.9998V4H8.9998V6Z"></path></svg>';
                    const okIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 26"><path d="M22.566406 4.730469L20.773438 3.511719C20.277344 3.175781 19.597656 3.304688 19.265625 3.796875L10.476563 16.757813L6.4375 12.71875C6.015625 12.296875 5.328125 12.296875 4.90625 12.71875L3.371094 14.253906C2.949219 14.675781 2.949219 15.363281 3.371094 15.789063L9.582031 22C9.929688 22.347656 10.476563 22.613281 10.96875 22.613281C11.460938 22.613281 11.957031 22.304688 12.277344 21.839844L22.855469 6.234375C23.191406 5.742188 23.0625 5.066406 22.566406 4.730469Z" fill="currentColor" /></svg>';

                    function initHighlighting() {
                        document.querySelectorAll("pre:not(.styled) code").forEach(el => {
                            const copy = document.createElement('span');
                            var div = document.createElement('div');
                            div.innerHTML = el.innerHTML;
                            copy.dataset.text = div.innerText;
                            copy.classList.add('copy');
                            Object.assign(copy.style, {
                                display: "flex",
                                alignItems: "center",
                                gap: ".25em",
                            });
                            if (!window.isSecureContext) {
                                Object.assign(copy.style, {
                                    cursor: "not-allowed",
                                });
                            }
                            copy.innerHTML = copyIcon;
                            copy.setAttribute('title', window.isSecureContext ? '<?php _e("点击复制"); ?>' : '<?php _e("非 HTTPS 不支持复制"); ?>')
                            copy.addEventListener('click', function () {
                                if (copy.hasAttribute("not-allowd")) return;
                                if (window.isSecureContext) {
                                    copy.setAttribute("not-allowd", "");
                                    navigator.clipboard.writeText(this.dataset.text);
                                    copy.innerHTML = okIcon;
                                    setTimeout(function () {
                                        copy.innerHTML = copyIcon;
                                        copy.removeAttribute("not-allowd");
                                    }, 1000);
                                }
                            });
                            el.after(copy);
                            hljs.highlightElement(el);
                            el.parentNode.classList.add('styled');
                        })
                    }

                    document.addEventListener("DOMContentLoaded", initHighlighting);
                    document.addEventListener("pjax:complete", initHighlighting);
                })()

            </script>
            <?php
        }
        $enableParse = $archive->fields->EnableShortCodeParse;
        if (Util::pluginOption('XShortCodeParse', 'on') === 'on' || $archive->is("single") && isset($enableParse)) {
            if (Util::pluginOption('XCombileModuleCss', 'off') === "on") {
                $cached = ob_get_clean();
                ob_start();
                foreach (Util::$archiveStatics as $method) {
                    call_user_func($method['parser'], $archive);
                }
                $static_html = ob_get_clean();
                $static_html = self::combineStyle($static_html);
                print $cached;
                print $static_html;
            } else {
                foreach (Util::$archiveStatics as $method) {
                    call_user_func($method['parser'], $archive);
                }
            }
        }
    }

    private static function combineStyle($input): string
    {
        // 构建标签的正则表达式
        $pattern = '/<link\s+rel="stylesheet"[^>]*\s+href="([^"]+)"[^>]*>/is';
        $cssFiles = [];
        $output = preg_replace_callback($pattern, function ($matches) use (&$cssFiles) {
            if (isset($matches[1])) {
                if (strpos($matches[1], Helper::options()->pluginUrl . '/AAEditor') !== false) {
                    $uri = str_replace(Helper::options()->pluginUrl . '/AAEditor', '', $matches[1]);
                    $arr = explode('?h=', $uri);
                    $cssFiles[] = [
                        'file' => Util::pluginDir($arr[0]),
                        'hash' => $arr[1]
                    ];
                    return '';
                }
            }
            return $matches[0];
        }, $input);
        $hash = '';
        foreach ($cssFiles as $file) {
            $hash .= $file['hash'];
        }
        $hash = md5($hash);
        if (file_exists(Util::pluginDir('cache/minify.css'))) {
            $url = Util::pluginUrl('cache/minify.css') . "?h=" . md5_file(Util::pluginDir('cache/minify.css'));
            // 拼接已合并的 CSS 文件
            $output = '<link rel="stylesheet" href="' . $url . '">' . $output;
        } else {
            // 合并 CSS 文件
            $cssContent = '';
            foreach ($cssFiles as $file) {
                $cssContent .= file_get_contents($file['file']);
            }
            $url = self::minifyJs($cssContent);
            if ($url) {
                // 拼接合并后的 CSS 文件
                $output = '<link rel="stylesheet" href="' . $url . '">' . '</head>' . $output;
            } else {
                $output = $input;
            }
        }
        return $output;
    }

    private static function minifyJs($input)
    {
        if (!is_dir(Util::pluginDir('cache'))) {
            try {
                mkdir(Util::pluginDir('cache'), 0755, true);
            } catch (\Exception $e) {
            }
        }
        try {
            file_put_contents(Util::pluginDir('cache/minify.css'), $input);
            return Util::pluginUrl('/cache/minify.css') . "?h=" . md5_file(Util::pluginDir('cache/minify.css'));
        } catch (\Exception $exception) {
        }
        return false;
    }

    /**
     * 编辑页面附加 CSS JS
     * @return void
     */
    public static function editorFooter()
    {
        $info = Plugin::parseInfo(Common::url('AAEditor/Plugin.php', Options::alloc()->pluginDir));
        $admin_dir = Common::url(trim(defined('__TYPECHO_ADMIN_DIR__') ? __TYPECHO_ADMIN_DIR__ : '/admin/', '/'), __TYPECHO_ROOT_DIR__);
        $css_path = Common::url('css/style.css', $admin_dir);
        if (is_file($css_path)): ?>
            <script>
                (function () {
                    let link = document.querySelector('link[href="<?php Helper::options()->adminStaticUrl('css', 'style.css') ?>"]');
                    if (link) link.parentNode.removeChild(link);
                    <?php if (Util::pluginOption('XInsertALlImages', 'on')): ?>
                    if ($('#ph-insert-images').length == 0)
                        $('#upload-panel').append(`<span id="ph-insert-images" class="ph-btn"><?php _e("插入所有图片") ?></span>`);

                    $('#ph-insert-images').off('click').on('click', function () {
                        let fileList = $('#file-list').children('li'),
                            text = "";
                        fileList.each((num, el) => {
                            let item = $(el);
                            if (item.data('image')) {
                                text += "\n" + "![{name}]({url})".replace('{name}', item.find('.insert').text()).replace('{url}', item.data('url'));
                            }
                        });
                        $('body').trigger('XEditorReplaceSelection', [text]);
                    });
                    <?php endif; ?>
                })();

            </script>
            <link rel="stylesheet"
                  href="<?php Helper::options()->index('action/editor?admin_style_css&version=' . $info['version'] ?? 'unknown'); ?>">
        <?php
        endif;
    }

    /**
     * 自定义编辑器
     * @param $content
     * @return void
     * @throws Exception
     */
    public static function richEditor($content)
    {
        $options = Helper::options();
        Util::collectManifest();
        if (Util::pluginOption('XEditorEnabled', 'on') === 'on') {
            ?>
            <link rel="stylesheet" href="<?php echo Util::pluginStatic('css', 'main.css'); ?>"/>
            <script src="<?php $options->adminStaticUrl('js', 'hyperdown.js'); ?>"></script>
            <script src="<?php $options->adminStaticUrl('js', 'pagedown.js'); ?>"></script>
            <script src="<?php $options->adminStaticUrl('js', 'paste.js'); ?>"></script>
            <script src="<?php $options->adminStaticUrl('js', 'purify.js'); ?>"></script>
            <script src="<?php echo Util::pluginStatic('js', 'previewUtils.js'); ?>"></script>
            <script
                    src="<?php echo Util::parseJSD('https://cdn.jsdelivr.net/npm/html-to-md@0.8.5/dist/index.min.js'); ?>"></script>
            <link rel="stylesheet" type="text/css"
                  href="<?php echo Util::parseJSD('https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css'); ?>">
            <script type="text/javascript"
                    src="<?php echo Util::parseJSD('https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.js'); ?>"></script>
            <script type="text/javascript"
                    src="<?php echo Util::parseJSD('https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js'); ?>"></script>
            <script>
                $(document).ready(function () {
                    // 伪工具栏处理
                    document.getElementById('wmd-button-bar').id = 'wmd-button-bar-fake';
                    document.getElementById('wmd-button-row').id = 'wmd-button-row-fake';

                    var options = {}, isMarkdown = <?php echo intval($content->isMarkdown || !$content->have()); ?>;

                    options.strings = {
                        bold: '<?php _e('加粗'); ?> <strong> Ctrl+B',
                        boldexample: '<?php _e('加粗文字'); ?>',

                        italic: '<?php _e('斜体'); ?> <em> Ctrl+I',
                        italicexample: '<?php _e('斜体文字'); ?>',

                        link: '<?php _e('链接'); ?> <a> Ctrl+L',
                        linkdescription: '<?php _e('请输入链接描述'); ?>',

                        quote: '<?php _e('引用'); ?> <blockquote> Ctrl+Q',
                        quoteexample: '<?php _e('引用文字'); ?>',

                        code: '<?php _e('代码'); ?> <pre><code> Ctrl+K',
                        codeexample: '<?php _e('请输入代码'); ?>',

                        image: '<?php _e('图片'); ?> <img> Ctrl+G',
                        imagedescription: '<?php _e('请输入图片描述'); ?>',

                        olist: '<?php _e('数字列表'); ?> <ol> Ctrl+O',
                        ulist: '<?php _e('普通列表'); ?> <ul> Ctrl+U',
                        litem: '<?php _e('列表项目'); ?>',

                        heading: '<?php _e('标题'); ?> <h1>/<h2> Ctrl+H',
                        headingexample: '<?php _e('标题文字'); ?>',

                        hr: '<?php _e('分割线'); ?> <hr> Ctrl+R',
                        more: '<?php _e('摘要分割线'); ?> <!--more--> Ctrl+M',

                        undo: '<?php _e('撤销'); ?> - Ctrl+Z',
                        redo: '<?php _e('重做'); ?> - Ctrl+Y',
                        redomac: '<?php _e('重做'); ?> - Ctrl+Shift+Z',

                        fullscreen: '<?php _e('全屏'); ?> - Ctrl+J',
                        exitFullscreen: '<?php _e('退出全屏'); ?> - Ctrl+E',
                        fullscreenUnsupport: '<?php _e('此浏览器不支持全屏操作'); ?>',

                        imagedialog: '<p><b><?php _e('插入图片'); ?></b></p><p><?php _e('请在下方的输入框内输入要插入的远程图片地址'); ?></p><p><?php _e('您也可以使用附件功能插入上传的本地图片'); ?></p>',
                        linkdialog: '<p><b><?php _e('插入链接'); ?></b></p><p><?php _e('请在下方的输入框内输入要插入的链接地址'); ?></p>',

                        ok: '<?php _e('确定'); ?>',
                        cancel: '<?php _e('取消'); ?>',

                        help: '<?php _e('Markdown语法帮助'); ?>'
                    };

                    var isFullScreen = false,
                        textarea = $('#text'),
                        toolbar = $('<div class="editor" id="wmd-button-bar" />').insertBefore(textarea.parent()),
                        pWrapper = $('<div id="wmd-preview-wrapper" />').insertAfter("#text"),
                        preview = $('<div id="wmd-preview" />').appendTo(pWrapper),
                        editor;

                    function initMarkdownEditor() {
                        var converter = new HyperDown();
                        editor = new Markdown.Editor(converter, '', options);

                        // 自动跟随
                        converter.enableHtml(true);
                        converter.enableLine(true);
                        reloadScroll = scrollableEditor(textarea, preview);

                        let additionalTags = ["x-tabs"];

                        converter.blockHtmlTags = additionalTags.length ?
                            converter.blockHtmlTags.concat("|" + additionalTags.join("|")) :
                            converter.blockHtmlTags;

                        // 修正白名单
                        converter.hook('makeHtml', function (html) {
                            // 不处理 pre/code
                            var blocks = [];
                            html = html.replace(/(?:<pre>.*?<\/pre>|<code>.*?<\/code>)/gims, function (match) {
                                blocks.push(match);
                                return `<pre>__BLOCK__</pre>`;
                            });

                            html = html.replace('<p><!--more--></p>', '<!--more-->');

                            if (html.indexOf('<!--more-->') > 0) {
                                var parts = html.split(/\s*<\!\-\-more\-\->\s*/),
                                    summary = parts.shift(),
                                    details = parts.join('');

                                html = '<div class="summary">' + summary + '</div>'
                                    + '<div class="details">' + details + '</div>';
                            }

                            // 替换block
                            html = html.replace(/<(iframe|embed)\s+([^>]*)>/ig, function (all, tag, src) {
                                if (src[src.length - 1] == '/') {
                                    src = src.substring(0, src.length - 1);
                                }

                                return '<div class="embed"><strong>'
                                    + tag + '</strong> : ' + $.trim(src) + '</div>';
                            });

                            // 处理短代码
                            if ("XPreviewUtils" in window && typeof window.XPreviewUtils.processHtml === "function") {
                                let processedHtml = window.XPreviewUtils.processHtml(html);
                                if (processedHtml && typeof html === "string") html = processedHtml;
                            }

                            // 还原 pre/code
                            html = html.replace(/<pre>__BLOCK__<\/pre>/g, function () {
                                return blocks.shift();
                            });

                            // 不注释塞不了 HTML
                            // return DOMPurify.sanitize(html, {USE_PROFILES: {html: true}});
                            return html;
                        });

                        editor.hooks.chain('onPreviewRefresh', function () {
                            var images = $('img', preview), count = images.length;

                            if (count == 0) {
                                reloadScroll(true);
                            } else {
                                images.bind('load error', function () {
                                    count--;

                                    if (count == 0) {
                                        reloadScroll(true);
                                    }
                                });
                            }

                            // 修复 p 包裹问题
                            if (preview.children().length === 1 && preview.children().first().is('p')) {
                                preview.html(preview.children().first().html());
                            }

                            $('body').trigger('XEditorPreviewEnd');
                        });

                        <?php \Typecho\Plugin::factory('admin/editor-js.php')->markdownEditor($content); ?>

                        var th = textarea.height(), ph = preview.height(),
                            uploadBtn = $('<button type="button" id="btn-fullscreen-upload" class="btn btn-link">'
                                + '<i class="i-upload"><?php _e('附件'); ?></i></button>')
                                .prependTo('.submit .right')
                                .click(function () {
                                    $('a', $('.typecho-option-tabs li').not('.active')).trigger('click');
                                    return false;
                                });

                        $('.typecho-option-tabs li').click(function () {
                            uploadBtn.find('i').toggleClass('i-upload-active',
                                $('#tab-files-btn', this).length > 0);
                        });

                        editor.hooks.chain('enterFakeFullScreen', function () {
                            th = textarea.height();
                            ph = preview.height();
                            let tbh = toolbar.outerHeight();
                            $(document.body).addClass('fullscreen');
                            var h = $(window).height() - tbh;
                            textarea.css('height', h).get(0).style.setProperty('--offset-top', tbh + 'px');
                            document.querySelector('.submit.clearfix').style.setProperty('--offset-height', tbh + 'px');
                            preview.parent().css('height', h).get(0).style.setProperty('--offset-top', tbh + 'px');
                            isFullScreen = true;
                        });

                        editor.hooks.chain('enterFullScreen', function () {
                            $(document.body).addClass('fullscreen');
                            var h = window.screen.height - toolbar.outerHeight();
                            textarea.css('height', h);
                            preview.parent().css('height', h);
                            isFullScreen = true;
                        });

                        editor.hooks.chain('exitFullScreen', function () {
                            $(document.body).removeClass('fullscreen');
                            textarea.height(th);
                            preview.parent().height(textarea.outerHeight());
                            isFullScreen = false;
                        });

                        editor.hooks.chain('commandExecuted', function () {
                            textarea.trigger('input');
                        });

                        editor.run();

                        textarea.get(0).pagedown = editor;

                        textarea.parent().addClass('edit-area');

                        // 移动按钮（如有需要）
                        $('#wmd-button-row-fake').children().toArray().reverse().forEach((i, el) => {
                            $('#wmd-button-row').append(el);
                        });

                        $('#wmd-button-bar-fake').remove();

                        // 初始化预览区域大小
                        let previewArea = $('#wmd-preview-wrapper');
                        previewArea.css('height', (parseInt($('#text').outerHeight())) + 'px');

                        // 拖拽实时改变大小
                        let resizeBtn = $('.edit-area .resize'),
                            btnPress = false;
                        resizeBtn.on('mousedown', () => {
                            document.addEventListener('mousemove', mouseMove);
                            document.addEventListener('mouseup', mouseUp);
                            btnPress = true;
                            previewArea.css("opacity", 0.25);

                            function mouseMove() {
                                if (btnPress) {
                                    previewArea.css('height', (parseInt(textarea.outerHeight()) + 'px'));
                                }
                            }

                            function mouseUp() {
                                btnPress = false;
                                previewArea.css("opacity", 1);
                                previewArea.css('height', (parseInt(textarea.outerHeight()) + 'px'));
                                document.removeEventListener('mousemove', mouseMove);
                                document.removeEventListener('mouseup', mouseUp);
                            }
                        });

                        // 优化图片及文件附件插入 Thanks to Markxuxiao
                        Typecho.insertFileToEditor = function (file, url, isImage) {
                            let html = isImage ? '![' + file + '](' + url + ')'
                                : '[' + file + '](' + url + ')';
                            if (XEditor.insertProcessors && Array.isArray(XEditor.insertProcessors)) {
                                for (let i = 0; i < XEditor.insertProcessors.length; i++) {
                                    if (typeof XEditor.insertProcessors[i] === 'function') {
                                        let {
                                            html: _html,
                                            done
                                        } = XEditor.insertProcessors[i].call(XEditor, file, url, isImage, html);
                                        if (typeof _html === "string") {
                                            html = _html;
                                        }
                                        if (done) {
                                            break;
                                        }
                                    }
                                }
                            }
                            $('body').trigger('XEditorReplaceSelection', [html]);
                        };

                        // 剪贴板复制图片
                        textarea.pastableTextarea().on('pasteImage', function (e, data) {
                            var name = data.name ? data.name.replace(/[\(\)\[\]\*#!]/g, '') : (new Date()).toISOString().replace(/\..+$/, '');
                            if (!name.match(/\.[a-z0-9]{2,}$/i)) {
                                var ext = data.blob.type.split('/').pop();
                                name += '.' + ext;
                            }

                            Typecho.uploadFile(new File([data.blob], name), name);
                        });

                        // 上传完成后自动插入
                        const splitRegex = /[\s,|]/gm;
                        const types = '<?php echo Util::pluginOption('XDisableAttachAutoInsert', ''); ?>'.replace(splitRegex, '|').split('|');
                        Typecho.uploadComplete = function (file) {
                            if (file.type.length && types.includes(file.type)) return;
                            Typecho.insertFileToEditor(file.title, file.url, file.isImage);
                        };
                    }

                    // 非 Markdown 提示
                    if (isMarkdown) {
                        $('<input type="hidden" name="markdown" value="1" />').appendTo('.submit');
                        textarea.attr('markdown', 1);
                        initMarkdownEditor();
                    } else {
                        let notice = $('<div class="message notice"><?php _e('这篇文章不是由Markdown语法创建的，为你转换为 Markdown 并继续编辑？'); ?> '
                            + '<button class="btn btn-xs primary yes"><?php _e('是'); ?></button> '
                            + '<button class="btn btn-xs no"><?php _e('否'); ?></button></div>')
                            .hide().insertBefore($("#text")).slideDown();

                        $('.yes', notice).click(function () {
                            notice.remove();
                            textarea.attr('markdown', 1)
                            $('<input type="hidden" name="markdown" value="1" />').appendTo('.submit');
                            let text = textarea.val(),
                                newText = html2md(text);
                            initMarkdownEditor();
                            editor.textOperation(function () {
                                textarea.val(newText);
                            });
                            $('body').trigger('XEditorInit', []);
                        });

                        $('.no', notice).click(function () {
                            textarea.attr('markdown', 0)
                            notice.remove();
                        });
                    }

                    // 增加发布工具栏显示方向切换
                    let toggleDirection = $(`<button id="toggle-vertical" class="btn square secondary" type="button" title="<?php _e("切换显示方向") ?>"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128" width="20" height="20"><path d="M17.699219 14.099609C15.999219 14.099609 14.699219 15.399609 14.699219 17.099609L14.699219 94C14.699219 105 23.599609 113.90039 34.599609 113.90039C45.599609 113.90039 54.5 105 54.5 94L54.5 17.099609C54.5 15.499609 53.2 14.099609 51.5 14.099609L17.699219 14.099609 z M 20.699219 20.099609L48.5 20.099609L48.5 33.699219L20.699219 33.699219L20.699219 20.099609 z M 76.199219 24.599609C75.399219 24.599609 74.599609 24.9 74.099609 25.5L58.300781 41.5C57.100781 42.7 57.200781 44.599219 58.300781 45.699219C59.500781 46.899219 61.4 46.799219 62.5 45.699219L76.199219 31.800781L96 51.599609L58.400391 89.900391C57.200391 91.100391 57.300391 92.999609 58.400391 94.099609C59.000391 94.699609 59.7 95 60.5 95C61.3 95 62.099609 94.699609 62.599609 94.099609L88.099609 80L107.40039 80L107.40039 108L54.300781 108.09961C52.700781 108.09961 51.400391 109.49961 51.400391 111.09961C51.400391 112.79961 52.700391 114.09961 54.400391 114.09961L110.5 114C112.2 114 113.5 112.7 113.5 111L113.40039 76.900391C113.40039 76.100391 113.1 75.300781 112.5 74.800781C111.9 74.200781 111.20039 73.900391 110.40039 73.900391L82.400391 74L102.30078 53.699219C103.40078 52.499219 103.40078 50.6 102.30078 49.5L78.300781 25.5C77.700781 24.9 76.999219 24.599609 76.199219 24.599609 z M 20.699219 39.699219L48.5 39.699219L48.5 54.199219L20.699219 54.199219L20.699219 39.699219 z M 20.699219 60.199219L48.5 60.199219L48.5 73.800781L20.699219 73.800781L20.699219 60.199219 z M 20.699219 79.800781L48.5 79.800781L48.5 94C48.5 101.7 42.299609 107.90039 34.599609 107.90039C26.899609 107.90039 20.699219 101.7 20.699219 94L20.699219 79.800781 z M 34.599609 91 A 3 3 0 0 0 31.599609 94 A 3 3 0 0 0 34.599609 97 A 3 3 0 0 0 37.599609 94 A 3 3 0 0 0 34.599609 91 z"/></svg></button`)
                        .on('click', function () {
                            let newVerticalStatus = (localStorage.getItem('submit-vertical') || "false") === 'true' ? 'false' : 'true';
                            localStorage.setItem('submit-vertical', newVerticalStatus);
                            $(".submit.clearfix").attr('vertical', newVerticalStatus);
                        });
                    $(".submit.clearfix").attr('vertical', localStorage.getItem('submit-vertical') || "false")
                        .prepend(toggleDirection);
                    setOffsetLeft();
                    window.addEventListener('resize', setOffsetLeft);

                    function setOffsetLeft() {
                        document.querySelector(".submit.clearfix").style.setProperty('--offset-left', (document.querySelector('.container').offsetLeft - 35) + 'px');
                    }

                    let toggleWideScreen = $(`<button id="toggle-widescreen" class="btn square secondary" type="button" title="<?php _e("切换宽屏显示") ?>"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="20" height="20"><path d="M4 8L4 24L6 24L6 8 Z M 8 8C7.449219 8 7 8.449219 7 9C7 9.550781 7.449219 10 8 10C8.550781 10 9 9.550781 9 9C9 8.449219 8.550781 8 8 8 Z M 12 8C11.449219 8 11 8.449219 11 9C11 9.550781 11.449219 10 12 10C12.550781 10 13 9.550781 13 9C13 8.449219 12.550781 8 12 8 Z M 16 8C15.449219 8 15 8.449219 15 9C15 9.550781 15.449219 10 16 10C16.550781 10 17 9.550781 17 9C17 8.449219 16.550781 8 16 8 Z M 20 8C19.449219 8 19 8.449219 19 9C19 9.550781 19.449219 10 20 10C20.550781 10 21 9.550781 21 9C21 8.449219 20.550781 8 20 8 Z M 24 8C23.449219 8 23 8.449219 23 9C23 9.550781 23.449219 10 24 10C24.550781 10 25 9.550781 25 9C25 8.449219 24.550781 8 24 8 Z M 26 8L26 24L28 24L28 8 Z M 11 13L7 16L11 19L11 17L21 17L21 19L25 16L21 13L21 15L11 15 Z M 8 22C7.449219 22 7 22.449219 7 23C7 23.550781 7.449219 24 8 24C8.550781 24 9 23.550781 9 23C9 22.449219 8.550781 22 8 22 Z M 12 22C11.449219 22 11 22.449219 11 23C11 23.550781 11.449219 24 12 24C12.550781 24 13 23.550781 13 23C13 22.449219 12.550781 22 12 22 Z M 16 22C15.449219 22 15 22.449219 15 23C15 23.550781 15.449219 24 16 24C16.550781 24 17 23.550781 17 23C17 22.449219 16.550781 22 16 22 Z M 20 22C19.449219 22 19 22.449219 19 23C19 23.550781 19.449219 24 20 24C20.550781 24 21 23.550781 21 23C21 22.449219 20.550781 22 20 22 Z M 24 22C23.449219 22 23 22.449219 23 23C23 23.550781 23.449219 24 24 24C24.550781 24 25 23.550781 25 23C25 22.449219 24.550781 22 24 22Z"/></svg></button`)
                    toggleWideScreen.on('click', () => {
                        if (currentIsWidescreen()) {
                            $('.container').removeClass('widescreen');
                            localStorage.setItem('editor-widescreen', false);
                        } else {
                            $('.container').addClass('widescreen');
                            localStorage.setItem('editor-widescreen', true);
                        }
                        setOffsetLeft();
                    });
                    toggleDirection.after(toggleWideScreen);
                    if (currentIsWidescreen()) {
                        $('.container').addClass('widescreen');
                        setOffsetLeft();
                    }

                    function currentIsWidescreen() {
                        return (localStorage.getItem('editor-widescreen') || 'false') === 'true';
                    }
                });
            </script>
            <script src="<?php echo Util::pluginStatic('js', 'main.js'); ?>"></script>
            <script>
                (function () {
                    fetch("https://xiamp.net/archives/aaeditor-update-log.html").then(response => response.text())
                        .then(text => {
                            const fragment = document.createElement('div');
                            fragment.innerHTML = text;
                            let firstNode = fragment.querySelector('h2, h3');
                            let upldateLog = firstNode.nextElementSibling;
                            let updateLinkNode = firstNode.previousElementSibling.querySelector('a[href^="https://"]');
                            let updateLinkText = firstNode.previousElementSibling.innerHTML;
                            let updateLink = 'https://xiamp.net/archives/aaeditor-is-another-typecho-editor-plugin.html';
                            if (updateLinkText.indexOf('<?php _e("最新版下载") ?>') && updateLinkNode) {
                                updateLink = updateLinkNode.href;
                            }
                            const serverVersion = firstNode.innerText.substring(0, 5);
                            const delay_notice_version = getCookie('aaeditor_delay_notice_version');
                            if (delay_notice_version && compareVersions(delay_notice_version, serverVersion) >= 0) {
                                return;
                            }
                            if (compareVersions(serverVersion, '<?php echo \TypechoPlugin\AAEditor\Plugin::version() ?>') > 0) {
                                let div = document.createElement('div');
                                div.className = 'aaeditor-update-log';
                                div.style.backgroundColor = '#fff';
                                div.innerHTML = `<div class="update-log-title"><strong><?php _e("AAEditor 有更新！"); ?>(<span style="color: red">${serverVersion}</span>)</strong><small class="delay-no-more">一周不再提醒</small></div><div class="update-log-content">${upldateLog.outerHTML}<a class="btn-download" href="${updateLink}" target="_blank"><?php _e("下载最新版") ?></a></div><div class="progress-bar"></div>`;
                                document.body.appendChild(div);
                                div.querySelector('.delay-no-more').addEventListener('click', function () {
                                    removeElWithFadeOut(div, serverVersion);
                                });
                                setTimeout(() => {
                                    removeElWithFadeOut(div);
                                }, 5000);
                            }
                        })

                    function removeElWithFadeOut(el, delay_notice_version) {
                        // cookie 插入 nerver_notice_version
                        if (delay_notice_version) {
                            setCookie('aaeditor_delay_notice_version', delay_notice_version, 7);
                        }
                        $(el).fadeOut(500, function () {
                            $(el).remove();
                        })
                    }

                    function setCookie(name, value, days) {
                        let expires = "";
                        if (days) {
                            const date = new Date();
                            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                            expires = "; expires=" + date.toUTCString();
                        }
                        document.cookie = name + "=" + (value || "") + expires + "; path=/";
                    }

                    function getCookie(name) {
                        const nameEQ = name + "=";
                        const ca = document.cookie.split(';');
                        for (let i = 0; i < ca.length; i++) {
                            let c = ca[i];
                            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
                        }
                        return null;
                    }

                    function compareVersions(serverVersion, currentVersion) {
                        const serverVersionArray = serverVersion.split('.');
                        const currentVersionArray = currentVersion.split('.');
                        const minLength = Math.min(serverVersionArray.length, currentVersionArray.length);

                        for (let i = 0; i < minLength; i++) {
                            const a = parseInt(serverVersionArray[i]);
                            const b = parseInt(currentVersionArray[i]);
                            if (a > b) {
                                return 1;
                            } else if (a < b) {
                                return -1;
                            }
                        }

                        if (serverVersionArray.length > currentVersionArray.length) {
                            for (let j = minLength; j < serverVersionArray.length; j++) {
                                if (parseInt(serverVersionArray[j]) !== 0) {
                                    return 1;
                                }
                            }
                            return 0;
                        } else if (serverVersionArray.length < currentVersionArray.length) {
                            for (let j = minLength; j < currentVersionArray.length; j++) {
                                if (parseInt(currentVersionArray[j]) !== 0) {
                                    return -1;
                                }
                            }
                            return 0;
                        }

                        return 0;
                    }
                })()
            </script>
            <style>
                .aaeditor-update-log {
                    position: fixed;
                    right: 40px;
                    top: 40px;
                    border: 1px solid rgba(0, 0, 0, 0.175);
                    overflow: hidden;
                    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
                    background-color: #fff;
                }

                .aaeditor-update-log .update-log-title {
                    display: flex;
                    align-items: center;
                    padding: 0.25em 0.75em;
                    color: rgba(33, 37, 41, 0.75);
                    background-color: rgba(255, 255, 255, 0.85);
                    background-clip: padding-box;
                }

                .aaeditor-update-log .update-log-title .delay-no-more {
                    margin-inline-start: auto;
                    transition: color .2s;
                    cursor: pointer;
                }

                .aaeditor-update-log .update-log-title .delay-no-more:hover {
                    color: #0d6efd;
                }

                .aaeditor-update-log .update-log-content {
                    padding: 0.5em 0.75em;
                }

                .aaeditor-update-log .update-log-content ol {
                    margin: 0;
                    padding-left: 20px;
                }

                .aaeditor-update-log .update-log-content .btn-download {
                    margin-block-start: .5em;
                    padding: .25em .5em;
                    font-size: .875em;
                    color: #fff;
                    background-color: #0d6efd;
                    border-radius: .25em;
                    display: inline-flex;
                    align-items: center;
                    border: 1px solid #0d6efd;
                    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15), 0 1px 1px rgba(0, 0, 0, 0.075);
                    transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
                }

                .aaeditor-update-log .update-log-content .btn-download:hover {
                    background-color: #0b5ed7;
                    border-color: #0a58ca;
                    text-decoration: none;
                }

                .aaeditor-update-log .update-log-content .btn-download:focus {
                    box-shadow: 0 0 0 0.25rem rgba(49, 132, 253, .5);
                }

                .aaeditor-update-log .update-log-content .btn-download:active {
                    box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
                    border-color: #0a53be;
                    background-color: #0a58ca;
                }

                @keyframes progress-bar-stripes {
                    to {
                        transform: translateX(-100%);
                    }
                }

                .aaeditor-update-log .progress-bar {
                    height: 3px;
                    background-color: #0d6efd;
                    animation: progress-bar-stripes 5s linear forwards;
                }
            </style>
            <link rel="stylesheet"
                  href="<?php echo Util::parseJSD('https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css') ?>">
            <?php
            if (Util::pluginOption('XMathJaxSupport', 'on') === 'on') {
                ?>
                <script id="MathJax-script" async
                        src="<?php echo Util::parseJSD('https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js'); ?>"></script>
                <script>
                    // 配置 MathJax
                    MathJax = {
                        tex: {
                            inlineMath: [['$', '$']],
                            displayMath: [['$$', '$$']],
                            processEscapes: true,
                            processEnvironments: true,
                        },
                        options: {
                            skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre'],
                        },
                    };
                    // 刷新预时重新渲染
                    $('body').on('XEditorPreviewEnd', async () => {
                        try {
                            MathJax.typesetPromise();
                        } catch (e) {

                        }
                    });
                </script>
            <?php }
            require_once self::pluginDir('assets/dist/js/editor-js.php');
            $enabledOptions = json_decode(Util::pluginOption('XModules', '[]'));
            foreach ($enabledOptions as $module) {
                $len = strlen($module);
                if ($len > 4 && strtolower(substr($module, $len - 4)) === ".php") {
                    $filePath = Util::pluginDir('Modules' . DIRECTORY_SEPARATOR . $module);
                    if (file_exists($filePath)) {
                        require_once $filePath;
                        $className = 'Module' . substr($module, 0, $len - 4);
                        if (class_exists($className) && method_exists($className, 'editorStatic')) {
                            call_user_func($className . '::editorStatic');
                        }
                    }
                } else {
                    $dir = Util::pluginDir('Modules' . DIRECTORY_SEPARATOR . $module);
                    $filePath = $dir . DIRECTORY_SEPARATOR . 'index.php';
                    if (is_dir($dir) && is_file($filePath)) {
                        require_once $filePath;
                        $className = 'Module' . $module;
                        if (class_exists($className) && method_exists($className, 'editorStatic')) {
                            call_user_func($className . '::editorStatic');
                        }
                    }
                }
            }
        }
    }

    /**
     * @throws Db\Exception
     */
    public static function hasOption(): bool
    {
        $db = Db::get();
        $sql = $db->select()->from('table.options')->where('name = ?', 'plugin:AAEditor');
        $result = $db->fetchAll($sql);
        return count($result) > 0;
    }

    /**
     * 缓存短代码替换规则
     *
     * @return void
     * @throws Exception
     * @throws Db\Exception
     */
    public static function shortCodeInit()
    {
        if (self::hasOption()) {
            Util::$contentParsers = [];
            Util::$excerptParsers = [];
            Util::$archiveStatics = [];
            $enabledOptions = json_decode(Util::pluginOption('XModules', '[]'));
            foreach ($enabledOptions as $module) {
                $len = strlen($module);
                if ($len > 4 && strtolower(substr($module, $len - 4)) === ".php") {
                    $filePath = Util::pluginDir('Modules' . DIRECTORY_SEPARATOR . $module);
                    if (file_exists($filePath)) {
                        require_once $filePath;
                        $className = 'Module' . substr($module, 0, $len - 4);
                        self::addParser($className);
                    }
                } else {
                    $dir = Util::pluginDir('Modules' . DIRECTORY_SEPARATOR . $module);
                    $filePath = $dir . DIRECTORY_SEPARATOR . 'index.php';
                    if (is_dir($dir) && is_file($filePath)) {
                        require_once $filePath;
                        $className = 'Module' . $module;
                        self::addParser($className);
                    }
                }
            }
        }
    }

    /**
     * 增加处理函数到队列中
     *
     * @param string $className
     * @return void
     */
    public static function addParser(string $className): void
    {
        if (class_exists($className)) {
            if (method_exists($className, 'parseContent')) {
                self::$contentParsers[] = [
                    'priority' => ($className::$priority ?? 99),
                    'parser' => $className . '::parseContent'
                ];
            }
            if (method_exists($className, 'parseExcerpt')) {
                self::$excerptParsers[] = [
                    'priority' => ($className::$priority ?? 99),
                    'parser' => $className . '::parseExcerpt'
                ];
            }
            if (method_exists($className, 'archiveStatic')) {
                self::$archiveStatics[] = [
                    'priority' => ($className::$priority ?? 99),
                    'parser' => $className . '::archiveStatic'
                ];
            }
        }
    }

    /**
     * 内容处理
     * @param $text
     * @param $archive
     * @param $last
     * @return string
     * @throws \Typecho\Db\Exception
     * @throws \Typecho\Plugin\Exception
     * @throws \Typecho\Widget\Exception
     */
    public
    static function contentEx($text, $archive, $last): string
    {
        if ($last) $text = $last;
        $enableParse = $archive->fields->EnableShortCodeParse;
        if (Util::pluginOption('XShortCodeParse', 'on') === 'on' || $archive->is("single") && isset($enableParse)) {
            // 隐藏代码块
            $blocks = [];
            $codeHolder = "<pre>__BLOCK__</pre>";
            $codeHolderLen = strlen($codeHolder);
            $text = preg_replace_callback('/(?:<pre>.*?<\/pre>|<code>.*?<\/code>)/ism', function ($match) use (&$blocks, $codeHolder) {
                $blocks[] = $match[0];
                return $codeHolder;
            }, $text);

            // Markdown 增强
            if (strpos($text, '[x]') !== false || strpos($text, '[ ]') !== false) {
                $text = strtr($text, array(
                    "[x]" => '<input type="checkbox" class="x-checkbox" checked disabled /><label class="x-checkbox-label"></label>',
                    "[ ]" => '<input type="checkbox" class="x-checkbox" disabled /><label class="x-checkbox-label"></label>'
                ));
            }

            foreach (Util::$contentParsers as $parserItem) {
                if (array_key_exists('parser', $parserItem)) {
                    $newText = call_user_func($parserItem['parser'], $text, $archive);
                    if ($newText) {
                        $text = $newText;
                    }
                }
            }

            // 还原代码块
            if (count($blocks)) {
                foreach ($blocks as $block) {
                    $pos = strpos($text, $codeHolder);
                    if ($pos !== false) {
                        $text = substr_replace($text, $block, $pos, $codeHolderLen);
                    }
                }
            }

            if (false !== strpos($text, '[hide')) {
                $pattern = "(?s)<pre[^<]*>.*?<\/pre>(*SKIP)(*F)|\[hide](.*?)\[\/hide]";
                $text = preg_replace_callback("/$pattern/ism", function ($m) use ($archive) {
                    $content = $m[1] ?? null;
                    return Util::hideCallback($content, $archive);
                }, $text);
            }
        }
        return $text;
    }

    /**
     * 摘要处理
     */
    public static function excerptEx($text, $archive, $last): string
    {
        if ($last) $text = $last;
        // 隐藏代码块
        $blocks = [];
        $codeHolder = "<pre>__BLOCK__</pre>";
        $codeHolderLen = strlen($codeHolder);
        $text = preg_replace_callback('/(?:<pre>.*?<\/pre>|<code>.*?<\/code>)/ism', function ($match) use (&$blocks, $codeHolder) {
            $blocks[] = $match[0];
            return $codeHolder;
        }, $text);

        // Markdown 增强
        if (strpos($text, '[x]') !== false || strpos($text, '[ ]') !== false) {
            $text = strtr($text, array(
                "[x]" => '【已完成】',
                "[ ]" => '【未完成】'
            ));
        }

        if (is_array(Util::$excerptParsers)) {
            foreach (Util::$excerptParsers as $parserItem) {
                $pos = stripos($parserItem['parser'], '::');
                $class = substr($parserItem['parser'], 0, $pos);
                $method = substr($parserItem['parser'], $pos + 2);
                if (array_key_exists('parser', $parserItem) && method_exists($class, $method)) {
                    $text = call_user_func([$class, $method], $text, $archive);
                }
            }
        }

        // 还原代码块
        if (count($blocks)) {
            foreach ($blocks as $block) {
                $pos = strpos($text, $codeHolder);
                if ($pos !== false) {
                    $text = substr_replace($text, $block, $pos, $codeHolderLen);
                }
            }
        }

        if (false !== strpos($text, '[hide')) {
            $pattern = "(?s)<pre[^<]*>.*?<\/pre>(*SKIP)(*F)|\[hide](.*?)\[\/hide]";
            $text = preg_replace("/$pattern/ism", '【内容回复可见】', $text);
        }
        return $text;
    }

    /**
     * Retrieve the shortcode regular expression for searching.
     *
     * The regular expression combines the shortcode tags in the regular expression
     * in a regex class.
     *
     * The regular expression contains 6 different sub matches to help with parsing.
     *
     * 1 - An extra [ to allow for escaping shortcodes with double [[]]
     * 2 - The shortcode name
     * 3 - The shortcode argument list
     * 4 - The self closing /
     * 5 - The content of a shortcode when it wraps some content.
     * 6 - An extra ] to allow for escaping shortcodes with double [[]]
     *
     * @param array $tagnames Optional. List of shortcodes to find. Defaults to all registered shortcodes.
     * @return string The shortcode search regular expression
     * @global array $shortcode_tags
     *
     * @since 2.5.0
     * @since 4.4.0 Added the `$tagnames` parameter.
     *
     */
    public static function get_shortcode_regex($tagnames = null): string
    {
        global $shortcode_tags;

        if (empty($tagnames)) {
            $tagnames = array_keys($shortcode_tags);
        }
        $tagregexp = implode('|', array_map('preg_quote', $tagnames));

        // WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag().
        // Also, see shortcode_unautop() and shortcode.js.

        // phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound -- don't remove regex indentation
        return '\\['                             // Opening bracket.
            . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]].
            . "($tagregexp)"                     // 2: Shortcode name.
            . '(?![\\w-])'                       // Not followed by word character or hyphen.
            . '('                                // 3: Unroll the loop: Inside the opening shortcode tag.
            . '[^\\]\\/]*'                   // Not a closing bracket or forward slash.
            . '(?:'
            . '\\/(?!\\])'               // A forward slash not followed by a closing bracket.
            . '[^\\]\\/]*'               // Not a closing bracket or forward slash.
            . ')*?'
            . ')'
            . '(?:'
            . '(\\/)'                        // 4: Self closing tag...
            . '\\]'                          // ...and closing bracket.
            . '|'
            . '\\]'                          // Closing bracket.
            . '(?:'
            . '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags.
            . '[^\\[]*+'             // Not an opening bracket.
            . '(?:'
            . '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag.
            . '[^\\[]*+'         // Not an opening bracket.
            . ')*+'
            . ')'
            . '\\[\\/\\2\\]'             // Closing shortcode tag.
            . ')?'
            . ')'
            . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]].
        // phpcs:enable
    }

    /**
     * Retrieve all attributes from the shortcodes tag.
     *
     * The attributes list has the attribute name as the key and the value of the
     * attribute as the value in the key/value pair. This allows for easier
     * retrieval of the attributes, since all attributes have to be known.
     *
     * @param string $text
     * @return array|string List of attribute values.
     *                      Returns empty array if '""' === trim( $text ).
     *                      Returns empty string if '' === trim( $text ).
     *                      All other matches are checked for not empty().
     * @since 2.5.0
     *
     */
    public static function shortcode_parse_atts($text)
    {
        $atts = array();
        $pattern = Util::get_shortcode_atts_regex();
        $text = preg_replace("/[\x{00a0}\x{200b}]+/u", ' ', $text);
        if (preg_match_all($pattern, $text, $match, PREG_SET_ORDER)) {
            foreach ($match as $m) {
                if (!empty($m[1])) {
                    $atts[strtolower($m[1])] = stripcslashes($m[2]);
                } elseif (!empty($m[3])) {
                    $atts[strtolower($m[3])] = stripcslashes($m[4]);
                } elseif (!empty($m[5])) {
                    $atts[strtolower($m[5])] = stripcslashes($m[6]);
                } elseif (isset($m[7]) && strlen($m[7])) {
                    $atts[] = stripcslashes($m[7]);
                } elseif (isset($m[8]) && strlen($m[8])) {
                    $atts[] = stripcslashes($m[8]);
                } elseif (isset($m[9])) {
                    $atts[] = stripcslashes($m[9]);
                }
            }

            // Reject any unclosed HTML elements.
            foreach ($atts as &$value) {
                if (false !== strpos($value, '<')) {
                    if (1 !== preg_match('/^[^<]*+(?:<[^>]*+>[^<]*+)*+$/', $value)) {
                        $value = '';
                    }
                }
            }
        } else {
            $atts = ltrim($text);
        }

        return $atts;
    }


    /**
     * Retrieve the shortcode attributes regex.
     *
     * @return string The shortcode attribute regular expression
     * @since 4.4.0
     *
     */
    public static function get_shortcode_atts_regex(): string
    {
        return '/([\w-]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w-]+)\s*=\s*\'([^\']*)\'(?:\s|$)|([\w-]+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|\'([^\']*)\'(?:\s|$)|(\S+)(?:\s|$)/';
    }

    /**
     * 获取插件资源 URL
     *
     * @param string $uri URI 相对于插件目录的资源相对目录
     * @return string 获取插件资源的 URL
     */
    public static function pluginUrl(string $uri = ""): string
    {
        return Common::url($uri, Helper::options()->pluginUrl . '/AAEditor');
    }

    /**
     * 获取模块资源 URL
     *
     * @param string $moduleName 模块目录名
     * @param string $uri 资源相对路径
     * @return string
     */
    public static function moduleUrl(string $moduleName, string $uri = ''): string
    {
        $relative_path = Common::url(Common::url($uri, $moduleName), 'Modules');
        $absolute_path = Common::url($relative_path, dirname(__FILE__));
        if (is_file($absolute_path)) {
            return self::pluginUrl($relative_path . '?h=' . md5($absolute_path));
        }
        return self::pluginUrl($relative_path);
    }

    /**
     * 收集 Manifest 数据
     *
     * @return void
     */
    public static function collectManifest()
    {
        $manifestPath = self::pluginDir('assets/dist/mix-manifest.json');
        $manifest = [];
        if (is_file($manifestPath)) {
            try {
                $manifest = json_decode(file_get_contents($manifestPath), true);
            } catch (\Exception $e) {
            }
        }
        self::$manifest = $manifest;
    }

    /**
     * 获取静态资源 URL
     *
     * @param string $type 资源类型 js/css
     * @param string $path
     * @return string
     */
    public static function pluginStatic(string $type, string $path): string
    {
        $uri = '/' . Common::url($path, $type);
        if (array_key_exists($uri, self::$manifest ?? [])) {
            $uri = self::$manifest[$uri];
        } else {
            $absolutePath = Util::pluginDir('assets/dist' . $uri);
            if (is_file($absolutePath)) {
                $uri .= '?h=' . md5_file($absolutePath);
            }
        }
        return self::pluginUrl('assets/dist/' . $uri);
    }

    /**
     * 获取本地镜像路径
     *
     * @Date 2024/8/19
     * @param string $uri 相对路径
     * @return string
     */
    public static function pluginMirror(string $uri): string
    {
        return Common::url($uri, self::pluginUrl('assets/mirror/'));
    }

    /**
     * 获取插件资源文件具体路径
     * @param string $uri 相对于插件目录的资源相对路径
     * @return string 资源绝对路径
     */
    public static function pluginDir(string $uri = ""): string
    {
        $uri = ltrim(str_replace("/", "\\", $uri), "/\\");
        $pluginDir = str_replace("/", "\\", rtrim(Helper::options()->pluginDir, "/\\") . '/AAEditor');
        return str_replace("\\", DIRECTORY_SEPARATOR, $pluginDir . '\\' . $uri);
    }

    /**
     * 获取插件配置
     *
     * @param String $key 关键字
     * @param mixed $default 默认值
     * @return mixed
     * @throws Exception
     */
    public static function pluginOption(string $key, $default = null)
    {
        $value = Helper::options()->plugin('AAEditor')->$key;
        return $value ?: $default;
    }

    /**
     * 获取 JSD 资源链接
     *
     * @param string $url 资源链接
     * @return string
     */
    public static function parseJSD(string $url): string
    {
        try {
            $mirror = self::pluginOption('XJsdelivrMirror', 'local');
        } catch (Exception $e) {
            $mirror = 'local';
        }

        if ($mirror === 'local') {
            if (array_key_exists($url, self::$staticMap)) {
                return self::pluginMirror(self::$staticMap[$url]);
            }
        } else {
            return preg_replace("/((https?:)?)\/\/cdn\.jsdelivr\.net/", $mirror, $url);
        }
        return $url;
    }

    /**
     * 截断文本
     * @param string $text 需要截断的文本
     * @param int $length 长度
     * @param string $trim 结尾
     * @return string
     */
    public static function subStr(string $text, int $length = 120, string $trim = '...'): string
    {
        return Common::fixHtml(Common::subStr($text, 0, $length, $trim));
    }

    /**
     * 通过反射获取内部变量
     *
     * @param mixed $object
     * @param string $name
     * @return mixed
     * @throws \ReflectionException
     */
    public static function reflectGetValue($object, string $name)
    {
        $reflect = new ReflectionClass($object);
        $property = $reflect->getProperty($name);
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    /**
     * 列出所有 Highlight.js 的样式文件
     * @return array
     */
    public static function listHljsCss(): array
    {
        $cssPath = Util::pluginDir('assets/dist/css/highlight.js');
        if (DIRECTORY_SEPARATOR === "\\") {
            $cssPath = str_replace("/", "\\", $cssPath);
        } else {
            $cssPath = str_replace("\\", "/", $cssPath);
        }
        $files = array_filter(glob($cssPath . DIRECTORY_SEPARATOR . '*'), function ($path) {
            return preg_match("/\.(css)$/i", $path);
        });
        $base16 = array_filter(glob($cssPath . DIRECTORY_SEPARATOR . 'base16' . DIRECTORY_SEPARATOR . '*'), function ($path) {
            return preg_match("/\.(css)$/i", $path);
        });
        $files = array_merge($files, $base16);
        foreach ($files as $file) {
            $_tf = explode(DIRECTORY_SEPARATOR, $file);
            $file = array_pop($_tf);
            if (array_pop($_tf) === "base16") {
                $cssFiles['base16/' . $file] = str_replace('.css', '', $file);
            } else {
                $cssFiles[$file] = str_replace('.css', '', $file);
            }
        }
        asort($cssFiles);
        return $cssFiles;
    }

    /**
     * 列出所有模块
     *
     * @return array
     */
    public static function listModules(): array
    {
        $modulesInfo = [];

        // 设置Modules目录的路径
        $modulesDir = Util::pluginDir('Modules');

        // 获取Modules目录下的所有文件和子目录
        $files = scandir($modulesDir);

        // 遍历每个文件和子目录
        foreach ($files as $file) {
            // 忽略当前目录(.)和上级目录(..)
            if ($file == '.' || $file == '..') {
                continue;
            }

            $filePath = $modulesDir . DIRECTORY_SEPARATOR . $file;
            // 检查是否是子目录
            if (is_dir($filePath)) {
                $filePath = $filePath . DIRECTORY_SEPARATOR . "index.php";
            }
            if (!file_exists($filePath))
                continue;
            $info = Util::parseInfo($filePath);
            $info['file'] = $file;
            if ($info['editorStatic'] || $info['parseContent'] || $info['parseExcerpt']) {
                $modulesInfo[] = $info;
            }
        }
        return $modulesInfo;
    }

    /**
     * 获取插件文件的头信息
     *
     * @param string $pluginFile 插件文件路径
     * @return array
     */
    public static function parseInfo(string $pluginFile): array
    {
        $tokens = token_get_all(file_get_contents($pluginFile));
        $isDoc = false;
        $isFunction = false;
        $isClass = false;
        $isInClass = false;
        $isInFunction = false;
        $isDefined = false;
        $current = null;

        /** 初始信息 */
        $info = [
            'description' => '',
            'title' => '',
            'author' => '',
            'homepage' => '',
            'version' => '',
            'since' => '',
            'editorStatic' => false,
            'parseContent' => false,
            'parseExcerpt' => false
        ];

        $map = [
            'package' => 'title',
            'author' => 'author',
            'link' => 'homepage',
            'since' => 'since',
            'version' => 'version'
        ];

        foreach ($tokens as $token) {
            /** 获取doc comment */
            if (!$isDoc && is_array($token) && T_DOC_COMMENT == $token[0]) {

                /** 分行读取 */
                $described = false;
                $lines = preg_split("(\r|\n)", $token[1]);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!empty($line) && '*' == $line[0]) {
                        $line = trim(substr($line, 1));
                        if (!$described && !empty($line) && '@' == $line[0]) {
                            $described = true;
                        }

                        if (!$described && !empty($line)) {
                            $info['description'] .= $line . "\n";
                        } elseif ($described && !empty($line) && '@' == $line[0]) {
                            $info['description'] = trim($info['description']);
                            $line = trim(substr($line, 1));
                            $args = explode(' ', $line);
                            $key = array_shift($args);

                            if (isset($map[$key])) {
                                $info[$map[$key]] = trim(implode(' ', $args));
                            }
                        }
                    }
                }

                $isDoc = true;
            }

            if (is_array($token)) {
                switch ($token[0]) {
                    case T_FUNCTION:
                        $isFunction = true;
                        break;
                    case T_IMPLEMENTS:
                        $isClass = true;
                        break;
                    case T_WHITESPACE:
                    case T_COMMENT:
                    case T_DOC_COMMENT:
                        break;
                    case T_STRING:
                        $string = strtolower($token[1]);
                        switch ($string) {
                            case 'module':
                                $isInClass = $isClass;
                                break;
                            case 'editorstatic':
                                if ($isFunction) {
                                    $current = 'editorStatic';
                                }
                                break;
                            case 'parsecontent':
                                if ($isFunction) {
                                    $current = 'parseContent';
                                }
                                break;
                            case 'parseExcerpt':
                                if ($isFunction) {
                                    $current = 'parseExcerpt';
                                }
                                break;
                            default:
                                if (!empty($current) && $isInFunction && $isInClass) {
                                    $info[$current] = true;
                                }
                                break;
                        }
                        break;
                    default:
                        if (!empty($current) && $isInFunction && $isInClass) {
                            $info[$current] = true;
                        }
                        break;
                }
            } else {
                $token = strtolower($token);
                switch ($token) {
                    case '{':
                        if ($isDefined) {
                            $isInFunction = true;
                        }
                        break;
                    case '(':
                        if ($isFunction && !$isDefined) {
                            $isDefined = true;
                        }
                        break;
                    case '}':
                    case ';':
                        $isDefined = false;
                        $isFunction = false;
                        $isInFunction = false;
                        $current = null;
                        break;
                    default:
                        if (!empty($current) && $isInFunction && $isInClass) {
                            $info[$current] = true;
                        }
                        break;
                }
            }
        }

        return $info;
    }

    /**
     * 增加一个假工具条，给适配原生编辑的的插件增加插入点
     *
     * @param Contents\Post\Edit|Contents\Page\Edit $single
     * @return void
     */
    public static function fakeToolbar($single)
    {
        ?>
        <script>
            let p = document.querySelector('.url-slug').nextElementSibling;
            let toolbar = p.parentNode.insertBefore($C('div', {
                class: 'editor hidden',
                id: 'wmd-button-bar'
            }), p);

            toolbar.appendChild($C('div', {
                id: 'wmd-button-row',
                class: 'wmd-button-row'
            }));

            function $C(tag = 'div', attrs = {}, skipAttrs = []) {
                if (tag) {
                    let el = document.createElement(tag);
                    for (let a in attrs) {
                        if (skipAttrs.includes(a)) continue;
                        el.setAttribute(a, attrs[a]);
                    }
                    return el;
                }
            }
        </script>
    <?php }

    /**
     * 输出缩略图
     *
     * @param \Widget\Archive|\Widget\Base\Contents|null $archive 文章对象
     * @param int $quantity 图片数量
     * @param bool $return 是否返回
     * @param bool $parse 是否转换
     * @param string $template 转换模板
     * @return mixed
     */
    public static function thumbs($archive, int $quantity = 3, bool $return = false, bool $parse = false, string $template = /** @lang text */ '<img alt="" src="%s" />')
    {
        $thumbs = [];
        if (isset($archive)) {
            $fields = unserialize($archive->fields);

            // 首先使用自定义字段 thumb
            if (array_key_exists('thumb', $fields) && (!empty($fields['thumb'])) && $quantity > 0) {
                if (!in_array($fields['thumb'], $thumbs)) {
                    $fieldThumbs = explode("\n", $fields['thumb']);
                    foreach ($fieldThumbs as $thumb) {
                        if ($quantity > 0 && !empty(trim($thumb))) {
                            $thumbs[] = preg_replace('/\|\d+x\d+\s*$/i', '', $thumb);
                            $quantity -= 1;
                        }
                    }
                }
            }

            $content = $archive->markdown($archive->text);
            // 然后是正文匹配
            preg_match_all("/<img(?<images>[^>]*?)>/i", $content, $matches);
            foreach ($matches['images'] as $value) {
                if ($quantity <= 0) {
                    break;
                }
                $match = '';

                preg_match('/data-src="(?<src>.*?)"/i', $value, $dataSrcMatch);
                if (array_key_exists('src', $dataSrcMatch)) {
                    $match = $dataSrcMatch['src'];
                }

                if (empty($match)) {
                    preg_match('/src="(?<src>.*?)"/i', $value, $srcMatch);
                    if (array_key_exists('src', $srcMatch)) {
                        $match = $srcMatch['src'];
                    }
                }
                if (!empty($match)) {
                    // 2020.03.29 修正输出插件图标的BUG
                    if (strpos($match, __TYPECHO_PLUGIN_DIR__ . "/") !== false) {
                        continue;
                    }
                    if (strpos($match, "//") === false) {
                        continue;
                    }
                    if (strpos($match, "resources/images/expression") !== false) {
                        // 过滤表情
                        continue;
                    }
                    if (!in_array($match, $thumbs)) {
                        $thumbs[] = $match;
                        $quantity -= 1;
                    }
                }
            }

            // 接着是附件匹配
            /** @var Contents\Attachment\Related $attachments */
            Contents\Attachment\Related::allocWithAlias($archive->cid, 'parentId=' . $archive->cid)->to($attachments);
            while ($attachments->next()) {
                if ($quantity <= 0) {
                    break;
                }
                if (isset($attachments->isImage) && $attachments->isImage == 1) {
                    if (!in_array($attachments->url, $thumbs)) {
                        $thumbs[] = $attachments->url;
                        $quantity -= 1;
                    }
                }
            }
        }

        // 最后是随机
        while ($quantity-- > 0) {
            $thumbs[] = Util::getRandomImage();
        }

        // 转换
        if ($parse && (!empty($template))) {
            for ($i = 0; $i < count($thumbs); $i++) {
                $thumbs[$i] = str_replace("%s", $thumbs[$i], $template);
            }
        }

        // 输出或返回
        if ($return) {
            if (count($thumbs) == 1) {
                return $thumbs[0];
            }
            return $thumbs;
        } else {
            foreach ($thumbs as $thumb) {
                echo $thumb;
            }
            return true;
        }
    }

    /**
     * 获取随机图片
     * @return string
     */
    public static function getRandomImage(): string
    {
        return Util::pluginUrl('/assets/images/thumbs/' . mt_rand(1, 42) . '.jpg');
    }

    /**
     * 对象转 HTML
     *
     * @param mixed $widget
     * @param String $template
     * @return string
     */
    public static function toString($widget, string $template): string
    {
        return preg_replace_callback(
            "/\{([_a-z0-9]+)\}/i",
            function ($matches) use ($widget) {
                return $widget->{$matches[1]};
            },
            $template
        );
    }

    /**
     * 回复可见区块处理
     *
     * @throws Db\Exception
     * @throws \Typecho\Widget\Exception
     */
    public static function hideCallback($text, $archive): string
    {
        $user = User::alloc();
        $db = Db::get();
        $mail = $user->hasLogin() ? $user->mail : $archive->remember('mail', true);
        $select = $db->select()->from('table.comments')
            ->where('cid = ?', $archive->cid)
            ->where('mail = ?', $mail)
            ->where('status = ?', 'approved')
            ->limit(1);

        $result = $db->fetchAll($select);
        if ($user->pass('administrator', true) || $result) {
            return '<div class="x-hide already-shown">' . $text . '</div>';
        } else {
            return sprintf('<div class="x-hide already-hide">%s</div>', '此处内容已隐藏，<a href="#comments">回复后(需要填写邮箱)</a>可见');
        }
    }
}
