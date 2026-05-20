<?php
require_once __DIR__ . '/../includes/functions.php';
if (isLoggedIn()) { header('Location: index.php?page=dashboard'); exit; }
$pageTitle='Welcome Explorer'; $currentPage='splash'; include __DIR__ . '/../includes/header.php';
?>
<div class="min-h-screen relative overflow-hidden bg-primary text-white">
  <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?q=80&w=1800&auto=format&fit=crop')] bg-cover bg-center opacity-70"></div>
  <div class="absolute inset-0 hero-gradient"></div>
  <header class="relative z-10 flex justify-between items-center px-8 py-8">
    <div><h1 class="text-2xl font-extrabold tracking-tighter">Gunungku</h1><p class="text-xs uppercase tracking-[.35em] text-white/70">Modern Explorer</p></div>
    <a href="<?= e(pageUrl('login')) ?>" class="bg-white/15 backdrop-blur-md border border-white/20 rounded-2xl px-5 py-3 font-bold">Masuk</a>
  </header>
  <main class="relative z-10 min-h-[calc(100vh-120px)] flex items-end px-8 md:px-16 pb-16">
    <div class="max-w-4xl">
      <p class="uppercase tracking-[.35em] text-white/70 font-bold mb-6">Welcome Explorer</p>
      <h2 class="font-headline text-5xl md:text-8xl font-extrabold leading-[.95] tracking-tight">Temukan Jalur. <br>Catat Puncak. <br>Jelajah Aman.</h2>
      <p class="mt-8 text-lg md:text-xl text-white/85 max-w-2xl">Platform pendakian untuk discovery gunung, pengajuan simaksi, checklist perlengkapan, peta jalur, komunitas, dan log pencapaian.</p>
      <div class="mt-10 flex flex-wrap gap-4"><a class="bg-white text-primary rounded-2xl px-7 py-4 font-extrabold shadow-xl" href="<?= e(pageUrl('login')) ?>">Mulai Sekarang</a><a class="bg-white/10 border border-white/20 rounded-2xl px-7 py-4 font-extrabold backdrop-blur-md" href="<?= e(pageUrl('register')) ?>">Daftar Akun</a></div>
    </div>
  </main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
