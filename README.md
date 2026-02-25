# 🌸 Anime & Waifu Vault — Setup Guide

## Struktur File

```
anime_waifu_vault/
├── index.php           # Halaman utama (Dashboard, Anime, Waifu)
├── api.php             # Backend handler (CRUD via PDO)
├── db.php              # Koneksi database + helper functions
├── search_api.js       # Logika pencarian Jikan API
├── manifest.json       # PWA manifest (install ke HP)
├── service-worker.js   # Service Worker (offline support)
├── database.sql        # Script SQL untuk buat database
├── icons/              # ← Buat folder ini + isi icon PWA
│   ├── icon-72.png
│   ├── icon-96.png
│   ├── icon-128.png
│   ├── icon-144.png
│   ├── icon-152.png
│   ├── icon-192.png
│   ├── icon-384.png
│   └── icon-512.png
└── uploads/            # ← Auto-dibuat otomatis saat pertama run
    ├── anime/
    ├── waifu/
    └── fanart/
```

## Cara Setup (XAMPP)

### 1. Taruh file di XAMPP
Salin seluruh folder ke:
```
C:/xampp/htdocs/anime_waifu_vault/
```

### 2. Import Database
- Buka `phpMyAdmin` → http://localhost/phpmyadmin
- Klik **"Import"**
- Pilih file `database.sql` → klik **"Go"**

### 3. Sesuaikan koneksi (jika perlu)
Buka `db.php` dan edit jika passwordmu bukan kosong:
```php
define('DB_USER', 'root');     // username MySQL
define('DB_PASS', '');          // password MySQL (kosong di XAMPP default)
```

### 4. Buat folder icons
Buat folder `icons/` dan isi dengan icon PNG ukuran sesuai.
Kamu bisa generate icon gratis di: https://realfavicongenerator.net

### 5. Akses aplikasi
Buka browser → http://localhost/anime_waifu_vault/

### 6. Install sebagai App di HP (PWA)
- Buka di browser HP (Chrome Android)
- Tap menu ⋮ → **"Add to Home Screen"**
- Atau tunggu banner "Install App" muncul otomatis
- Di iOS Safari: tap Share → **"Add to Home Screen"**

---

## Fitur Lengkap

| Fitur | Keterangan |
|-------|-----------|
| 🔍 Cari Anime | Integrasi Jikan API v4 (MyAnimeList) |
| ✏️ Customizable | Edit judul, episode, gambar setelah pilih dari API |
| 📷 Upload Gambar | Upload gambar dari laptop/HP (max 5MB) |
| 📊 Progress Tracking | Lacak episode yang sudah ditonton |
| 💕 Waifu Favorit | Jadikan 1 waifu sebagai favorit, muncul di background |
| 🖼️ Gallery Waifu | Upload Official Pict & Fan Art terpisah |
| 📱 PWA Ready | Bisa diinstall sebagai app di HP |
| 🌐 Offline Support | Cache halaman utama untuk akses offline |
| 🎨 Glassmorphism | UI dark mode dengan efek blur transparan |

## Teknologi

- **Backend**: PHP 8+ dengan PDO (MySQL)
- **Frontend**: HTML5, Tailwind CSS v3 (CDN), Vanilla JS
- **API**: Jikan API v4 (https://api.jikan.moe/v4)
- **PWA**: Web App Manifest + Service Worker
- **Database**: MySQL via XAMPP

## Tips

- Jikan API punya rate limit ~3 req/detik, sudah ada debounce 500ms
- Gambar dari API disimpan sebagai URL (bukan download), jadi butuh internet
- Upload gambar sendiri = tersimpan lokal di folder `uploads/`
- Waifu favorit otomatis muncul sebagai background transparan di dashboard
