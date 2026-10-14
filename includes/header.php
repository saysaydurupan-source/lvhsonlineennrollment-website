<?php require_once __DIR__ . '/../config.php';
$title ??= 'LVHS Online Enrollment';
$f = flash();
$acct = account_info($pdo);
$role = $acct['role'] ?? null;

// Neat name: if the name is an email, use the part before the @
$nice = '';
if ($role) {
  $raw = trim($acct['name']);
  $nice = strpos($raw, '@') !== false ? ucfirst(explode('@', $raw)[0]) : $raw;
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= e($title) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link
    href="https://fonts.googleapis.com/css2?family=Sora:wght@500;700;800&family=Source+Sans+3:wght@400;600&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
  <?php foreach ((array) ($css ?? []) as $c): ?>
    <link rel="stylesheet" href="<?= e($c) ?>">
  <?php endforeach; ?>
</head>

<body>
  <div class="sky" aria-hidden="true">
    <i class="f f1"></i><i class="f f2"></i><i class="f f3"></i><i class="f f4"></i><i class="f f5"></i><i
      class="f f6"></i>
  </div>
  <header class="nav"><a class="brand" href="index.php"><b>LVHS</b><span>Online Enrollment</span></a>
    <nav><a href="index.php">Home</a><a href="map.php" <?= ($active ?? '') === 'map' ? ' class="active" aria-current="page"' : '' ?>>Map</a><a href="index.php#about">About</a><a href="index.php#portals">Portals</a><a
        href="index.php#contact">Contact</a>
      <?php if ($role === 'student'): ?>
        <a href="temporary_id.php" <?= ($active ?? '') === 'temporary_id' ? ' class="active" aria-current="page"' : '' ?>>Temporary ID</a><a class="pill" href="dashboard.php">Dashboard</a>
      <?php elseif ($role === 'registrar'): ?>
        <a class="pill" href="registrar.php">Registrar</a>
      <?php else: ?><a href="registrar_login.php">Registrar</a><a class="pill" href="login.php">Student sign
          in</a><?php endif; ?>

      <?php if ($role):
        $ini = strtoupper(mb_substr($nice, 0, 1)); ?>
        <div class="ax" id="ax">
          <button class="ax-btn" id="axBtn" type="button" aria-haspopup="menu" aria-expanded="false">
            <span class="ax-ring"><span class="ax-av"><?= e($ini) ?></span></span>
            <span class="ax-who"><b><?= e($nice) ?></b><small><?= e(ucfirst($role)) ?></small></span>
            <svg class="ax-caret" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
              stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
              <path d="M6 9l6 6 6-6" />
            </svg>
          </button>

          <div class="ax-menu" id="axMenu" role="menu">
            <div class="ax-card">
              <span class="ax-av big"><?= e($ini) ?></span>
              <div><b><?= e($nice) ?></b>
                <small
                  style="display:block;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= e($acct['email']) ?></small>
                <em class="ax-badge"><?= e(ucfirst($role)) ?></em>
              </div>
            </div>

            <a class="ax-item" role="menuitem" href="switch_account.php" style="--i:1">
              <i>🔄</i><span>Switch account<small>Sign in as someone else</small></span><kbd>S</kbd></a>

            <div class="ax-sep"></div>
            <a class="ax-item danger" role="menuitem" href="logout.php" id="axLogout" style="--i:2">
              <i>⏻</i><span>Log out<small>End this session</small></span><kbd>L</kbd></a>
          </div>
        </div>
      <?php endif; ?>
    </nav>
  </header>

  <?php if ($role): ?>
    <div class="ax-modal" id="axModal" hidden>
      <div class="ax-dialog" role="dialog" aria-modal="true" aria-labelledby="axT">
        <div class="ax-wave">👋</div>
        <h3 id="axT">Leaving so soon, <?= e(explode(' ', $nice)[0]) ?>?</h3>
        <p>You'll need to sign in again to keep working on your enrollment.</p>
        <div class="ax-actions">
          <button type="button" id="axStay">Stay signed in</button>
          <a href="logout.php" class="ax-go">Log out</a>
        </div>
      </div>
    </div>
  <?php endif; ?>


  <main>
    <?php if ($f): ?>
      <div class="toast <?= e($f[1]) ?>" role="status"><?= e($f[0]) ?></div><?php endif; ?>