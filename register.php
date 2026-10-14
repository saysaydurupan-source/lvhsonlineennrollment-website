<?php $title = 'Student sign up';
require 'includes/header.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  check_csrf();
  $u = trim($_POST['username'] ?? '');
  $m = trim($_POST['email'] ?? '');
  $p = $_POST['password'] ?? '';
  if (!preg_match('/^[A-Za-z0-9_.]{4,30}$/', $u))
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
      $pdo->prepare("INSERT INTO users(username,email,password_hash,role) VALUES(?,?,?,'student')")->execute([$u, $m, password_hash($p, PASSWORD_DEFAULT)]);
      flash('Student account created. You can now sign in.');
      header('Location: login.php');
      exit;
    }
  }
  header('Location: register.php');
  exit;
} ?>
<form class="panel narrow" method="post" data-busy><input type="hidden" name="csrf" value="<?= csrf() ?>">
  <h1>Student sign up</h1>
  <label for="u">Name</label><input id="u" name="username" required autocomplete="username">
  <label for="m">Email</label><input id="m" name="email" type="email" required>
  <label for="p">Password</label><input id="p" name="password" type="password" minlength="8" required
    autocomplete="new-password"><br>
  <button class="btn" type="submit">Create student account</button>
  <p>Already registered? <a href="login.php">Student sign in</a></p>
  <p class="swap">Are you the registrar? <a href="registrar_register.php">Registrar sign up</a></p>
</form>
<?php require 'includes/footer.php'; ?>