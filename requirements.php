<?php $title='Requirements'; require 'includes/header.php'; require_login('student');
$uid=$_SESSION['uid'];$types=DOC_TYPES;
$ok=['pdf','jpg','jpeg','png','doc','docx'];
if ($_SERVER['REQUEST_METHOD']==='POST') { check_csrf(); $t=$_POST['doc_type']??'';$f=$_FILES['file']??null;
  $ext=strtolower(pathinfo($f['name']??'',PATHINFO_EXTENSION));
  if (!in_array($t,$types,true)) flash('Choose a document type.','err');
  elseif (!$f||$f['error']!==0) flash('Choose a file to upload.','err');
  elseif (!in_array($ext,$ok,true)) flash('Allowed files: PDF, JPG, PNG, DOC, DOCX.','err');
  elseif ($f['size']>5*1024*1024) flash('File is larger than 5 MB.','err');
  else { $name=bin2hex(random_bytes(10)).'.'.$ext;
    if (!move_uploaded_file($f['tmp_name'],__DIR__.'/uploads/'.$name)) flash('Could not save the file. Please try again.','err');
    else { $pdo->prepare('INSERT INTO documents(user_id,doc_type,file_name,file_path) VALUES(?,?,?,?)')->execute([$uid,$t,basename($f['name']),$name]); flash('Document uploaded.'); } }
  header('Location: requirements.php'); exit; }
$l=$pdo->prepare('SELECT * FROM documents WHERE user_id=? ORDER BY id DESC');$l->execute([$uid]);$rows=$l->fetchAll();
$s=$pdo->prepare('SELECT student_type FROM students WHERE user_id=?');$s->execute([$uid]);$stu=$s->fetch();
$need=required_docs($stu['student_type']??'New');$have=array_column($rows,'doc_type'); ?>
<form class="panel" method="post" enctype="multipart/form-data" data-busy><input type="hidden" name="csrf" value="<?= csrf() ?>">
<h1>Requirements</h1><p class="lead">Upload files requested by the registrar. Allowed: PDF, JPG, PNG, DOC, DOCX, up to 5 MB per file. The registrar can see everything you upload here.</p>
<div class="reqs" style="margin-bottom:8px"><?php foreach($need as $t): $d=in_array($t,$have,true); ?><span class="req <?= $d?'ok':'miss' ?>"><?= $d?'&#10003;':'&#10007;' ?> <?= e($t) ?></span><?php endforeach; ?></div>
<label>Document type</label><select name="doc_type"><?php foreach($types as $t) echo '<option>'.e($t).'</option>'; ?></select>
<label>File</label><input type="file" name="file" required><br><button class="btn" type="submit">Upload document</button></form>
<?php if ($rows): ?><div class="panel" style="margin-top:20px"><h2 class="sec first">Uploaded documents</h2>
<table><tr><th>Type</th><th>File</th><th>Uploaded</th></tr><?php foreach($rows as $r): ?>
<tr><td><?= e($r['doc_type']) ?></td><td><a href="file.php?doc=<?= (int)$r['id'] ?>" target="_blank" rel="noopener"><?= e($r['file_name']) ?></a></td><td><?= e($r['uploaded_at']) ?></td></tr><?php endforeach; ?></table>
<div class="row" style="margin-top:20px"><a class="btn" href="temporary_id.php">Get my temporary ID</a><a class="btn ghost" href="dashboard.php">Back to dashboard</a></div></div><?php endif; ?>
<?php require 'includes/footer.php'; ?>
