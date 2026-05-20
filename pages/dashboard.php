<?php
require_once __DIR__ . '/../config/database.php'; require_once __DIR__ . '/../includes/functions.php'; requireLogin();
$userId=(int)$_SESSION['user_id']; $message=getFlash('log_ok'); $error='';

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_log'])){
  $mId=(int)($_POST['mountain_id']??0);
  $mulai=trim($_POST['tanggal_mulai']??'');
  $selesai=trim($_POST['tanggal_selesai']??'') ?: null;
  $status=trim($_POST['status_hike']??'planned');
  $catatan=trim($_POST['catatan']??'');
  $jarak=(float)($_POST['jarak_km']??0);
  $durasi=(float)($_POST['durasi_jam']??0);
  if($mId<=0||$mulai==='') $error='Gunung dan tanggal mulai wajib diisi.';
  else {
    $stmt=$conn->prepare('INSERT INTO hike_logs (user_id,mountain_id,tanggal_mulai,tanggal_selesai,jarak_km,durasi_jam,status_hike,catatan) VALUES (?,?,?,?,?,?,?,?)');
    $stmt->bind_param('iissddss',$userId,$mId,$mulai,$selesai,$jarak,$durasi,$status,$catatan);
    if($stmt->execute()){setFlash('log_ok','Log pendakian berhasil ditambahkan!');header('Location: '.pageUrl('dashboard'));exit;}
    else $error='Gagal menyimpan log pendakian.';
  }
}

$totalMountains=0;$totalSimaksi=0;$totalChecklist=0;
$userStats=['total_summit'=>0,'total_km'=>0,'total_elevation_gain'=>0,'total_jam_trail'=>0,'avg_pace'=>0];$recentLogs=[];$mountains=[];
if($q=$conn->query('SELECT COUNT(*) total FROM mountains'))$totalMountains=(int)($q->fetch_assoc()['total']??0);
if($q=$conn->query("SELECT COUNT(*) total FROM simaksi_applications WHERE user_id={$userId}"))$totalSimaksi=(int)($q->fetch_assoc()['total']??0);
if($q=$conn->query("SELECT COUNT(*) total FROM user_checklists WHERE user_id={$userId}"))$totalChecklist=(int)($q->fetch_assoc()['total']??0);
if($q=$conn->query("SELECT * FROM user_stats WHERE user_id={$userId} LIMIT 1"))$userStats=$q->fetch_assoc()?:$userStats;
if($q=$conn->query("SELECT hl.*, m.nama_gunung FROM hike_logs hl LEFT JOIN mountains m ON m.id=hl.mountain_id WHERE hl.user_id={$userId} ORDER BY hl.tanggal_mulai DESC LIMIT 5")){while($r=$q->fetch_assoc())$recentLogs[]=$r;}
if($q=$conn->query('SELECT id, nama_gunung FROM mountains ORDER BY nama_gunung ASC')){while($r=$q->fetch_assoc())$mountains[]=$r;}
$pageTitle='Dashboard';$currentPage='dashboard';include __DIR__.'/../includes/header.php';include __DIR__.'/../includes/layout_top.php';
?>
<div class="mb-8 overflow-hidden rounded-[2rem] mountain-hero editorial-shadow min-h-[260px] relative">
  <div class="absolute inset-0 p-8 md:p-10 flex flex-col justify-end text-white">
    <p class="uppercase tracking-[.35em] text-white/75 text-xs font-bold mb-3">Modern Explorer</p>
    <h1 class="text-4xl md:text-6xl font-extrabold leading-tight">Halo, <?= e(userName()) ?></h1>
    <p class="max-w-xl text-white/85 mt-3">Ringkasan aktivitas pendakian, simaksi, checklist, dan progres komunitas Anda.</p>
    <div class="mt-6 flex flex-wrap gap-3">
      <a class="bg-white text-primary rounded-2xl px-6 py-3 font-extrabold inline-flex items-center gap-2" href="<?= e(pageUrl('simaksi')) ?>"><span class="material-symbols-outlined">assignment</span>Ajukan Simaksi</a>
      <button onclick="document.getElementById('modal-log').classList.remove('hidden')" class="bg-white/15 border border-white/30 rounded-2xl px-6 py-3 font-extrabold inline-flex items-center gap-2 backdrop-blur-sm"><span class="material-symbols-outlined">add</span>Catat Pendakian</button>
    </div>
  </div>
