<?php

use TypechoPlugin\AAEditor\Module;
use TypechoPlugin\AAEditor\Util;

/**
 * 按钮自定义类型，图标，圆角
 *
 * @package 多彩按钮
 * @author Ryan
 * @version 0.0.1
 * @link https://doufu.ru
 *
 */
class ModuleButton implements Module
{

    public static function editorStatic(): void
    {
        ?>
        <script>
            $('body').trigger('XEditorAddButton', [{
                id: 'wmd-button-button',
                name: '<?php _e("多彩按钮"); ?>',
                icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path fill="none" d="M0 0h24v24H0z"></path><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm0-3a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"></path></svg>',
                insertBefore: '#wmd-spacer4',
                command() {
                    this.openModal({
                        title: '<?php _e("插入多彩按钮"); ?>',
                        innerHTML: `<div class="form-item">
<label for="type"><?php _e("按钮类型"); ?></label>
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
    <label for="icon"><a href="https://fontawesome.dashgame.com/" target="_blank" title="<?php _e("点此查找图标Class"); ?>"><?php _e("按钮图标"); ?></a></label>
    <input type="text" name="icon" />
</div>
<div class="form-item"><label for="href" class="required"><?php _e("按钮链接"); ?></label>
    <input type="text" required="required" name="href" />
</div>
<div class="form-item">
    <label for="content"><?php _e("按钮文字"); ?></label>
    <input type="text" name="content" />
</div>
<div class="form-item">
    <label for="radius"><?php _e("按钮圆角"); ?></label>
    <input type="number" name="radius" min="0" max="16" step="1" />
</div>`,
                        confirm(modal) {
                            let type = $('[name="type"]', modal).val(),
                                href = $('[name="href"]', modal).val(),
                                content = $('[name="content"]', modal).val() || "<?php _e("点此查看"); ?>",
                                icon = $('[name="icon"]', modal).val(),
                                cornerRadius = parseInt($('[name="radius"]', modal).val()) || 0;

                            // Ensure cornerRadius does not exceed the maximum value of 16
                            if (cornerRadius > 16) {
                                cornerRadius = 16;
                            }
                            if (cornerRadius < 0) {
                                cornerRadius = 0;
                            }
                            this.replaceSelection(`[x-btn type="${type}" href="${href}" content="${content}"${icon ? ' icon="' + icon + '"' : ""}${cornerRadius ? ' radius="' + cornerRadius + '"' : ""}/]`);
                            return true;
                        }
                    });
                }
            }]).trigger('XEditorAddHtmlProcessor', [
                function (html) {
                    if (html.indexOf("[x-btn")) {
                        html = html.replace(this.getShortCodeRegex("x-btn"), `<div class="x-btn-wrapper"><x-btn$3>$5</x-link></div>`);
                    }
                    if (html.indexOf("[btn")) {
                        html = html.replace(this.getShortCodeRegex("btn"), `<div class="x-btn-wrapper"><x-btn$3>$5</x-link></div>`);
                    }
                    return html;
                }
            ]);
        </script>
        <script>
            customElements.define(
                'x-btn',
                class xBtn extends HTMLElement {
                    constructor() {
                        super();
                        this.options = {
                            icon: this.getAttribute('icon'),
                            href: this.getAttribute('href') || '#',
                            type: /^primary$|^secondary$|^success$|^danger$|^warning$|^info$|^light$|^dark$|^weibo$|^weixin$|^alipay$|^youku$|^toutiao$|^youtube$|^twitter$|^facebook$|^bilibili$|^ins$|^tumblr$/.test(this.getAttribute('type')) ? this.getAttribute('type') : 'primary',
                            content: this.getAttribute('content') || "",
                            radius: this.getAttribute('radius')
                        };
                        if (this.options.href) {
                            let iconHTML = this.options.icon ?
                                `<span class="x-btn-icon"><i class="fa ${this.options.icon === "{icon}" ? "fa-link" : this.options.icon}"></i></span>` : '';
                            let html = `
				    <a class="x-btn x-btn-${this.options.type}" href="${this.options.href === "" || this.options.href === "{href}" ? "https://doufu.ru" : this.options.href}" target="_blank" rel="noopener noreferrer nofollow" style="${this.options.radius ? "border-radius:" + this.options.radius + "px" : ""}">
					    ${iconHTML}<span class="x-btn-content">${(this.options.content === "" || this.options.content === "{content}") ? '<?php _e("点此查看"); ?>' : this.options.content}</span>
				    </a>
            `;
                            if (this.parentNode?.classList?.contains('x-btn-wrapper')) {
                                this.parentNode.outerHTML = html;
                            } else {
                                this.outerHTML = html;
                            }
                        }
                    }

                }
            );
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
              href="<?php echo Util::moduleUrl('Button', 'index.css'); ?>">
        <?php
    }

    public static function parseContent($text, $archive): string
    {
        $pattern = Util::get_shortcode_regex(['x-btn', 'btn']);
        return preg_replace_callback(/**
         * @throws \Typecho\Exception
         */ "/$pattern/", function ($m) {
            return self::btnCallback($m);
        }, $text);
    }

    /**
     * 引用文章回调
     * @param array $m 匹配内容
     * @return string
     */
    public static function btnCallback(array $m): string
    {
        // Allow [[foo]] syntax for escaping a tag.
        if ('[' === $m[1] && ']' === $m[6]) {
            return substr($m[0], 1, -1);
        }
        $attr = htmlspecialchars_decode($m[3]);
        $attrs = Util::shortcode_parse_atts($attr);

        $icon = trim($attrs['icon'] ?? '');
        if (strpos($icon, 'fa-') !== false) {
            $icon = sprintf('<span class="x-btn-icon"><i class="fa %s"></i></span>', $icon);
        }
        $href = trim($attrs['href'] ?? '#');
        $radius = intval(trim($attrs['radius'] ?? '0'));
        if ($radius < 0) {
            $radius = 0;
        }
        // 圆角半径超过15就显示为药丸按钮
        if ($radius > 15) {
            $radius = 9999;
        }
        $radiusHTML = $radius ? "border-radius: {$radius}px;" : '';
        $content = $attrs['content'] ?? _t("点此查看");
        $attributeType = $attrs['type'] ?? 'primary';
        if (preg_match('/^(primary|secondary|success|danger|warning|info|light|dark|weibo|weixin|alipay|youku|toutiao|youtube|twitter|facebook|bilibili|ins|tumblr)$/', $attributeType)) {
            $type = $attributeType;
        } else {
            $type = 'primary';
        }
        return sprintf('<a class="reset x-btn x-btn-%s" href="%s" target="_blank" rel="noopener noreferrer nofollow" style="%s">%s<span class="x-btn-content">%s</span></a>'
            , $type, $href, $radiusHTML, $icon, $content);
    }

    public static function parseExcerpt($text, $archive): string
    {
        $pattern = Util::get_shortcode_regex(['x-btn']);
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
        if (array_key_exists('href', $attrs)) {
            return $attrs['href'];
        }
        return '';
    }
}
