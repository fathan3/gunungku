<?php
require_once __DIR__ . '/includes/functions.php';
$page = $_GET['page'] ?? (isLoggedIn() ? 'dashboard' : 'splash');
$allowed = [
  'splash','login','register','logout','dashboard','discovery','detail_gunung','simaksi','checklist','komunitas','peta','profil','chatbot'
];
if (!in_array($page, $allowed, true)) { $page = isLoggedIn() ? 'dashboard' : 'splash'; }
$file = __DIR__ . '/pages/' . $page . '.php';
if (!file_exists($file)) { http_response_code(404); echo 'Halaman tidak ditemukan.'; exit; }
require $file;
