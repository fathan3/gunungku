<?php
require_once __DIR__ . '/../config/database.php'; require_once __DIR__ . '/../includes/functions.php'; requireLogin(); $userId=(int)$_SESSION['user_id']; $message=getFlash('kom_ok');$error='';

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['create_post'])){
  $judul=trim($_POST['judul']??'');$konten=trim($_POST['konten']??'');$cat=(int)($_POST['category_id']??0);
  if($konten==='') $error='Isi postingan tidak boleh kosong.';
  else{ $stmt=$conn->prepare('INSERT INTO community_posts (user_id,category_id,judul,konten,status_post) VALUES (?,?,?,?,"published")');$stmt->bind_param('iiss',$userId,$cat,$judul,$konten);
    if($stmt->execute()){setFlash('kom_ok','Postingan berhasil dibuat!');header('Location: '.pageUrl('komunitas'));exit;}
    else $error='Postingan gagal dibuat.'; }
}
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['like_post'])){
  $pid=(int)$_POST['post_id'];
  $chk=$conn->prepare('SELECT id FROM post_likes WHERE post_id=? AND user_id=?');$chk->bind_param('ii',$pid,$userId);$chk->execute();
  if($chk->get_result()->fetch_assoc()){
    $del=$conn->prepare('DELETE FROM post_likes WHERE post_id=? AND user_id=?');$del->bind_param('ii',$pid,$userId);$del->execute();
  } else {
    $ins=$conn->prepare('INSERT IGNORE INTO post_likes (post_id,user_id) VALUES (?,?)');$ins->bind_param('ii',$pid,$userId);$ins->execute();
  }
  $conn->query("UPDATE community_posts SET jumlah_like=(SELECT COUNT(*) FROM post_likes WHERE post_id={$pid}) WHERE id={$pid}");
  header('Location: '.pageUrl('komunitas')); exit;
}
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_comment'])){
  $pid=(int)$_POST['post_id'];$kom=trim($_POST['komentar']??'');
  if($kom!==''){$stmt=$conn->prepare('INSERT INTO post_comments (post_id,user_id,komentar) VALUES (?,?,?)');$stmt->bind_param('iis',$pid,$userId,$kom);$stmt->execute();
    $conn->query("UPDATE community_posts SET jumlah_komentar=(SELECT COUNT(*) FROM post_comments WHERE post_id={$pid}) WHERE id={$pid}");}
  header('Location: '.pageUrl('komunitas')); exit;
}

$categories=[];$posts=[];
if($q=$conn->query('SELECT * FROM post_categories ORDER BY nama_kategori')){while($r=$q->fetch_assoc())$categories[]=$r;}
if($q=$conn->query('SELECT cp.*,u.nama_lengkap,u.username,pc.nama_kategori,m.nama_gunung FROM community_posts cp LEFT JOIN users u ON u.id=cp.user_id LEFT JOIN post_categories pc ON pc.id=cp.category_id LEFT JOIN mountains m ON m.id=cp.mountain_id WHERE cp.status_post="published" ORDER BY cp.created_at DESC')){while($r=$q->fetch_assoc())$posts[]=$r;}

$likedPosts=[];
if($q=$conn->query("SELECT post_id FROM post_likes WHERE user_id={$userId}")){while($r=$q->fetch_assoc())$likedPosts[]=(int)$r['post_id'];}
$commentsByPost=[];
if($posts){$ids=implode(',',array_column($posts,'id'));if($ids&&$q=$conn->query("SELECT pc.*,u.nama_lengkap FROM post_comments pc LEFT JOIN users u ON u.id=pc.user_id WHERE pc.post_id IN({$ids}) ORDER BY pc.created_at ASC")){while($r=$q->fetch_assoc())$commentsByPost[$r['post_id']][]=$r;}}

