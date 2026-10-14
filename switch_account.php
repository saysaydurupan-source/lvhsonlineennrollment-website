<?php
// "Log in to another account": ends the current login only (nothing is deleted), then opens the matching sign-in page.
require_once 'config.php';
$loginPage = (($_SESSION['role'] ?? '') === 'registrar') ? 'registrar_login.php' : 'login.php';
end_session();
session_start();
session_regenerate_id(true);   // fresh session for the next sign-in
flash('You are signed out. Sign in with another account.');
header('Location: ' . $loginPage);
exit;
