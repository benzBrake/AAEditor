# AAEditor

## 功能自定义

### 自定义按钮/自定义短代码渲染

1.比如你想增加一个 QQ 卡片的功能

2.进入`Modules`目录，随便复制一份可以用的目录，并改名为`QQ`

3.修改`Modules\QQ\index.php`，这个文件前边的格式类似下面这样的，我这个是`Tabs`模块的范例，你要把备注信息修改一下，然后把类名`ModulesTab`改为`ModuleQQ`

```php
<?php

/** 包引用 */
use Typecho\Config;
use TypechoPlugin\AAEditor\Module;
use TypechoPlugin\AAEditor\Util;
use Utils\Helper;

/**
 * 以多标签形式排版正文
 *
 * @package 多标签卡片
 * @author Ryan
 * @version 0.0.1
 * @link https://doufu.ru
 *
 */
class ModuleTabs implements Module
```

这个文件里有4个必须实现的函数

```php
    /**
     * 编辑器页面插入静态资源
     * @return void
     */
    public static function editorStatic(): void;

    /**
     * 前台插入静态资源
     * @param {Widget_Archive} $archive 页面对象
     * @return mixed
     */
    public static function archiveStatic($archive): void;

    /**
     * 正文内容处理
     *
     * @param {string} $text 处理前的 html
     * @return string 处理后的 html
     */
    public static function parseContent($text, $archive): string;

    /**
     * 摘要内容处理
     *
     * @param {string} $text 处理前的 html
     * @return string 处理后的 html
     */
    public static function parseExcerpt($text, $archive): string;
```

### editorStatic

这是编辑器初始化以后执行的脚本，一般用于添加按钮，增加编辑实时渲染代码。

