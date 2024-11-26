<?php
/**
 *
 * @Date 2024/8/20
 */

namespace TypechoPlugin\AAEditor;

use Typecho\Common;
use Typecho\Widget;
use Utils\Helper;
use Widget\ActionInterface;

class Action extends Widget implements ActionInterface
{
    public function adminStyleCss()
    {
        ob_start();
        Helper::options()->adminStaticUrl('css', 'style.css');
        $url = ob_get_clean();
        $url = preg_replace('/\?.*/', '', $url);
        $root_url = Helper::options()->rootUrl;
        $url = str_replace($root_url, '', $url);
        $relative_path = dirname($url, 2);
        $css_path = Common::url($url, __TYPECHO_ROOT_DIR__);
        if (DIRECTORY_SEPARATOR === '\\') {
            $css_path = str_replace('/', '\\', $css_path);
        }
        $css_content = file_get_contents($css_path);
        $css_content = preg_replace('/pre\s*code\s*{.*}/', '', $css_content ?? '');
        $css_content = str_replace('#wmd-preview code, #wmd-preview pre', '#wmd-preview code:not(.hljs)', $css_content);
        $css_content = str_replace('#wmd-preview pre { padding: 1em; }', '', $css_content);
        $css_content = str_replace('../img', sprintf('%s/img', $relative_path), $css_content);
        header('Content-Type: text/css; charset=UTF-8');
        header('Cache-Control: max-age=86400, must-revalidate');
        echo $css_content;
    }

    /**
     * @inheritDoc
     */
    public function action()
    {
        $this->on($this->request->is('admin_style_css'))->adminStyleCss();
    }
}
