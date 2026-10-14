<?php
ob_start();
session_start();

$host = 'db';
$dbname = 'lvhs_enrollment';
$username = 'lvhs_app';
$password = getenv('DB_PASSWORD');

$pdo = new PDO(
    "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
    $username,
    $password,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

// Registrars must type this access code on the registrar sign-up page. CHANGE IT before going live.
const REGISTRAR_CODE = 'LVHS-REGISTRAR-2026';

// Requirements every student must pass. 'Transfer Credential' is only required from transferees.
const DOC_TYPES = ['Birth Certificate', 'Report Card', 'Good Moral Certificate', 'Transfer Credential', 'Other'];
function required_docs($studentType)
{
  $r = ['Birth Certificate', 'Report Card', 'Good Moral Certificate'];
  if ($studentType === 'Transferee')
    $r[] = 'Transfer Credential';
  return $r;
}

// Upgrade older databases: the temporary ID stores the student's picture.
if (!$pdo->query("SHOW COLUMNS FROM students LIKE 'photo'")->fetch()) {
  $pdo->exec("ALTER TABLE students ADD COLUMN photo VARCHAR(255) NULL");
}
// Default registrar account (change the password after first login)
if (!$pdo->query("SELECT 1 FROM users WHERE role='registrar' LIMIT 1")->fetch()) {
  $pdo->prepare("INSERT INTO users(username,email,password_hash,role) VALUES('registrar','registrar@lvhs.local',?,'registrar')")
    ->execute([password_hash('registrar123', PASSWORD_DEFAULT)]);
}
if (!is_dir(__DIR__ . '/uploads'))
  @mkdir(__DIR__ . '/uploads', 0755, true);

function e($v)
{
  return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}
function flash($msg = null, $type = 'ok')
{
  if ($msg !== null) {
    $_SESSION['flash'] = [$msg, $type];
    return;
  }
  $f = $_SESSION['flash'] ?? null;
  unset($_SESSION['flash']);
  return $f;
}
function csrf()
{
  return $_SESSION['csrf'] ??= bin2hex(random_bytes(16));
}
function check_csrf()
{
  if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    http_response_code(400);
    exit('Invalid session token. Go back and try again.');
  }
}
function require_login($role = 'student')
{
  if (empty($_SESSION['uid']) || $_SESSION['role'] !== $role) {
    header('Location: ' . ($role === 'registrar' ? 'registrar_login.php' : 'login.php'));
    exit;
  }
}

// The account that is signed in right now, read fresh from the database (never from the browser).
// Returns null when nobody is signed in or the session points to an account that no longer exists.
function account_info($pdo)
{
  if (empty($_SESSION['uid']))
    return null;
  $q = $pdo->prepare('SELECT u.username,u.email,u.role,s.first_name,s.last_name FROM users u LEFT JOIN students s ON s.user_id=u.id WHERE u.id=?');
  $q->execute([$_SESSION['uid']]);
  $u = $q->fetch();
  if (!$u || $u['role'] !== ($_SESSION['role'] ?? '')) {
    unset($_SESSION['uid'], $_SESSION['role'], $_SESSION['name']);
    return null;
  }
  $full = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
  return ['role' => $u['role'], 'username' => $u['username'], 'email' => $u['email'], 'name' => $full !== '' ? $full : $u['username']];
}
// Ends the current login completely: session data, server-side session, and the session cookie.
// Only the session is cleared. Accounts and database records are not touched.
function end_session()
{
  if (session_status() !== PHP_SESSION_ACTIVE)
    session_start();
  $_SESSION = [];
  if (ini_get('session.use_cookies')) {
    $c = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $c['path'], $c['domain'], $c['secure'], $c['httponly']);
  }
  session_destroy();
}