```php+HTML
public static function editorStatic(): void {
?>
<script>
// 增加按钮，触发 XEditorAddButton 事件可以用于增加按钮，该事件只接受一个参数，该参数指定按钮配置
$('body').trigger('XEditorAddButton', [{
    id: 'wmd-tabs-button', // 按钮 id
    name: '<?php _e("多标签"); ?>', // 鼠标在按钮上悬浮时显示的文字
    icon: '<svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="20" height="20"><path d="M202.667 149.333c-52.651 0-96 43.35-96 96v533.334c0 52.65 43.349 96 96 96h618.666c52.651 0 96-43.35 96-96V245.333c0-52.65-43.349-96-96-96h-448a32 32 0 0 0-5.504.427c-2.816-.213-5.653-.427-8.49-.427H202.667zm0 64h156.672a53.32 53.32 0 0 1 37.696 15.616l102.997 103.019a32 32 0 0 0 22.635 9.365h298.666c18.048 0 32 13.952 32 32v405.334c0 18.048-13.952 32-32 32H202.667c-18.048 0-32-13.952-32-32V245.333c0-18.048 13.952-32 32-32zm269.248 0h349.418c18.048 0 32 13.952 32 32v37.974a94.016 94.016 0 0 0-32-5.974H535.915l-64-64z"></path></svg>', // 图标，推荐 SVG，建议设定 width 和 height 防止 css 未加载是图标过大
    insertBefore: '#wmd-spacer4', // 插入到哪个 HTMLElement 前边
    shortcut: 'ctrl+alt+t', // 快捷键，如果不需要就别用这项
    command({ target }) {
        // 点击按钮或按下快捷键运行的函数，target 是按钮 DOM
        // 这里的 this 绑定的是 assets/src/main.js 定义的 XEditor 的实例，具体函数请查看 XEditor，常用的有 openModal, insertText, replaceSelection, getSelectedText, wrapText
        // this.insertText(123); // 往光标处插入文本 123
        // this.replaceSelection('哈哈哈') // 替换选中文本为哈哈哈
        // this.getSelectedText() // 获取选中文本
        // 新版 XEditor 针对 textarea 写了一个工具类，可以通过 this.textarea 获取改工具类，具体有什么函数可以查看 assets/src/utils/textarea.js
        this.openModal({
            title: '<?php _e("插入多标签卡片"); ?>',
            innerHTML: `<x-custom-tabs></x-custom-tabs>`,
            checkEmptyOnConfirm: false,
            confirm(modal) {
                // 这里可以通过 this 拿到 XEditor实例，modal 是模态框 HTMLELement
                // confirm 是点击确定后运行的函数，需要返回 true 关闭模态框，返回 false 保持模态框
                let items = [];
                $(modal).find('.x-custom-tabs-nav-item').each(function () {
                    let tab = $(this);
                    let tabId = tab.attr('data-id'),
                        content = $('.x-custom-tabs-content-item[data-id="' + tabId + '"]', modal);
                    if (content) {
                        let tabTitle = $('input[name="title"]', content),
                            tabContent = $('textarea[name="content"]', content),
                            isDefault = $('input[name="is-default"]', content)[0].checked;
                        items.push(`[tab name="${tabTitle.val()}"${isDefault ? ' active="true"' : ''}]${tabContent.val()}[/tab]`);
                    }
                });
                if (items.length) {
                    this.replaceSelection((this.textarea.isAtLineStart() ? '' : '\n') + '[tabs]\n' + items.join('\n') + '\n[/tabs]' + (this.textarea.isAtLineEnd() ? '' : '\n'));
                }
                return true;
            },
            handle(modal) {
                // 创建模态框后会运行的函数
                $(modal).find('.aa-modal-frame').css({
                    'width': '100%',
                    'max-width': '640px'
                });
            }
        });
    }
}])
</script>
<script>
// 触发 XEditorAddHtmlProcessor 事件，增加实时渲染处理器，该事件只接受一个函数参数
$('body').trigger('XEditorAddHtmlProcessor', [
    function (html) {
        // 只有一个参数，html 是上个渲染函数处理后的 html 文本，如果无需处理
        // this 是 assets/src/previewUtils.js的实例
        // this.getShortCodeRegex 用户获取短代码的正则表达式
        if (html.indexOf("[tabs")) {
            html = html.replace(this.getShortCodeRegex("tabs"), `<div class="x-tabs-wrapper"><x-tabs$3>$5</x-tabs></div>`);
        }
        if (html.indexOf("[x-tabs")) {
            html = html.replace(this.getShortCodeRegex("x-tabs"), `<div class="x-tabs-wrapper"><x-tabs$3>$5</x-tabs></div>`);
        }
        if (html.indexOf("[tab")) {
            html = html.replace(this.getShortCodeRegex("tab"), `<div class="x-tab"$3>$5</div>`);
        }
        if (html.indexOf("[x-tab")) {
            return html.replace(this.getShortCodeRegex("x-tab"), `<div class="x-tab"$3>$5</div>`);
        }
    }
]);
</script>
<script>
	// 你还可以增加渲染后处理代码，编辑器渲染完成后触发 XEditorPreviewEnd 事件
	$('body').on('XEditorPreviewEnd', function() {
        
    });
</script>
<?php 
}
```

这里也许会需要插入模块目录里的 css/js，相对路径请自行修改

```php+HTML
<link rel="stylesheet" href="<?php echo TypechoPlugin\AAEditor\Util::pluginUrl('Modules/Tabs/index.css'); ?>">
```



### archiveStatic

这是前台页面底部增加静态资源用的

### parseContent

正文处理，如果不需要处理，直接返回`$text`即可

```php
public static function parseContent($text, $archive): string {
    return $text;
}
```

如果需要处理短代码

```php
public static function parseContent($text, $archive): string {
    /*
     * 获取短代码正则表达式
     * 下面获取的表达式能匹配[tabs active='1']xxx[/tabs]和[x-tabs active='1']xxx[/x-tabs]这两种，get_shortcode_regex需要的参数是数组
     */
    $pattern = TypechoPlugin\AAEditor\Util::get_shortcode_regex(['tabs', 'x-tabs']); 
    /*
     * $1 $2 $3 $4 $5，对于[tabs active='1']xxx[/tabs]，$3对应 active='1' 这部分，$5 对应于 xxx 这部分
     */
    return preg_replace("/$patter/", '<x-tabs$3>$5</x-tabs>', $html);
}
```

### parseExcerpt

摘要处理，同上边一样的

## 编译JS
1.需要 nodejs 环境
2.在插件目录运行 CMD
```
npm install # 安装依赖
npm run prod # 编译
```
3.如果需要实时编译
```
npm run watch
```


## 授权

[Mozilla Public License Version 2.0](LICENSE)

学习可以，禁止直接改名商用！！！
