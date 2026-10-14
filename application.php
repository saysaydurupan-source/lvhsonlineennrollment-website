<?php $title = 'My enrollment form';
require 'includes/header.php';
require_login('student');
$s = $pdo->prepare('SELECT * FROM students WHERE user_id=?');
$s->execute([$_SESSION['uid']]);
$r = $s->fetch();
if (!$r) {
    flash('Submit the enrollment form first.', 'err');
    header('Location: enroll.php');
    exit;
}
$G = [
    '1. Student information' => ['lrn' => 'LRN', 'first_name' => 'First name', 'middle_name' => 'Middle name', 'last_name' => 'Last name', 'sex' => 'Sex', 'birthdate' => 'Birthdate', 'contact' => 'Contact number'],
    '2. Address' => ['address' => 'Complete address'],
    '3. Parent / guardian' => ['guardian_name' => 'Guardian name', 'relationship' => 'Relationship', 'guardian_contact' => 'Guardian contact', 'guardian_address' => 'Guardian address'],
    '4. Academic information' => ['grade_level' => 'Grade level', 'student_type' => 'Student type', 'school_year' => 'School year', 'prev_school' => 'Previous school', 'prev_school_address' => 'Previous school address']
]; ?>
<div class="panel">
    <h1>Enrollment form <span class="status <?= e($r['status']) ?>"><?= e($r['status']) ?></span></h1>
    <?php foreach ($G as $h => $f): ?>
        <h2 class="sec"><?= e($h) ?></h2>
        <dl class="sum"><?php foreach ($f as $k => $l): ?>
                <dt><?= e($l) ?></dt>
                <dd><?= e($r[$k] ?: '-') ?></dd><?php endforeach; ?>
        </dl><?php endforeach; ?>
    <h2 class="sec">5. Certification</h2>
    <p>Certified true and complete on <?= e(date('F j, Y', strtotime($r['submitted_at']))) ?>.</p>
    <div class="row noprint"><button class="btn" onclick="print()">Print form</button><a class="btn ghost"
            href="enroll.php">Edit form</a></div>
</div>
<?php require 'includes/footer.php'; ?>