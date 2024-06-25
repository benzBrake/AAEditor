<?php

use TypechoPlugin\AAEditor\Module;
use TypechoPlugin\AAEditor\Util;

/**
 * 仿 Bootstrap 的高亮引用框
 *
 * @package 高亮引用
 * @author Ryan
 * @version 0.0.1
 * @link https://doufu.ru
 *
 */
class ModuleScode implements Module
{

    public static function editorStatic(): void
    {
        ?>
        <script>
            $('body').trigger('XEditorAddButton', [{
                id: 'wmd-scode-button',
                name: '<?php _e("高亮引用"); ?>',
                icon: '<svg class="icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="20" height="20"><path d="M193.024 795.99c0 17.692 14.222 32.028 31.972 32.028h574.008a31.972 31.972 0 0 0 31.972-32.029V562.972a318.976 318.976 0 1 0-637.952 0V795.99zm71.964-233.018a247.012 247.012 0 0 1 494.024 0v193.024H404.025V584.988a10.012 10.012 0 0 0-10.013-10.012H349.98a10.012 10.012 0 0 0-9.955 10.012v171.008h-75.037V562.972zM216.918 310.5l39.594-39.595a8.021 8.021 0 0 0 0-11.321l-67.925-67.868a8.021 8.021 0 0 0-11.264 0l-39.595 39.594a8.021 8.021 0 0 0 0 11.264l67.868 67.926a8.021 8.021 0 0 0 11.321 0zM886.5 231.31l-39.595-39.594a8.021 8.021 0 0 0-11.321 0l-67.868 67.868a8.021 8.021 0 0 0 0 11.32L807.31 310.5a8.021 8.021 0 0 0 11.264 0l67.926-67.926a8.021 8.021 0 0 0 0-11.264zM832 892.018H192a31.972 31.972 0 0 0-32.028 31.971v24.007c0 4.38 3.64 7.965 8.02 7.965h688.015c4.38 0 7.965-3.584 7.965-7.965V923.99A31.972 31.972 0 0 0 832 892.018zM484.01 179.996h55.98a7.951 7.951 0 0 0 7.964-7.964V75.947a8.021 8.021 0 0 0-7.965-7.965h-55.978a8.021 8.021 0 0 0-7.965 7.965v95.971c0 4.438 3.527 8.022 7.965 8.022z"></path></svg>',
                insertAfter: '#wmd-spacer3',
                command() {
                    const {textarea} = this;
                    let lastSelection = textarea.getSelection();
                    let selectedText = textarea.getSelectedText();
                    this.openModal({
                        title: '<?php _e("插入高亮引用"); ?>',
                        innerHTML: `<div class="form-item">
    <label for="type"><?php _e("高亮类型"); ?></label>
    <select name="type">
        <option value="primary">primary</option>
        <option value="secondary">secondary</option>
        <option value="light">light</option>
        <option value="dark">dark</option>
        <option value="info">info</option>
        <option value="success">success</option>
        <option value="warning">warning</option>
        <option value="danger">danger</option>
    </select>
</div>
<div class="form-item">
    <label for="size"><?php _e("区块尺寸"); ?></label>
    <select name="size">
        <option value=""><?php _e("默认"); ?></option>
        <option value="small"><?php _e("迷你"); ?></option>
    </select>
</div>
<div class="form-item">
    <label for="content"><?php _e("提示内容"); ?></label>
    <textarea rows="3" autocomplete="off" name="content" placeholder="<?php _e("请输入提示内容"); ?>">${selectedText}</textarea>
</div>`,
                        confirm(modal) {
                            let type = $('[name="type"]', modal).val(),
                                size = $('[name="size"]', modal).val(),
                                content = $('[name="content"]', modal).val() || "<?php _e("这里编辑高亮内容"); ?>";
                            let prefix = `[x-alert type="${type}"${size ? ' size="' + size + '"' : ""}]`;
                            let postfix = `[/x-alert]`;
                            this.textarea.setSelection(lastSelection.start, lastSelection.end);
                            textarea.executeAndAddUndoStack('replaceSelectionText', prefix + content + postfix);
                            if (selectedText)
                                this.textarea.setSelection(lastSelection.start + prefix.length, lastSelection.start + prefix.length + content.length);
                            return true;
                        }
                    });
                }
            }]).trigger('XEditorAddHtmlProcessor', [
                function (html) {
                    if (html.indexOf("[scode")) {
                        html = html.replace(this.getShortCodeRegex("scode"), `<div class="x-alert"$3><span class="x-alert-icon"></span><div class="x-alert-content">$5</div></div>`);
                    }
                    if (html.indexOf("[alert")) {
                        html = html.replace(this.getShortCodeRegex("alert"), `<div class="x-alert"$3><span class="x-alert-icon"></span><div class="x-alert-content">$5</div></div>`);
                    }
                    if (html.indexOf("[x-alert")) {
                        html = html.replace(this.getShortCodeRegex("x-alert"), `<div class="x-alert"$3><span class="x-alert-icon"></span><div class="x-alert-content">$5</div></div>`);
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
              href="<?php echo Util::moduleUrl('Scode', 'index.css'); ?>">
        <?php
    }

    public static function parseContent($text, $archive): string
    {
        if (strpos($text, '[scode') === false && strpos($text, '[alert') === false && strpos($text, '[x-alert') === false) { //提高效率，避免每篇文章都要解析
            return $text;
        }
        $pattern = Util::get_shortcode_regex(['scode', 'alert', 'x-alert']);
        return preg_replace("/$pattern/", '<div class="x-alert"$3><span class="x-alert-icon"></span><div class="x-alert-content">$5</div></div>', $text);
    }


    public static function parseExcerpt($text, $archive): string
    {
        if (strpos($text, '[scode') === false && strpos($text, '[alert') === false && strpos($text, '[x-alert') === false) { //提高效率，避免每篇文章都要解析
            return $text;
        }
        $pattern = Util::get_shortcode_regex(['scode', 'alert', 'x-alert']);
        return preg_replace("/$pattern/", '$5', $text);
    }
}
