<?php

use TypechoPlugin\AAEditor\Module;
use TypechoPlugin\AAEditor\Util;

/**
 * 插入 Github 仓库/用户卡片
 *
 * @package Github
 * @author Ryan
 * @version 0.0.1
 * @link https://doufu.ru
 *
 */
class ModuleGithubCard implements Module
{

    public static function editorStatic(): void
    {
        ?>
        <script>
            $('body').trigger('XEditorAddButton', [{
                id: 'wmd-github-card-button',
                name: '<?php _e("插入 Github 卡片"); ?>',
                icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M12.001 2C6.47598 2 2.00098 6.475 2.00098 12C2.00098 16.425 4.86348 20.1625 8.83848 21.4875C9.33848 21.575 9.52598 21.275 9.52598 21.0125C9.52598 20.775 9.51348 19.9875 9.51348 19.15C7.00098 19.6125 6.35098 18.5375 6.15098 17.975C6.03848 17.6875 5.55098 16.8 5.12598 16.5625C4.77598 16.375 4.27598 15.9125 5.11348 15.9C5.90098 15.8875 6.46348 16.625 6.65098 16.925C7.55098 18.4375 8.98848 18.0125 9.56348 17.75C9.65098 17.1 9.91348 16.6625 10.201 16.4125C7.97598 16.1625 5.65098 15.3 5.65098 11.475C5.65098 10.3875 6.03848 9.4875 6.67598 8.7875C6.57598 8.5375 6.22598 7.5125 6.77598 6.1375C6.77598 6.1375 7.61348 5.875 9.52598 7.1625C10.326 6.9375 11.176 6.825 12.026 6.825C12.876 6.825 13.726 6.9375 14.526 7.1625C16.4385 5.8625 17.276 6.1375 17.276 6.1375C17.826 7.5125 17.476 8.5375 17.376 8.7875C18.0135 9.4875 18.401 10.375 18.401 11.475C18.401 15.3125 16.0635 16.1625 13.8385 16.4125C14.201 16.725 14.5135 17.325 14.5135 18.2625C14.5135 19.6 14.501 20.675 14.501 21.0125C14.501 21.275 14.6885 21.5875 15.1885 21.4875C19.259 20.1133 21.9999 16.2963 22.001 12C22.001 6.475 17.526 2 12.001 2Z"></path></svg>',
                insertBefore: '#wmd-spacer4',
                command() {
                    this.openModal({
                        title: '<?php _e("Github 卡片"); ?>',
                        innerHTML: `<div class="form-item">
    <label class="required" for="url"><?php _e("Github 链接"); ?></label>
    <input type="text" placeholder="<?php _e("支持用户/仓库链接"); ?>" value="" name="url" required>
</div>`,
                        confirm(modal) {
                            let url = $('[name="url"]', modal).val();
                            this.replaceSelection(`[x-github url="${url}"/]`);
                            return true;
                        }
                    });
                }
            }]).trigger('XEditorAddHtmlProcessor', [
                function (html) {
                    if (html.indexOf("[github")) {
                        html = html.replace(this.getShortCodeRegex("github"), `<div class="x-github-wrapper"><x-github$3>$5</x-github></div>`);
                    }
                    if (html.indexOf("[x-github")) {
                        html = html.replace(this.getShortCodeRegex("x-github"), `<div class="x-github-wrapper"><x-github$3>$5</x-github></div>`);
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
              href="<?php echo Util::moduleUrl('GithubCard', 'index.css'); ?>">
        <script>
            customElements.define(
                'x-github',
                class xGithub extends HTMLElement {
                    constructor() {
                        super();
                        if (!this.hasAttribute("url") || !this.getAttribute("url").trim()) {
                            this.innerHTML = ``;
                        } else {
                            let url = this.getAttribute("url").trim();
                            let regex;
                            if (url.indexOf("github.com") !== false)
                                regex = /(?:git@|https?:\/\/)(?:github.com)(?:\/)([^\/]*)(\/?)(?<=\/)([.\w-]*=?)/is;
                            else
                                regex = /([^\/]*)(\/?)([^\/.]*)/is;
                            let m, api;
                            let that = this;
                            if ((m = regex.exec(url)) !== null) {
                                if (m.length === 4 && m[2] === "/") {
                                    if (m[3].endsWith(".git")) m[3] = m[3].replace(/\.git$/is, "");
                                    let cache = getWithExpiry("github-repo:" + m[3]);
                                    if (cache) {
                                        let json = JSON.parse(cache);
                                        this.innerHTML = parseHTML(json);
                                    } else {
                                        api = "https://api.github.com/repos/" + m[1] + "/" + m[3];
                                        fetch(api).then(resp => resp.json()).then(json => {
                                            that.innerHTML = parseHTML(json);
                                            setWithExpiry("github-repo:" + m[3], JSON.stringify(json), 7200);
                                        });
                                    }

                                    function parseHTML(json) {
                                        return `<div class="x-github">
    <div class="x-github-title">
        <span class="icon"><width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 496 512" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M165.9 397.4c0 2-2.3 3.6-5.2 3.6-3.3.3-5.6-1.3-5.6-3.6 0-2 2.3-3.6 5.2-3.6 3-.3 5.6 1.3 5.6 3.6zm-31.1-4.5c-.7 2 1.3 4.3 4.3 4.9 2.6 1 5.6 0 6.2-2s-1.3-4.3-4.3-5.2c-2.6-.7-5.5.3-6.2 2.3zm44.2-1.7c-2.9.7-4.9 2.6-4.6 4.9.3 2 2.9 3.3 5.9 2.6 2.9-.7 4.9-2.6 4.6-4.6-.3-1.9-3-3.2-5.9-2.9zM244.8 8C106.1 8 0 113.3 0 252c0 110.9 69.8 205.8 169.5 239.2 12.8 2.3 17.3-5.6 17.3-12.1 0-6.2-.3-40.4-.3-61.4 0 0-70 15-84.7-29.8 0 0-11.4-29.1-27.8-36.6 0 0-22.9-15.7 1.6-15.4 0 0 24.9 2 38.6 25.8 21.9 38.6 58.6 27.5 72.9 20.9 2.3-16 8.8-27.1 16-33.7-55.9-6.2-112.3-14.3-112.3-110.5 0-27.5 7.6-41.3 23.6-58.9-2.6-6.5-11.1-33.3 2.6-67.9 20.9-6.5 69 27 69 27 20-5.6 41.5-8.5 62.8-8.5s42.8 2.9 62.8 8.5c0 0 48.1-33.6 69-27 13.7 34.7 5.2 61.4 2.6 67.9 16 17.7 25.8 31.5 25.8 58.9 0 96.5-58.9 104.2-114.8 110.5 9.2 7.9 17 22.9 17 46.4 0 33.7-.3 75.4-.3 83.6 0 6.5 4.6 14.4 17.3 12.1C428.2 457.8 496 362.9 496 252 496 113.3 383.5 8 244.8 8zM97.2 352.9c-1.3 1-1 3.3.7 5.2 1.6 1.6 3.9 2.3 5.2 1 1.3-1 1-3.3-.7-5.2-1.6-1.6-3.9-2.3-5.2-1zm-10.8-8.1c-.7 1.3.3 2.9 2.3 3.9 1.6 1 3.6.7 4.3-.7.7-1.3-.3-2.9-2.3-3.9-2-.6-3.6-.3-4.3.7zm32.4 35.6c-1.6 1.3-1 4.3 1.3 6.2 2.3 2.3 5.2 2.6 6.5 1 1.3-1.3.7-4.3-1.3-6.2-2.2-2.3-5.2-2.6-6.5-1zm-11.4-14.7c-1.6 1-1.6 3.6 0 5.9 1.6 2.3 4.3 3.3 5.6 2.3 1.6-1.3 1.6-3.9 0-6.2-1.4-2.3-4-3.3-5.6-2z"></path></svg></span>
        <a class="user" href="${json.owner.html_url}" target="_blank">${json.owner.login}</a>
        <span>/</span>
        <a class="x-github-repository" href="${json.html_url}" target="_blank">${json.name}</a>
        <div class="x-github-statics">
            <span class="forks"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="20" height="20"><path d="M18 4C13.582 4 10 7.582 10 12C10 15.356026 12.069798 18.224527 15 19.412109L15 42.919922L15 44.587891C12.069798 45.775473 10 48.643974 10 52C10 56.418 13.582 60 18 60C22.418 60 26 56.418 26 52C26 48.643974 23.930202 45.775473 21 44.587891L21 42.919922C21 41.494922 22.014156 40.256563 23.410156 39.976562L43.765625 35.90625C47.958625 35.06625 51 31.354078 51 27.080078L51 24.412109C53.930202 23.224527 56 20.356026 56 17C56 12.582 52.418 9 48 9C43.582 9 40 12.582 40 17C40 20.356026 42.069798 23.224527 45 24.412109L45 27.080078C45 28.505078 43.985844 29.743437 42.589844 30.023438L23.392578 33.857422C22.154578 34.104422 21 33.158484 21 31.896484L21 19.412109C23.930202 18.224527 26 15.356026 26 12C26 7.582 22.418 4 18 4 z M 18 10C19.103 10 20 10.897 20 12C20 13.103 19.103 14 18 14C16.897 14 16 13.103 16 12C16 10.897 16.897 10 18 10 z M 48 15C49.103 15 50 15.897 50 17C50 18.103 49.103 19 48 19C46.897 19 46 18.103 46 17C46 15.897 46.897 15 48 15 z M 18 50C19.103 50 20 50.897 20 52C20 53.103 19.103 54 18 54C16.897 54 16 53.103 16 52C16 50.897 16.897 50 18 50 z"/></svg>${json.forks_count}</span>
            <span class="slash">/</span>
            <span class="stars"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M12 5.109375L13.542969 8.742188L14.015625 9.84375L15.210938 9.949219L19.140625 10.296875L16.164063 12.886719L15.257813 13.675781L15.527344 14.84375L16.414063 18.691406L13.027344 16.660156L12 16.042969L10.972656 16.660156L7.585938 18.691406L8.472656 14.84375L8.742188 13.675781L7.835938 12.886719L4.859375 10.296875L8.789063 9.953125L9.984375 9.847656L10.457031 8.742188L12 5.109375 M 12 0L8.613281 7.960938L0 8.71875L6.523438 14.398438L4.585938 22.824219L12 18.378906L19.414063 22.828125L17.476563 14.398438L24 8.71875L15.386719 7.960938Z"/></svg>${json.stargazers_count}</span>
        </div>
    </div>
    <div class="x-github-content">
        ${json.description}
    </div>
    <div class="x-github-footer">
        <a class="x-github-btn secondary" href="${json.html_url}" target="_blank"><span class="x-github-btn-content"><?php _e("仓库") ?></span></a>
        <a class="x-github-btn warning" href="${json.html_url}/zipball/master" target="_blank"><span class="x-github-btn-content"><?php _e("下载 zip 文件") ?></span></a>
    </div>
</div>`
                                    }
                                } else if (!m[1] && m[3]) {
                                    let cache = getWithExpiry("github-user:" + m[3]);
                                    if (cache) {
                                        let json = JSON.parse(cache);
                                        this.innerHTML = parseHTML(json);
                                    } else {
                                        api = "https://api.github.com/users/" + m[3];
                                        fetch(api).then(resp => resp.json()).then(json => {
                                            that.innerHTML = parseHTML(json);
                                            setWithExpiry("github-user:" + m[3], JSON.stringify(json), 7200);
                                        });
                                    }

                                    function parseHTML(json) {
                                        return `<div class="x-github x-github-user">
<a href="${json.html_url}" target="_blank">
   <span class="icon"><svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 496 512" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M165.9 397.4c0 2-2.3 3.6-5.2 3.6-3.3.3-5.6-1.3-5.6-3.6 0-2 2.3-3.6 5.2-3.6 3-.3 5.6 1.3 5.6 3.6zm-31.1-4.5c-.7 2 1.3 4.3 4.3 4.9 2.6 1 5.6 0 6.2-2s-1.3-4.3-4.3-5.2c-2.6-.7-5.5.3-6.2 2.3zm44.2-1.7c-2.9.7-4.9 2.6-4.6 4.9.3 2 2.9 3.3 5.9 2.6 2.9-.7 4.9-2.6 4.6-4.6-.3-1.9-3-3.2-5.9-2.9zM244.8 8C106.1 8 0 113.3 0 252c0 110.9 69.8 205.8 169.5 239.2 12.8 2.3 17.3-5.6 17.3-12.1 0-6.2-.3-40.4-.3-61.4 0 0-70 15-84.7-29.8 0 0-11.4-29.1-27.8-36.6 0 0-22.9-15.7 1.6-15.4 0 0 24.9 2 38.6 25.8 21.9 38.6 58.6 27.5 72.9 20.9 2.3-16 8.8-27.1 16-33.7-55.9-6.2-112.3-14.3-112.3-110.5 0-27.5 7.6-41.3 23.6-58.9-2.6-6.5-11.1-33.3 2.6-67.9 20.9-6.5 69 27 69 27 20-5.6 41.5-8.5 62.8-8.5s42.8 2.9 62.8 8.5c0 0 48.1-33.6 69-27 13.7 34.7 5.2 61.4 2.6 67.9 16 17.7 25.8 31.5 25.8 58.9 0 96.5-58.9 104.2-114.8 110.5 9.2 7.9 17 22.9 17 46.4 0 33.7-.3 75.4-.3 83.6 0 6.5 4.6 14.4 17.3 12.1C428.2 457.8 496 362.9 496 252 496 113.3 383.5 8 244.8 8zM97.2 352.9c-1.3 1-1 3.3.7 5.2 1.6 1.6 3.9 2.3 5.2 1 1.3-1 1-3.3-.7-5.2-1.6-1.6-3.9-2.3-5.2-1zm-10.8-8.1c-.7 1.3.3 2.9 2.3 3.9 1.6 1 3.6.7 4.3-.7.7-1.3-.3-2.9-2.3-3.9-2-.6-3.6-.3-4.3.7zm32.4 35.6c-1.6 1.3-1 4.3 1.3 6.2 2.3 2.3 5.2 2.6 6.5 1 1.3-1.3.7-4.3-1.3-6.2-2.2-2.3-5.2-2.6-6.5-1zm-11.4-14.7c-1.6 1-1.6 3.6 0 5.9 1.6 2.3 4.3 3.3 5.6 2.3 1.6-1.3 1.6-3.9 0-6.2-1.4-2.3-4-3.3-5.6-2z"></path></svg></span>
   <span class="name">${json.login}(${json.name})</span>
</a>
</div>`;
                                    }
                                }

                                function setWithExpiry(key, value, ttl) {
                                    const now = new Date()

                                    // `item` is an object which contains the original value
                                    // as well as the time when it's supposed to expire
                                    const item = {
                                        value: value,
                                        expiry: now.getTime() + ttl,
                                    }
                                    localStorage.setItem(key, JSON.stringify(item))
                                }

                                function getWithExpiry(key) {
                                    const itemStr = localStorage.getItem(key)
                                    // if the item doesn't exist, return null
                                    if (!itemStr) {
                                        return null
                                    }
                                    const item = JSON.parse(itemStr)
                                    const now = new Date()
                                    // compare the expiry time of the item with the current time
                                    if (now.getTime() > item.expiry) {
                                        // If the item is expired, delete the item from storage
                                        // and return null
                                        localStorage.removeItem(key)
                                        return null
                                    }
                                    return item.value
                                }
                            }
                        }
                    }
                }
            );
        </script>
        <?php
    }

    public static function parseContent($text, $archive): string
    {
        if (strpos($text, '[github') === false && strpos($text, '[x-github') === false) { //提高效率，避免每篇文章都要解析
            return $text;
        }
        $pattern = Util::get_shortcode_regex(['x-github', 'github']);
        return preg_replace("/$pattern/", '<div class="x-github-wrapper"><x-github$3>$5</x-github></div>', $text);
    }


    public static function parseExcerpt($text, $archive): string
    {
        if (strpos($text, '[x-link') === false) { //提高效率，避免每篇文章都要解析
            return $text;
        }
        $pattern = Util::get_shortcode_regex(['x-link']);
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
        if (array_key_exists('url', $attrs)) {
            return $attrs['url'];
        }
        return '';
    }
}
