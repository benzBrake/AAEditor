<?php

require_once dirname(__DIR__) . '/HyperDown.php';

use TypechoPlugin\AAEditor\HyperDown;

$cases = json_decode(file_get_contents(__DIR__ . '/image-attributes.json'), true);
if (!is_array($cases)) {
    throw new RuntimeException('Invalid image test fixtures');
}

foreach ($cases as $case) {
    $html = (new HyperDown())->makeHtml($case['markdown']);
    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    $images = $dom->getElementsByTagName('img');
    if ($images->length !== 1) {
        throw new RuntimeException('Failed image count: ' . $case['name']);
    }
    $image = $images->item(0);
    // 按浏览器的一次 HTML 实体解析检查实际属性，不重复解码用户文本。
    if ($image->attributes->length !== 3) {
        throw new RuntimeException('Unexpected image attribute: ' . $case['name']);
    }
    foreach (['src', 'alt', 'title'] as $attribute) {
        if (!$image->hasAttribute($attribute) || $image->getAttribute($attribute) !== $case[$attribute]) {
            throw new RuntimeException('Failed image ' . $attribute . ': ' . $case['name']);
        }
    }
}

echo "image attribute tests passed\n";
