<?php
/* register.php - create an account; an OTP is e-mailed to verify the address */
require_once __DIR__ . '/inc/bootstrap.php';
require_once __DIR__ . '/inc/catalog.php';
require_once __DIR__ . '/inc/cart.php';
require_once __DIR__ . '/inc/pricing.php';
require_once __DIR__ . '/inc/account.php';
require_once __DIR__ . '/inc/layout.php';

if (user()) {
    redirect('account');
}
$err = '';
$f = ['name' => '', 'email' => '', 'mobile' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_require();
    $f = ['name' => post('name', 120), 'email' => strtolower(post('email', 190)), 'mobile' => preg_replace('/\D+/', '', post('mobile', 15))];
    $pass = (string)($_POST['password'] ?? '');
    if (strlen($f['mobile']) > 10) {
        $f['mobile'] = substr($f['mobile'], -10);
    }
    if (post('website') !== '') {                                         // honeypot: bots fill hidden fields
        redirect('');
    }
    if (!wc_throttle('reg:' . wc_ip(), 10, 3600)) {
        $err = 'Too many attempts. Please try again later.';
    } elseif (mb_strlen($f['name']) < 2) {
        $err = 'Please enter your full name.';
    } elseif (!filter_var($f['email'], FILTER_VALIDATE_EMAIL)) {
        $err = 'Please enter a valid email address.';
    } elseif (!preg_match('/^[6-9][0-9]{9}$/', $f['mobile'])) {
        $err = 'Please enter a valid 10 digit mobile number.';
    } elseif (($pe = password_error($pass)) !== '') {
        $err = $pe;
    } elseif ($pass !== (string)($_POST['password2'] ?? '')) {
        $err = 'Passwords do not match.';
    } elseif (empty($_POST['terms'])) {
        $err = 'Please accept the Terms and Privacy Policy.';
    } else {
        $ex = wc_one('SELECT * FROM web_users WHERE email = ?', 's', [$f['email']]);
        if ($ex && $ex['email_verified_at']) {
            $err = 'An account with this email already exists. Please log in.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            if ($ex) {
                wc_run('UPDATE web_users SET name = ?, mobile = ?, password_hash = ? WHERE id = ?', 'sssi', [$f['name'], $f['mobile'], $hash, (int)$ex['id']]);
                $uid = (int)$ex['id'];
            } else {
                wc_run('INSERT INTO web_users (name, email, mobile, password_hash) VALUES (?,?,?,?)', 'ssss', [$f['name'], $f['email'], $f['mobile'], $hash]);
                $uid = wc_insert_id();
            }
            $row = wc_one('SELECT * FROM web_users WHERE id = ?', 'i', [$uid]);
            $e2 = otp_send($row, 'register');
            if ($e2 !== '' && !$ex) {
                $err = $e2;
            } else {
                $_SESSION['verify_uid'] = $uid;
                wc_log_activity($uid, 'register', '');
                flash('info', $e2 === '' ? 'We have sent a 6-digit code to ' . $f['email'] . '.' : $e2);
                redirect('verify');
            }
        }
    }
}
page_start(['title' => 'Create account', 'robots' => 'noindex']);
?>
<div class="container auth-box"><div class="box"><h1>Create account</h1><p class="muted" style="text-align:center">Track orders, save favourites and checkout faster.</p>
  <?= $err !== '' ? '<div class="alert alert-danger">' . e($err) . '</div>' : flash_html() ?>
  <form method="post" action="<?= e(url('register')) ?>" novalidate><?= csrf_field() ?>
    <div style="position:absolute;left:-9999px" aria-hidden="true"><input name="website" tabindex="-1" autocomplete="off"></div>
    <div class="field"><label for="n">Full name</label><input id="n" name="name" value="<?= e($f['name']) ?>" autocomplete="name" required></div>
    <div class="field"><label for="e">Email address</label><input id="e" type="email" name="email" value="<?= e($f['email']) ?>" autocomplete="email" required></div>
    <div class="field"><label for="m">Mobile number</label><input id="m" name="mobile" value="<?= e($f['mobile']) ?>" inputmode="numeric" maxlength="12" autocomplete="tel" required></div>
    <div class="row2"><div class="field"><label for="p">Password</label><input id="p" type="password" name="password" autocomplete="new-password" required></div><div class="field"><label for="p2">Confirm</label><input id="p2" type="password" name="password2" autocomplete="new-password" required></div></div>
    <p class="help" style="margin-top:-6px">At least 8 characters, with letters and numbers.</p>
    <label class="check" style="margin:12px 0 18px"><input type="checkbox" name="terms" value="1"> <span>I agree to the <a href="<?= e(url('page/terms-and-conditions')) ?>" target="_blank"><u>Terms</u></a> and <a href="<?= e(url('page/privacy-policy')) ?>" target="_blank"><u>Privacy Policy</u></a></span></label>
    <button class="btn btn-primary btn-block" type="submit">Create account</button>
  </form><p style="text-align:center;margin:18px 0 0">Already have an account? <a href="<?= e(url('login')) ?>"><b>Log in</b></a></p></div></div>
<?php page_end();
