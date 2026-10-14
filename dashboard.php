<?php $title = 'Student portal';
require 'includes/header.php';
require_login('student');
$s = $pdo->prepare('SELECT * FROM students WHERE user_id=?');
$s->execute([$_SESSION['uid']]);
$st = $s->fetch();
$d = $pdo->prepare('SELECT COUNT(*) c FROM documents WHERE user_id=?');
$d->execute([$_SESSION['uid']]);
$docs = $d->fetch()['c']; ?>
<h1 style="font-size:2.6rem">Welcome, <?= e($_SESSION['name']) ?></h1>
<p class="lead">Manage your enrollment application and profile.</p>
<div class="grid">
    <div class="card">
        <h3>Application status</h3>
        <p><span class="status <?= e($st['status'] ?? '') ?>"><?= $st ? e($st['status']) : 'Not submitted' ?></span></p>
        <p><?= $st ? e($st['remarks'] ?: 'The registrar is reviewing your form.') : 'Start a new application.' ?></p>
    </div>
    <div class="card">
        <h3>Student information</h3>
        <p><?= $st ? e($st['first_name'] . ' ' . $st['last_name']) . ', Grade ' . e($st['grade_level']) : 'No student profile yet.' ?>
        </p>
        <a href="enroll.php"><?= $st ? 'Edit enrollment form' : 'Start enrollment' ?></a><?php if ($st): ?> &middot; <a
                href="application.php">View submitted form</a><?php endif; ?>
    </div>
    <div class="card">
        <h3>Requirements</h3>
        <p><?= (int) $docs ?> document(s) uploaded.</p><a href="requirements.php">Open requirements</a>
    </div>
    <div class="card">
        <h3>Temporary ID</h3>
        <p><?= $st ? 'Your details are already filled in. Add your photo and save it.' : 'Available after you submit your enrollment form.' ?>
        </p><a
            href="<?= $st ? 'temporary_id.php' : 'enroll.php' ?>"><?= $st ? 'Open temporary ID' : 'Start enrollment' ?></a>
    </div>
    <div class="card">
        <h3>Campus map</h3>
        <p>Pick a number and the map zooms to that building or room.</p><a href="map.php">Open the map</a>
    </div>
</div>
<?php require 'includes/footer.php'; ?>