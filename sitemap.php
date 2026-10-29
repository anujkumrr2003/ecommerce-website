<?php
/* sitemap.php -> /sitemap.xml */
define('SHOP_NO_HOUSEKEEPING', true);
require_once __DIR__ . '/inc/bootstrap.php';
require_once __DIR__ . '/inc/catalog.php';
header('Content-Type: application/xml; charset=utf-8');
$u = function (string $path, ?string $mod = null, string $freq = 'weekly', string $pr = '0.6') {
    return '<url><loc>' . e(abs_url($path)) . '</loc>' . ($mod ? '<lastmod>' . date('Y-m-d', strtotime($mod)) . '</lastmod>' : '') . '<changefreq>' . $freq . '</changefreq><priority>' . $pr . '</priority></url>';
};
echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
echo $u('', null, 'daily', '1.0') . $u('shop', null, 'daily', '0.9') . $u('faq', null, 'monthly', '0.4') . $u('contact', null, 'monthly', '0.4');
foreach (categories_all() as $c) {
    echo $u('category/' . $c['slug'], null, 'daily', '0.8');
}
[$rows] = products_list(['per' => 60, 'page' => 1]);
for ($pg = 1; $pg <= 100; $pg++) {
    [$rows] = products_list(['per' => 60, 'page' => $pg]);
    if (!$rows) {
        break;
    }
    foreach ($rows as $p) {
        echo $u(substr(product_url($p), strlen(BASE_PATH) + 1), $p['created_at'], 'weekly', '0.7');
    }
}
foreach (wc_all('SELECT slug, updated_at FROM web_pages WHERE is_active = 1') as $p) {
    echo $u('page/' . $p['slug'], $p['updated_at'], 'monthly', '0.3');
}
echo '</urlset>';
