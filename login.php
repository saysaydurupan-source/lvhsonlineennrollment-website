<?php $title = 'Student sign in';
require 'includes/header.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  check_csrf();
  $s = $pdo->prepare("SELECT * FROM users WHERE username=? AND role='student'");
  $s->execute([trim($_POST['username'] ?? '')]);
  $u = $s->fetch();
  if ($u && password_verify($_POST['password'] ?? '', $u['password_hash'])) {
    session_regenerate_id(true);
    $_SESSION['uid'] = $u['id'];
    $_SESSION['role'] = 'student';
    $_SESSION['name'] = $u['username'];
    header('Location: dashboard.php');
    exit;
  }
  flash('Wrong username or password.', 'err');
  header('Location: login.php');
  exit;
} ?>
<form class="panel narrow" method="post" data-busy><input type="hidden" name="csrf" value="<?= csrf() ?>">
  <h1>Student sign in</h1>
  <label for="u">Name</label><input id="u" name="username" required autocomplete="username">
  <label for="p">Password</label><input id="p" name="password" type="password" required
    autocomplete="current-password"><br>
  <button class="btn" type="submit">Sign in</button>
  <p>No account yet? <a href="register.php">Create a student account</a></p>
  <p class="swap">Are you the registrar? <a href="registrar_login.php">Registrar sign in</a></p>
</form>
<?php require 'includes/footer.php'; ?>