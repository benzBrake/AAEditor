<?php

use TypechoPlugin\AAEditor\HyperDown;
use TypechoPlugin\AAEditor\Util;

define('__TYPECHO_ROOT_DIR__', dirname(__DIR__));
require_once dirname(__DIR__) . '/Util.php';
require_once dirname(__DIR__) . '/HyperDown.php';

$cases = [
    'direct image and caption' => [
        '<img src="one.jpg" alt="first"><p>Caption</p>',
        2, ['one.jpg'],
    ],
    'direct images only' => [
        '<img src="one.jpg"><img src="two.jpg">',
        2, ['one.jpg', 'two.jpg'],
    ],
    'mixed inline and block content' => [
        'Before<img src="one.jpg"><p>Caption</p><img src="two.jpg">After',
        5, ['one.jpg', 'two.jpg'],
    ],
    'empty media elements' => [
        '<img src="one.jpg"><video src="clip.mp4"></video><iframe src="frame.html"></iframe>',
        3, ['one.jpg'],
    ],
];

foreach ($cases as $name => $case) {
    list($content, $expectedItems, $expectedImages) = $case;
    $html = '<div class="fence-masonry"><div class="fence-content">' . $content . '</div></div>';
    $result = Util::processFencesBlock($html, null, true);
    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="UTF-8">' . $result, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    $xpath = new DOMXPath($dom);
    if ($xpath->query('//div[@class="masonry-item"]')->length !== $expectedItems) {
        throw new RuntimeException('Failed item count: ' . $name);
    }
    $images = [];
    foreach ($xpath->query('//div[@class="masonry-item-inner"]//img') as $image) {
        $images[] = $image->getAttribute('src');
    }
    if ($images !== $expectedImages) {
        throw new RuntimeException('Failed image identity or order: ' . $name);
    }
    foreach (['Before', 'Caption', 'After', 'clip.mp4', 'frame.html', 'alt="first"'] as $literal) {
        if (strpos($html, $literal) !== false && strpos($result, $literal) === false) {
            throw new RuntimeException('Failed content preservation: ' . $name . ' / ' . $literal);
        }
    }
    $again = Util::processFencesBlock($result, null, true);
    if (substr_count($again, 'class="masonry-wrapper"') !== 1) {
        throw new RuntimeException('Failed repeated normalization: ' . $name);
    }
}

$markdown = "::: masonry\n![one](https://example.test/one.jpg)\n\n![two](https://example.test/two.jpg)\n:::";
$result = Util::processFencesBlock((new HyperDown())->makeHtml($markdown), null, true);
if (substr_count($result, 'class="masonry-item"') !== 2 || substr_count($result, '<img ') !== 2) {
    throw new RuntimeException('Failed Markdown image gallery');
}
echo "masonry content tests passed\n";
