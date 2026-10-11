<?php

define('__TYPECHO_ROOT_DIR__', dirname(__DIR__));
require_once dirname(__DIR__) . '/Util.php';

use TypechoPlugin\AAEditor\Util;

$cases = [
    'public paragraphs' => '<p>PUBLIC_BEFORE</p><div class="fence fence-pass-comment"><div class="fence-title">PASS</div><div class="fence-content">SECRET_BODY</div></div><p>PUBLIC_AFTER</p>',
    'multiple fences' => '<p>PUBLIC_BEFORE</p><div class="fence-pass-comment"><div class="fence-content">SECRET_ONE</div></div><p>PUBLIC_AFTER</p><div class="fence-pass-comment"><div class="fence-content">SECRET_TWO</div></div>',
    'nested fences' => '<p>PUBLIC_BEFORE</p><div class="fence-pass-comment"><div class="fence-content">SECRET_OUTER<div class="fence-pass-comment"><div class="fence-title">SECRET_TITLE</div><div class="fence-content">SECRET_INNER</div></div></div></div><p>PUBLIC_AFTER</p>',
    'missing content container' => '<p>PUBLIC_BEFORE</p><div class="fence-pass-comment">SECRET_BODY</div><p>PUBLIC_AFTER</p>',
    'empty fence' => '<p>PUBLIC_BEFORE</p><div class="fence-pass-comment"></div><p>PUBLIC_AFTER</p>',
];

foreach ($cases as $name => $html) {
    // 摘要无需访问用户、文章或数据库，已登录用户的摘要也不应暴露隐藏内容。
    $result = html_entity_decode(Util::processFencesBlock($html, null, true), ENT_QUOTES, 'UTF-8');
    if (strpos($result, 'SECRET') !== false
        || strpos($result, 'PUBLIC_BEFORE') === false
        || strpos($result, 'PUBLIC_AFTER') === false
        || strpos($result, '【内容回复可见】') === false) {
        throw new RuntimeException('Failed: ' . $name);
    }
}

$plain = '<p>PUBLIC_BEFORE</p><p>PUBLIC_AFTER</p>';
if (Util::processFencesBlock($plain, null, true) !== $plain) {
    throw new RuntimeException('Failed: unchanged public content');
}

echo "pass-comment excerpt tests passed\n";