$pageTitle='Community';$currentPage='komunitas'; include __DIR__.'/../includes/header.php'; include __DIR__.'/../includes/layout_top.php';
?>
<div class="grid xl:grid-cols-[1fr_360px] gap-6">
  <div class="space-y-6">
    <?php if($message):?><div class="bg-green-100 text-green-800 rounded-xl px-4 py-3 flex items-center gap-3"><span class="material-symbols-outlined">check_circle</span><?=e($message)?></div><?php endif;?>
    <?php if($error):?><div class="bg-error-container text-on-error-container rounded-xl px-4 py-3"><?=e($error)?></div><?php endif;?>
    <div class="editorial-card p-6">
      <h3 class="text-2xl font-extrabold mb-4">Bagikan Cerita Pendakian</h3>
      <form method="POST" class="space-y-4">
        <input type="hidden" name="create_post" value="1">
        <div class="grid md:grid-cols-2 gap-4">
          <input name="judul" class="rounded-2xl bg-surface-variant/40 border-0 px-4 py-3 focus:ring-2 focus:ring-primary outline-none" placeholder="Judul postingan">
          <select name="category_id" class="rounded-2xl bg-surface-variant/40 border-0 px-4 py-3 focus:ring-2 focus:ring-primary outline-none"><option value="0">Kategori</option><?php foreach($categories as $c):?><option value="<?= (int)$c['id']?>"><?=e($c['nama_kategori'])?></option><?php endforeach;?></select>
        </div>
        <textarea name="konten" rows="4" class="w-full rounded-2xl bg-surface-variant/40 border-0 px-4 py-3 focus:ring-2 focus:ring-primary outline-none" placeholder="Bagikan pengalaman, review jalur, kondisi cuaca, atau tips gear..." required></textarea>
        <button class="btn-primary">Posting Cerita <span class="material-symbols-outlined">send</span></button>
      </form>
    </div>

    <?php if($posts): foreach($posts as $p): $isLiked=in_array((int)$p['id'],$likedPosts); $postComments=$commentsByPost[$p['id']]??[]; ?>
    <article class="editorial-card p-6">
      <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-full bg-primary-container text-white flex items-center justify-center font-extrabold shrink-0"><?=e(initialName($p['nama_lengkap']??'U'))?></div>
        <div class="flex-1 min-w-0">
          <div class="flex flex-wrap justify-between gap-3">
            <div><h3 class="font-extrabold text-xl"><?=e($p['judul']?:'Cerita Pendakian')?></h3><p class="text-sm text-on-surface-variant"><?=e($p['nama_lengkap']??'User')?> • <?=e($p['nama_kategori']??'General')?> • <?=e(formatDateId($p['created_at']??null))?></p></div>
            <?php if($p['nama_gunung']):?><span class="badge-soft bg-primary-fixed text-primary"><?=e($p['nama_gunung'])?></span><?php endif;?>
          </div>
          <p class="mt-4 leading-7 text-slate-700"><?=nl2br(e($p['konten']))?></p>
          <div class="mt-5 flex flex-wrap items-center gap-3">
            <form method="POST"><input type="hidden" name="like_post" value="1"><input type="hidden" name="post_id" value="<?= (int)$p['id']?>">
              <button class="flex items-center gap-2 rounded-2xl px-4 py-2 font-bold text-sm transition <?=$isLiked?'bg-rose-100 text-rose-600':'bg-surface-variant/60 text-on-surface-variant hover:bg-rose-50 hover:text-rose-500'?>">
                <span class="material-symbols-outlined text-base <?=$isLiked?'material-fill':''?>">favorite</span> <?= (int)$p['jumlah_like']?> Like
              </button>
            </form>
            <span class="flex items-center gap-2 text-sm text-on-surface-variant"><span class="material-symbols-outlined text-base">chat_bubble</span> <?= (int)$p['jumlah_komentar']?> Komentar</span>
          </div>

          <?php if($postComments): ?>
          <div class="mt-5 space-y-3 border-t border-outline-variant/20 pt-4">
            <?php foreach($postComments as $cm): ?>
            <div class="flex gap-3">
              <div class="w-8 h-8 rounded-full bg-secondary-fixed text-secondary flex items-center justify-center font-bold text-sm shrink-0"><?=e(initialName($cm['nama_lengkap']??'U'))?></div>
              <div class="flex-1 min-w-0 bg-surface-variant/40 rounded-2xl px-4 py-3">
                <p class="font-bold text-sm"><?=e($cm['nama_lengkap']??'User')?></p>
                <p class="text-sm text-slate-700 mt-0.5"><?=nl2br(e($cm['komentar']))?></p>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>

          <form method="POST" class="mt-4 flex gap-3">
            <input type="hidden" name="add_comment" value="1"><input type="hidden" name="post_id" value="<?= (int)$p['id']?>">
            <input name="komentar" class="flex-1 rounded-2xl bg-surface-variant/40 border-0 px-4 py-3 focus:ring-2 focus:ring-primary outline-none text-sm" placeholder="Tulis komentar...">
            <button class="btn-primary py-3 px-4"><span class="material-symbols-outlined">send</span></button>
          </form>
        </div>
      </div>
    </article>
    <?php endforeach; else:?>
    <div class="editorial-card p-16 text-center">
      <span class="material-symbols-outlined text-6xl text-on-surface-variant/30">groups</span>
      <h3 class="text-2xl font-extrabold mt-4">Belum Ada Postingan</h3>
      <p class="text-on-surface-variant mt-2">Jadilah yang pertama berbagi cerita pendakian!</p>
    </div>
    <?php endif; ?>
  </div>

  <aside class="space-y-6">
    <div class="editorial-card p-6"><h3 class="text-xl font-extrabold mb-4">Kategori Populer</h3><div class="flex flex-wrap gap-2"><?php foreach($categories as $c):?><span class="badge-soft bg-secondary-fixed text-secondary"><?=e($c['nama_kategori'])?></span><?php endforeach;?></div></div>
    <div class="editorial-card p-6 bg-alpine-gradient text-white"><h3 class="text-xl font-extrabold mb-3">Trail Talks</h3><p class="text-white/80">Gunakan komunitas untuk berbagi kondisi jalur terbaru, review basecamp, dan rekomendasi perlengkapan.</p></div>
    <div class="editorial-card p-6"><h3 class="text-xl font-extrabold mb-3">Statistik</h3><div class="space-y-3"><div class="editorial-card-soft p-4 flex justify-between items-center"><span class="text-on-surface-variant text-sm">Total Postingan</span><b class="text-primary"><?=count($posts)?></b></div><div class="editorial-card-soft p-4 flex justify-between items-center"><span class="text-on-surface-variant text-sm">Total Komentar</span><b class="text-primary"><?=array_sum(array_map('count',$commentsByPost))?></b></div></div></div>
  </aside>
</div>
<?php include __DIR__.'/../includes/layout_bottom.php'; include __DIR__.'/../includes/footer.php'; ?>
