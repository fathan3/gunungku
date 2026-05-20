<?php require_once __DIR__ . '/functions.php'; $currentPage=$currentPage??''; ?>
<div class="min-h-screen editorial-bg overflow-x-hidden">
  <aside class="fixed left-0 top-0 h-full hidden md:flex flex-col p-4 z-50 side-gradient w-64 border-r-0">
    <div class="mb-8 px-4 pt-2">
      <h1 class="text-xl font-extrabold text-[#154212] tracking-tighter">Gunungku</h1>
      <p class="text-xs uppercase tracking-widest text-on-surface-variant opacity-70">Modern Explorer</p>
    </div>
    <nav class="flex-1 space-y-1">
      <?php $menus=['dashboard'=>['dashboard','Dashboard'],'discovery'=>['explore','Discovery'],'simaksi'=>['assignment','Simaksi'],'peta'=>['map','Interactive Map'],'checklist'=>['checklist','Checklist'],'komunitas'=>['groups','Community'],'chatbot'=>['smart_toy','Chatbot'],'profil'=>['person','Profile']]; foreach($menus as $key=>$m): $is=currentActive($currentPage,$key); ?>
      <a class="nav-link <?= $is?'nav-active':'text-[#42493E] hover:text-[#154212]' ?>" href="<?= e(pageUrl($key)) ?>">
        <span class="material-symbols-outlined <?= $is?'material-fill':'' ?>"><?= e($m[0]) ?></span><span><?= e($m[1]) ?></span>
      </a>
      <?php endforeach; ?>
      <a class="nav-link text-[#42493E] hover:text-[#154212]" href="<?= e(pageUrl('logout')) ?>" onclick="return confirm('Yakin ingin keluar dari Gunungku?')"><span class="material-symbols-outlined">logout</span><span>Logout</span></a>
    </nav>
    <div class="mt-auto p-4 editorial-card-soft">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-11 h-11 rounded-full bg-primary-container text-white flex items-center justify-center font-extrabold"><?= e(initialName()) ?></div>
        <div class="min-w-0"><p class="text-sm font-extrabold text-primary truncate"><?= e(userName()) ?></p><p class="text-xs text-on-surface-variant">Pro Explorer</p></div>
      </div>
      <a class="btn-primary w-full py-2.5" href="<?= e(pageUrl('simaksi')) ?>">Start New Trek</a>
    </div>
  </aside>
  <main class="md:ml-64 min-w-0 flex flex-col overflow-x-hidden">
    <header class="sticky top-0 z-40 glass-effect border-b border-outline-variant/20 px-6 md:px-8 py-4 flex items-center justify-between">
      <div><p class="text-xs uppercase tracking-[0.3em] text-on-surface-variant"><?= e($pageTitle) ?></p><h2 class="text-2xl md:text-3xl font-extrabold text-primary mt-1"><?= e($pageTitle) ?></h2></div>
      <div class="flex items-center gap-4"><span class="material-symbols-outlined text-on-surface-variant">notifications</span><span class="hidden sm:block text-sm text-on-surface-variant">Halo, <b class="text-on-surface"><?= e(userName()) ?></b></span></div>
    </header>
    <section class="p-6 md:p-8 flex-1">
