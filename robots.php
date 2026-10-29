<?php
/* robots.php -> /robots.txt */
define('SHOP_NO_HOUSEKEEPING', true);
require_once __DIR__ . '/inc/bootstrap.php';
header('Content-Type: text/plain; charset=utf-8');
echo "User-agent: *\nDisallow: " . BASE_PATH . "/account\nDisallow: " . BASE_PATH . "/cart\nDisallow: " . BASE_PATH . "/checkout\nDisallow: " . BASE_PATH . "/payment\nDisallow: " . BASE_PATH . "/api.php\nDisallow: " . BASE_PATH . "/login\nDisallow: " . BASE_PATH . "/register\n\nSitemap: " . abs_url('sitemap.xml') . "\n";
