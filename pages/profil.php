<?php
require_once __DIR__ . '/../config/database.php'; require_once __DIR__ . '/../includes/functions.php'; requireLogin();
$userId=(int)$_SESSION['user_id']; $message=getFlash('profil_ok'); $error='';

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_profil'])){
  $nama=trim($_POST['nama_lengkap']??'');
  $uname=trim($_POST['username']??'') ?: null;
  $bio=trim($_POST['bio']??'');
  if($nama==='') $error='Nama lengkap wajib diisi.';
  else {
    $stmt=$conn->prepare('UPDATE users SET nama_lengkap=?,username=?,bio=? WHERE id=?');
    $stmt->bind_param('sssi',$nama,$uname,$bio,$userId);
    if($stmt->execute()){ $_SESSION['user_name']=$nama; setFlash('profil_ok','Profil berhasil diperbarui!'); }
    else $error='Gagal memperbarui profil. Username mungkin sudah dipakai.';
  }
  if(!$error){ header('Location: '.pageUrl('profil')); exit; }
}

$user=[];$stats=[];$achievements=[];$logs=[];
if($q=$conn->query("SELECT * FROM users WHERE id={$userId} LIMIT 1"))$user=$q->fetch_assoc()?:[];
if($q=$conn->query("SELECT * FROM user_stats WHERE user_id={$userId} LIMIT 1"))$stats=$q->fetch_assoc()?:[];
if($q=$conn->query("SELECT a.* FROM user_achievements ua LEFT JOIN achievements a ON a.id=ua.achievement_id WHERE ua.user_id={$userId} ORDER BY ua.earned_at DESC")){while($r=$q->fetch_assoc())$achievements[]=$r;}
if($q=$conn->query("SELECT hl.*,m.nama_gunung FROM hike_logs hl LEFT JOIN mountains m ON m.id=hl.mountain_id WHERE hl.user_id={$userId} ORDER BY hl.tanggal_mulai DESC")){while($r=$q->fetch_assoc())$logs[]=$r;}
$pageTitle='Profil & Achievement';$currentPage='profil'; include __DIR__.'/../includes/header.php'; include __DIR__.'/../includes/layout_top.php';
?>
<div class="grid xl:grid-cols-[380px_1fr] gap-6">
  <!-- Sidebar Profil -->
  <div class="space-y-6">
    <div class="editorial-card p-8">
      <div class="w-24 h-24 rounded-full bg-primary text-white flex items-center justify-center text-4xl font-extrabold mb-5"><?=e(initialName($user['nama_lengkap']??userName()))?></div>
      <h3 class="text-2xl font-extrabold"><?=e($user['nama_lengkap']??userName())?></h3>
      <p class="text-on-surface-variant mt-1">@<?=e($user['username']??'explorer')?></p>
      <p class="mt-4 text-slate-700 leading-7 text-sm"><?=e($user['bio']??'Life is better at 3,000 meters.')?></p>
      <div class="mt-6 space-y-2 text-sm">
        <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-on-surface-variant">mail</span><?=e($user['email']??userEmail())?></p>
        <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-on-surface-variant">grade</span>Level: <b><?=e($user['level_pendaki']??'Explorer')?></b></p>
      </div>
      <button class="btn-soft w-full mt-6 flex items-center justify-center gap-2" onclick="document.getElementById('edit-profil-panel').classList.toggle('hidden')">
        <span class="material-symbols-outlined text-sm">edit</span> Edit Profil
      </button>
    </div>

    <!-- Edit Profil Form -->
    <div id="edit-profil-panel" class="editorial-card p-6 hidden">
      <h3 class="text-xl font-extrabold mb-4">Edit Profil</h3>
      <?php if($error):?><div class="mb-4 rounded-xl bg-error-container text-on-error-container px-4 py-3 text-sm"><?=e($error)?></div><?php endif;?>
      <?php if($message):?><div class="mb-4 rounded-xl bg-green-100 text-green-800 px-4 py-3 text-sm flex items-center gap-2"><span class="material-symbols-outlined text-sm">check_circle</span><?=e($message)?></div><?php endif;?>
      <form method="POST" class="space-y-4">
        <input type="hidden" name="update_profil" value="1">
        <div><label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Nama Lengkap *</label><input name="nama_lengkap" value="<?=e($user['nama_lengkap']??'')?>" class="w-full rounded-2xl bg-surface-variant/40 border-0 px-4 py-3 focus:ring-2 focus:ring-primary outline-none" required></div>
        <div><label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Username</label><input name="username" value="<?=e($user['username']??'')?>" placeholder="opsional" class="w-full rounded-2xl bg-surface-variant/40 border-0 px-4 py-3 focus:ring-2 focus:ring-primary outline-none"></div>
        <div><label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Bio</label><textarea name="bio" rows="3" class="w-full rounded-2xl bg-surface-variant/40 border-0 px-4 py-3 focus:ring-2 focus:ring-primary outline-none"><?=e($user['bio']??'')?></textarea></div>
        <button class="btn-primary w-full">Simpan Perubahan</button>
      </form>
    </div>
    <?php if($message && !$error): ?>
    <script>document.getElementById('edit-profil-panel').classList.remove('hidden');</script>
    <?php endif; ?>
  </div>

  <!-- Konten Kanan -->
  <div class="space-y-6">
    <div class="editorial-card p-8">
      <h3 class="text-2xl font-extrabold mb-6">Statistik Pendakian</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="editorial-card-soft p-5 text-center"><b class="text-3xl text-primary block"><?= (int)($stats['total_summit']??0) ?></b><p class="text-on-surface-variant text-sm mt-1">Summit</p></div>
        <div class="editorial-card-soft p-5 text-center"><b class="text-3xl text-primary block"><?=number_format((float)($stats['total_km']??0),1)?></b><p class="text-on-surface-variant text-sm mt-1">Total KM</p></div>
        <div class="editorial-card-soft p-5 text-center"><b class="text-3xl text-primary block"><?= (int)($stats['total_elevation_gain']??0) ?></b><p class="text-on-surface-variant text-sm mt-1">Elevation (m)</p></div>
        <div class="editorial-card-soft p-5 text-center"><b class="text-3xl text-primary block"><?= (int)($stats['total_jam_trail']??0) ?></b><p class="text-on-surface-variant text-sm mt-1">Jam Trail</p></div>
      </div>
    </div>

    <div class="editorial-card p-8">
      <h3 class="text-2xl font-extrabold mb-5">Achievements</h3>
      <?php if($achievements): ?>
      <div class="flex flex-wrap gap-3">
        <?php foreach($achievements as $a):?><span class="badge-soft bg-secondary-fixed text-tertiary-container px-5 py-3"><span class="material-symbols-outlined mr-2 text-sm"><?=e($a['icon_name']??'emoji_events')?></span><?=e($a['nama_badge'])?></span><?php endforeach;?>
      </div>
      <?php else:?>
      <div class="text-center py-8"><span class="material-symbols-outlined text-5xl text-on-surface-variant/30">emoji_events</span><p class="text-on-surface-variant mt-3">Belum ada achievement. Mulai mendaki!</p></div>
      <?php endif;?>
    </div>

    <div class="editorial-card p-8">
      <h3 class="text-2xl font-extrabold mb-5">Riwayat Pendakian</h3>
      <?php if($logs):?>
      <div class="overflow-auto"><table class="w-full text-left"><thead><tr class="border-b border-outline-variant/40"><th class="py-3 pr-4 text-sm font-bold">Gunung</th><th class="py-3 pr-4 text-sm font-bold">Mulai</th><th class="py-3 pr-4 text-sm font-bold">Selesai</th><th class="py-3 text-sm font-bold">Status</th></tr></thead><tbody>
      <?php foreach($logs as $l):?><tr class="border-b border-outline-variant/20 hover:bg-surface-variant/20"><td class="py-3 pr-4 font-semibold"><?=e($l['nama_gunung']??'-')?></td><td class="py-3 pr-4 text-sm"><?=e(formatDateId($l['tanggal_mulai']))?></td><td class="py-3 pr-4 text-sm"><?=e(formatDateId($l['tanggal_selesai']))?></td><td class="py-3"><?=statusBadge($l['status_hike']??'-')?></td></tr><?php endforeach;?>
      </tbody></table></div>
      <?php else:?>
      <div class="text-center py-8"><span class="material-symbols-outlined text-5xl text-on-surface-variant/30">hiking</span><p class="text-on-surface-variant mt-3">Belum ada riwayat pendakian.</p><a href="<?=e(pageUrl('dashboard'))?>" class="btn-primary inline-flex mt-4">Catat Pendakian</a></div>
      <?php endif;?>
    </div>
  </div>
</div>
<?php include __DIR__.'/../includes/layout_bottom.php'; include __DIR__.'/../includes/footer.php'; ?>
