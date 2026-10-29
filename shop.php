<?php
/* shop.php - product listing: category, search, filters, sorting, pagination */
require_once __DIR__ . '/inc/bootstrap.php';
require_once __DIR__ . '/inc/catalog.php';
require_once __DIR__ . '/inc/cart.php';
require_once __DIR__ . '/inc/pricing.php';
require_once __DIR__ . '/inc/layout.php';

$slug = (string)($_GET['cat'] ?? '');
$cat  = $slug !== '' ? category_by_slug($slug) : null;
if ($slug !== '' && !$cat) {
    not_found('This category does not exist.');
}
$f = [
    'cat' => $slug, 'q' => trim((string)($_GET['q'] ?? '')), 'min' => $_GET['min'] ?? '', 'max' => $_GET['max'] ?? '', 'sizes' => (array)($_GET['size'] ?? []), 'colors' => (array)($_GET['color'] ?? []),
    'avail' => !empty($_GET['avail']), 'sale' => !empty($_GET['sale']), 'flag' => in_array($_GET['flag'] ?? '', ['featured', 'new', 'best'], true) ? $_GET['flag'] : '',
    'sort' => (string)($_GET['sort'] ?? ''), 'page' => (int)($_GET['page'] ?? 1), 'per' => max(4, min(48, (int)S('web_per_page', '12'))),
];
[$rows, $total] = products_list($f);

/* options for the filter panel: sizes / colours that exist in this category */
$opt = ['size' => [], 'color' => []];
foreach (['size', 'color'] as $col) {
    $sql = "SELECT DISTINCT v.$col AS x FROM product_variants v JOIN products p ON p.id = v.product_id LEFT JOIN web_product_meta m ON m.product_id = p.id
             WHERE p.status = 'Active' AND COALESCE(m.show_on_website,1) = 1 AND v.$col <> ''" . ($cat ? ' AND p.category = ?' : '') . " ORDER BY v.$col LIMIT 40";
    $opt[$col] = array_column(wc_all($sql, $cat ? 's' : '', $cat ? [$cat['name']] : []), 'x');
}
$title = $cat ? $cat['name'] : ($f['q'] !== '' ? 'Search: ' . $f['q'] : ($f['flag'] === 'new' ? 'New arrivals' : ($f['sale'] ? 'Offers' : 'All products')));
$params = array_filter(['q' => $f['q'], 'min' => $f['min'], 'max' => $f['max'], 'size' => $f['sizes'], 'color' => $f['colors'], 'avail' => $f['avail'] ? 1 : '', 'sale' => $f['sale'] ? 1 : '', 'flag' => $f['flag'], 'sort' => $f['sort']], fn($v) => $v !== '' && $v !== []);
$action = $cat ? url('category/' . $cat['slug']) : url('shop');
$noindex = ($f['q'] !== '' || $f['sizes'] || $f['colors'] || $f['min'] !== '' || $f['max'] !== '' || $f['page'] > 1) ? 'noindex,follow' : '';

page_start(['title' => $cat ? ($cat['meta_title'] ?: $cat['name']) : $title, 'description' => $cat ? ($cat['meta_description'] ?: $cat['description']) : 'Browse our collection. ' . S('web_tagline'), 'robots' => $noindex,
    'canonical' => abs_url($cat ? 'category/' . $cat['slug'] : 'shop' . ($f['flag'] ? '?flag=' . $f['flag'] : '')), 'class' => 'shop']);
