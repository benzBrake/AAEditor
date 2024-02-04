<?php
if (!isset($_GET['url'])) {
    return http_response_code(404);
}

require "./Favicon.php";
$favicon = new \Jerrybendy\Favicon\Favicon;
$defaultIco = 'assets/images/favicon/' . mt_rand(1, 10) . '.png';   // 默认图标路径
$expire = 2592000;           // 缓存有效期30天, 单位为:秒，为0时不缓存

$favicon->setDefaultIcon($defaultIco);

$url = $_GET['url'];

$formatUrl = $favicon->formatUrl($url);
if ($expire == 0) {
    $favicon->getFavicon($formatUrl, false);
    exit;
} else {
    $defaultMD5 = md5(file_get_contents($defaultIco));
    $data = Cache::get($formatUrl, $defaultMD5, $expire);
    if ($data !== NULL) {
        foreach ($favicon->getHeader() as $header) {
            @header($header);
        }
        echo $data;
        exit;
    }

    $content = $favicon->getFavicon($formatUrl, TRUE);
    if (md5($content) == $defaultMD5) {
        $expire = 43200; //如果返回默认图标，设置过期时间为12小时。
    }
    Cache::set($formatUrl, $content, $expire);
    foreach ($favicon->getHeader() as $header) {
        @header($header);
    }
    echo $content;
    exit;
}

//缓存类
class Cache
{
    public static function get($key, $default, $expire)
    {
        $dir = 'cache'; //图标缓存目录
        //$f = md5( strtolower( $key ) );
        $f = parse_url($key)['host'];
        $a = $dir . '/' . md5($f) . '.txt';
        if (is_file($a)) {
            $data = file_get_contents($a);
            if (md5($data) == $default) {
                $expire = 43200; //如果返回默认图标，过期时间为12小时。
            }
            if ((time() - filemtime($a)) > $expire) {
                return null;
            } else {
                return $data;
            }
        } else {
            return null;
        }
    }

    //设置缓存
    public static function set($key, $value, $expire)
    {
        $dir = 'cache'; //图标缓存目录
        //$f = md5( strtolower( $key ) );
        $f = parse_url($key)['host'];
        $a = $dir . '/' . md5($f) . '.txt';
        //如果缓存目录不存在则创建
        if (!is_dir($dir)) mkdir($dir, 0777, true) or die('创建缓存目录失败！');
        if (!is_file($a) || (time() - filemtime($a)) > $expire) {
            $imgdata = fopen($a, "w") or die("Unable to open file!");
            fwrite($imgdata, $value);
            fclose($imgdata);
        }
    }
}
