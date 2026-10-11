<?php

// 用内存宿主验证完整配置回调；不连接 Typecho 站点或数据库。
namespace Typecho\Plugin {
    interface PluginInterface {}
    class Exception extends \RuntimeException {}
}

namespace Typecho {
    class Config {}
    class Plugin
    {
        public static function parseInfo($file) { return ['version' => 'test']; }
    }
    class Common
    {
        public static function url($path, $base) { return rtrim($base, '/') . '/' . ltrim($path, '/'); }
    }
    class Request
    {
        public static $instance;
        public $values = [];
        public $method = 'GET';
        public $url = 'https://example.test/admin/options-plugin.php?config=AAEditor';
        public $referer;
        public static function getInstance() { return self::$instance; }
        public function get($name) { return isset($this->values[$name]) ? $this->values[$name] : null; }
        public function isPost() { return $this->method === 'POST'; }
        public function getRequestUrl() { return $this->url; }
        public function getReferer() { return $this->referer; }
    }
    class Response
    {
        public static function getInstance() { return new self(); }
    }
    class Query
    {
        public $operation;
        public $name;
        public $values = [];
        public function __construct($operation) { $this->operation = $operation; }
        public function from($table) { return $this; }
        public function where($condition, $name) { $this->name = $name; return $this; }
        public function rows($values) { $this->values = $values; return $this; }
    }
    class Db
    {
        public static $instance;
        public $options = [];
        public $writes = [];
        public static function get() { return self::$instance; }
        public function select() { return new Query('select'); }
        public function update($table) { return new Query('update'); }
        public function insert($table) { return new Query('insert'); }
        public function delete($table) { return new Query('delete'); }
        public function fetchRow($query) {
            return isset($this->options[$query->name]) ? ['value' => $this->options[$query->name]] : [];
        }
        public function query($query) {
            $this->writes[] = $query->operation;
            $name = $query->operation === 'insert' ? $query->values['name'] : $query->name;
            if ($query->operation === 'delete') unset($this->options[$name]);
            else $this->options[$name] = $query->values['value'];
        }
    }
}

namespace Typecho\Widget {
    class Request
    {
        private $request;
        public function __construct($request, $params = null) { $this->request = $request; }
        public function __get($name) { return $this->request->get($name); }
        public function isPost() { return $this->request->isPost(); }
        public function getRequestUrl() { return $this->request->getRequestUrl(); }
    }
    class Response
    {
        public function __construct($request, $response) {}
        public function goBack() { throw new \Redirect(); }
    }
}

namespace Typecho\Widget\Helper {
    class Layout
    {
        public function setTagName($name) {}
        public function setAttribute($name, $value) {}
        public function html($html) {}
    }
    class Form
    {
        public $action = '/action/plugins-edit?config=AAEditor';
        public $inputs = [];
        public function addItem($item) {}
        public function addInput($input) { $this->inputs[$input->name] = $input; }
        public function render() {
            echo '<form id="normal-config-form" method="post" action="' . $this->action . '">';
            foreach ($this->inputs as $name => $input) echo '<input name="' . $name . '">';
            echo '<button type="submit">Save</button></form>';
        }
    }
}

namespace Typecho\Widget\Helper\Form\Element {
    class Select
    {
        public $name;
        public function __construct($name, ...$args) { $this->name = $name; }
        public function setAttribute($name, $value) {}
        public function multiMode() { return $this; }
    }
    class Text extends Select {}
    class Radio extends Select {}
}

namespace Utils {
    class Helper
    {
        public static function options() { return (object) ['adminUrl' => 'https://example.test/admin/']; }
    }
}

namespace Widget {
    class Notice
    {
        public static function alloc() { return new self(); }
        public function set($message, $status) {}
    }
    class User
    {
        public static $admin = true;
        public static function alloc() { return new self(); }
        public function pass($role) {
            if ($role !== 'administrator' || !self::$admin) throw new \RuntimeException('Administrator required');
        }
    }
    class Security
    {
        public static function alloc() { return new self(); }
        // Typecho 1.2 Security::getToken/protect 的 URL/Referer 绑定契约。
        public function getToken($suffix) { return md5('test-fixture' . '&' . $suffix); }
        public function protect() {
            $request = \Typecho\Request::getInstance();
            if ($request->get('_') != $this->getToken($request->getReferer())) throw new \Redirect();
        }
    }
}

namespace TypechoPlugin\AAEditor {
    class Util
    {
        public static function collectManifest() {}
        public static function pluginStatic($type, $file) { return '/assets/' . $file; }
        public static function listHljsCss() { return []; }
    }
}

namespace {
    class Redirect extends RuntimeException {}
    function _t($message, ...$args) { return $args ? vsprintf($message, $args) : $message; }
    function _e($message) { echo $message; }
    function check($condition, $name) { if (!$condition) throw new RuntimeException('Failed: ' . $name); }