echo breadcrumb($cat ? ['Shop' => url('shop'), $cat['name'] => ''] : ['Shop' => $title === 'All products' ? '' : url('shop'), ...($title === 'All products' ? [] : [$title => ''])]);
echo '<div class="container page-title"><h1>' . e($title) . '</h1>' . ($cat && $cat['description'] !== '' ? '<p>' . e($cat['description']) . '</p>' : '') . '</div>';
?>
<div class="container shop-layout">
  <aside class="filters" aria-label="Filters">
    <form method="get" action="<?= e($action) ?>">
      <?php if ($f['q'] !== ''): ?><input type="hidden" name="q" value="<?= e($f['q']) ?>"><?php endif; ?>
      <?php if ($f['flag'] !== ''): ?><input type="hidden" name="flag" value="<?= e($f['flag']) ?>"><?php endif; ?>
      <h4>Categories</h4>
      <?php foreach (categories_all() as $c): ?><label><a href="<?= e(url('category/' . $c['slug'])) ?>" <?= $cat && $cat['id'] === $c['id'] ? 'style="color:var(--accent);font-weight:600"' : '' ?>><?= e($c['name']) ?> <span class="muted">(<?= (int)$c['n'] ?>)</span></a></label><?php endforeach; ?>
      <h4>Price</h4>
      <div class="row2"><input type="number" name="min" min="0" placeholder="Min" value="<?= e($f['min']) ?>" aria-label="Minimum price"><input type="number" name="max" min="0" placeholder="Max" value="<?= e($f['max']) ?>" aria-label="Maximum price"></div>
      <?php foreach (['size' => 'Size', 'color' => 'Colour'] as $col => $label): if (!$opt[$col]) { continue; } ?>
        <h4><?= $label ?></h4><div class="chips">
        <?php foreach ($opt[$col] as $x): $on = in_array($x, $col === 'size' ? $f['sizes'] : $f['colors'], true); ?>
          <label class="chip<?= $on ? ' on' : '' ?>"><input type="checkbox" name="<?= $col ?>[]" value="<?= e($x) ?>" <?= $on ? 'checked' : '' ?> hidden data-auto-submit><?= e($x) ?></label>
        <?php endforeach; ?></div>
      <?php endforeach; ?>
      <h4>Availability</h4>
      <label><input type="checkbox" name="avail" value="1" <?= $f['avail'] ? 'checked' : '' ?> data-auto-submit> In stock only</label>
      <label><input type="checkbox" name="sale" value="1" <?= $f['sale'] ? 'checked' : '' ?> data-auto-submit> On offer</label>
      <input type="hidden" name="sort" value="<?= e($f['sort']) ?>">
      <button class="btn btn-primary btn-block" type="submit">Apply filters</button>
      <?php if ($params): ?><a class="btn btn-ghost btn-block" href="<?= e($action) ?>">Clear all</a><?php endif; ?>
    </form>
  </aside>
  <section>
    <div class="toolbar"><button class="btn btn-ghost btn-sm filter-toggle" data-filter-toggle><i class="bi bi-sliders"></i> Filters</button>
      <span class="muted"><?= (int)$total ?> product<?= $total === 1 ? '' : 's' ?></span>
      <form method="get" action="<?= e($action) ?>"><?php foreach ($params as $k => $v) { if ($k === 'sort') { continue; } foreach ((array)$v as $vv) { echo '<input type="hidden" name="' . e($k) . (is_array($v) ? '[]' : '') . '" value="' . e($vv) . '">'; } } ?>
        <select name="sort" aria-label="Sort by" data-auto-submit>
          <?php foreach (['' => 'Featured', 'new' => 'Newest', 'popular' => 'Best selling', 'rating' => 'Top rated', 'price_asc' => 'Price: low to high', 'price_desc' => 'Price: high to low', 'name' => 'Name A-Z'] as $k => $l): ?><option value="<?= $k ?>" <?= $f['sort'] === $k ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?>
        </select></form></div>
    <?php if ($rows): ?>
      <div class="grid-p" style="grid-template-columns:repeat(3,1fr)"><?php foreach ($rows as $r) { echo product_card($r); } ?></div>
      <?= pagination($f['page'], $total, $f['per'], $params) ?>
    <?php else: ?>
      <div class="empty-big"><i class="bi bi-search"></i><h3>No products found</h3><p>Try removing a filter or searching for something else.</p><a class="btn btn-primary" href="<?= e(url('shop')) ?>">See all products</a></div>
    <?php endif; ?>
  </section>
</div>
<style>@media(max-width:1100px){.shop .grid-p{grid-template-columns:repeat(2,1fr)!important}}@media(max-width:720px){.shop .grid-p{grid-template-columns:repeat(2,1fr)!important}}</style>
<?php page_end();
