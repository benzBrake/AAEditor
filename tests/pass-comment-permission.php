<?php

namespace Widget {
    class User
    {
        public static $admin = false;
        public static $loggedIn = false;
        public $uid = 7;
        public $mail = 'registered@example.test';

        public static function alloc() { return new self(); }
        public function pass($role, $return) { return self::$admin; }
        public function hasLogin() { return self::$loggedIn; }
    }
}

namespace Typecho {
    class Cookie
    {
        public static $values = [];
        public static function get($name) { return self::$values[$name] ?? null; }
        public static function set($name, $value, $expires) { self::$values[$name] = $value; }
    }

    class Db
    {
        public static $rows = [];
        private $conditions = [];
        public static function get() { return new self(); }
        public function select() { return $this; }
        public function from($table) { return $this; }
        public function limit($count) { return $this; }
        public function where($field, $value)
        {
            $this->conditions[substr($field, 0, strpos($field, ' '))] = $value;
            return $this;
        }
        public function fetch($query)
        {
            foreach (self::$rows as $row) {
                foreach ($query->conditions as $field => $value) {
                    if (!isset($row[$field]) || $row[$field] != $value) {
                        continue 2;
                    }
                }
                return $row;
            }
            return false;
        }
    }

    class Plugin
    {
        public static $hooks = [];
        public static function factory($name)
        {
            if (!isset(self::$hooks[$name])) {
                self::$hooks[$name] = new \stdClass();
            }
            return self::$hooks[$name];
        }
    }

    class Common
    {
        public static function url($path, $base) { return $base . $path; }
    }
}

namespace Typecho\Plugin {
    class Exception extends \Exception {}
}

namespace Utils {
    class Helper
    {
        public static $secret = 'test-only-site-secret';
        public static function options()
        {
            return (object) ['secret' => self::$secret, 'adminUrl' => 'https://example.test/admin/'];
        }
        public static function addAction($name, $class) {}
    }
}

namespace {
    use Typecho\Cookie;
    use Typecho\Db;
    use TypechoPlugin\AAEditor\Util;
    use Widget\User;

    define('__TYPECHO_ROOT_DIR__', dirname(__DIR__));
    require_once dirname(__DIR__) . '/Util.php';
    function _t($text, ...$args) { return $args ? vsprintf($text, $args) : $text; }

    $archive = new class {
        public $cid = 1;
        public $permalink = 'https://example.test/post';
        public function remember($name, $return) { return Cookie::get('__typecho_remember_' . $name); }
    };
    $html = '<div class="fence-pass-comment"><div class="fence-content">SECRET_BODY</div></div>';
    function checkPermission($name, $expected)
    {
        global $archive, $html;
        $content = Util::contentEx($html, $archive, null);
        if ((strpos($content, 'SECRET_BODY') !== false) !== $expected) {
            throw new \RuntimeException('Failed: ' . $name);
        }
        if (strpos(Util::excerptEx($html, $archive, null), 'SECRET_BODY') !== false) {
            throw new \RuntimeException('Failed excerpt: ' . $name);
        }
    }

    Util::activate();
    $hook = \Typecho\Plugin::$hooks['\Widget\Feedback']->finishComment;
    if ($hook !== [Util::class, 'rememberCommentPermission']) {
        throw new \RuntimeException('Failed comment hook registration');
    }
    Db::$rows = [
        ['cid' => 1, 'coid' => 11, 'authorId' => 0, 'mail' => 'registered@example.test', 'type' => 'comment', 'status' => 'approved'],
    ];
    Cookie::$values['__typecho_remember_mail'] = 'registered@example.test';
    checkPermission('forged legacy email', false);
    Cookie::$values['__aaeditor_comment_1'] = '1:11:' . (time() + 300) . ':' . str_repeat('0', 64);
    checkPermission('forged signature', false);
    call_user_func($hook, (object) ['cid' => 1, 'coid' => 11]);
    $valid = Cookie::$values['__aaeditor_comment_1'];
    checkPermission('approved guest comment', true);
    foreach (['waiting', 'spam'] as $status) {
        Db::$rows[0]['status'] = $status;
        checkPermission($status . ' comment', false);
    }
    Db::$rows[0]['status'] = 'approved';
    Db::$rows[0]['type'] = 'trackback';
    checkPermission('trackback', false);
    Db::$rows[0]['type'] = 'comment';
    Db::$rows = [];
    checkPermission('deleted comment', false);
    Db::$rows = [['cid' => 1, 'coid' => 11, 'authorId' => 0, 'type' => 'comment', 'status' => 'approved']];
    Cookie::$values['__aaeditor_comment_1'] = str_replace('1:11:', '1:12:', $valid);
    checkPermission('tampered comment id', false);
    $archive->cid = 2;
    Cookie::$values['__aaeditor_comment_2'] = $valid;
    checkPermission('cross-article replay', false);
    $archive->cid = 1;
    $payload = '1:11:' . (time() - 1);
    Cookie::$values['__aaeditor_comment_1'] = $payload . ':' . hash_hmac('sha256', 'aaeditor-pass-comment:' . $payload, \Utils\Helper::$secret);
    checkPermission('expired signed token', false);
    foreach ([[], '', 'invalid'] as $token) {
        Cookie::$values['__aaeditor_comment_1'] = $token;
        checkPermission('malformed token', false);
    }
    Cookie::$values['__aaeditor_comment_1'] = $valid;
    \Utils\Helper::$secret = 'test-only-rotated-secret';
    checkPermission('rotated site secret', false);
    \Utils\Helper::$secret = 'test-only-site-secret';
    User::$loggedIn = true;
    checkPermission('guest impersonates account email', false);
    Db::$rows[0]['authorId'] = 7;
    checkPermission('authenticated comment author', true);
    Db::$rows[0]['authorId'] = 8;
    checkPermission('another account', false);
    User::$admin = true;
    checkPermission('administrator', true);
    User::$admin = false;
    User::$loggedIn = false;
    \Utils\Helper::$secret = '';
    try {
        Util::contentEx($html, $archive, null);
        throw new \RuntimeException('Missing site secret must fail explicitly');
    } catch (\Typecho\Plugin\Exception $e) {
        // Authorization errors must fail instead of returning unredacted content.
    }
    echo "pass-comment permission tests passed\n";
}
