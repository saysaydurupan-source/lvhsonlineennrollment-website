<?php
// Serves uploaded requirements and ID photos. Uploaded files are never reachable by direct URL.
require_once 'config.php';
if (empty($_SESSION['uid'])) { http_response_code(403); exit('Please sign in first.'); }
$uid = (int)$_SESSION['uid']; $isReg = ($_SESSION['role'] ?? '') === 'registrar';
$mime = ['pdf'=>'application/pdf','jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','webp'=>'image/webp',
         'doc'=>'application/msword','docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
$stored = $shown = null;

if (isset($_GET['doc'])) {                       // a requirement document
  $q = $pdo->prepare('SELECT user_id,file_name,file_path FROM documents WHERE id=?'); $q->execute([(int)$_GET['doc']]); $d = $q->fetch();
  if ($d && ($isReg || (int)$d['user_id'] === $uid)) { $stored = $d['file_path']; $shown = $d['file_name']; }
} elseif (isset($_GET['photo'])) {               // the student's ID photo
  $owner = (int)$_GET['photo'];
  if ($isReg || $owner === $uid) {
    $q = $pdo->prepare('SELECT photo FROM students WHERE user_id=?'); $q->execute([$owner]); $p = $q->fetch();
    if ($p && $p['photo']) { $stored = $p['photo']; $shown = 'photo'; }
  }
}
$path = $stored ? __DIR__.'/uploads/'.basename($stored) : null;
if (!$path || !is_file($path)) { http_response_code(404); exit('File not found.'); }

$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
while (ob_get_level()) ob_end_clean();
header('Content-Type: '.($mime[$ext] ?? 'application/octet-stream'));
header('X-Content-Type-Options: nosniff');
header('Content-Length: '.filesize($path));
header("Content-Disposition: inline; filename*=UTF-8''".rawurlencode($shown));
readfile($path);
