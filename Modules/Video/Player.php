<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8"/>
    <meta name="renderer" content="webkit"/>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, shrink-to-fit=no, viewport-fit=cover"/>
    <title>H.265 Video Player</title>
    <link href="https://cdn.bootcdn.net/ajax/libs/plyr/3.7.8/plyr.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
            outline: none;
            text-decoration: none;
        }

        html,
        body,
        #player {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
    </style>
</head>
<body>
<video id="player" playsinline
       controls<?php if (isset($_GET['poster'])): ?> data-poster="<?php echo $_GET['poster'] ?>"<?php endif; ?>>
    <source src="<?php echo $_GET['url'] ?>" type="<?php echo $_GET['mime'] ?? 'video/mp4'; ?>"/>
    <!-- Captions are optional -->
    <?php if (isset($_GET['caption'])): ?>
        <track kind="captions" label="<?php echo "字幕"; ?>" src="<?php echo $_GET['caption']; ?>" srclang="<?php echo $_GET['caption-lang'] ?? "en"; ?>" default/>
    <?php endif; ?>
</video>
<script src="https://cdn.bootcdn.net/ajax/libs/plyr/3.7.8/plyr.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const player = new Plyr('#plyr-player', {
            controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
            autoplay: true, // Set to true if you want the video to autoplay
            keyboard: {
                focused: true,
                global: false,
            },
            tooltips: {
                controls: true,
            },
            hideControls: false
        });

        // Add your H.265 video source here
        const source = {
            type: 'video',
            sources: [
                {
                    src: '<?php echo $_GET['url'] ?>',
                    type: 'video/mp4', // Update the MIME type if necessary
                },
            ],
        };

        player.source = source;
    });
</script>
</body>

</html>
