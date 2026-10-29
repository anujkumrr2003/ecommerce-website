<?php
/* verify.php - enter the e-mail OTP */
require_once __DIR__ . '/inc/bootstrap.php';
require_once __DIR__ . '/inc/catalog.php';
require_once __DIR__ . '/inc/cart.php';
require_once __DIR__ . '/inc/pricing.php';
require_once __DIR__ . '/inc/account.php';
require_once __DIR__ . '/inc/layout.php';

$uid = (int)($_SESSION['verify_uid'] ?? 0);
$u = $uid ? wc_one('SELECT * FROM web_users WHERE id = ?', 'i', [$uid]) : null;
if (!$u || $u['email_verified_at']) {
    redirect($u ? 'login' : 'register');
}
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_require();
    if (post('do') === 'resend') {
        $e = otp_send($u, 'register');
        flash($e === '' ? 'success' : 'danger', $e === '' ? 'A new code has been sent to your email.' : $e);
        redirect('verify');
    }
    [$ok, $msg] = otp_check($uid, 'register', preg_replace('/\D/', '', post('code', 10)));
    if ($ok) {
        $cid = crm_customer_link($u);
        wc_run('UPDATE web_users SET email_verified_at = NOW(), customer_id = ? WHERE id = ?', 'ii', [$cid, $uid]);
        unset($_SESSION['verify_uid']);
        $u = wc_one('SELECT * FROM web_users WHERE id = ?', 'i', [$uid]);
        login_user($u);
        wc_after_response(function () use ($u) {
            wc_send_template('welcome', $u['email'], ['name' => $u['name']]);
        });
        wc_crm_notify('web_customer', 'New website customer', $u['name'] . ' (' . $u['email'] . ') created an account', 'web-customers.php?id=' . $uid, 'person-plus', 'blue', 'web.customers.view', 'web_user:' . $uid);
        flash('success', 'Your email is verified. Welcome, ' . explode(' ', $u['name'])[0] . '!');
        $next = safe_next((string)($_SESSION['after_login'] ?? ''));
        unset($_SESSION['after_login']);
        redirect($next !== '' ? $next : 'account');
    }
    $err = $msg;
}
$mask = preg_replace('/(?<=.).(?=[^@]*@)/', '*', $u['email']);
page_start(['title' => 'Verify your email', 'robots' => 'noindex']);
?>
<div class="container auth-box"><div class="box"><h1>Verify your email</h1><p class="muted" style="text-align:center">Enter the 6-digit code we sent to <b><?= e($mask) ?></b></p>
  <?= $err !== '' ? '<div class="alert alert-danger">' . e($err) . '</div>' : flash_html() ?>
  <form method="post" action="<?= e(url('verify')) ?>"><?= csrf_field() ?>
    <div class="field"><input class="otp-input" name="code" inputmode="numeric" maxlength="6" autocomplete="one-time-code" placeholder="------" required autofocus></div>
    <button class="btn btn-primary btn-block" type="submit">Verify &amp; continue</button></form>
  <form method="post" action="<?= e(url('verify')) ?>" style="text-align:center;margin-top:16px"><?= csrf_field() ?><input type="hidden" name="do" value="resend"><span class="muted small">Did not get it? Check spam, then </span><button class="link" type="submit">resend code</button></form>
  <p style="text-align:center;margin:12px 0 0"><a class="small" href="<?= e(url('register')) ?>">Use a different email</a></p></div></div>
<?php page_end();
