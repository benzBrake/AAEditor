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
                name: '<?php _e("插入 Github/Gitee 卡片"); ?>',
                icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M12.001 2C6.47598 2 2.00098 6.475 2.00098 12C2.00098 16.425 4.86348 20.1625 8.83848 21.4875C9.33848 21.575 9.52598 21.275 9.52598 21.0125C9.52598 20.775 9.51348 19.9875 9.51348 19.15C7.00098 19.6125 6.35098 18.5375 6.15098 17.975C6.03848 17.6875 5.55098 16.8 5.12598 16.5625C4.77598 16.375 4.27598 15.9125 5.11348 15.9C5.90098 15.8875 6.46348 16.625 6.65098 16.925C7.55098 18.4375 8.98848 18.0125 9.56348 17.75C9.65098 17.1 9.91348 16.6625 10.201 16.4125C7.97598 16.1625 5.65098 15.3 5.65098 11.475C5.65098 10.3875 6.03848 9.4875 6.67598 8.7875C6.57598 8.5375 6.22598 7.5125 6.77598 6.1375C6.77598 6.1375 7.61348 5.875 9.52598 7.1625C10.326 6.9375 11.176 6.825 12.026 6.825C12.876 6.825 13.726 6.9375 14.526 7.1625C16.4385 5.8625 17.276 6.1375 17.276 6.1375C17.826 7.5125 17.476 8.5375 17.376 8.7875C18.0135 9.4875 18.401 10.375 18.401 11.475C18.401 15.3125 16.0635 16.1625 13.8385 16.4125C14.201 16.725 14.5135 17.325 14.5135 18.2625C14.5135 19.6 14.501 20.675 14.501 21.0125C14.501 21.275 14.6885 21.5875 15.1885 21.4875C19.259 20.1133 21.9999 16.2963 22.001 12C22.001 6.475 17.526 2 12.001 2Z"></path></svg>',
                insertBefore: '#wmd-spacer4',
                command() {
                    this.openModal({
                        title: '<?php _e("Github 卡片"); ?>',
                        innerHTML: `<div class="form-item">
    <label class="required" for="url"><?php _e("Github/Gitee 链接"); ?></label>
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
                        this.init();
                    }

                    async init() {
                        const url = this.getAttribute("url")?.trim();
                        if (!url) {
                            this.innerHTML = ``;
                            return;
                        }

                        const is_github = url.includes("github.com");
                        const is_gitee = url.includes("gitee.com");
                        const regex = this.getRegex(is_github, is_gitee);
                        const match = regex.exec(url);


                        if (match) { // Ensure the repository part is present
                            const platform = is_github ? 'github' : 'gitee';
                            if (match[1].length > 0 && match[2] === '/' && match[3].length > 0) {
                                const [, user, , repo] = match;
                                const cacheKey = `${platform}-repo:${user}/${repo}`;
                                const cache = this.getWithExpiry(cacheKey);

                                if (cache) {
                                    const json = JSON.parse(cache);
                                    this.renderRepo(json, is_gitee);
                                } else {
                                    const api = this.getRepoApiUrl(is_github, is_gitee, user, repo);
                                    if (!api) return;

                                    try {
                                        const response = await fetch(api);
                                        const json = await response.json();
                                        this.renderRepo(json, is_gitee);
                                        this.setWithExpiry(cacheKey, JSON.stringify(json), 86400);
                                    } catch (error) {
                                        console.error('Error fetching API:', error);
                                    }
                                }
                            } else if ((match[2] !== '/' && match[3].length > 0) || (match[2] === '/' && !match[3].length > 0)) {
                                const user = match[3].length ? match[3] : match[1];
                                const cacheKey = `${platform}-user:${user}`;
                                const cache = this.getWithExpiry(cacheKey);

                                if (cache) {
                                    const json = JSON.parse(cache);
                                    this.renderUser(json, is_gitee);
                                } else {
                                    const api = this.getUserApiUrl(is_github, is_gitee, user);
                                    if (!api) return;

                                    try {
                                        const response = await fetch(api);
                                        const json = await response.json();
                                        this.renderUser(json, is_gitee);
                                        this.setWithExpiry(cacheKey, JSON.stringify(json), 86400);
                                    } catch (error) {
                                        console.error('Error fetching API:', error);
                                    }
                                }
                            } else {
                                this.innerHTML = `Invalid URL format.`;
                            }
                        } else {
                            this.innerHTML = `Invalid URL format.`;
                        }
                    }

                    getRegex(is_github, is_gitee) {
                        if (is_github) {
                            return /(?:git@|https?:\/\/)github.com\/([^\/]*)(\/?)(?<=\/)([.\w-]*=?)/is;
                        } else if (is_gitee) {
                            return /https?:\/\/gitee.com\/([^\/]*)(\/?)(?<=\/)([.\w-]*=?)/is;
                        } else {
                            return /([^\/]+)\/([^\/]+)/i;
                        }
                    }

                    getRepoApiUrl(is_github, is_gitee, user, repo) {
                        if (is_github) {
                            return `https://api.github.com/repos/${user}/${repo}`;
                        } else if (is_gitee) {
                            return `https://gitee.com/api/v5/repos/${user}/${repo}`;
                        }
                        return null;
                    }

                    getUserApiUrl(is_github, is_gitee, user) {
                        if (is_github) {
                            return `https://api.github.com/users/${user}`;
                        } else if (is_gitee) {
                            return `https://gitee.com/api/v5/users/${user}`;
                        }
                    }

                    renderRepo(json, is_gitee) {
                        const icon = is_gitee ? this.giteeIcon() : this.githubIcon();
                        this.innerHTML = this.parseRepoHTML(json, icon);
                        if (is_gitee) {
                            this.querySelector('.download-zip').style.display = 'none';
                        }
                    }

                    renderUser(json, is_gitee) {
                        const icon = is_gitee ? this.giteeIcon() : this.githubIcon();
                        this.innerHTML = this.parseUserHTML(json, icon);
                    }

                    parseRepoHTML(json, icon) {
                        return `<div class="x-github">
                <div class="x-github-title">
                    <span class="icon">${icon}</span>
                    <a class="user reset" href="${json.owner.html_url}" target="_blank">${json.owner.login}</a>
                    <span>/</span>
                    <a class="x-github-repository reset" href="${json.html_url}" target="_blank">${json.name}</a>
                    <div class="x-github-statics">
                        <span class="forks">${this.forksIcon()}${json.forks_count}</span>
                        <span class="slash">/</span>
                        <span class="stars">${this.starsIcon()}${json.stargazers_count}</span>
                    </div>
                </div>
                <div class="x-github-content">${json.description}</div>
                <div class="x-github-footer">
                    <a class="x-github-btn secondary reset" href="${json.html_url}" target="_blank"><span class="x-github-btn-content">仓库</span></a>
                    <a class="x-github-btn warning download-zip reset" href="${json.html_url}/zipball/master" target="_blank"><span class="x-github-btn-content">下载 zip 文件</span></a>
                </div>
            </div>`;
                    }

                    parseUserHTML(json, icon) {
                        return `<div class="x-github x-github-user">
<a class="reset" href="${json.html_url}" target="_blank">
   <span class="icon">${icon}</span>
   <span class="name">${json.login}(${json.name})</span>
</a>`;
                    }

                    giteeIcon() {
                        return `<svg fill="#C71D23" width="16px" height="16px" viewBox="0 0 24 24" role="img" xmlns="http://www.w3.org/2000/svg">
                <path d="M11.984 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.016 0zm6.09 5.333c.328 0 .593.266.592.593v1.482a.594.594 0 0 1-.593.592H9.777c-.982 0-1.778.796-1.778 1.778v5.63c0 .327.266.592.593.592h5.63c.982 0 1.778-.796 1.778-1.778v-.296a.593.593 0 0 0-.592-.593h-4.15a.592.592 0 0 1-.592-.592v-1.482a.593.593 0 0 1 .593-.592h6.815c.327 0 .593.265.593.592v3.408a4 4 0 0 1-4 4H5.926a.593.593 0 0 1-.593-.593V9.778a4.444 4.444 0 0 1 4.445-4.444h8.296z"/>
            </svg>`;
                    }

                    githubIcon() {
                        return `<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 496 512" xmlns="http://www.w3.org/2000/svg">
                <path fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M165.9 397.4c0 2-2.3 3.6-5.2 3.6-3.3.3-5.6-1.3-5.6-3.6 0-2 2.3-3.6 5.2-3.6 3-.3 5.6 1.3 5.6 3.6zm-31.1-4.5c-.7 2 1.3 4.3 4.3 4.9 2.6 1 5.6 0 6.2-2s-1.3-4.3-4.3-5.2c-2.6-.7-5.5.3-6.2 2.3zm44.2-1.7c-2.9.7-4.9 2.6-4.6 4.9.3 2 2.9 3.3 5.9 2.6 2.9-.7 4.9-2.6 4.6-4.6-.3-1.9-3-3.2-5.9-2.9zM244.8 8C106.1 8 0 113.3 0 252c0 110.9 69.8 205.8 169.5 239.2 12.8 2.3 17.3-5.6 17.3-12.1 0-6.2-.3-40.4-.3-61.4 0 0-70 15-84.7-29.8 0 0-11.4-29.1-27.8-36.6 0 0-22.9-15.7 1.6-15.4 0 0 24.9 2 38.6 25.8 21.9 38.6 58.6 27.5 72.9 20.9 2.3-16 8.8-27.1 16-33.7-55.9-6.2-112.3-14.3-112.3-110.5 0-27.5 7.6-41.3 23.6-58.9-2.6-6.5-11.1-33.3 2.6-67.9 20.9-6.5 69 27 69 27 20-5.6 41.5-8.5 62.8-8.5s42.8 2.9 62.8 8.5c0 0 48.1-33.6 69-27 13.7 34.7 5.2 61.4 2.6 67.9 16 17.7 25.8 31.5 25.8 58.9 0 96.5-58.9 104.3-114.8 110.5 9.2 8 17.3 23.2 17.3 47.1 0 33.7-.3 74.9-.3 82.7 0 6.5 4.6 14.7 17.6 12.1C426.2 457.9 496 362.9 496 252 496 113.3 383.5 8 244.8 8z"/>
            </svg>`;
                    }

                    forksIcon() {
                        return `<svg viewBox="0 0 16 16" width="16" height="16" aria-hidden="true">
                <path fill-rule="evenodd" fill="currentColor" d="M5 3.09V12.9c-.6.3-1 .8-1 1.4 0 .8.8 1.7 2 1.7s2-.9 2-1.7c0-.6-.4-1.1-1-1.4v-4h3v.9c-.6.3-1 .8-1 1.4 0 .8.8 1.7 2 1.7s2-.9 2-1.7c0-.6-.4-1.1-1-1.4V6.09c.6-.3 1-.8 1-1.4 0-.8-.8-1.7-2-1.7s-2 .9-2 1.7c0 .6.4 1.1 1 1.4v2H7v-2c.6-.3 1-.8 1-1.4 0-.8-.8-1.7-2-1.7s-2 .9-2 1.7c0 .6.4 1.1 1 1.4z"></path>
            </svg>`;
                    }

                    starsIcon() {
                        return `<svg viewBox="0 0 16 16" width="16" height="16" aria-hidden="true">
                <path fill-rule="evenodd" fill="currentColor" d="M8 12.7l-4.3 2.3c-.5.3-1-.2-.8-.8l.8-4.7-3.5-3.4c-.4-.4-.2-1.1.4-1.2l4.8-.7 2.2-4.5c.3-.5 1-.5 1.2 0l2.2 4.5 4.8.7c.6.1.8.8.4 1.2l-3.5 3.4.8 4.7c.1.6-.5 1.1-.9.8L8 12.7z"></path>
            </svg>`;
                    }

                    setWithExpiry(key, value, ttl) {
                        const now = new Date();
                        const item = {
                            value: value,
                            expiry: now.getTime() + ttl * 1000,
                        };
                        localStorage.setItem(key, JSON.stringify(item));
                    }

                    getWithExpiry(key) {
                        const itemStr = localStorage.getItem(key);
                        if (!itemStr) return null;

                        const item = JSON.parse(itemStr);
                        const now = new Date();
                        if (now.getTime() > item.expiry) {
                            localStorage.removeItem(key);
                            return null;
                        }
                        return item.value;
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
        if (strpos($text, '[github') === false && strpos($text, '[x-github') === false) { //提高效率，避免每篇文章都要解析
            return $text;
        }
        $pattern = Util::get_shortcode_regex(['x-github', 'github']);
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