</div>

<?php if($message):?><div class="mb-6 rounded-2xl bg-green-100 text-green-800 px-5 py-4 flex items-center gap-3 font-medium"><span class="material-symbols-outlined">check_circle</span><?=e($message)?></div><?php endif;?>
<?php if($error):?><div class="mb-6 rounded-2xl bg-error-container text-on-error-container px-5 py-4 font-medium"><?=e($error)?></div><?php endif;?>

<div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-6">
<?php $cards=[['flag',$userStats['total_summit']??0,'Total Summit'],['landscape',$totalMountains,'Gunung Tersedia'],['assignment_turned_in',$totalSimaksi,'Pengajuan Simaksi'],['checklist',$totalChecklist,'Checklist Aktif']]; foreach($cards as $c): ?>
  <div class="editorial-card p-6"><div class="flex items-center justify-between"><div><p class="text-3xl font-extrabold text-primary"><?= e($c[1]) ?></p><p class="text-on-surface-variant text-sm mt-1"><?= e($c[2]) ?></p></div><span class="material-symbols-outlined text-primary/25 text-5xl"><?= e($c[0]) ?></span></div></div>
<?php endforeach; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
  <div class="editorial-card p-8"><h3 class="text-2xl font-extrabold mb-6">Statistik Profil</h3><div class="grid grid-cols-2 gap-4"><div class="editorial-card-soft p-4"><p class="text-sm text-on-surface-variant">Total KM</p><p class="text-2xl font-extrabold"><?= number_format((float)($userStats['total_km']??0),2) ?></p></div><div class="editorial-card-soft p-4"><p class="text-sm text-on-surface-variant">Elevation Gain</p><p class="text-2xl font-extrabold"><?= (int)($userStats['total_elevation_gain']??0) ?> m</p></div><div class="editorial-card-soft p-4"><p class="text-sm text-on-surface-variant">Jam Trail</p><p class="text-2xl font-extrabold"><?= (int)($userStats['total_jam_trail']??0) ?></p></div><div class="editorial-card-soft p-4"><p class="text-sm text-on-surface-variant">Avg Pace</p><p class="text-2xl font-extrabold"><?= number_format((float)($userStats['avg_pace']??0),2) ?></p></div></div></div>
  <div class="editorial-card p-8"><h3 class="text-2xl font-extrabold mb-6">Navigasi Cepat</h3><div class="flex flex-col gap-3 max-w-xs"><a class="btn-soft" href="<?= e(pageUrl('discovery')) ?>">Lihat Discovery</a><a class="btn-soft" href="<?= e(pageUrl('checklist')) ?>">Kelola Checklist</a><a class="btn-soft" href="<?= e(pageUrl('komunitas')) ?>">Buka Komunitas</a><a class="btn-soft" href="<?= e(pageUrl('chatbot')) ?>">Tanya Chatbot</a></div></div>
</div>

