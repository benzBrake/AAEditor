<?php

require_once dirname(__DIR__) . '/HyperDown.php';

use TypechoPlugin\AAEditor\HyperDown;

$cases = json_decode(file_get_contents(__DIR__ . '/url-attributes.json'), true);
if (!is_array($cases)) {
    throw new RuntimeException('Invalid URL test fixtures');
}
foreach ($cases as $case) {
    $url = $case['url'];
    $paths = [
        ['[label](' . $url . ' "caption")', 'a', 'href', 'caption'],
        ['![label](' . $url . ' "caption")', 'img', 'src', 'caption'],
        ["[label][target]\n\n[target]: " . $url, 'a', 'href', null],
        ["![label][target]\n\n[target]: " . $url, 'img', 'src', 'label'],
    ];
    if (!empty($case['angle']) || !empty($case['autolink'])) {
        $paths[] = ['<' . $url . '>', 'a', 'href', null];
    }
    if (!empty($case['autolink'])) {
        $paths[] = [$url, 'a', 'href', null];
    }
    foreach ($paths as $path) {
        $html = (new HyperDown())->makeHtml($path[0]);
        $dom = new DOMDocument();
        // 自动链接的正文含裸 & 时 libxml 会报告 HTML4 实体警告；仍验证解析结果。
        if (!$dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NOERROR | LIBXML_NOWARNING)) {
            throw new RuntimeException('Failed HTML parsing: ' . $case['name']);
        }
        $elements = $dom->getElementsByTagName($path[1]);
        if ($elements->length !== 1) {
            throw new RuntimeException('Failed URL element count: ' . $case['name']);
        }
        $element = $elements->item(0);
        $actual = $element->getAttribute($path[2]);
        if ($actual !== $case['expected']) {
            throw new RuntimeException('Failed parsed URL: ' . $case['name'] . ' / ' . $path[0]);
        }
        if (preg_match('/^[a-z][a-z0-9+.-]*:/i', $actual) && !preg_match('/^(https?|mailto):/i', $actual)) {
            throw new RuntimeException('Unsafe parsed URL scheme: ' . $case['name']);
        }
        foreach ($element->attributes as $attribute) {
            if (!in_array($attribute->name, [$path[2], 'alt', 'title'], true)) {
                throw new RuntimeException('Unexpected URL attribute: ' . $case['name']);
            }
        }
        if ($path[3] !== null && $element->getAttribute('title') !== $path[3]) {
            throw new RuntimeException('Failed retained title: ' . $case['name']);
        }
        if ($path[1] === 'img' && $element->getAttribute('alt') !== 'label') {
            throw new RuntimeException('Failed retained image description: ' . $case['name']);
        }
    }
}

// Markdown 的行/制表符预处理会先改写这些字符，单独验证 URL 边界清理。
$cleanUrl = new ReflectionMethod(HyperDown::class, 'cleanUrl');
$cleanUrl->setAccessible(true);
foreach (["java\tscript:alert", "java\nscript:alert", "java\rscript:alert", "\0javascript:alert"] as $url) {
    $result = $cleanUrl->invoke(new HyperDown(), $url . ' "caption"', true);
    $dom = new DOMDocument();
    $dom->loadHTML('<a href="' . $result[0] . '">label</a>');
    if ($dom->getElementsByTagName('a')->item(0)->getAttribute('href') !== '#' || $result[1] !== 'caption') {
        throw new RuntimeException('Failed raw control URL sanitization');
    }
}

echo "URL attribute tests passed\n";
