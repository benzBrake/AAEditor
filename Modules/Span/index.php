<?php

use TypechoPlugin\AAEditor\Module;
use TypechoPlugin\AAEditor\Util;

/**
 * 设置文本颜色，背景色
 *
 * @package 多彩文本
 * @author Ryan
 * @version 0.0.1
 * @link https://doufu.ru
 *
 */
class ModuleSpan implements Module
{

    public static function editorStatic(): void
    {
        ?>
        <script>
            function generateAttrs(modal) {
                let type = $('select[name="type"]', modal).val(),
                    color = $('input[name="color"]', modal).val(),
                    background = $('input[name="background-color"]', modal).val(),
                    padding = $('input[name="padding"]', modal).val(),
                    styles = [];
                if (type === 'custom') {
                    if (color && color !== "#60a5fa") {
                        styles.push("--color:" + color);
                    }
                    if (background && background !== "#ffffff") {
                        styles.push("--background:" + background);
                    }
                    if (padding && padding !== "0") {
                        styles.push("--padding:" + padding + 'px');
                    }
                    return (styles.length > 0 ? 'style="' + styles.join(';') + '"' : '');
                } else {
                    return 'type="' + type + '"';
                }
            }

            $('body').trigger('XEditorAddButton', [{
                id: 'wmd-span-button',
                name: '<?php _e("多彩文字"); ?>',
                icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="20" height="20"><path d="m16,2.6875c-7.34095,0 -13.3125,5.97155 -13.3125,13.3125c0,7.34095 5.97155,13.3125 13.3125,13.3125c7.34095,0 13.3125,-5.97155 13.3125,-13.3125c0,-7.34095 -5.97155,-13.3125 -13.3125,-13.3125zm0,2.21875c6.14057,0 11.09375,4.95318 11.09375,11.09375c0,6.14057 -4.95318,11.09375 -11.09375,11.09375c-6.14057,0 -11.09375,-4.95318 -11.09375,-11.09375c0,-6.14057 4.95318,-11.09375 11.09375,-11.09375zm-0.83203,5.54688l-0.27734,0.72803l-3.25879,8.875l-0.06934,0.17334l0,1.31738l2.21875,0l0,-0.90137l0.48535,-1.31738l3.4668,0l0.48535,1.31738l0,0.90137l2.21875,0l0,-1.31738l-0.06934,-0.17334l-3.25879,-8.875l-0.27734,-0.72803l-1.66406,0zm0.83203,4.12549l0.93604,2.53076l-1.87207,0l0.93604,-2.53076z"></path></svg>',
                insertAfter: '#wmd-spacer3',
                command() {
                    const {textarea} = this;
                    let lastSelection = textarea.getSelection();
                    let selectedText = textarea.getSelectedText();
                    this.openModal({
                        title: '<?php _e("多彩文字")  ?>',
                        innerHTML: `<div class="form-item">
    <label for="type"><?php _e("文字样式") ?></label>
    <select name="type">
        <option value="primary">primary</option>
        <option value="secondary">secondary</option>
        <option value="light">light</option>
        <option value="dark">dark</option>
        <option value="info">info</option>
        <option value="success">success</option>
        <option value="warning">warning</option>
        <option value="danger">danger</option>
        <option value="custom"><?php _e("自定义") ?></option>
    </select>
</div>
<div id="custom-colors" class="form-item hidden">
<div class="columns-2 full-width">
<div class="column flex">
    <label for="color"><?php _e("文字颜色"); ?></label>
    <input type="color" name="color" value="#60a5fa" />
</div>
<div class="column flex">
    <label for="background-color"><?php _e("背景颜色"); ?></label>
    <input type="color" name="background-color" value="#ffffff" />
</div>
</div>
</div>
<div class="form-item">
    <label for="text"><?php _e("文字边距"); ?></label>
    <input type="number" min="0" max="5" value="0" name="padding" />
</div>
<div class="form-item">
    <label for="text"><?php _e("多彩文字"); ?></label>
    <input type="text" name="text" />
</div>
`,
                        handle(modal) {
                            if (selectedText) {
                                const regex = /\[\/*x-span[^\]]*]/gm;
                                $('input[name="text"]', modal).val(selectedText.replace(regex, ''));
                                refreshPreview();
                            }
                            $('.aa-modal-body', modal).append($('<div class="preview-area" style="text-align: center">'));
                            $('select[name="type"]', modal).on('change', function () {
                                let type = $(this).val();
                                if (type === "custom") {
                                    $("#custom-colors").removeClass("hidden");
                                } else {
                                    $("#custom-colors").addClass("hidden");
                                }
                                refreshPreview();
                            });
                            $('input').on('change input', refreshPreview);

                            function refreshPreview() {
                                $('.preview-area', modal).html(toSpanHTML($('input[name="text"]', modal).val()));
                            }

                            function toSpanHTML(text) {
                                return '<span class="x-span" ' + generateAttrs(modal) + '>' + text + '</span>';
                            }
                        },
                        confirm(modal) {
                            let val = $('input[name="text"]', modal).val();
                            let text = '[x-span ' + generateAttrs(modal) + ']' +
                                 val + '[/x-span]';
                            this.textarea.setSelection(lastSelection.start, lastSelection.end);
                            this.textarea.executeAndAddUndoStack('replaceSelectionText', text);
                            this.textarea.setSelection(lastSelection.start + text.length - 9 - val.length, lastSelection.start + text.length - 9);
                            return true;
                        }
                    })
                },
            }]).trigger('XEditorAddHtmlProcessor', [
                function (html) {
                    if (html.indexOf("[x-span")) {
                        html = html.replace(this.getShortCodeRegex("x-span"), function (...m) {
                            // m[3] 正则 class="xxx";
                            const regex = /class="[^"]*text-([^ "]+)"/gm;
                            let m1 = regex.exec(m[3]);
                            if (Array.isArray(m1) && m1) {
                                let type = "primary";
                                if (/^(primary|secondary|success|danger|warning|info|light|dark)/.test(m1[1])) {
                                    type = m1[1];
                                }
                                return `<span class="x-span" type="${type}"${m[3].replace(regex, "")}>${m[5]}</span>`;
                            }
                            return '<span class="x-span"' + m[3] + '>' + m[5] + '</span>'
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
              href="<?php echo Util::moduleUrl('Span', 'index.css'); ?>">
        <?php
    }

    public static function parseContent($text, $archive): string
    {
        if (strpos($text, '[x-span') === false) { //提高效率，避免每篇文章都要解析
            return $text;
        }
        $pattern = Util::get_shortcode_regex(['x-span']);
        return preg_replace_callback("/$pattern/", function ($m) {
            return self::spanCallback($m);
        }, $text);
    }

    public static function parseExcerpt($text, $archive): string
    {
        if (strpos($text, '[x-span') === false) { //提高效率，避免每篇文章都要解析
            return $text;
        }
        $pattern = Util::get_shortcode_regex(['x-span']);
        return preg_replace_callback("/$pattern/", function ($m) {
            return self::spanCallback($m);
        }, $text);
    }

    public static function spanCallback($m): string
    {
        // Allow [[foo]] syntax for escaping a tag.
        if ('[' === $m[1] && ']' === $m[6]) {
            return substr($m[0], 1, -1);
        }
        $attrs = Util::shortcode_parse_atts(htmlspecialchars_decode($m[3]));
        if (array_key_exists('class', $attrs)) {
            $attrs['class'] .= ' x-span';
        } else {
            $attrs['class'] = 'x-span';
        }
        $attrs_text = '';
        foreach ($attrs as $key => $value) {
            $attrs_text .= ' ' . $key . '="' . $value . '"';
        }
        return '<span' . $attrs_text . '>' . $m[5] . '</span>';
    }
}
