<?php $title='Registrar portal'; require 'includes/header.php'; require_login('registrar');
if ($_SERVER['REQUEST_METHOD']==='POST') { check_csrf(); $st=$_POST['status']??'';
  if (in_array($st,['Pending','Approved','Rejected'],true)) { $pdo->prepare('UPDATE students SET status=?,remarks=? WHERE id=?')->execute([$st,trim($_POST['remarks']??''),(int)$_POST['id']]); flash('Status updated.'); }
  header('Location: registrar.php?status='.urlencode($_POST['view']??'Pending')); exit; }

$view = in_array($_GET['status']??'Pending',['Pending','Approved','Rejected','All'],true) ? ($_GET['status']??'Pending') : 'Pending';
$counts = ['Pending'=>0,'Approved'=>0,'Rejected'=>0];
foreach ($pdo->query('SELECT status,COUNT(*) c FROM students GROUP BY status') as $c) $counts[$c['status']] = (int)$c['c'];
$counts['All'] = array_sum($counts);

if ($view==='All') $rows=$pdo->query('SELECT * FROM students ORDER BY submitted_at DESC')->fetchAll();
else { $q=$pdo->prepare('SELECT * FROM students WHERE status=? ORDER BY submitted_at DESC'); $q->execute([$view]); $rows=$q->fetchAll(); }

// every uploaded requirement of the listed students, grouped by student
$docs = [];
if ($rows) {
  $ids = array_map(fn($r)=>(int)$r['user_id'],$rows);
  $q = $pdo->query('SELECT * FROM documents WHERE user_id IN ('.implode(',',$ids).') ORDER BY uploaded_at DESC');
  foreach ($q as $d) $docs[$d['user_id']][] = $d;
}
function is_image($n){ return in_array(strtolower(pathinfo($n,PATHINFO_EXTENSION)),['jpg','jpeg','png','webp'],true); } ?>
<h1 style="font-size:2.4rem">Registrar portal</h1>
<p class="lead"><?= $view==='Pending' ? 'Pending enrollees and every requirement they have passed.' : 'Applications and the requirements they passed.' ?> Open a student to see their documents.</p>

<div class="stats">
<?php foreach(['Pending'=>'Pending enrolling','Approved'=>'Approved','Rejected'=>'Rejected','All'=>'All applications'] as $k=>$label): ?>
  <a class="stat <?= $view===$k?'on':'' ?>" href="registrar.php?status=<?= $k ?>"><b><?= $counts[$k] ?></b><span><?= e($label) ?></span></a>
<?php endforeach; ?></div>

<?php if (!$rows): ?><div class="panel empty">No <?= $view==='All' ? '' : e(strtolower($view)).' ' ?>applications right now.</div><?php endif; ?>

<?php foreach($rows as $r):
  $mine = $docs[$r['user_id']] ?? [];
  $have = array_values(array_unique(array_column($mine,'doc_type')));
  $need = required_docs($r['student_type']);
  $passed = count(array_intersect($need,$have)); ?>
<details class="enrollee"<?= $view==='Pending' && count($rows)===1 ? ' open' : '' ?>>
  <summary>
    <div class="nm"><?= e($r['last_name'].', '.$r['first_name']) ?><small>LRN <?= e($r['lrn']) ?></small></div>
    <div>Grade <?= e($r['grade_level']) ?></div>
    <div><?= e($r['student_type']) ?></div>
    <div class="prog"><?= $passed ?> / <?= count($need) ?> requirements</div>
    <span class="status <?= e($r['status']) ?>"><?= e($r['status']) ?></span>
  </summary>
  <div class="body">
    <h3>Requirements passed</h3>
    <div class="reqs">
      <?php foreach($need as $t): $ok=in_array($t,$have,true); ?>
        <span class="req <?= $ok?'ok':'miss' ?>"><?= $ok?'&#10003;':'&#10007;' ?> <?= e($t) ?><?= $ok?'':' (missing)' ?></span>
      <?php endforeach; ?>
    </div>

    <h3>Submitted files (<?= count($mine) ?>)</h3>
    <?php if ($mine): ?><div class="docs">
      <?php foreach($mine as $d): $u='file.php?doc='.(int)$d['id']; ?>
        <a class="doc" href="<?= e($u) ?>" target="_blank" rel="noopener">
          <div class="th"><?php if (is_image($d['file_name'])): ?><img src="<?= e($u) ?>" alt="" loading="lazy"><?php else: ?><?= e(strtoupper(pathinfo($d['file_name'],PATHINFO_EXTENSION))) ?><?php endif; ?></div>
          <b><?= e($d['doc_type']) ?></b><span><?= e($d['file_name']) ?></span><span><?= e(date('M j, Y g:i A',strtotime($d['uploaded_at']))) ?></span>
        </a>
      <?php endforeach; ?></div>
    <?php else: ?><p class="lead" style="margin:0">This student has not uploaded any requirement yet.</p><?php endif; ?>

    <h3>Student information</h3>
    <dl class="info-grid">
      <?php foreach(['sex'=>'Sex','birthdate'=>'Birthdate','contact'=>'Contact','address'=>'Address','guardian_name'=>'Guardian','relationship'=>'Relationship','guardian_contact'=>'Guardian contact','school_year'=>'School year','prev_school'=>'Previous school'] as $k=>$l): ?>
        <div><dt><?= e($l) ?></dt><dd><?= e($r[$k]?:'-') ?></dd></div>
      <?php endforeach; ?></dl>

    <form method="post" class="upd"><input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><input type="hidden" name="view" value="<?= e($view) ?>">
      <select name="status" aria-label="Status"><?php foreach(['Pending','Approved','Rejected'] as $o) echo '<option '.($o===$r['status']?'selected':'').'>'.$o.'</option>'; ?></select>
      <input name="remarks" placeholder="Remarks for the student" value="<?= e($r['remarks']) ?>">
      <button class="btn small" type="submit">Save status</button></form>
  </div>
</details>
<?php endforeach; ?>
<?php require 'includes/footer.php'; ?>