<div class="editorial-card p-8">
  <div class="flex items-center justify-between mb-6">
    <h3 class="text-2xl font-extrabold">Riwayat Pendakian Terbaru</h3>
    <button onclick="document.getElementById('modal-log').classList.remove('hidden')" class="btn-primary text-sm py-2 px-4"><span class="material-symbols-outlined text-sm">add</span> Tambah Log</button>
  </div>
  <div class="overflow-auto">
    <table class="w-full text-left">
      <thead><tr class="border-b border-outline-variant/40"><th class="py-4 pr-4 text-sm font-bold">Gunung</th><th class="py-4 pr-4 text-sm font-bold">Mulai</th><th class="py-4 pr-4 text-sm font-bold">Selesai</th><th class="py-4 text-sm font-bold">Status</th></tr></thead>
      <tbody>
        <?php if($recentLogs): foreach($recentLogs as $log): ?>
        <tr class="border-b border-outline-variant/20 hover:bg-surface-variant/20">
          <td class="py-4 pr-4 font-semibold"><?= e($log['nama_gunung']??'-') ?></td>
          <td class="py-4 pr-4 text-sm"><?= e(formatDateId($log['tanggal_mulai']??null)) ?></td>
          <td class="py-4 pr-4 text-sm"><?= e(formatDateId($log['tanggal_selesai']??null)) ?></td>
          <td class="py-4"><?= statusBadge($log['status_hike']??'-') ?></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="4" class="py-12 text-center">
          <span class="material-symbols-outlined text-5xl text-on-surface-variant/30">hiking</span>
          <p class="text-on-surface-variant mt-3">Belum ada riwayat pendakian.</p>
          <button onclick="document.getElementById('modal-log').classList.remove('hidden')" class="btn-primary inline-flex mt-4">Catat Pendakian Pertama</button>
        </td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Tambah Log Pendakian -->
<div id="modal-log" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modal-log').classList.add('hidden')"></div>
  <div class="relative bg-white rounded-[2rem] p-8 w-full max-w-lg shadow-2xl">
    <button class="absolute top-5 right-5 text-on-surface-variant hover:text-on-surface" onclick="document.getElementById('modal-log').classList.add('hidden')">
      <span class="material-symbols-outlined text-3xl">close</span>
    </button>
    <p class="text-xs uppercase tracking-[.3em] text-on-surface-variant font-bold mb-2">New Entry</p>
    <h3 class="text-2xl font-extrabold mb-6">Catat Pendakian</h3>
    <form method="POST" class="space-y-4">
      <input type="hidden" name="add_log" value="1">
      <div><label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Gunung *</label>
        <select name="mountain_id" class="w-full rounded-2xl bg-surface-variant/40 border-0 px-4 py-3 focus:ring-2 focus:ring-primary outline-none" required>
          <option value="">Pilih gunung</option>
          <?php foreach($mountains as $m):?><option value="<?=(int)$m['id']?>"><?=e($m['nama_gunung'])?></option><?php endforeach;?>
        </select>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Tanggal Mulai *</label><input type="date" name="tanggal_mulai" class="w-full rounded-2xl bg-surface-variant/40 border-0 px-4 py-3 focus:ring-2 focus:ring-primary outline-none" required></div>
        <div><label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Tanggal Selesai</label><input type="date" name="tanggal_selesai" class="w-full rounded-2xl bg-surface-variant/40 border-0 px-4 py-3 focus:ring-2 focus:ring-primary outline-none"></div>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Jarak (km)</label><input type="number" step="0.1" min="0" name="jarak_km" placeholder="0.0" class="w-full rounded-2xl bg-surface-variant/40 border-0 px-4 py-3 focus:ring-2 focus:ring-primary outline-none"></div>
        <div><label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Durasi (jam)</label><input type="number" step="0.5" min="0" name="durasi_jam" placeholder="0" class="w-full rounded-2xl bg-surface-variant/40 border-0 px-4 py-3 focus:ring-2 focus:ring-primary outline-none"></div>
      </div>
      <div><label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Status</label>
        <select name="status_hike" class="w-full rounded-2xl bg-surface-variant/40 border-0 px-4 py-3 focus:ring-2 focus:ring-primary outline-none">
          <option value="planned">Planned</option>
          <option value="ongoing">Ongoing</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>
      <div><label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Catatan</label><textarea name="catatan" rows="3" class="w-full rounded-2xl bg-surface-variant/40 border-0 px-4 py-3 focus:ring-2 focus:ring-primary outline-none" placeholder="Ceritakan pengalaman singkatmu..."></textarea></div>
      <button class="btn-primary w-full">Simpan Log Pendakian <span class="material-symbols-outlined">save</span></button>
    </form>
  </div>
</div>
<?php if($error): ?><script>document.getElementById('modal-log').classList.remove('hidden');</script><?php endif;?>
<?php include __DIR__.'/../includes/layout_bottom.php'; include __DIR__.'/../includes/footer.php'; ?>
