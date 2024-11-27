<?php

namespace TypechoPlugin\AAEditor;

use Typecho\Common;
use Typecho\Config;
use Typecho\Db;
use Typecho\Plugin\Exception;
use Typecho\Plugin\PluginInterface;
use Typecho\Request;
use Typecho\Response;
use Typecho\Widget\Helper\Form;
use Utils\Helper;
use Widget\Notice;

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 虾米皮皮乐的编辑器 <a href="https://malleable-flare-263.notion.site/AAEditor-0a003324bf3a4a608d6d563ef3a9779e" target="_blank" style="color: #fff; background-color: #f1404b; font-weight: bold; padding: 3px 5px; margin: 0 5px;">使用说明</span>
 *
 * @package AAEditor
 * @author Ryan
 * @version 0.5.7
 * @link https://doufu.ru
 *
 */
class Plugin implements PluginInterface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return string
     */
    public static function activate(): string
    {
        return Util::activate();
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return string
     * @throws Db\Exception
     * @throws Exception
     */
    public static function deactivate(): string
    {
        return Util::deactivate();
    }

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Form $form 配置面板
     * @return void
     * @throws Db\Exception
     */
    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Form $form 配置面板
     * @return void
     * @throws Db\Exception
     */
    public static function config(Form $form)
    {
        $db = Db::get();
        $notice = Notice::alloc();
        $request = new \Typecho\Widget\Request(Request::getInstance(), isset($request) ? new Config($request) : null);
        $response = new \Typecho\Widget\Response(Request::getInstance(), Response::getInstance());
        $plugin = "AAEditor";
        $pluginDataRow = $db->fetchRow($db->select()->from('table.options')->where('name = ?', "plugin:$plugin"));
        $pluginData_backupRow = $db->fetchRow($db->select()->from('table.options')->where('name = ?', "plugin:{$plugin}Backup"));
        $pluginData = empty($pluginDataRow) ? null : $pluginDataRow['value'];
        $pluginData_backup = empty($pluginData_backupRow) ? null : $pluginData_backupRow['value'];
        if (isset($request->type)) {
            if ($request->type == 'backup') {
                if ($db->fetchRow($db->select()->from('table.options')->where('name = ?', "plugin:{$plugin}Backup"))) {
                    $updateQuery = $db->update('table.options')->rows(array('value' => $pluginData))->where('name = ?', "plugin:{$plugin}Backup");
                    $db->query($updateQuery);
                    $notice->set(_t('备份已更新!'), 'success');
                    $response->goBack();
                } else {
                    if ($pluginData) {
                        $insertQuery = $db->insert('table.options')->rows(array('name' => "plugin:{$plugin}Backup", 'user' => '0', 'value' => $pluginData));
                        $db->query($insertQuery);
                        $notice->set(_t('备份完成!'), 'success');
                        $response->goBack();
                    }
                }
            } elseif ($request->type == 'restore') {
                if ($pluginData_backup) {
                    $updateQuery = $db->update('table.options')->rows(array('value' => $pluginData_backup))->where('name = ?', "plugin:$plugin");
                    $db->query($updateQuery);
                    $notice->set(_t('检测到模板备份数据，恢复完成'), 'success');
                } else {
                    $notice->set(_t('没有模板备份数据，恢复不了哦！'), 'error');
                }
                $response->goBack();
            } elseif ($request->type == 'delete') {
                if ($pluginData_backup) {
                    $deleteQuery = $db->delete('table.options')->where('name = ?', "plugin:{$plugin}Backup");
                    $db->query($deleteQuery);
                    $notice->set(_t('删除成功！！！'), 'success');
                } else {
                    $notice->set(_t('不用删了！备份不存在！！！'), 'error');
                }
                $response->goBack();
            }
        }
        $errorMessage = [];
        if (!$pluginData_backup) {
            $errorMessage[] = _t(/** @lang text */'检测到设置备份不存在，<a href="%s">点此</a>备份设置', Common::url('/options-plugin.php?config=AAEditor&type=backup', Helper::options()->adminUrl));
        }
        Util::collectManifest();
        ?>
        <link rel="stylesheet" href="<?php echo Util::pluginStatic('css', 'config.css'); ?>">
        <script>window.XEditorModules = JSON.parse('<?php echo json_encode(Util::listModules()) ?>');
            window.XEditorUpldateURL = 'https://xiamp.net/archives/aaeditor-update-log.html';</script>
        <script src="<?php echo Util::pluginStatic('js', 'config.js'); ?>"></script>
        <div class="x-config">
            <?php if (count($errorMessage)): ?>
                <div class="x-warning">
                    <?php foreach ($errorMessage as $msg): ?>
                        <div class="warning-item"><?php echo $msg; ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="x-sticky">
                <div class="x-logo"><?php echo 'AAEditor' . Plugin::version(); ?></div>
                <ul class="x-tabs">
                    <li class="item" data-class="x-notice"><?php _e("最新公告"); ?></li>
                    <li class="item" data-class="x-basic"><?php _e("基础设置"); ?></li>
                    <li class="item" data-class="x-warn"><?php _e("慎重修改"); ?></li>
                </ul>
                <div class="x-backup">
                    <span class="backup"
                          onclick="window.location.href='<?php Helper::options()->adminUrl('/options-plugin.php?config=AAEditor&type=backup') ?>'"><?php _e("备份设置"); ?></span>
                    <span class="restore"
                          onclick="window.location.href='<?php Helper::options()->adminUrl('/options-plugin.php?config=AAEditor&type=restore') ?>'"><?php _e("还原设置"); ?></span>
                    <span class="delete"
                          onclick="window.location.href='<?php Helper::options()->adminUrl('/options-plugin.php?config=AAEditor&type=delete') ?>'"><?php _e("删除备份"); ?></span>
                </div>
            </div>
            <div class="x-content">

            </div>
        </div>
        <?php
        $edit = new Label('<ul class="x-item x-notice"><h2 class="title" data-version="' . Plugin::version() . '"><span class="loading">' . _t("加载中...") . '</span><span class="latest">' . _t("最新版本") . '</span><span class="latest found">' . _t("发现新版本：") . '</span><span class="latest version"></span></span></h2><div class="message"></div></ul>');
        $form->addItem($edit);
        $edit = new Form\Element\Select(
            'XEditorEnabled',
            array(
                'off' => _t('关闭'),
                'on' => _t('开启（默认）')
            ),
            'on',
            _t('是否开启编辑器'),
            _t('介绍：如果你仅需要短代码处理功能，请关闭编辑器')
        );
        $edit->setAttribute('class', 'x-item x-basic');
        $form->addInput($edit->multiMode());

        $edit = new Form\Element\Select(
            'XShortCodeParse',
            array(
                'off' => _t('关闭'),
                'on' => _t('开启（默认）')
            ),
            'on',
            _t('是否开启短代码转换'),
            _t('介绍：如果短代码转换功能与你当前主题冲突，请关闭此功能！！!</br>注意：关闭此功能后可以使用自定义字段<span style="display: inline-block; padding:0 5px; color: firebrick">EnableShortCodeParse</span>强制打开指定文章的短代码解析')
        );
        $edit->setAttribute('class', 'x-item x-basic');
        $form->addInput($edit->multiMode());


        $edit = new Form\Element\Select(
            'XEditorContentStyle',
            array(
                'on' => _t('开启'),
                'off' => _t('关闭（默认）')
            ), 'off',
            _t('前台是否加载插件自带正文样式'),
            _t('说明：只适配的默认主题，其他主题需要手动给正文加上<code>post-content</code>类名'));
        $edit->setAttribute('class', 'x-item x-basic');
        $form->addInput($edit);

        $edit = new Form\Element\Select(
            'XMathJaxSupport',
            [
                'on' => _t('开启（默认）'),
                'off' => _t('关闭')
            ], 'on',
            _t('是否开启公式支持'),
            _t('说明：开启公式支持后前台会引入 JS 解析公式'));
        $edit->setAttribute('class', 'x-item x-basic');
        $form->addInput($edit);

        $edit = new Form\Element\Select(
            'XLoadFontAwesome',
            [
                'auto' => _t('自动（默认）'),
                'on' => _t('开启'),
                'off' => _t('关闭')
            ], 'auto',
            _t('前台载入FontAwesome'),
            _t('说明：关闭后需要自行载入相关字体图标'));
        $edit->setAttribute('class', 'x-item x-basic');
        $form->addInput($edit);

        $edit = new Form\Element\Select(
            'XHljs',
            ['off' => _t("关闭（默认）")] + Util::listHljsCss(), 'off',
            _t('前台载入 Highlight.js 代码高亮库'),
            _t('说明：关闭后需要自行渲染代码块'));
        $edit->setAttribute('class', 'x-item x-basic');
        $form->addInput($edit);

        $edit = new Form\Element\Select('XInsertALlImages',
            [
                'on' => _t('开启（默认）'),
                'off' => _t('关闭')
            ],
            'on',
            _t('图片一键插入全部'),
            _t('说明：开启后可以在附件列表中一键插入所有图片'));
        $edit->setAttribute('class', 'x-item x-basic');
        $form->addInput($edit->multiMode());

        $edit = new Form\Element\Select('XJsdelivrMirror',
            [
                'local' => _t('本地（默认）'),
                'https://cdn.jsdmirror.com' => _t('JSDMirror'),
                'https://jsd.onmicrosoft.cn' => _t('渺软公益 CDN'),
                'https://jsd.proxy.aks.moe' => _t('晓白云公益 CDN'),
                'https://jsd.lihaoyu.cn' => _t('Xiao Yu\'s CDN'),
                'https://cdn.bili33.top' => _t('哔哩 CDN（不推荐）'),
                'https://cdn.jsdelivr.net' => _t('jsDelivr 官方（不推荐）'),
                'https://gcore.jsdelivr.net' => _t('jsDelivr 官方（GCore 节点，不推荐）')
            ],
            'local',
            _t('jsDelivr 镜像'),
            _t('说明：因为jsDelivr的CDN在国内访问不稳定，使用 jsDelivr 的镜像可以提高访问速度，默认为为本地'));
        $edit->setAttribute('class', 'x-item x-basic');
        $form->addInput($edit->multiMode());

        $edit = new Form\Element\Text('XModules', null, '', _t('选择需要启用的模块'), _t(""));
        $edit->setAttribute('class', 'x-item x-basic');
        $form->addInput($edit->multiMode());

        $edit = new Form\Element\Select(
            'XCombileModuleCss',
            ['off' => _t("关闭（默认）"), 'on' => _t("开启")], 'off',
            _t('合并模块的 CSS 文件'),
            _t('说明：修改 CSS 后需要手动清理缓存<code>插件目录/cache/minify.css</code>'));
        $edit->setAttribute('class', 'x-item x-basic');
        $form->addInput($edit);

        $edit = new Form\Element\Text('XDisableAttachAutoInsert', null, null, _t('附件上传后禁止自动插入到正文'), _t('填写后缀，格式：后缀|后缀'));
        $edit->setAttribute('class', 'x-item x-basic');
        $form->addInput($edit->multiMode());

        $edit = new Form\Element\Radio('XCleanDatabase', array('clean' => _t('清理'), 'none' => _t('保留')), 'none', _t('禁用插件后是否保留数据'), _t('注意：如果打开了此开关，禁用插件时自动清理插件产生的数据（包括插件选项备份）'));
        $edit->setAttribute('class', 'x-item x-warn');
        $form->addInput($edit);
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Form $form
     * @return void
     */
    public static function personalConfig(Form $form)
    {
    }

    /**
     * 获取主题版本
     * @return string
     */
    public static function version(): string
    {
        $info = \Typecho\Plugin::parseInfo(__FILE__);
        return $info['version'];
    }
}
