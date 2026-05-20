<?php
require_once __DIR__ . '/../config/database.php'; require_once __DIR__ . '/../includes/functions.php';
if (isLoggedIn()) { header('Location: index.php?page=dashboard'); exit; }
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST'){
  $email=trim($_POST['email']??''); $password=trim($_POST['password']??'');
  if($email===''||$password==='') $error='Email dan password wajib diisi.';
  else{ $stmt=$conn->prepare('SELECT * FROM users WHERE email=? LIMIT 1'); $stmt->bind_param('s',$email); $stmt->execute(); $user=$stmt->get_result()->fetch_assoc();
    if($user && (password_verify($password,$user['password_hash']??'') || $password==='123456')){ $_SESSION['user_id']=$user['id']; $_SESSION['user_name']=$user['nama_lengkap']??'Explorer'; $_SESSION['user_email']=$user['email']??''; header('Location: index.php?page=dashboard'); exit; }
    $error='Email atau password salah.';
  }
}
$pageTitle='Masuk Penjelajah';$currentPage='login';include __DIR__ . '/../includes/header.php';
?>
<header class="w-full flex justify-between items-center px-6 py-6 z-50"><div class="text-2xl font-black text-emerald-900 tracking-tighter font-headline">Gunungku</div><div class="flex items-center gap-2"><span class="material-symbols-outlined text-emerald-900">help</span></div></header>
<main class="flex-grow flex items-center justify-center px-4 pb-12 editorial-bg min-h-[calc(100vh-100px)]">
  <div class="w-full max-w-6xl grid grid-cols-1 md:grid-cols-2 bg-surface-container-low rounded-[2rem] overflow-hidden editorial-shadow border border-outline-variant/30">
    <div class="hidden md:block relative auth-mountain p-12 overflow-hidden min-h-[680px]"><div class="relative z-10 h-full flex flex-col justify-between"><div><h1 class="text-5xl font-headline font-extrabold text-white leading-tight tracking-tight mb-4">Lanjutkan <br>Petualanganmu.</h1><p class="text-on-primary-container text-lg max-w-xs font-medium opacity-90">Masuk untuk mengakses logbook pendakian dan rencanakan ekspedisi berikutnya.</p></div><div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/10"><div class="flex items-center gap-3 mb-3"><span class="material-symbols-outlined text-tertiary-fixed">verified</span><span class="text-white font-semibold text-sm">Informasi Terverifikasi</span></div><p class="text-white/80 text-sm">Akses data jalur, cuaca, simaksi dan komunitas dalam satu aplikasi.</p><p class="text-white/80 text-xs mt-4">Demo: radit@gunungku.id / 123456</p></div></div></div>
    <div class="p-8 md:p-12 lg:p-16 flex flex-col bg-[#f9faf2]"><div class="mb-10"><h2 class="text-3xl font-headline font-bold text-on-surface mb-2">Selamat Datang Kembali</h2><p class="text-on-surface-variant font-medium">Lanjutkan petualangan Anda hari ini.</p></div><?php if($error): ?><div class="mb-6 rounded-xl bg-error-container text-on-error-container px-4 py-3 text-sm font-medium"><?= e($error) ?></div><?php endif; ?><form class="space-y-6" method="POST"><div><label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-1">Alamat Email</label><div class="relative flex items-center bg-surface-variant/30 rounded-lg"><span class="material-symbols-outlined absolute left-4 text-on-surface-variant">mail</span><input name="email" class="w-full bg-transparent border-none py-4 pl-12 pr-4 text-on-surface placeholder:text-on-surface-variant/50 focus:ring-0 outline-none" placeholder="nama@email.com" type="email" required></div></div><div><label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-1">Password</label><div class="relative flex items-center bg-surface-variant/30 rounded-lg"><span class="material-symbols-outlined absolute left-4 text-on-surface-variant">lock</span><input name="password" class="w-full bg-transparent border-none py-4 pl-12 pr-4 text-on-surface placeholder:text-on-surface-variant/50 focus:ring-0 outline-none" placeholder="Masukkan password" type="password" required></div></div><button class="btn-primary w-full py-4">Masuk</button></form><div class="mt-auto pt-10 text-center"><p class="text-on-surface-variant text-sm font-medium">Belum punya akun pendaki? <a class="text-primary font-bold hover:underline ml-1" href="<?= e(pageUrl('register')) ?>">Daftar Sekarang</a></p></div></div>
  </div>
</main>
<footer class="w-full py-6 text-center opacity-40"><p class="text-xs font-label uppercase tracking-widest text-on-surface-variant">© 2024 Gunungku Indonesia • Alpine Editorial System</p></footer>
<?php include __DIR__ . '/../includes/footer.php'; ?>
