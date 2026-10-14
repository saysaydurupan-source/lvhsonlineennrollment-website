<?php require_once 'config.php';
// Receives the contact form from index.html
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $n=trim($_POST['name']??'');$m=trim($_POST['email']??'');$t=trim($_POST['message']??'');
  if ($n&&filter_var($m,FILTER_VALIDATE_EMAIL)&&$t) {
    $pdo->prepare('INSERT INTO messages(name,email,message) VALUES(?,?,?)')->execute([$n,$m,$t]);
    header('Location: index.html?sent=1#contact'); exit; }
  header('Location: index.html?sent=0#contact'); exit; }
header('Location: index.html#contact');
