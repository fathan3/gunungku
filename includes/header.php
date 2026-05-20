<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? 'Gunungku';
$currentPage = $currentPage ?? '';
?><!DOCTYPE html>
<html class="light overflow-x-hidden" lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?> - Gunungku</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
  <script>
    tailwind.config = { darkMode: 'class', theme: { extend: { colors: {
      'surface-bright':'#f9faf2','surface-container-low':'#f3f4ed','surface-container-high':'#e7e9e1','surface-variant':'#e2e3dc','on-primary':'#ffffff','surface-dim':'#d9dbd3','secondary':'#77574d','primary-fixed-dim':'#a1d494','error-container':'#ffdad6','on-secondary-fixed-variant':'#5d4037','on-surface':'#191c18','primary-container':'#2d5a27','error':'#ba1a1a','on-secondary-container':'#795950','on-secondary-fixed':'#2c160e','on-primary-fixed-variant':'#23501e','on-tertiary':'#ffffff','on-primary-container':'#9dd090','on-secondary':'#ffffff','on-error':'#ffffff','surface-container-lowest':'#ffffff','primary':'#154212','inverse-primary':'#a1d494','secondary-fixed':'#ffdbd0','surface':'#f9faf2','tertiary-container':'#803e00','on-background':'#191c18','primary-fixed':'#bcf0ae','inverse-on-surface':'#f0f1ea','inverse-surface':'#2e312c','on-tertiary-fixed':'#311300','background':'#f9faf2','secondary-container':'#fed3c7','surface-container-highest':'#e2e3dc','tertiary-fixed-dim':'#ffb786','on-tertiary-fixed-variant':'#723600','on-surface-variant':'#42493e','on-primary-fixed':'#002201','tertiary-fixed':'#ffdcc6','tertiary':'#5e2b00','on-tertiary-container':'#ffb17d','outline-variant':'#c2c9bb','outline':'#72796e','surface-tint':'#3b6934','surface-container':'#edefe7','on-error-container':'#93000a','secondary-fixed-dim':'#e7bdb1'
    }, fontFamily: { headline:['Manrope','sans-serif'], body:['Inter','sans-serif'], label:['Inter','sans-serif'] }, borderRadius: { DEFAULT:'0.125rem', lg:'0.25rem', xl:'0.75rem', full:'9999px' } } } }
  </script>
  <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="bg-surface font-body text-on-surface min-h-screen overflow-x-hidden selection:bg-primary-container selection:text-white">
