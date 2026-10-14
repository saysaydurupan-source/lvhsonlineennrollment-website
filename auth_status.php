<?php
// Tells the static pages (index.html, ending.html) who is signed in. Only the current session's own account is returned.
require_once 'config.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$a = account_info($pdo);
echo json_encode($a ? ['loggedIn'=>true, 'role'=>$a['role'], 'name'=>$a['name'], 'email'=>$a['email']] : ['loggedIn'=>false]);
