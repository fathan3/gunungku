<?php
require_once __DIR__ . '/../config/database.php'; require_once __DIR__ . '/../includes/functions.php'; requireLogin(); $trailId=(int)($_GET['trail_id']??1); $trails=[];$waypoints=[];$trail=null; if($q=$conn->query('SELECT t.*,m.nama_gunung,m.ketinggian_mdpl FROM trails t LEFT JOIN mountains m ON m.id=t.mountain_id ORDER BY t.id')){while($r=$q->fetch_assoc()){$trails[]=$r;if(!$trail||$r['id']==$trailId)$trail=$r;}} if($trail){$tid=(int)$trail['id']; if($q=$conn->query("SELECT * FROM trail_waypoints WHERE trail_id={$tid} ORDER BY urutan")){while($r=$q->fetch_assoc())$waypoints[]=$r;}} $pageTitle='Peta Jalur Interaktif';$currentPage='peta'; include __DIR__.'/../includes/header.php'; include __DIR__.'/../includes/layout_top.php';
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="grid xl:grid-cols-[1fr_380px] gap-6 min-h-[680px]">
  <div class="editorial-card overflow-hidden relative" style="height:680px;">
    <div id="map" class="w-full h-full"></div>
    <div class="absolute bottom-8 left-8 right-8 glass-panel rounded-2xl p-5 border border-white/50 z-[1000]">
        <p class="text-xs uppercase tracking-[.3em] text-on-surface-variant">Active Trail</p>
        <h3 class="text-3xl font-extrabold text-primary mt-1"><?=e($trail['nama_gunung']??'Gunungku')?> • <?=e($trail['nama_jalur']??'Jalur')?></h3>
        <p class="text-on-surface-variant mt-2">Jarak <?=e($trail['jarak_km']??'-')?> km • Estimasi <?=e($trail['estimasi_jam']??'-')?> jam • <?=e($trail['tingkat_kesulitan']??'-')?></p>
    </div>
  </div>
  <aside class="space-y-6"><div class="editorial-card p-6"><h3 class="text-xl font-extrabold mb-4">Pilih Jalur</h3><div class="space-y-3"><?php foreach($trails as $t):?><a class="block p-4 rounded-2xl <?=($trail&&$trail['id']==$t['id'])?'bg-primary text-white':'bg-surface-variant/50 text-on-surface hover:bg-surface-container-high'?>" href="<?=e(pageUrl('peta',['trail_id'=>(int)$t['id']]))?>"><b><?=e($t['nama_jalur'])?></b><p class="text-sm opacity-80"><?=e($t['nama_gunung'])?></p></a><?php endforeach;?></div></div><div class="editorial-card p-6"><h3 class="text-xl font-extrabold mb-4">Waypoint</h3><div class="space-y-3 max-h-[360px] overflow-auto custom-scrollbar"><?php foreach($waypoints as $w):?><div class="editorial-card-soft p-4"><b><?=e($w['nama_waypoint'])?></b><p class="text-sm text-on-surface-variant">Ketinggian <?=e($w['ketinggian_mdpl'])?> mdpl • <?=e($w['jarak_dari_start_km'])?> km</p><p class="text-sm text-on-surface-variant mt-1"><?=e($w['keterangan'])?></p></div><?php endforeach;?></div></div></aside>
</div>

<script>
    var map = L.map('map').setView([-7.5, 110.5], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    <?php foreach($waypoints as $w): ?>
    L.marker([<?= (float)$w['lat'] ?>, <?= (float)$w['lng'] ?>])
        .addTo(map)
        .bindPopup("<b><?= e($w['nama_waypoint']) ?></b>");
    <?php endforeach; ?>
</script>

<?php include __DIR__.'/../includes/layout_bottom.php'; include __DIR__.'/../includes/footer.php'; ?>
