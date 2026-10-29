<?php
/* wishlist.php */
require_once __DIR__ . '/inc/bootstrap.php';
require_once __DIR__ . '/inc/catalog.php';
require_once __DIR__ . '/inc/cart.php';
require_once __DIR__ . '/inc/pricing.php';
require_once __DIR__ . '/inc/account.php';
require_once __DIR__ . '/inc/layout.php';

$u = user();
$rows = [];
if ($u) {
    foreach (wishlist_ids() as $id) {
        if ($p = product_get((int)$id)) {
            $rows[] = $p;
        }
    }
}
page_start(['title' => 'Wishlist', 'robots' => 'noindex']);
echo breadcrumb(['Wishlist' => '']);
?>
<div class="container page-pad"><h1>My wishlist</h1>
<?php if (!$u): ?><div class="empty-big"><i class="bi bi-heart"></i><h3>Save the pieces you love</h3><p>Log in to keep a wishlist and find it on any device.</p><a class="btn btn-primary" href="<?= e(url('login')) ?>">Log in</a> <a class="btn btn-ghost" href="<?= e(url('register')) ?>">Create account</a></div>
<?php elseif (!$rows): ?><div class="empty-big"><i class="bi bi-heart"></i><h3>Your wishlist is empty</h3><p>Tap the heart on any product to save it here.</p><a class="btn btn-primary" href="<?= e(url('shop')) ?>">Discover products</a></div>
<?php else: ?><div class="grid-p"><?php foreach ($rows as $r) { echo str_replace('data-wish="', 'data-remove-card data-wish="', product_card($r)); } ?></div><?php endif; ?>
</div>
<?php page_end();
