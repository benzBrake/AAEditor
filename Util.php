<?php

namespace TypechoPlugin\AAEditor;

use ReflectionClass;
use Typecho\Common;
use Typecho\Db;
use Typecho\Plugin;
use Typecho\Plugin\Exception;
use Utils\Helper;
use Widget\Contents;
use Widget\User;

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

class Util
{
    static $manifest;
    private static $contentParsers;
    private static $excerptParsers;
    private static $archiveStatics;

    /**
     * 激活插件
     * @return string
     */
    public static function activate(): string
    {
        // 添加公共内容
        Plugin::factory('Widget_Archive')->footer = [__CLASS__, 'archiveFooter'];

        // 添加文章编辑选项
        Plugin::factory('admin/write-post.php')->richEditor = [__CLASS__, 'richEditor'];
        Plugin::factory('admin/write-page.php')->richEditor = [__CLASS__, 'richEditor'];
        Plugin::factory('admin/write-post.php')->option = [__CLASS__, 'fakeToolbar'];
        Plugin::factory('admin/write-page.php')->option = [__CLASS__, 'fakeToolbar'];
        Plugin::factory('admin/write-post.php')->bottom = [__CLASS__, 'editorFooter'];
        Plugin::factory('admin/write-page.php')->bottom = [__CLASS__, 'editorFooter'];

        // 短代码
        Plugin::factory('admin/common.php')->begin = [__CLASS__, 'shortCodeInit'];
        Plugin::factory('Widget_Archive')->handleInit = [__CLASS__, 'shortCodeInit'];

        // 内容替换处理
        Plugin::factory('Widget_Abstract_Contents')->contentEx = [__CLASS__, 'contentEx'];
        Plugin::factory('Widget_Abstract_Contents')->excerptEx = [__CLASS__, 'excerptEx'];
        return _t('插件已启用，请进入插件设置启用你需要的模块！');
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
            // 引入字体图标
            ?>
            <link rel="stylesheet"
                  href="<?php echo Util::pluginStatic('css', 'content.css'); ?>">
            <?php
        }
        if (Util::pluginOption('XLoadFontAwesome', 'on') === 'on') {
            // 引入字体图标
            ?>
            <link rel="stylesheet"
                  href="<?php echo Util::parseJSD('https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css'); ?>">
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
                    MathJax.typesetPromise();
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
                src="<?php echo Util::parseJSD('https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.8.0/build/highlight.min.js') ?>"></script>
            <link rel="stylesheet"
                  href="<?php echo Common::url(preg_replace("/(?<!\.min)\.css$/", ".min.css", $filename), Util::parseJSD('https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.8.0/build/styles/')) ?>">
            <script>
                document.querySelectorAll("pre code").forEach(el => {
                    hljs.highlightElement(el);
                })
            </script>
            <?php
        }
        $enableParse = $archive->fields->EnableShortCodeParse;
        if (Util::pluginOption('XShortCodeParse', 'on') === 'on' || $archive->is("single") && isset($enableParse)) {
            foreach (Util::$archiveStatics as $method) {
                call_user_func($method['parser'], $archive);
            }
        }
    }

    /**
     * 编辑页面附加 CSS JS
     * @return void
     */
    public static function editorFooter()
    {

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
                src="<?php echo Util::parseJSD('https://cdn.jsdelivr.net/npm/html-to-md@0.8.3/dist/index.min.js'); ?>"></script>
            <link rel="stylesheet" type="text/css"
                  href="<?php echo Util::parseJSD('https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css'); ?>">
            <script type="text/javascript"
                    src="<?php echo Util::parseJSD('https://cdn.jsdelivr.net/npm/toastify-js'); ?>"></script>
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
                                let block = blocks.shift();
                                return block;
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
                        resizeBtn.on('mousedown', (e) => {
                            document.addEventListener('mousemove', mouseMove);
                            document.addEventListener('mouseup', mouseUp);
                            btnPress = true;
                            previewArea.css("opacity", 0.25);

                            function mouseMove(e) {
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
                        MathJax.typesetPromise();
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
                Util::$contentParsers[] = [
                    'priority' => ($className::$priority ?? 99),
                    'parser' => $className . '::parseContent'
                ];
            }
            if (method_exists($className, 'parseExcerpt')) {
                Util::$excerptParsers[] = [
                    'priority' => ($className::$priority ?? 99),
                    'parser' => $className . '::parseExcerpt'
                ];
            }
            if (method_exists($className, 'archiveStatic')) {
                Util::$archiveStatics[] = [
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
                "[x]" => '【已经成】',
                "[ ]" => '【未经成】'
            ));
        }

        foreach (Util::$excerptParsers as $parserItem) {
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
            } catch (Exception $e) {
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
        }
        return self::pluginUrl('assets/dist/' . $uri);
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
     * @throws Exception
     */
    public static function parseJSD($url): string
    {
        return preg_replace("/((https?:)?)\/\/cdn\.jsdelivr\.net/", self::pluginOption('XJsdelivrMirror', 'https://jsd.onmicrosoft.cn'), $url);
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
    public static function reflectGetValue($object, $name)
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
    public static function thumbs($archive, int $quantity = 3, bool $return = false, bool $parse = false, string $template = '<img alt="" src="%s" />')
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
                            $thumbs[] = $thumb;
                            $quantity -= 1;
                        }
                    }
                }
            }

            // 然后是正文匹配
            preg_match_all("/<img(?<images>[^>]*?)>/i", $archive->content, $matches);
            $text = implode("\n", $matches["images"]);
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