    define('__TYPECHO_ROOT_DIR__', dirname(__DIR__));
    require_once dirname(__DIR__) . '/Plugin.php';

    function runConfig($method, $type, $token, $hasBackup = true, $referer = null, $admin = true) {
        $request = new \Typecho\Request();
        $request->method = $method;
        $request->referer = $referer === null ? $request->url : $referer;
        $request->values = ['type' => $type, '_' => $token];
        \Typecho\Request::$instance = $request;
        \Widget\User::$admin = $admin;
        $db = new \Typecho\Db();
        $db->options = ['plugin:AAEditor' => 'current-options'];
        if ($hasBackup) $db->options['plugin:AAEditorBackup'] = 'backup-options';
        \Typecho\Db::$instance = $db;
        $form = new \Typecho\Widget\Helper\Form();
        $exception = null;
        ob_start();
        try {
            \TypechoPlugin\AAEditor\Plugin::config($form);
            $form->render();
        } catch (RuntimeException $error) {
            $exception = $error;
        }
        $html = ob_get_clean();
        return [$db, $form, $html, $exception];
    }

    $url = (new \Typecho\Request())->url;
    $token = (new \Widget\Security())->getToken($url);
    foreach (['backup', 'restore', 'delete'] as $type) {
        foreach ([['GET', $token], ['GET', null], ['POST', null], ['POST', 'wrong-token']] as $invalid) {
            $result = runConfig($invalid[0], $type, $invalid[1]);
            check(!$result[0]->writes && $result[3] !== null, $type . ' rejects invalid request');
        }
        foreach ([['https://foreign.test/', true], [$url, false]] as $invalidContext) {
            $result = runConfig('POST', $type, $token, true, $invalidContext[0], $invalidContext[1]);
            check(!$result[0]->writes && $result[3] !== null, $type . ' rejects invalid context');
        }
        $result = runConfig('POST', $type, $token);
        check(count($result[0]->writes) === 1 && $result[3] instanceof Redirect, $type . ' accepts protected POST');
        if ($type === 'backup') check($result[0]->options['plugin:AAEditorBackup'] === 'current-options', 'backup data');
        if ($type === 'restore') check($result[0]->options['plugin:AAEditor'] === 'backup-options', 'restore data');
        if ($type === 'delete') check(!isset($result[0]->options['plugin:AAEditorBackup']), 'delete data');
    }
    $result = runConfig('POST', 'backup', $token, false);
    check($result[0]->writes === ['insert'] && $result[0]->options['plugin:AAEditorBackup'] === 'current-options', 'new backup');

    foreach ([true, false] as $hasBackup) {
        $result = runConfig('GET', null, null, $hasBackup);
        check(!$result[0]->writes && $result[3] === null, 'configuration rendering does not write');
        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . $result[2]);
        $xpath = new DOMXPath($dom);
        check($xpath->query('//form')->length === 4 && $xpath->query('//form//form')->length === 0, 'independent forms');
        foreach (['backup', 'restore', 'delete'] as $type) {
            $form = $xpath->query('//form[@id="aaeditor-backup-' . $type . '"]')->item(0);
            check($form && $form->getAttribute('method') === 'post', $type . ' POST form');
            check($form->getAttribute('action') === $url, $type . ' form endpoint');
            check($xpath->query('.//input[@name="type"]', $form)->item(0)->getAttribute('value') === $type, $type . ' operation');
            check($xpath->query('.//input[@name="_"]', $form)->item(0)->getAttribute('value') === $token, $type . ' form token');
            check($xpath->query('.//span', $form)->item(0)->getAttribute('onclick') === 'this.parentNode.submit();', $type . ' submit control');
        }
        $warning = $xpath->query('//a[@href="#aaeditor-backup-backup"]');
        check($warning->length === ($hasBackup ? 0 : 1), 'warning presence');
        if (!$hasBackup) check(strpos($warning->item(0)->getAttribute('onclick'), "getElementById('aaeditor-backup-backup').submit()") !== false, 'warning submits protected form');
        check($xpath->query('//form[@id="normal-config-form"]//input[@name="XEditorEnabled"]')->length === 1, 'normal settings retained');
        check($result[1]->action === '/action/plugins-edit?config=AAEditor', 'normal save action retained');
    }
    $result = runConfig('POST', null, null);
    check(!$result[0]->writes && $result[3] === null && isset($result[1]->inputs['XEditorEnabled']), 'normal settings POST unaffected');

    if (isset($argv[1]) && $argv[1] === '--html-fixture') {
        echo '<div id="config-host">' . runConfig('GET', null, null, false)[2] . '</div>';
    } else {
        echo "backup CSRF tests passed\n";
    }
}
