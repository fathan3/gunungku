<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool { return isset($_SESSION['user_id']); }
function requireLogin(): void { if (!isLoggedIn()) { header('Location: index.php?page=login'); exit; } }
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function userName(): string { return $_SESSION['user_name'] ?? 'Explorer'; }
function userEmail(): string { return $_SESSION['user_email'] ?? ''; }
function userId(): int { return (int)($_SESSION['user_id'] ?? 0); }
function initialName(?string $name=null): string { $n = trim($name ?? userName()); return $n !== '' ? strtoupper(substr($n,0,1)) : 'G'; }
function pageUrl(string $page, array $params=[]): string { $params = array_merge(['page'=>$page], $params); return 'index.php?' . http_build_query($params); }
function formatDateId(?string $date): string { if (!$date) return '-'; $ts=strtotime($date); return $ts?date('d M Y',$ts):$date; }
function statusBadge(string $status): string { $status=strtolower(trim($status)); $map=['aktif'=>'bg-emerald-100 text-emerald-800','buka'=>'bg-emerald-100 text-emerald-800','selesai'=>'bg-emerald-100 text-emerald-800','completed'=>'bg-emerald-100 text-emerald-800','diverifikasi'=>'bg-emerald-100 text-emerald-800','published'=>'bg-emerald-100 text-emerald-800','terbatas'=>'bg-amber-100 text-amber-800','draft'=>'bg-amber-100 text-amber-800','diajukan'=>'bg-blue-100 text-blue-800','planned'=>'bg-amber-100 text-amber-800','ongoing'=>'bg-blue-100 text-blue-800','tutup'=>'bg-rose-100 text-rose-800','ditolak'=>'bg-rose-100 text-rose-800','cancelled'=>'bg-rose-100 text-rose-800']; $cls=$map[$status]??'bg-slate-100 text-slate-700'; return '<span class="px-3 py-1 rounded-full text-xs font-bold '.$cls.'">'.e(ucwords(str_replace('_',' ',$status))).'</span>'; }
function currentActive(string $current, string $target): bool { if ($target==='discovery' && $current==='detail_gunung') return true; return $current===$target; }
function setFlash(string $key, string $msg): void { if(session_status()===PHP_SESSION_NONE)session_start(); $_SESSION['_flash'][$key]=$msg; }
function getFlash(string $key): string { $v=$_SESSION['_flash'][$key]??''; unset($_SESSION['_flash'][$key]); return $v; }
