<?php
require_once __DIR__ . '/../config/database.php'; require_once __DIR__ . '/../includes/functions.php'; requireLogin();
$search = trim($_GET['q'] ?? '');
$diff   = trim($_GET['difficulty'] ?? '');
$mountains = [];
$sql = 'SELECT * FROM mountains WHERE 1=1';
$params = []; $types = '';
if($search !== '') { $sql .= ' AND nama_gunung LIKE ?'; $params[] = "%{$search}%"; $types .= 's'; }
if($diff  !== '') { $sql .= ' AND tingkat_kesulitan = ?'; $params[] = $diff; $types .= 's'; }
$sql .= ' ORDER BY nama_gunung ASC';
if($params){ $st=$conn->prepare($sql); $st->bind_param($types,...$params); $st->execute(); $res=$st->get_result(); while($r=$res->fetch_assoc())$mountains[]=$r; }
else { if($q=$conn->query($sql)){while($r=$q->fetch_assoc())$mountains[]=$r;} }
$pageTitle='Discovery'; $currentPage='discovery';
include __DIR__.'/../includes/header.php'; include __DIR__.'/../includes/layout_top.php';
?>
<div class="mb-8 rounded-[2rem] discover-hero min-h-[280px] editorial-shadow relative overflow-hidden"><div class="absolute inset-0 p-8 md:p-10 flex flex-col justify-end text-white"><p class="uppercase tracking-[.35em] text-white/75 text-xs font-bold mb-3">Explore Routes</p><h1 class="text-4xl md:text-6xl font-extrabold">Temukan Gunung</h1><p class="mt-3 max-w-2xl text-white/85">Pilih destinasi, cek jalur, cuaca, estimasi pendakian, dan ajukan simaksi langsung dari detail gunung.</p></div></div>

<form method="GET" action="index.php" class="mb-8 flex flex-col sm:flex-row gap-3">
  <input type="hidden" name="page" value="discovery">
  <div class="relative flex-1"><span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span><input name="q" value="<?=e($search)?>" placeholder="Cari gunung..." class="w-full pl-12 pr-4 py-4 rounded-2xl bg-white border border-outline-variant/50 focus:outline-none focus:ring-2 focus:ring-primary text-sm"></div>
  <select name="difficulty" class="rounded-2xl bg-white border border-outline-variant/50 px-4 py-4 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
    <option value="">Semua Kesulitan</option>
    <?php foreach(['mudah','sedang','sulit','ekstrem'] as $d): ?><option value="<?=e($d)?>" <?=$diff===$d?'selected':''?>><?=ucfirst($d)?></option><?php endforeach; ?>
  </select>
  <button class="btn-primary" type="submit"><span class="material-symbols-outlined">filter_list</span> Filter</button>
  <?php if($search!==''||$diff!==''): ?><a href="<?=e(pageUrl('discovery'))?>" class="btn-soft flex items-center gap-2"><span class="material-symbols-outlined text-sm">close</span> Reset</a><?php endif; ?>
</form>

<?php if(empty($mountains)): ?>
<div class="editorial-card p-16 text-center">
  <span class="material-symbols-outlined text-6xl text-on-surface-variant/30">landscape</span>
  <h3 class="text-2xl font-extrabold mt-4">Gunung Tidak Ditemukan</h3>
  <p class="text-on-surface-variant mt-2">Coba kata kunci atau filter yang berbeda.</p>
  <a href="<?=e(pageUrl('discovery'))?>" class="btn-primary inline-flex mt-6">Lihat Semua Gunung</a>
</div>
<?php else: ?>
<div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
<?php foreach($mountains as $m): ?>
  <div class="editorial-card overflow-hidden group"><div class="h-56 relative mountain-hero"><div class="absolute inset-x-0 bottom-0 p-5 text-white"><div class="flex items-end justify-between gap-4"><div><p class="text-xs uppercase tracking-widest text-white/70"><?= e($m['provinsi']??'Indonesia') ?></p><h3 class="text-2xl font-extrabold mt-1"><?= e($m['nama_gunung']) ?></h3></div><?= statusBadge($m['status_gunung']) ?></div></div></div><div class="p-6"><p class="text-on-surface-variant"><?= e($m['lokasi']) ?></p><div class="grid grid-cols-2 gap-4 mt-5 text-sm"><div class="editorial-card-soft p-4"><div class="text-on-surface-variant">Ketinggian</div><div class="font-bold text-primary mt-1"><?= (int)$m['ketinggian_mdpl'] ?> mdpl</div></div><div class="editorial-card-soft p-4"><div class="text-on-surface-variant">Kesulitan</div><div class="font-bold text-primary mt-1"><?= e($m['tingkat_kesulitan']) ?></div></div></div><p class="text-slate-600 mt-4 line-clamp-3"><?= e($m['deskripsi']) ?></p><div class="mt-5 flex gap-3"><a href="<?= e(pageUrl('detail_gunung',['id'=>(int)$m['id']])) ?>" class="btn-primary">Lihat Detail</a><a href="<?= e(pageUrl('peta')) ?>" class="btn-soft">Peta</a></div></div></div>
<?php endforeach; ?>
</div>
<?php endif; ?>
<?php include __DIR__.'/../includes/layout_bottom.php'; include __DIR__.'/../includes/footer.php'; ?>
