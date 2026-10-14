<?php $title = 'Enrollment form';
require 'includes/header.php';
require_login('student');
$uid = $_SESSION['uid'];
$s = $pdo->prepare('SELECT * FROM students WHERE user_id=?');
$s->execute([$uid]);
$v = $s->fetch() ?: [];
$F = ['lrn', 'first_name', 'middle_name', 'last_name', 'sex', 'birthdate', 'contact', 'address', 'guardian_name', 'relationship', 'guardian_contact', 'guardian_address', 'grade_level', 'student_type', 'school_year', 'prev_school', 'prev_school_address'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  check_csrf();
  $d = [];
  foreach ($F as $k)
    $d[$k] = trim($_POST[$k] ?? '');
  $err = null;
  if (!preg_match('/^\d{12}$/', $d['lrn']))
    $err = 'LRN must be 12 digits.';
  elseif (!$d['first_name'] || !$d['last_name'] || !$d['address'] || !$d['guardian_name'])
    $err = 'Fill in all required fields.';
  elseif (!in_array($d['sex'], ['Male', 'Female']) || !in_array($d['grade_level'], ['7', '8', '9', '10', '11', '12']))
    $err = 'Select sex and grade level.';
  elseif (empty($_POST['certify']))
    $err = 'Please certify that your information is true and complete.';
  if ($err) {
    flash($err, 'err');
    header('Location: enroll.php');
    exit;
  }
  $cols = implode(',', $F);
  $ph = implode(',', array_fill(0, count($F), '?'));
  $upd = implode(',', array_map(fn($k) => "$k=VALUES($k)", $F));
  $pdo->prepare("INSERT INTO students(user_id,$cols) VALUES(?,$ph) ON DUPLICATE KEY UPDATE $upd,status='Pending'")->execute(array_merge([$uid], array_values($d)));
  flash('Enrollment form submitted. Upload your requirements next.');
  header('Location: requirements.php');
  exit;
}
function val($k)
{
  global $v;
  return e($v[$k] ?? '');
}
function sel($k, $o)
{
  global $v;
  return ($v[$k] ?? '') === $o ? 'selected' : '';
} ?>
<form class="panel" method="post" data-busy><input type="hidden" name="csrf" value="<?= csrf() ?>">
  <h2 class="sec first">1. Student information</h2>
  <div class="cols">
    <div><label>LRN</label><input name="lrn" inputmode="numeric" maxlength="12" value="<?= val('lrn') ?>" required>
    </div>
    <div><label>Sex</label><select name="sex" required>
        <option value="">Select</option>
        <option <?= sel('sex', 'Male') ?>>Male</option>
        <option <?= sel('sex', 'Female') ?>>Female</option>
      </select></div>
    <div><label>First name</label><input name="first_name" value="<?= val('first_name') ?>" required></div>
    <div><label>Middle name</label><input name="middle_name" value="<?= val('middle_name') ?>"></div>
    <div><label>Last name</label><input name="last_name" value="<?= val('last_name') ?>" required></div>
    <div><label>Birthdate</label><input type="date" name="birthdate" value="<?= val('birthdate') ?>" required></div>
    <div><label>Contact number</label><input name="contact" value="<?= val('contact') ?>" required></div>
  </div>
  <h2 class="sec">2. Address</h2><label>Complete address</label><textarea name="address"
    required><?= val('address') ?></textarea>
  <h2 class="sec">3. Parent / guardian</h2>
  <div class="cols">
    <div><label>Guardian name</label><input name="guardian_name" value="<?= val('guardian_name') ?>" required></div>
    <div><label>Relationship</label><input name="relationship" value="<?= val('relationship') ?>" required></div>
    <div><label>Guardian contact</label><input name="guardian_contact" value="<?= val('guardian_contact') ?>" required>
    </div>
    <div><label>Guardian address</label><input name="guardian_address" value="<?= val('guardian_address') ?>"></div>
  </div>
  <h2 class="sec">4. Academic information</h2>
  <div class="cols">
    <div><label>Grade level</label><select name="grade_level" required>
        <option value="">Select</option>
        <?php foreach ([7, 8, 9, 10, 11, 12] as $g)
          echo "<option value=$g " . sel('grade_level', (string) $g) . ">Grade $g</option>"; ?>
      </select></div>
    <div><label>Student type</label><select
        name="student_type"><?php foreach (['New', 'Transferee', 'Returning'] as $t)
          echo "<option " . sel('student_type', $t) . ">$t</option>"; ?></select>
    </div>
    <div><label>School year</label><input name="school_year" value="<?= val('school_year') ?: '2026-2027' ?>" readonly>
    </div>
    <div><label>Previous school</label><input name="prev_school" value="<?= val('prev_school') ?>"></div>
  </div>
  <label>Previous school address</label><textarea
    name="prev_school_address"><?= val('prev_school_address') ?></textarea>
  <h2 class="sec">5. Review &amp; certification</h2>
  <div class="check"><input type="checkbox" id="cert" name="certify" value="1"><label for="cert">I certify that the
      information provided is true and complete.</label></div>
  <button class="btn" type="submit">Submit enrollment form</button>
</form>
<?php require 'includes/footer.php'; ?>