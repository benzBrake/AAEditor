<?php

namespace Widget {
    class User
    {
        public static $allowed = false;

        public static function alloc()
        {
            return new self();
        }

        public function pass($role, $return)
        {
            return self::$allowed;
        }

        public function hasLogin()
        {
            return false;
        }
    }
}

namespace Typecho {
    class Cookie
    {
        public static function get($name)
        {
            return null;
        }
    }

    class Db
    {
        public static function get()
        {
            return new self();
        }

        public function select()
        {
            throw new \RuntimeException('The test must not access a database');
        }
    }
}

namespace {
    use TypechoPlugin\AAEditor\HyperDown;
    use TypechoPlugin\AAEditor\Util;
    use Widget\User;

    define('__TYPECHO_ROOT_DIR__', dirname(__DIR__));
    require_once dirname(__DIR__) . '/Util.php';
    require_once dirname(__DIR__) . '/HyperDown.php';

    function _t($message, ...$args)
    {
        return $args ? vsprintf($message, $args) : $message;
    }

    function codeTexts($html)
    {
        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $texts = [];
        foreach ((new DOMXPath($dom))->query('//pre | //code') as $node) {
            $texts[] = $node->textContent;
        }
        return $texts;
    }

    $archive = new class {
        public $cid = 1;
        public $permalink = 'https://example.test/post';

        public function remember($name, $return)
        {
            return '';
        }
    };

    $cases = [
        'markdown reproducer' => (new HyperDown())->makeHtml("::: pass:comment\n`SECRET_CODE`\n:::\n\nVisible `PUBLIC_CODE`"),
        'multiple hidden blocks' => '<p><code>PUBLIC_BEFORE</code></p><div class="fence-pass-comment"><div class="fence-content"><code>SECRET_ONE</code></div></div><p><code>PUBLIC_CODE</code></p><div class="fence-pass-comment"><div class="fence-content"><pre>SECRET_TWO</pre></div></div><p><code>PUBLIC_AFTER</code></p>',
        'nested hidden blocks' => '<div class="fence-pass-comment"><div class="fence-content"><code>SECRET_OUTER</code><div class="fence-pass-comment"><div class="fence-content"><code>SECRET_INNER</code></div></div></div></div><p><pre>PUBLIC_CODE</pre></p>',
        'literal placeholder collision' => '<pre>__BLOCK__</pre><pre>__AAEDITOR_CODE_0__</pre><pre>&#95;&#95;AAEDITOR_CODE__0__</pre><div class="fence-pass-comment"><div class="fence-content"><code>SECRET_CODE</code></div></div><p><code>PUBLIC_CODE</code></p>',
        'DOM-normalized placeholder collision' => '<div class="fence-pass-comment"><div class="fence-content"><code>SECRET_ATTACK</code></div></div><pre >&#95&#95AAEDITOR_CODE_0__</pre><p><code>PUBLIC_CODE</code></p>',
    ];

    foreach ($cases as $name => $html) {
        foreach ([false, true] as $allowed) {
            User::$allowed = $allowed;
            $content = html_entity_decode(Util::contentEx($html, $archive, null), ENT_QUOTES, 'UTF-8');
            $excerpt = html_entity_decode(Util::excerptEx($html, $archive, null), ENT_QUOTES, 'UTF-8');
            $originalCodes = codeTexts($html);
            $publicCodes = array_values(array_filter($originalCodes, function ($code) {
                return strpos($code, 'SECRET') === false;
            }));
            if (codeTexts($content) !== ($allowed ? $originalCodes : $publicCodes)
                || codeTexts($excerpt) !== $publicCodes) {
                throw new RuntimeException('Failed code identity or order: ' . $name);
            }
            if (strpos($content, 'PUBLIC_CODE') === false
                || strpos($excerpt, 'PUBLIC_CODE') === false
                || strpos($excerpt, 'SECRET') !== false
                || (strpos($content, 'SECRET') !== false) !== $allowed) {
                throw new RuntimeException('Failed visibility: ' . $name);
            }
            foreach (['PUBLIC_BEFORE', 'PUBLIC_AFTER', '__BLOCK__', '__AAEDITOR_CODE_0__', '__AAEDITOR_CODE__0__'] as $literal) {
                if (strpos(html_entity_decode($html, ENT_QUOTES, 'UTF-8'), $literal) !== false
                    && (strpos($content, $literal) === false || strpos($excerpt, $literal) === false)) {
                    throw new RuntimeException('Failed public content: ' . $name . ' / ' . $literal);
                }
            }
            if ($allowed) {
                foreach (['SECRET_CODE', 'SECRET_ONE', 'SECRET_TWO', 'SECRET_OUTER', 'SECRET_INNER'] as $secret) {
                    if (strpos($html, $secret) !== false && strpos($content, $secret) === false) {
                        throw new RuntimeException('Failed authorized content: ' . $name);
                    }
                }
            }
        }
    }

    $plain = '<p><code>[x] __BLOCK__</code></p><pre>[ ] PUBLIC_CODE</pre>';
    if (Util::contentEx($plain, $archive, null) !== $plain || Util::excerptEx($plain, $archive, null) !== $plain) {
        throw new RuntimeException('Failed unchanged public code');
    }

    echo "code-block visibility tests passed\n";
}
