<?php $title = 'Registrar sign up';
require 'includes/header.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  check_csrf();
  $u = trim($_POST['username'] ?? '');
  $m = trim($_POST['email'] ?? '');
  $p = $_POST['password'] ?? '';
  $c = $_POST['code'] ?? '';
  if (!hash_equals(REGISTRAR_CODE, $c))
    flash('Wrong registrar access code.', 'err');
  elseif (!preg_match('/^[A-Za-z0-9_.]{4,30}$/', $u))
    flash('Username must be 4-30 letters, numbers, dots or underscores.', 'err');
  elseif (!filter_var($m, FILTER_VALIDATE_EMAIL))
    flash('Enter a valid email address.', 'err');
  elseif (strlen($p) < 8)
    flash('Password must be at least 8 characters.', 'err');
  else {
    $x = $pdo->prepare('SELECT 1 FROM users WHERE username=? OR email=?');
    $x->execute([$u, $m]);
    if ($x->fetch())
      flash('That username or email is already registered.', 'err');
    else {
      $pdo->prepare("INSERT INTO users(username,email,password_hash,role) VALUES(?,?,?,'registrar')")->execute([$u, $m, password_hash($p, PASSWORD_DEFAULT)]);
      flash('Registrar account created. You can now sign in.');
      header('Location: registrar_login.php');
      exit;
    }
  }
  header('Location: registrar_register.php');
  exit;
} ?>
<form class="panel narrow" method="post" data-busy><input type="hidden" name="csrf" value="<?= csrf() ?>">
  <h1>Registrar sign up</h1>
  <label for="u">Username</label><input id="u" name="username" required autocomplete="username">
  <label for="m">Email</label><input id="m" name="email" type="email" required>
  <label for="p">Password</label><input id="p" name="password" type="password" minlength="8" required
    autocomplete="new-password">
  <label for="c">Registrar access code</label><input id="c" name="code" type="password" required autocomplete="off"><br>
  <button class="btn" type="submit">Create registrar account</button>
  <p>Already have one? <a href="registrar_login.php">Registrar sign in</a></p>
  <p class="swap">Are you a student? <a href="register.php">Student sign up</a></p>
</form>
<?php require 'includes/footer.php'; ?>