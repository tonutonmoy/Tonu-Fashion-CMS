<?php

$publicPath = getcwd();

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

if ($uri !== '/' && file_exists($publicPath.$uri)) {
    $ext = strtolower(pathinfo($uri, PATHINFO_EXTENSION));
    $cacheable = ['css', 'js', 'mjs', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico', 'woff', 'woff2', 'ttf'];

    if (in_array($ext, $cacheable, true)) {
        header('Cache-Control: public, max-age=31536000, immutable');
    }

    return false;
}

require_once $publicPath.'/index.php';
