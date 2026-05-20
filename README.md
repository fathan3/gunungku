# 🏔️ Gunungku - Asisten & Manajemen Pendakian Gunung

**Gunungku** adalah aplikasi web berbasis PHP Native yang dirancang untuk membantu para pendaki gunung dalam merencanakan pendakian, mengelola perlengkapan (*checklist gear*), mendaftar simaksi secara digital, berbagi pengalaman di forum komunitas, serta berkonsultasi langsung dengan asisten pintar bertenaga **Google Gemini AI**.

---

## 🚀 Fitur Utama

1. **🤖 Gunungku Assistant (AI Chatbot)**:
   * Terintegrasi dengan **Google Gemini API** (menggunakan model dinamis dengan *automatic fallback* dari `gemini-3.5-flash` hingga `gemini-1.5-flash` untuk menjamin ketersediaan kuota).
   * Mendukung pemformatan Markdown pada hasil respons AI agar teks lebih mudah dibaca (bullet points, bold text, dll.).
   * Dilengkapi fitur *Typing Indicator* (animasi ketik bouncing) untuk UX yang responsif dan interaktif.
   * Fitur hapus riwayat obrolan secara langsung dengan satu klik.

2. **📝 Manajemen Simaksi (Registrasi Pendakian)**:
   * Formulir registrasi simaksi digital lengkap dengan verifikasi tanggal naik dan tanggal turun secara real-time.
   * Validasi client-side dan server-side yang memastikan tanggal turun tidak mendahului tanggal naik.

3. **🎒 Checklist Gear (Daftar Perlengkapan)**:
   * Kelola daftar perlengkapan mendaki Anda agar tidak ada barang yang tertinggal saat mendaki.

4. **🔍 Discovery & Informasi Gunung**:
   * Jelajahi informasi lengkap mengenai berbagai gunung, termasuk tingkat kesulitan rute pendakian, peta jalur, dan estimasi waktu tempuh.
   * Dilengkapi fitur pencarian (*search bar*) dan filter tingkat kesulitan untuk mempermudah pencarian.

5. **💬 Forum Komunitas**:
   * Berbagi cerita pendakian, foto, dan informasi terkini sesama pendaki.
   * Fitur interaksi suka (*like*) postingan secara real-time dan kolom komentar interaktif.

6. **📊 Dashboard Log Pendakian**:
   * Catat riwayat pendakian yang telah Anda selesaikan sebagai portofolio pendakian pribadi.

---

## 🛠️ Persyaratan Sistem

* **XAMPP / Laragon** (mendukung PHP 8.0 ke atas).
* **MySQL / MariaDB**.
* **Ekstensi cURL** pada PHP diaktifkan (bawaan XAMPP sudah aktif).
* Koneksi internet aktif (untuk integrasi Gemini API).

---

## 💻 Panduan Instalasi & Konfigurasi

### 1. Kloning atau Salin File Proyek
Ekstrak atau letakkan folder proyek ini ke dalam direktori web server lokal Anda:
* Jika menggunakan XAMPP di Windows: `C:\xampp\htdocs\gunungku`

### 2. Import Database
1. Jalankan panel kontrol XAMPP dan aktifkan modul **Apache** dan **MySQL**.
2. Buka browser dan akses **phpMyAdmin** di [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/).
3. Buat database baru bernama `gunungku`.
4. Import berkas `gunungku_xampp_database.sql` ke dalam database `gunungku` tersebut.

### 3. Konfigurasi API Gemini
Untuk menggunakan fitur chatbot AI:
1. Masuk ke direktori `config/`.
2. Salin berkas `gemini.example.php` dan ubah namanya menjadi `gemini.php`.
3. Buka berkas `config/gemini.php` dan masukkan API Key Gemini Anda di tempat yang disediakan:
   ```php
   return [
       'api_key' => 'INPUT_API_KEY_GEMINI_ANDA_DI_SINI',
   ];
   ```
   *(Catatan: Berkas `config/gemini.php` telah ditambahkan ke `.gitignore` sehingga API Key Anda aman dari eksposur publik saat didorong ke GitHub).*

### 4. Akses Aplikasi
Buka browser Anda dan akses tautan berikut:
👉 [http://localhost/gunungku/](http://localhost/gunungku/)

---

## 🔑 Akun Demo (Login Cepat)

Untuk menguji fitur-fitur di dalam aplikasi, Anda dapat menggunakan akun demo berikut:

| Peran (Role) | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@gunungku.id` | `123456` |
| **Pendaki** | `radit@gunungku.id` | `123456` |

*Catatan: Password akun demo di atas menggunakan enkripsi bawaan di database lokal. Untuk pendaftaran pengguna baru via halaman Register, enkripsi akan otomatis menggunakan hash password standar PHP (`password_hash`).*

---

## 📁 Struktur Proyek

```text
gunungku/
├── api/
│   ├── gemini.php                # Handler backend untuk API Gemini (cURL)
│   └── gemini.example.php        # Template konfigurasi API Gemini
├── assets/                       # Aset CSS, JS, dan gambar pendukung
├── config/
│   ├── database.php              # Pengaturan koneksi database MySQL
│   └── gemini.php                # Konfigurasi API Key (Diabaikan oleh git)
├── includes/                     # Komponen reusable (Header, Footer, Navbar)
├── pages/                        # Halaman fungsional (Chatbot, Simaksi, Komunitas, dll.)
├── index.php                     # Router dan gerbang utama aplikasi
├── .gitignore                    # Konfigurasi ignore git
└── gunungku_xampp_database.sql   # Dump database MySQL
```
