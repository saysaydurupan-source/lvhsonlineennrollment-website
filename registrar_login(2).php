<?php $title = 'Registrar sign in';
require 'includes/header.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  check_csrf();
  $s = $pdo->prepare("SELECT * FROM users WHERE username=? AND role='registrar'");
  $s->execute([trim($_POST['username'] ?? '')]);
  $u = $s->fetch();
  if ($u && password_verify($_POST['password'] ?? '', $u['password_hash'])) {
    session_regenerate_id(true);
    $_SESSION['uid'] = $u['id'];
    $_SESSION['role'] = 'registrar';
    $_SESSION['name'] = $u['username'];
    header('Location: registrar.php');
    exit;
  }
  flash('Wrong registrar username or password.', 'err');
  header('Location: registrar_login.php');
  exit;
} ?>
<form class="panel narrow" method="post" data-busy><input type="hidden" name="csrf" value="<?= csrf() ?>">
  <h1>Registrar sign in</h1>
  <label for="u">Name</label><input id="u" name="username" required autocomplete="username">
  <label for="p">Password</label><input id="p" name="password" type="password" required
    autocomplete="current-password"><br>
  <button class="btn" type="submit">Sign in as registrar</button>
  <p>New registrar? <a href="registrar_register.php">Registrar sign up</a></p>
  <p class="swap">Are you a student? <a href="login.php">Student sign in</a></p>
</form>
<?php require 'includes/footer.php'; ?>