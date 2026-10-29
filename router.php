<?php
/* router.php - ONLY for testing with PHP's built-in server:   php -S localhost:8000 router.php
   (On Apache / Hostinger the .htaccess file does this job.) */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;
if ($path !== '/' && is_file($file) && !preg_match('/\.php$/', $path)) {
    return false;
}
if (preg_match('#^/install/?$#', $path)) {
    chdir(__DIR__ . '/install');
    require __DIR__ . '/install/index.php';
    return true;
}
$map = [
    '#^/product/([0-9]+)(-[^/]*)?/?$#' => 'product.php?id=$1', '#^/category/([a-z0-9-]+)/?$#' => 'shop.php?cat=$1', '#^/page/([a-z0-9-]+)/?$#' => 'page.php?slug=$1',
    '#^/account/?$#' => 'account.php', '#^/account/profile/?$#' => 'account.php?tab=profile', '#^/account/orders/?$#' => 'orders.php', '#^/account/order/([A-Za-z0-9-]+)/?$#' => 'order.php?no=$1',
    '#^/account/addresses/?$#' => 'addresses.php', '#^/order-success/([A-Za-z0-9-]+)/?$#' => 'order-success.php?no=$1', '#^/payment/(pay|verify|failed|webhook)/?$#' => 'payment-$1.php',
    '#^/(shop|cart|checkout|wishlist|login|register|verify|logout|faq|contact|forgot-password)/?$#' => '$1.php', '#^/sitemap\.xml$#' => 'sitemap.php', '#^/robots\.txt$#' => 'robots.php',
];
foreach ($map as $rx => $to) {
    if (preg_match($rx, $path, $m)) {
        $target = preg_replace_callback('/\$(\d)/', fn($x) => $m[(int)$x[1]] ?? '', $to);
        [$script, $qs] = array_pad(explode('?', $target, 2), 2, '');
        parse_str($qs, $extra);
        $_GET = $extra + $_GET;
        $_SERVER['SCRIPT_NAME'] = '/' . $script;
        require __DIR__ . '/' . $script;
        return true;
    }
}
if ($path === '/' || $path === '') {
    require __DIR__ . '/index.php';
    return true;
}
if (is_file($file)) {
    return false;
}
http_response_code(404);
require __DIR__ . '/404.php';
return true;
