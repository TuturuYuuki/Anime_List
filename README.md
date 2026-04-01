# Anime & Waifu Vault

Aplikasi web berbasis PHP untuk mengelola koleksi anime dan waifu pribadi, lengkap dengan pencarian, progress, favorit, galeri gambar, dan dukungan PWA.

## Ringkasan

Anime & Waifu Vault membantu kamu menyimpan daftar tontonan anime dan karakter waifu dalam satu dashboard. Data disimpan di MySQL, UI dibangun dengan Vanilla JS + Tailwind CDN, dan aplikasi bisa di-install seperti app di perangkat mobile.

## Fitur Utama

- Manajemen koleksi anime (tambah, edit, hapus, status, progress episode)
- Manajemen koleksi waifu (tambah, edit, hapus, favorit)
- Galeri waifu (official pict dan fanart)
- Integrasi pencarian data anime/karakter via API eksternal
- Autentikasi akun pengguna
- PWA (manifest + service worker)

## Struktur Proyek

```text
anime_waifu_vault/
|- index.php
|- api.php
|- db.php
|- auth.js
|- search_api.js
|- service-worker.js
|- manifest.json / manifest.php
|- database.sql
|- icons/
|- uploads/
`- vendor/
```

## Cara Menjalankan (Local)

1. Clone repository ini ke web root lokal (XAMPP/Laragon).
2. Buat database MySQL baru.
3. Import file `database.sql` ke database.
4. Sesuaikan konfigurasi koneksi database di `db.php` untuk environment lokalmu.
5. Jalankan server lokal, lalu buka aplikasi dari browser.

## Keamanan dan Privasi

- Jangan commit kredensial database, token, atau data rahasia lain ke repository.
- Folder `uploads/` diperlakukan sebagai data privat pengguna dan di-ignore dari Git.
- Jika ada file sensitif yang pernah ter-track, lakukan untrack sebelum push.
- Gunakan nilai konfigurasi yang aman untuk environment produksi.

## Catatan Pengembangan

- Repository ini ditujukan untuk source code aplikasi.
- Data pengguna, file upload, dan artefak runtime tidak untuk dipublikasikan.
- Untuk kontribusi, pastikan perubahan tidak memasukkan data pribadi atau rahasia.
