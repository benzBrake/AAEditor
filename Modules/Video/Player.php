<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8"/>
    <meta name="renderer" content="webkit"/>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, shrink-to-fit=no, viewport-fit=cover"/>
    <title>H.265 Video Player</title>
    <link href="plyr.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
            outline: none;
            text-decoration: none;
        }

        html, body, #player {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
    </style>
</head>

<body>
<?php
function getMimeTypeFromUrl($url)
{
    $headers = @get_headers($url, 1);
    if ($headers && strpos($headers[0], '200')) {
        return $headers['Content-Type'] ?? null;
    }
    return null;
}

function isYoutube($url): bool
{
    return strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false;
}

function convertYoutubeUrlToEmbedUrl($url): ?string
{
    $from = (isSecure() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    $parsedUrl = parse_url($url);

    if ($parsedUrl['host'] === 'youtu.be') {
        $videoId = ltrim($parsedUrl['path'], '/');
    } elseif (strpos($parsedUrl['host'], 'youtube.com') !== false) {
        parse_str($parsedUrl['query'], $queryParams);
        $videoId = $queryParams['v'] ?? null;
    } else {
        return null;
    }

    return "https://www.youtube.com/embed/" . $videoId . "?" . http_build_query([
            'origin' => $from,
            'iv_load_policy' => 3,
            'modestbranding' => 1,
            'playsinline' => 1,
            'showinfo' => 0,
            'rel' => 0,
            'enablejsapi' => 1
        ]);
}

function isSecure(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
}
?>

<?php if (isset($_GET['url'])): ?>
    <?php if (isYoutube($_GET['url'])): ?>
    <div class="plyr__video-embed" id="player">
        <iframe
                src="<?php echo htmlspecialchars(convertYoutubeUrlToEmbedUrl($_GET['url'])); ?>"
                allowfullscreen
                allowtransparency
            <?php if (!empty($_GET['autoplay'])): ?> allow="autoplay" <?php endif; ?>
        ></iframe>
    </div>
<?php else: ?>
    <video id="player" playsinline controls <?php if (isset($_GET['poster'])): ?> data-poster="<?php echo htmlspecialchars($_GET['poster']); ?>" <?php endif; ?>>
        <source src="<?php echo htmlspecialchars($_GET['url']); ?>" type="<?php echo htmlspecialchars($_GET['mime'] ?? getMimeTypeFromUrl($_GET['url']) ?? 'video/mp4'); ?>"/>
        <?php if (isset($_GET['caption'])): ?>
            <track kind="captions" label="字幕" src="<?php echo htmlspecialchars($_GET['caption']); ?>" srclang="<?php echo htmlspecialchars($_GET['caption-lang'] ?? 'en'); ?>" default/>
        <?php endif; ?>
    </video>
    <script src="plyr.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new Plyr('#player', {
                controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
                autoplay: <?php echo json_encode(!empty($_GET["autoplay"])); ?>,
                keyboard: {
                    focused: true,
                    global: false
                },
                tooltips: {
                    controls: true
                },
                hideControls: false
            });
        });
    </script>
<?php endif; ?>
<?php else: ?>
    <h1>请提供视频URL</h1>
<?php endif; ?>
</body>
</html>
