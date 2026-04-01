<!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="theme-color" content="#7c3aed">
        <meta name="description" content="Anime & Waifu Vault - Koleksi Anime dan Waifu Favoritmu">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-title" content="WaifuVault">
        <title>Anime & Waifu Vault ✨</title>
        <link rel="icon" type="image/png" href="icons/icon.png">
        <link rel="apple-touch-icon" href="icons/icon.png">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" crossorigin="anonymous">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js" crossorigin="anonymous"></script>
        <link rel="manifest" href="manifest.php" crossorigin="use-credentials">
        <link rel="apple-touch-icon" href="icons/icon.png">
        <script src="vendor/tailwindcss-cdn.js"></script>
        <!-- SortableJS: dimuat dari CDN, TIDAK dicache oleh SW (lihat service-worker.js Rule 0) -->
        <!-- Browser mengelola HTTP cache-nya sendiri. crossorigin dihapus agar tidak ada CORS preflight -->
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Nunito:wght@400;600;700;800&display=swap');

            
            

            .logo img {
                width: 48px;
                height: 48px;
                border-radius: 50%; 
                border: 2px solid #7c3aed; 
            }
            
            #splash-screen img {
                width: 100px;
                height: 100px;
                border-radius: 50%;
                object-fit: cover;
            }

            html, body {
                max-width: 100%;
                overflow-x: hidden; 
                position: relative;
                margin: 0;
                padding: 0;
            }

            main {
                overflow-x: hidden; 
                width: 100%;
            }

            /* KEMBALI KE ANIMASI LAMA YANG SANGAT RINGAN DI HP (HW Accelerated) */
            .card-entrance {
                /* Animasi otomatis jalan saat halaman dibuka, tanpa menunggu scroll */
                animation: cardFadeIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
                /* Mengaktifkan GPU HP untuk menanggung beban animasi */
                transform: translateZ(0);
                -webkit-transform: translateZ(0);
                will-change: transform, opacity;
            }

            @keyframes cardFadeIn {
                from { 
                    opacity: 0; 
                    transform: translateY(30px) translateZ(0); 
                    -webkit-transform: translateY(30px) translateZ(0);
                }
                to { 
                    opacity: 1; 
                    transform: translateY(0) translateZ(0); 
                    -webkit-transform: translateY(0) translateZ(0);
                }
            }

            
            .page-header-sticky {
                position: relative; 
                background: transparent !important; 
                backdrop-filter: none !important; 
                -webkit-backdrop-filter: none !important;
                border: none !important; 
                box-shadow: none !important; 
                padding-left: 0 !important;
                padding-right: 0 !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
            }

            :root {
                --glass: rgba(255,255,255,0.08);
                --glass-border: rgba(255,255,255,0.15);
                --purple-glow: rgba(167,139,250,0.3);
            }

            * { box-sizing: border-box; }
            body {
                font-family: 'Inter', sans-serif;
                background: #0f0a1e;
                min-height: 100vh;
                color: #e2d9f3;
                overflow-x: hidden;
            }
                html.modal-open,
                body.modal-open {
                    overflow: hidden;
                }
                body.modal-open {
                    position: fixed;
                    left: 0;
                    right: 0;
                    width: 100%;
                }

            /* Animated background */
            body::before {
                content: '';
                position: fixed;
                inset: 0;
                background: 
                    radial-gradient(ellipse at 20% 20%, rgba(124,58,237,0.2) 0%, transparent 60%),
                    radial-gradient(ellipse at 80% 80%, rgba(236,72,153,0.15) 0%, transparent 60%),
                    radial-gradient(ellipse at 50% 50%, rgba(59,130,246,0.1) 0%, transparent 60%);
                z-index: 0;
                pointer-events: none;
            }
        
            /* --- MODERN SWITCHER (Adaptasi dari contoh.txt) --- */
            .modern-switcher {
                position: relative;
                display: grid;
                grid-template-columns: 1fr 1fr;
                padding: 4px;
                background: rgba(255, 255, 255, 0.05); /* Warna Glass */
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 12px;
                box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
                width: 100%;
                min-width: 200px;
            }

            @media (min-width: 640px) {
                .modern-switcher { width: fit-content; min-width: 240px; }
            }

            .modern-switcher input[type="radio"] { display: none; }

            .modern-switcher label {
                position: relative;
                z-index: 10;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 8px 16px;
                cursor: pointer;
                border-radius: 8px;
                transition: color 0.3s ease;
                color: #6b7280; /* Gray (Non-aktif) */
            }

            .modern-switcher label:has(input:checked) {
                color: #c4b5fd; /* Purple Terang (Aktif) */
            }

            /* Layer / Kaca yang Melayang */
            .modern-switcher .layer {
                position: absolute;
                top: 4px;
                bottom: 4px;
                left: 4px;
                width: calc(50% - 4px);
                background: rgba(167, 139, 250, 0.25);
                border: 1px solid rgba(167, 139, 250, 0.4);
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(124, 58, 237, 0.2);
                z-index: 1;
                pointer-events: none;
                transition: left 0.4s cubic-bezier(0.4, 0, 0.2, 1), width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                will-change: left, width;
                transform: translateZ(0);
            }

            /* Posisi Kaca */
            .modern-switcher:has(label:nth-child(1) input:checked) .layer { left: 4px; }
            .modern-switcher:has(label:nth-child(2) input:checked) .layer { left: 50%; }

            /* Animasi Membal (Squish) dari trackPrevious di contoh.txt */
            .modern-switcher[c-previous="1"]:has(label:nth-child(2) input:checked) .layer {
                animation: slideRight 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            }
            .modern-switcher[c-previous="2"]:has(label:nth-child(1) input:checked) .layer {
                animation: slideLeft 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            }

            @keyframes slideRight {
                0% { left: 4px; width: calc(50% - 4px); }
                50% { left: 4px; width: calc(100% - 8px); }
                100% { left: 50%; width: calc(50% - 4px); }
            }
            @keyframes slideLeft {
                0% { left: 50%; width: calc(50% - 4px); }
                50% { left: 4px; width: calc(100% - 8px); }
                100% { left: 4px; width: calc(50% - 4px); }
            }

            /* --- MODERN FILTER SWITCHER (For multiple options) --- */
            .modern-filter-switcher {
                position: relative;
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                padding: 4px;
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.08);
                border-radius: 12px;
                box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
                width: 100%;
                scrollbar-width: none;
                -ms-overflow-style: none;
            }
            .modern-filter-switcher::-webkit-scrollbar { display: none; }
            .modern-filter-switcher input[type="radio"] { display: none; }
            .modern-filter-switcher label {
                position: relative;
                z-index: 10;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 8px 16px;
                cursor: pointer;
                border-radius: 8px;
                transition: color 0.3s ease;
                color: #6b7280;
                white-space: nowrap;
                flex: 1 0 auto;
            }
            .modern-filter-switcher label:has(input:checked) {
                color: #c4b5fd;
            }
            .modern-filter-switcher .layer {
                position: absolute;
                top: 4px;
                bottom: 4px;
                left: 4px;
                width: 0px;
                background: rgba(167, 139, 250, 0.25);
                border: 1px solid rgba(167, 139, 250, 0.4);
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(124, 58, 237, 0.2);
                z-index: 1;
                pointer-events: none;
            }

            /* Waifu Background */
            #waifu-bg {
                position: fixed;
                bottom: 0;
                right: 0;
                z-index: 0;
                pointer-events: none;
                transition: all 0.8s ease-in-out;
                opacity: 0; 
                object-fit: contain;
                object-position: bottom right;
                
                /* Teknik Masking: Agar gambar memudar halus ke kiri dan atas */
            -webkit-mask-image: linear-gradient(to right, rgba(0,0,0,0) 0%, rgba(0,0,0,1) 80%),
                                    linear-gradient(to top, rgba(0,0,0,1) 70%, rgba(0,0,0,0) 100%);
                mask-image: linear-gradient(to right, rgba(0,0,0,0) 0%, rgba(0,0,0,1) 80%),
                            linear-gradient(to top, rgba(0,0,0,1) 70%, rgba(0,0,0,0) 100%);
                -webkit-mask-composite: source-in;
                mask-composite: intersect;
            }

            /* Pengaturan khusus di Web / laptop */
            @media (min-width: 1024px) {
                #waifu-bg {
                    height: 95vh;
                    width: 45vw;
                    right: -2%; /* Sedikit dirapatkan ke kanan */
                }
                #waifu-bg.active { opacity: 0.3; } /* Opacity sedikit dinaikkan agar cantik */
            }

            /* Pengaturan khusus di HP */        
            /* index.php — Perbaikan agar BG Waifu tidak turun/naik saat search bar Chrome toggle */
            @media (max-width: 1023px) {
                #waifu-bg {
                    position: fixed !important; 
                    
                    /* 1. Gunakan svh (Small Viewport) agar tinggi tetap konsisten */
                    height: 75svh !important; 
                    width: auto;
                    max-width: 90vw;
                    right: 0 !important;

                    /* 2. KUNCI POSISI: svh memastikan jarak dari atas tidak berubah saat search bar sembunyi */
                    top: 12svh !important; 
                    bottom: auto !important;
                    margin: 0 !important;
                    
                    transform: none !important; 
                    -webkit-transform: translateZ(0);
                    transform: translateZ(0);

                    object-fit: contain;
                    object-position: center right;
                    pointer-events: none;

                    /* Masking tetap sama */
                    -webkit-mask-image: 
                        linear-gradient(to right, rgba(0,0,0,0) 0%, rgba(0,0,0,1) 40%),
                        linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,1) 15%, rgba(0,0,0,1) 85%, rgba(0,0,0,0) 100%);
                    mask-image: 
                        linear-gradient(to right, rgba(0,0,0,0) 0%, rgba(0,0,0,1) 40%),
                        linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,1) 15%, rgba(0,0,0,1) 85%, rgba(0,0,0,0) 100%);
                    
                    -webkit-mask-composite: source-in;
                    mask-composite: intersect;
                }

                #waifu-bg.active { 
                    opacity: 0.25 !important; 
                } 
            }

            .glass {
                background: var(--glass);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid var(--glass-border);
            }

            .glass-card {
                background: linear-gradient(140deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0.05));
                border: 1px solid rgba(255, 255, 255, 0.28);
                box-shadow:
                    0 10px 25px rgba(0, 0, 0, 0.35),
                    inset 0 1px 0 rgba(255, 255, 255, 0.35),
                    inset 0 -1px 0 rgba(255, 255, 255, 0.08);
                backdrop-filter: blur(18px) saturate(145%);
                -webkit-backdrop-filter: blur(18px) saturate(145%);
                position: relative;
                overflow: hidden;
                border-radius: 16px;
                transition: all 0.3s ease;
            }
            .glass-card::before {
                content: '';
                position: absolute;
                inset: 0;
                pointer-events: none;
                background:
                    radial-gradient(140px 90px at 14% 10%, rgba(255, 255, 255, 0.26), transparent 70%),
                    radial-gradient(220px 130px at 85% 100%, rgba(167, 139, 250, 0.20), transparent 72%);
                z-index: 0;
                border-radius: inherit;
            }
            .glass-card > * {
                position: relative;
                z-index: 1;
            }
            .glass-card:hover {
                background: linear-gradient(140deg, rgba(255, 255, 255, 0.22), rgba(255, 255, 255, 0.08));
                border-color: rgba(167, 139, 250, 0.6);
                transform: translateY(-3px);
                box-shadow:
                    0 15px 35px rgba(0, 0, 0, 0.45),
                    inset 0 1px 0 rgba(255, 255, 255, 0.45),
                    inset 0 -1px 0 rgba(255, 255, 255, 0.1);
            }

            .glare-overlay {
                position: absolute;
                inset: 0;
                background: linear-gradient(-30deg,
                    hsla(0,0%,0%,0) 60%,
                    rgba(199, 101, 255, 0.50) 80%,
                    hsla(0,0%,0%,0) 100%);
                background-size: 350% 350%, 100% 100%;
                background-repeat: no-repeat;
                background-position: -100% -100%, 0 0;
                pointer-events: none;
                border-radius: inherit;
                z-index: 15;
            }

            /* Navbar */
            #navbar {
                position: fixed;
                top: 0; left: 0; right: 0;
                z-index: 100;
                background: rgba(15,10,30,0.7);
                backdrop-filter: blur(20px);
                border-bottom: 1px solid rgba(167,139,250,0.2);
            }

            /* Sidebar tabs */
            .tab-btn {
                transition: color 0.4s ease; /* Transisi warna teks yang smooth */
                position: relative;
                z-index: 10;
            }
            .tab-btn.active {
                color: #c4b5fd;
            }
            #nav-indicator {
                position: absolute;
                bottom: -1px; /* Menempel pada border bawah navbar */
                height: 2px;
                background: linear-gradient(90deg, #7c3aed, #ec4899);
                border-radius: 2px;
                /* Gerakan organik cubic-bezier */
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                pointer-events: none;
                z-index: 5;
            }

            /* Page sections */
            .page { 
                display: none; 
                opacity: 0;
                transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                will-change: transform, opacity, filter;
            }

            .page.slide-next {
                display: block;
                animation: slideNext 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            }

            /* Animasi Mundur (Ke kiri) */
            .page.slide-prev {
                display: block;
                animation: slidePrev 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            }

            @keyframes slideNext {
                from { opacity: 0; transform: translateX(30px); filter: blur(4px); }
                to { opacity: 1; transform: translateX(0); filter: blur(0); }
            }

            @keyframes slidePrev {
                from { opacity: 0; transform: translateX(-30px); filter: blur(4px); }
                to { opacity: 1; transform: translateX(0); filter: blur(0); }
            }

            /* Tetap butuh .active untuk kondisi awal/default */
            .page.active { display: block; opacity: 1; }
            

            /* Anime card */
            .anime-card {
                position: relative;
                overflow: hidden;
            }
            .anime-card .status-badge {
                font-size: 0.65rem;
                padding: 2px 8px;
                border-radius: 999px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }
            .status-watching { background: rgba(59,130,246,0.2); color: #93c5fd; border: 1px solid rgba(59,130,246,0.3); }
            .status-completed { background: rgba(34,197,94,0.2); color: #86efac; border: 1px solid rgba(34,197,94,0.3); }
            .status-on_hold { background: rgba(234,179,8,0.2); color: #fde047; border: 1px solid rgba(234,179,8,0.3); }
            .status-dropped { background: rgba(239,68,68,0.2); color: #fca5a5; border: 1px solid rgba(239,68,68,0.3); }
            .status-plan_to_watch { background: rgba(167,139,250,0.2); color: #c4b5fd; border: 1px solid rgba(167,139,250,0.3); }

            /* Progress bar */
            .progress-bar {
                height: 4px;
                background: rgba(255,255,255,0.1);
                border-radius: 4px;
                overflow: hidden;
            }
            .progress-fill {
                height: 100%;
                background: linear-gradient(90deg, #7c3aed, #ec4899);
                border-radius: 4px;
                transition: width 0.5s ease;
            }

            /* Input styles */
            .glass-input {
                background: rgba(255,255,255,0.06);
                border: 1px solid rgba(167,139,250,0.25);
                border-radius: 10px;
                padding: 10px 14px;
                color: #e2d9f3;
                width: 100%;
                outline: none;
                transition: all 0.2s ease;
                font-family: 'Inter', sans-serif;
            }
            .glass-input:focus {
                border-color: rgba(167,139,250,0.6);
                background: rgba(255,255,255,0.09);
                box-shadow: 0 0 0 3px rgba(124,58,237,0.15);
            }
            .glass-input::placeholder { color: rgba(255,255,255,0.3); }
            select.glass-input option { background: #1e1535; color: #e2d9f3; }

            /* Buttons */
            .btn-primary {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                color: white;
                padding: 10px 20px;
                border-radius: 10px;
                font-weight: 600;
                border: none;
                cursor: pointer;
                transition: all 0.2s ease;
                font-family: 'Nunito', sans-serif;
            }
            .btn-primary:hover {
                background: linear-gradient(135deg, #8b5cf6, #7c3aed);
                transform: translateY(-1px);
                box-shadow: 0 4px 15px rgba(124,58,237,0.4);
            }
            .btn-glass-primary {
                background: rgba(124, 58, 237, 0.15) !important;
                backdrop-filter: blur(12px) saturate(150%) url(#glass-displacement) !important;
                -webkit-backdrop-filter: blur(12px) saturate(150%) url(#glass-displacement) !important;
                border: 1px solid rgba(167, 139, 250, 0.3) !important;
                color: #c4b5fd !important;
                padding: 10px 20px;
                border-radius: 10px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 
                    0 0 2px 1px rgba(167, 139, 250, 0.1) inset,
                    0 4px 15px -3px rgba(124, 58, 237, 0.2);
                font-family: 'Nunito', sans-serif;
            }
            .btn-glass-primary:hover {
                background: rgba(124, 58, 237, 0.25) !important;
                border-color: rgba(167, 139, 250, 0.5) !important;
                transform: translateY(-2px);
                box-shadow: 
                    0 0 4px 1px rgba(167, 139, 250, 0.2) inset,
                    0 8px 20px -3px rgba(124, 58, 237, 0.4);
                color: #ffffff !important;
            }
            .btn-glass-primary:active {
                transform: scale(0.95) translateY(0);
            }
            .btn-danger {
                background: rgba(239,68,68,0.3);
                color: #fca5a5;
                font-weight: 800;
                letter-spacing: 0.5px;
                border: 1.5px solid rgba(239,68,68,0.5);
                padding: 6px 12px;
                border-radius: 8px;
                font-size: 0.8rem;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            .btn-danger:hover { background: rgba(239,68,68,0.5); border-color: rgba(239,68,68,0.7); color: #ffffff; }
            .btn-edit {
                background: rgba(167,139,250,0.3);
                color: #c4b5fd;
                font-weight: 800;
                letter-spacing: 0.5px;
                border: 1.5px solid rgba(167,139,250,0.5);
                padding: 6px 12px;
                border-radius: 8px;
                font-size: 0.8rem;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            .btn-edit:hover { background: rgba(167,139,250,0.5); border-color: rgba(167,139,250,0.7); color: #ffffff; }

            /* Waifu card */
            .waifu-card { position: relative; }
            .heart-btn {
                transition: all 0.3s ease;
                cursor: pointer;
            }
            .heart-btn.fav { color: #f43f5e; filter: drop-shadow(0 0 6px rgba(244,63,94,0.5)); }
            .heart-btn:not(.fav) { color: rgba(255,255,255,0.3); }
            .heart-btn:hover { transform: scale(1.2); }

            /* Search results */
            #search-results-list { max-height: 300px; overflow-y: auto; }
            #search-results-list::-webkit-scrollbar { width: 4px; }
            #search-results-list::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
            #search-results-list::-webkit-scrollbar-thumb { background: rgba(167,139,250,0.4); border-radius: 4px; }

            /* Cari dan ganti bagian ini di index.php */
            .search-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 10px;
                cursor: pointer;
                transition: all 0.2s; 
                border-bottom: none !important;          
                outline: none !important;            
                -webkit-tap-highlight-color: transparent;
            }

            /* Pastikan juga hover-nya halus tanpa garis tambahan */
            .search-item:hover {
                background: rgba(255, 255, 255, 0.05);
                border-radius: 8px; /* Memberi kesan modern saat melayang */
            }

            .search-thumb-wrap {
                width: 40px;
                height: 56px;
                border-radius: 8px;
                overflow: hidden;
                background: #2d1b4e;
                position: relative;
                flex-shrink: 0;
            }

            .search-thumb-wrap.waifu {
                width: 40px;
                height: 40px;
                border-radius: 999px;
            }

            .search-thumb-img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                opacity: 0;
                filter: blur(8px);
                transition: opacity 180ms ease, filter 220ms ease;
            }

            .search-thumb-img.loaded {
                opacity: 1;
                filter: blur(0);
            }

            .search-thumb-loading {
                position: absolute;
                inset: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                background: rgba(26, 16, 48, 0.35);
                transition: opacity 160ms ease;
            }

            .search-thumb-wrap.loaded .search-thumb-loading {
                opacity: 0;
                pointer-events: none;
            }

            .search-skeleton-item {
                cursor: default;
                pointer-events: none;
            }

            .search-skeleton-thumb,
            .search-skeleton-line {
                background: rgba(255, 255, 255, 0.08);
                animation: searchPulse 1.1s ease-in-out infinite;
            }

            .search-skeleton-pink .search-skeleton-thumb,
            .search-skeleton-pink .search-skeleton-line {
                background: rgba(236, 72, 153, 0.18);
            }

            .search-skeleton-pink .search-skeleton-line.short,
            .search-skeleton-pink .search-skeleton-line.tiny {
                background: rgba(236, 72, 153, 0.12);
            }

            .search-skeleton-thumb {
                width: 40px;
                height: 56px;
                border-radius: 8px;
                flex-shrink: 0;
            }

            .search-skeleton-lines {
                flex: 1;
                min-width: 0;
                display: flex;
                flex-direction: column;
                gap: 6px;
            }

            .search-skeleton-line {
                height: 10px;
                border-radius: 6px;
                width: 100%;
            }

            .search-skeleton-line.short {
                width: 72%;
                height: 9px;
            }

            .search-skeleton-line.tiny {
                width: 54%;
                height: 8px;
            }

            @keyframes searchPulse {
                0% { opacity: 0.45; }
                50% { opacity: 1; }
                100% { opacity: 0.45; }
            }

            .modal-box::-webkit-scrollbar { width: 4px; }
            .modal-box::-webkit-scrollbar-thumb { background: rgba(167,139,250,0.4); border-radius: 4px; }

            /* Toast */
            #toast {
                position: fixed;
                bottom: 24px;
                right: 24px;
                z-index: 999;
                transform: translateX(200%);
                transition: transform 0.3s ease;
            }
            #toast.show { transform: translateX(0); }

            /* Scrollbar global */
            ::-webkit-scrollbar { width: 5px; }
            ::-webkit-scrollbar-track { background: rgba(255,255,255,0.03); }
            ::-webkit-scrollbar-thumb { background: rgba(167,139,250,0.3); border-radius: 4px; }

            .label { font-size: 0.8rem; font-weight: 600; color: rgba(196,181,253,0.8); margin-bottom: 4px; display: block; }

            /* Image preview */
            .img-preview {
                width: 80px;
                height: 80px;
                object-fit: cover;
                border-radius: 10px;
                border: 2px solid rgba(167,139,250,0.3);
            }

            /* Waifu gallery */
            .gallery-img {
                border-radius: 12px;
                object-fit: cover;
                border: 2px solid rgba(255,255,255,0.1);
                transition: all 0.3s ease;
                cursor: pointer;
            }
            .gallery-img:hover {
                border-color: rgba(167,139,250,0.5);
                transform: scale(1.02);
            }

            /* Stats cards (StarBorder Effect) */
            .star-border-container {
                position: relative;
                overflow: hidden;
                border-radius: 14px;
                padding: 1.5px; /* Border thickness */
                background: rgba(255,255,255,0.06);
            }
            .stat-card-inner {
                position: relative;
                z-index: 10;
                background: rgba(26,16,48,0.4); /* Opaque dark background to see border */
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border-radius: 12.5px;
                padding: 16px;
                text-align: center;
                height: 100%;
            }
            @keyframes starMovementBar {
                0% { transform: translateX(0%); }
                100% { transform: translateX(250%); }
            }
            .star-movement-bottom, .star-movement-top {
                position: absolute;
                width: 80%;
                height: 50%;
                opacity: 1;
                left: -100%;
                border-radius: 0;
                animation: starMovementBar linear infinite alternate;
                z-index: 0;
            }
            .star-movement-bottom {
                bottom: 0;
                top: auto;
            }
            .star-movement-top {
                top: 0;
                bottom: auto;
            }

            @media (max-width: 640px) {
                .glass-input {
                    padding: 8px 12px !important;
                    font-size: 11px !important;
                }
                
                #anime-sub-controls {
                    padding: 10px !important; /* Kurangi ruang kosong di dalam kotak */
                    min-height: auto !important;
                }

                /* Tombol Status agar tidak terlalu raksasa, dinaikkan sedikit ukurannya */
                .filter-status-btn {
                    padding: 6px 14px !important;
                    font-size: 11px !important;
                }
            }

            /* index.php - Tambahkan di bagian bawah blok <style> */

            /* 1. Atur urutan tumpukan modal (Layering System) */
            #favorites-list-modal { z-index: 200; } /* Lapisan bawah */
            #anime-modal, #waifu-modal, #detail-modal-anime, #detail-modal-waifu, #crop-modal { 
                z-index: 300; /* Lapisan tengah (Detail & Edit muncul di atas favorit) */
            }
            #lightbox { z-index: 1000; } /* Lapisan atas (Gambar zoom) */
            #toast { z-index: 1200; }    /* Paling atas (Notifikasi) */

            /* 2. Perbaikan Header Modal agar tidak "transparan" saat di-scroll (Jembatan) */
            .sticky-header-fix {
                position: sticky;
                top: 0;
                z-index: 50; /* Pastikan di atas list */
                
                /* Efek Liquid Glass iOS: Tebal tapi tetap blur */
                background-color: rgba(26, 16, 48, 0.94) !important; 
                backdrop-filter: blur(20px) saturate(180%);
                -webkit-backdrop-filter: blur(20px) saturate(180%);
                
                /* Tambahan border halus dan bayangan agar ada dimensi */
                border-bottom: 1px solid rgba(167, 139, 250, 0.15);
                box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
                
                /* Spacing agar teks judul punya rongga dan tidak mepet ke atas kotak */
                padding: 1.5rem 1.5rem; 
            }

            /* index.php — Tambahkan di dalam blok <style> */
            .active-view {
                background: rgba(167, 139, 250, 0.2);
                color: #c4b5fd;
                box-shadow: 0 0 10px rgba(167, 139, 250, 0.1);
            }

            /* Desain List (Memanjang ke Samping) */
            .waifu-card-list {
                display: flex;
                flex-direction: row;
                height: 120px;
                align-items: center;
                transition: all 0.3s ease;
            }

            .waifu-card-list:hover {
                border-color: rgba(167, 139, 250, 0.4);
                background: rgba(255, 255, 255, 0.1);
            }

            .pagination-btn {
                padding: 6px 12px;
                border-radius: 8px;
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
                color: #a78bfa;
                font-size: 0.85rem;
                font-weight: 600;
                transition: all 0.2s ease;
            }

            .pagination-btn:hover:not(:disabled) {
                background: rgba(167, 139, 250, 0.2);
                border-color: rgba(167, 139, 250, 0.4);
            }

            .pagination-btn.active {
                background: #7c3aed;
                color: white;
                border-color: #7c3aed;
            }

            .pagination-btn:disabled {
                opacity: 0.3;
                cursor: not-allowed;
                color: #4b5563;
            }

            /* Animasi muncul untuk elemen filter */
            .filter-item-anim {
                animation: filterPop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            }

            @keyframes filterPop {
                from { 
                    opacity: 0; 
                    transform: scale(0.92) translateY(5px); 
                }
                to { 
                    opacity: 1; 
                    transform: scale(1) translateY(0); 
                }
            }

            /* Membuat transisi warna tombol aktif jadi smooth */
            .active-view {
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }

            /* Efek hover pada tombol filter status */
            .filter-status-btn {
                transition: all 0.3s ease;
            }

            .filter-row-element {
                height: 42px !important; /* Tinggi seragam untuk semua */
                display: flex;
                align-items: center;
            }

            @media (min-width: 640px) {
                .glass-input {
                    padding: 0 18px !important; /* Padding horizontal saja agar tinggi terkontrol */
                    height: 42px !important;
                    font-size: 13px !important;
                }
            }

            /* index.php — Ganti bagian ini untuk memperbaiki kasta lapisan */

            /* --- LIQUID GLASS DROPDOWN MENU (Perbaikan 100% Mirip txt) --- */
            .custom-dropdown-menu {
                position: absolute;
                top: calc(100% + 10px);
                left: 0;
                width: 100%;
                z-index: 99999 !important;
                display: none;
                
                /* Kunci efek pinggiran tebal membal */
                padding: 0.4rem;
                border-radius: 1.8rem;
                transition: padding 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
                isolation: isolate;
                background: transparent !important;
                border: none !important;
                box-shadow: none !important;
            }

            .custom-dropdown-menu.show {
                display: flex;
                flex-direction: column;
                animation: filterPop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            }

            /* Saat di-hover, padding luar membesar mendorong kaca ke dalam (Squish) */
            .custom-dropdown-menu:hover {
                padding: 0.6rem;
            }

            /* Mesin distorsi ditaruh di layer paling belakang menu */
            .custom-dropdown-menu::before {
                content: "";
                position: absolute;
                inset: 0;
                border-radius: inherit;
                /* TINT UNGU-PUTIH: Agak pekat tapi tetap transparan */
                background-color: rgba(200, 190, 255, 0.40); 
                /* FROSTED EFFECT: Blur dinaikkan menjadi 16px untuk efek embun tebal */
                backdrop-filter: blur(16px) url(#glass-distortion);
                -webkit-backdrop-filter: blur(16px) url(#glass-distortion);
                z-index: -1;
            }

            /* --- WADAH DALAM (Warna Kaca) --- */
            .glass-inner {
                display: flex;
                flex-direction: column;
                /* Latar kaca: agak pekat agar dropdown lebih kelihatan */
                background-color: rgba(255, 255, 255, 0.14); 
                /* Garis pembatas layer: cukup kelihatan tapi tidak terlalu mencolok */
                border: 1px solid rgba(255, 255, 255, 0.30);
                border-radius: 1.4rem;
                padding: 0.4rem;
                box-shadow: 
                    inset 0 2px 15px rgba(255, 255, 255, 0.04),
                    0 0 0 0.5px rgba(255, 255, 255, 0.10);
                /* BUGFIX: Pastikan dropdown-item di layer atas pseudo-element */
                position: relative;
                z-index: 1;
            }

            /* KUNCI UTAMA: Paksa list anime berada di lapisan paling belakang */
            #anime-list {
                position: relative !important;
                z-index: 1 !important;
            }

            /* Pastikan container filter punya kasta lebih tinggi dari list */
            #anime-sub-controls,
            #waifu-sub-controls {
                position: relative !important;
                z-index: 100 !important;
                overflow: visible !important;
            }

            .glass-input-search {
                padding-left: 2.75rem !important; /* pl-11 setara 44px */
            }

            .dropdown-item {
                padding: 12px 16px;
                font-size: 13px;
                font-weight: 700;
                /* Putih tegas agar terbaca di atas background apapun */
                color: #ffffff;
                /* Bayangan teks gelap tipis = kontras tinggi tanpa merusak estetika glass */
                text-shadow: 
                    0 1px 4px rgba(0, 0, 0, 0.6),
                    0 0 8px rgba(0, 0, 0, 0.4);
                letter-spacing: 0.03em;
                transition: all 0.2s ease-in;
                display: flex;
                align-items: center;
                gap: 14px;
                cursor: pointer;
                border-radius: 1rem;
                background: transparent;
                border-left: none !important;
                /* BUGFIX MOBILE: Pastikan item selalu bisa diklik/disentuh */
                position: relative;
                z-index: 2;
                pointer-events: auto;
            }

            /* Efek Hover & Active Item */
            .dropdown-item:hover, 
            .dropdown-item.active {
                /* Sorotan menu dibuat sangat tipis agar tetap elegan */
                background-color: rgba(255, 255, 255, 0.08);
                box-shadow: inset -1px -1px 2px rgba(0, 0, 0, 0.1);
                backdrop-filter: blur(2px);
                -webkit-backdrop-filter: blur(2px);
                color: #fff;
            }

            /* index.php — CSS untuk Efek Shiny Text Tanpa React */
            .shiny-text-vault {
                display: inline-block;
                /* Gradasi warna sesuai kode React Senpai (#7300e6 & #f993fb) */
                background: linear-gradient(120deg, #7300e6 0%, #7300e6 35%, #f993fb 50%, #7300e6 65%, #7300e6 100%);
                background-size: 200% auto;
                -webkit-background-clip: text;
                background-clip: text;
                -webkit-text-fill-color: transparent;
                /* Animasi 2 detik, linear, dan terus menerus */
                animation: shine-animation 2s linear infinite;
            }

            @keyframes shine-animation {
                0% { background-position: 150% center; }
                100% { background-position: -50% center; }
            }

            /* index.php — Gaya Kursor Drag */
            .drag-active .glass-card {
                border: 1px dashed rgba(167, 139, 250, 0.5);
            }

            #fav-modal-content.drag-active {
                -webkit-user-select: none;
                user-select: none;
                -webkit-touch-callout: none;
            }

            /* KUNCI UTAMA 1: Matikan klik pada elemen anak (seperti teks/gambar) agar HP menerima sinyal sentuhan yang 100% utuh untuk Drag! */
            #fav-modal-content.drag-active .glass-card * {
                pointer-events: none !important;
            }

            /*
             * FIX MOBILE DAD v40:
             * touch-action: pan-y DIHAPUS — ini akar masalah! pan-y membuat browser
             * "mencuri" event sentuhan sebelum SortableJS bisa mendeteksi drag.
             * Solusi: touch-action: none pada CONTAINER agar SortableJS pegang kendali penuh.
             * SortableJS (scroll:true + scrollEl) yang handle auto-scroll ke tepi.
             * content-visibility: visible wajib agar BoundingRect akurat untuk hit-testing.
             */
            #fav-modal-content.drag-active {
                touch-action: none;
                overscroll-behavior: none;
            }
            #fav-modal-content.drag-active .glass-card {
                touch-action: none !important;
                content-visibility: visible !important;
                contain-intrinsic-size: unset !important;
                cursor: grab;
                -webkit-user-drag: none;
            }
            #fav-modal-content.drag-active .glass-card:active {
                cursor: grabbing;
            }

            #fav-modal-content .sortable-ghost {
                opacity: 0.35 !important;
                transform: scale(0.98);
            }

            #fav-modal-content .sortable-chosen {
                transform: scale(1.03);
                box-shadow: 0 10px 30px rgba(124,58,237,0.3);
            }

            #fav-modal-content .sortable-drag {
                transform: scale(1.04);
            }

            #fav-modal-content {
                align-items: stretch;
                padding-bottom: 24px;
            }

            #fav-modal-content .glass-card {
                display: flex;
                flex-direction: column;
                height: 100%;
            }

            #fav-modal-content .glass-card .aspect-square {
                aspect-ratio: 1 / 1;
                overflow: hidden;
                flex-shrink: 0;
            }

            #fav-modal-content .glass-card p {
                min-height: 1.4rem;
                line-height: 1.2;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-top: auto;
            }

            #favorites-list-modal .fav-top-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-bottom: 16px;
            }

            #favorites-list-modal .fav-toolbar {
                display: flex;
                align-items: center;
                /* Mengembalikan fungsi agar tombol EDIT/SIMPAN berada di pojok kanan */
                justify-content: space-between !important; 
                gap: 16px;
                border-top: 1px solid rgba(255,255,255,0.06);
                padding-top: 16px;
            }

            #favorites-list-modal .modal-box {
                border: 1px solid rgba(167, 139, 250, 0.2) !important;
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.6); 
                overflow-x: hidden;
                overflow-y: auto; /* KUNCI 1: Buka gembok agar bisa di-scroll ke bawah */
                overscroll-behavior: contain; /* Cegah scroll bablas ke background web */
            }

            #favorites-list-modal .fav-toggle {
                background: rgba(255, 255, 255, 0.03);
                border: 1px solid rgba(167, 139, 250, 0.2);
                padding: 5px;
                border-radius: 14px;
                width: auto !important;
                flex-shrink: 0; /* Jangan biarkan switch mengecil di HP */
            }

            #favorites-list-modal .fav-toggle-inner {
                display: flex;
                gap: 2px;            
            }

            #favorites-list-modal .fav-toggle-btn {
                padding: 10px 18px !important; /* Ukuran tombol lebih besar */
                font-size: 11px !important;     /* Teks lebih tegas */
                min-width: 90px;
                border-radius: 10px;
            }

            #favorites-list-modal #manual-controls {
                flex-shrink: 0;
                align-items: center;
            }

            #favorites-list-modal .btn-edit, 
            #favorites-list-modal .btn-primary {
                height: 46px !important;
                padding: 0 24px !important;
                font-size: 11px !important;
                min-width: 90px;
                margin-top: 0 !important; /* Pastikan sejajar sempurna */
            }

            /* index.php — Perbaikan tampilan HP agar sama seperti LAPTOP */

            /* index.php — Perbaikan Final Khusus HP */
            @media (max-width: 640px) {
                #favorites-list-modal .sticky-header-fix {
                    padding: 1rem 0.75rem; /* Kurangi padding samping agar ruang lebih luas */
                }

                #favorites-list-modal .fav-toolbar {                
                    justify-content: space-between !important; 
                    gap: 4px; /* Jarak antar elemen sangat rapat agar muat */
                    flex-wrap: nowrap !important; 
                }

                #favorites-list-modal .fav-toggle {
                    width: auto !important; 
                    flex: none !important; /* Hapus paksaan memanjang */
                    padding: 3px;
                }

                #favorites-list-modal .fav-toggle-btn {
                    height: 36px !important;   /* Ukuran lebih kecil untuk HP */
                    padding: 0 10px !important;
                    min-width: 60px;
                    font-size: 9px !important;
                }

                #favorites-list-modal .btn-edit, 
                #favorites-list-modal .btn-primary {
                    height: 36px !important; /* Tinggi tombol aksi disesuaikan */
                    padding: 0 16px !important;
                    min-width: 65px;
                    font-size: 9px !important;
                }

                #favorites-list-modal #fav-order-slider {
                    width: calc(50% - 3px);
                }
            }

            /* Toolbar pagination: dropdown tetap di sisi kiri, controls fleksibel di kanan */
            .pagination-toolbar {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .pagination-per-page {
                flex-shrink: 0;
                white-space: nowrap;
            }

            .pagination-controls-host {
                flex: 1;
                min-width: 0;
                width: 100%;
            }

            /* 1. Wrapper utama agar Prev di pojok kiri dan Next di pojok kanan */
            .pagination-wrapper {
                display: grid;
                grid-template-columns: auto minmax(0, 1fr) auto;
                align-items: center;
                width: 100%;
                gap: 8px;
            }

            /* 2. Cegah tombol Prev dan Next jadi gepeng/mengecil di HP */
            .pagination-wrapper > button.pagination-btn {
                flex-shrink: 0;
                min-width: 40px; /* Pastikan tombol prev/next ukurannya nyaman dipencet */
            }

            /* 3. Kotak khusus angka yang memakan sisa ruang tengah dan bisa di-scroll */
            .pagination-numbers {
                display: flex;
                align-items: center;
                overflow-x: auto;
                scroll-behavior: smooth;
                gap: 6px;
                padding: 4px 2px;
                touch-action: pan-x;
                -webkit-overflow-scrolling: touch;
                
                /* Sembunyikan scrollbar jelek */
                -ms-overflow-style: none;
                scrollbar-width: none;
            }

            .pagination-numbers::-webkit-scrollbar {
                display: none;
            }

            /* 4. Cegah tombol angka jadi gepeng */
            .pagination-numbers button {
                flex-shrink: 0; 
            }

            @media (hover: hover) and (pointer: fine) {
                .pagination-numbers {
                    cursor: grab;
                }

                .pagination-numbers.is-dragging {
                    cursor: grabbing;
                    user-select: none;
                }
            }

            @media (max-width: 640px) {
                .pagination-toolbar {
                    gap: 8px;
                    flex-direction: column-reverse;
                    align-items: stretch;
                }

                .pagination-wrapper {
                    gap: 6px;
                }

                .pagination-controls-host {
                    width: 100%;
                }

                .pagination-per-page {
                    align-self: flex-start;
                }

                .pagination-per-page .glass-input {
                    width: 68px !important;
                    font-size: 11px !important;
                }

                .pagination-wrapper > button.pagination-btn {
                    min-width: 36px;
                    padding: 6px 10px;
                }

                .pagination-numbers {
                    gap: 5px;
                }
            }

            /* --- MODERN TOAST MANAGER --- */
            #toast-container {
                position: fixed;
                bottom: 24px;
                right: 24px;
                z-index: 9999;
                width: min(350px, calc(100vw - 48px));
                height: 190px;
                pointer-events: none;
                perspective: 900px;
            }

            .toast-item {
                background: rgba(15, 10, 30, 0.45) !important;
                backdrop-filter: blur(12px) saturate(150%) url(#glass-displacement) !important;
                -webkit-backdrop-filter: blur(12px) saturate(150%) url(#glass-displacement) !important;
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-left: 4px solid #7c3aed;
                color: #fff;
                padding: 16px;
                border-radius: 12px;
                box-shadow: 
                    0 0 2px 1px rgba(255, 255, 255, 0.15) inset,
                    0 0 10px 4px rgba(255, 255, 255, 0.05) inset,
                    0 10px 25px -5px rgba(0, 0, 0, 0.5);
                display: flex;
                flex-direction: column;
                gap: 4px;
                width: 100%;
                pointer-events: auto;
                position: absolute;
                right: 0;
                bottom: 0;
                --stack-index: 0;
                --stack-y: calc(var(--stack-index) * -10px);
                --stack-scale: calc(1 - (var(--stack-index) * 0.04));
                --stack-rotate: calc(var(--stack-index) * -1deg);
                transform: translateX(120%) translateY(0) scale(1) rotate(0deg);
                opacity: 0;
                transition: transform 0.42s cubic-bezier(0.215, 0.610, 0.355, 1), opacity 0.3s ease;
                will-change: transform, opacity;
            }

            .toast-item.show {
                transform: translateX(0) translateY(var(--stack-y)) scale(var(--stack-scale)) rotate(var(--stack-rotate));
                opacity: calc(1 - (var(--stack-index) * 0.16));
            }

            .toast-item.hide {
                opacity: 0;
                transform: translateX(120%) translateY(0) scale(0.98);
            }

            .toast-title {
                font-weight: 700;
                font-size: 14px;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .toast-desc {
                font-size: 12px;
                color: #9ca3af;
                line-height: 1.5;
            }

            /* Varian Warna Toast */
            .toast-item.success { border-left-color: #10b981; }
            .toast-item.success .toast-title { color: #10b981; }
            .toast-item.error { border-left-color: #ef4444; }
            .toast-item.error .toast-title { color: #ef4444; }
            .toast-item.info { border-left-color: #3b82f6; }
            .toast-item.info .toast-title { color: #3b82f6; }
            .toast-item.warning { border-left-color: #f59e0b; }
            .toast-item.warning .toast-title { color: #f59e0b; }

            @media (max-width: 640px) {
                #toast-container {
                    left: 20px;
                    right: 20px;
                    width: auto;
                    height: 175px;
                }
                .toast-item { width: 100%; max-width: 100%; }
            }

            /* */
            /* --- ANIMASI MODAL ALA LINEAR / FRAMER MOTION --- */
            .modal-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.4);
                backdrop-filter: blur(8px);
                z-index: 1000;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 12px;
                box-sizing: border-box;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.3s ease;
            }

            .modal-overlay.open {
                opacity: 1;
                pointer-events: auto;
            }

            /* Kotak Modal Awal (Kecil & Ke Atas) */
            .modal-box {
                position: relative;
                background: #0f0a1e; /* Warna gelap */
                border: 2px solid rgba(124, 58, 237, 0.3); /* Border ungu tipis */
                border-radius: 24px; /* Sudut sangat bulat seperti di kodinganmu */
                width: 90%;
                max-width: 500px;
                max-height: 85vh;
                overflow-y: auto;
                box-sizing: border-box;
                /* Mulai dari posisi kecil dan agak ke atas */
                transform: scale(0.8) translateY(-40px); 
                opacity: 0;
                /* Transisi dengan timing function "Spring" manual */
                transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            }

            /* Khusus popup form: beri jarak isi agar rapi di HP maupun desktop kecil */
            #anime-modal .modal-box,
            #waifu-modal .modal-box,
            #profile-settings-modal .modal-box {
                padding: 14px;
                width: min(92vw, 760px);
                overflow-x: hidden;
            }

            #anime-modal .modal-box input,
            #anime-modal .modal-box textarea,
            #anime-modal .modal-box select,
            #waifu-modal .modal-box input,
            #waifu-modal .modal-box textarea,
            #waifu-modal .modal-box select,
            #profile-settings-modal .modal-box input,
            #profile-settings-modal .modal-box textarea,
            #profile-settings-modal .modal-box select {
                max-width: 100%;
                min-width: 0;
                box-sizing: border-box;
            }

            @media (max-width: 640px) {
                #anime-modal .modal-box,
                #waifu-modal .modal-box,
                #profile-settings-modal .modal-box {
                    width: calc(100vw - 24px);
                    padding: 12px;
                }
            }

            /* Saat Modal Terbuka (Membal & Pas di Tengah) */
            .modal-overlay.open .modal-box {
                transform: scale(1) translateY(0);
                opacity: 1;
            }

            /* Animasi untuk konten di dalam modal agar ikut meluncur perlahan */
            .modal-overlay.open .modal-box > div {
                animation: fadeSlideUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
            }

            @keyframes fadeSlideUp {
                0% { opacity: 0; transform: translateY(20px); }
                100% { opacity: 1; transform: translateY(0); }
            }

            /* Sembunyikan scrollbar agar rapi */
            .modal-box::-webkit-scrollbar { display: none; }
            .modal-box { -ms-overflow-style: none; scrollbar-width: none; }

            /* Liquid glass khusus popup card detail Anime/Waifu */
            #detail-modal-anime .modal-box,
            #detail-modal-waifu .modal-box,
            #modal-detail .modal-box {
                position: relative;
                background: linear-gradient(140deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0.05));
                border: 1px solid rgba(255, 255, 255, 0.28);
                box-shadow:
                    0 24px 55px rgba(0, 0, 0, 0.45),
                    inset 0 1px 0 rgba(255, 255, 255, 0.35),
                    inset 0 -1px 0 rgba(255, 255, 255, 0.08);
                backdrop-filter: blur(18px) saturate(145%);
                -webkit-backdrop-filter: blur(18px) saturate(145%);
                overflow-x: hidden;
                overflow-y: auto;
            }

            #detail-modal-anime .modal-box::before,
            #detail-modal-waifu .modal-box::before,
            #modal-detail .modal-box::before {
                content: '';
                position: absolute;
                inset: 0;
                pointer-events: none;
                background:
                    radial-gradient(140px 90px at 14% 10%, rgba(255, 255, 255, 0.26), transparent 70%),
                    radial-gradient(220px 130px at 85% 100%, rgba(167, 139, 250, 0.20), transparent 72%);
                z-index: 0;
            }

            #detail-modal-anime .modal-box > *,
            #detail-modal-waifu .modal-box > *,
            #modal-detail .modal-box > * {
                position: relative;
                z-index: 1;
            }

            #detail-modal-anime .modal-box,
            #modal-detail .modal-box {
                max-width: 560px;
            }

            #detail-modal-waifu .modal-box {
                max-width: 640px;
            }

            /* --- LIQUID GLASS STATS CARDS (Total Anime, dkk) --- */
            #stats-grid .star-border-container {
                background: rgba(255, 255, 255, 0.02);
                border: none;
                box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.5);
                backdrop-filter: none;
                -webkit-backdrop-filter: none;
                /* Animasi membal saat kotak di-hover */
                transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            }

            #stats-grid .star-border-container:hover {
                transform: scale(1.05) translateY(-4px);
            }

            #stats-grid .stat-card-inner {
                /* Warna kaca bening super tipis */
                background-color: rgba(255, 255, 255, 0.03) !important;
                border: 1px solid rgba(255, 255, 255, 0.15) !important;
                
                /* EFEK DISTORSI KACA (Liquid Glass) */
                backdrop-filter: blur(12px) url(#glass-distortion) !important;
                -webkit-backdrop-filter: blur(12px) url(#glass-distortion) !important;
                
                /* Cahaya pantulan 3D di dalam kaca */
                box-shadow: inset 0 2px 15px rgba(255, 255, 255, 0.05);
                border-radius: 12.5px;
            }

            .view-toggle-container,
            #anime-sub-controls,
            #waifu-sub-controls,
           .pagination-toolbar {
                background: linear-gradient(140deg, rgba(255, 255, 255, 0.12), rgba(255, 255, 255, 0.03));
                border: 1px solid rgba(255, 255, 255, 0.18) !important;
                box-shadow:
                    0 10px 24px rgba(0, 0, 0, 0.26),
                    inset 0 1px 0 rgba(255, 255, 255, 0.2);
                backdrop-filter: blur(12px) saturate(140%);
                -webkit-backdrop-filter: blur(12px) saturate(140%);
                
                /* INI TAMBAHANNYA AGAR TIDAK NEMBUS CARD */
                position: relative !important;
                z-index: 200 !important; 
                overflow: visible !important; 
            }

            .pagination-toolbar {
                border-radius: 16px;
                padding: 14px;
            }

            .pagination-per-page .glass-input {
                background: rgba(255, 255, 255, 0.08) !important;
                border: 1px solid rgba(255, 255, 255, 0.22) !important;
                box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.16);
            }

            .pagination-wrapper,
            .pagination-numbers {
                background: rgba(255, 255, 255, 0.02);
                border-radius: 12px;
            }

            .pagination-btn {
                background: linear-gradient(140deg, rgba(255, 255, 255, 0.14), rgba(255, 255, 255, 0.04));
                border: 1px solid rgba(255, 255, 255, 0.22);
                color: #ddd6fe;
                box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.22);
            }

            .pagination-btn:hover:not(:disabled) {
                background: linear-gradient(140deg, rgba(167, 139, 250, 0.26), rgba(167, 139, 250, 0.12));
                border-color: rgba(196, 181, 253, 0.55);
            }

            .pagination-btn.active {
                background: linear-gradient(140deg, rgba(167, 139, 250, 0.45), rgba(124, 58, 237, 0.34));
                border-color: rgba(221, 214, 254, 0.7);
                color: #fff;
            }

            

            

            /* --- DASHBOARD: Tombol "Lihat Semua" --- */
            .dashboard-see-all {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 5px 14px;
                border-radius: 99px;
                font-size: 10px;
                font-weight: 700;
                letter-spacing: 0.04em;
                /* Glass container (rumah kecil) */
                background: rgba(255, 255, 255, 0.08);
                border: 1px solid rgba(255, 255, 255, 0.18);
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
                box-shadow: inset 0 1px 0 rgba(255,255,255,0.15), 0 4px 12px rgba(0,0,0,0.2);
                /* Animasi transisi */
                transition: transform 0.15s cubic-bezier(0.34,1.56,0.64,1),
                            background 0.15s ease,
                            box-shadow 0.15s ease;
                cursor: pointer;
                text-decoration: none;
            }
            .dashboard-see-all:hover {
                background: rgba(255, 255, 255, 0.14);
                box-shadow: inset 0 1px 0 rgba(255,255,255,0.2), 0 6px 16px rgba(0,0,0,0.25);
                transform: translateY(-2px);
                color: #fff !important;
            }
            /* Animasi "membal" saat ditekan - disamakan dengan tombol Tambah */
            .dashboard-see-all:active {
                transform: scale(0.95) translateY(0);
                background: rgba(255, 255, 255, 0.20);
                box-shadow: inset 0 2px 8px rgba(0,0,0,0.3);
            }

            /* --- BOTTOM NAVIGATION BAR (Liquid Glass            /* --- BOTTOM NAVIGATION BAR (FabBar / Liquid Glass iOS 18) --- */
            .bottom-nav-bar {
                position: fixed;
                bottom: env(safe-area-inset-bottom, 20px);
                left: 0;
                right: 0;
                z-index: 150;
                display: flex;
                justify-content: center;
                /* Beri jarak dari dasar layar agar benar-benar melayang (FabBar look) */
                padding: 0 16px 12px 16px; 
                pointer-events: none;
                transition: transform 0.4s ease, opacity 0.4s ease;
            }
            /* Spacer diperlebar karena bar sekarang lebih melayang */
            .bottom-nav-spacer {
                height: 110px;
            }

            .bottom-nav-switcher {
                position: relative;
                display: grid;
                grid-template-columns: 1fr 1fr 1fr;
                padding: 6px;
                width: 100%;
                max-width: 440px;
                pointer-events: auto;
                /* Perfect pill shape */
                border-radius: 40px; 
                /* Premium Deep Glass */
                background: rgba(20, 10, 40, 0.45);
                backdrop-filter: blur(28px) saturate(210%);
                -webkit-backdrop-filter: blur(28px) saturate(210%);
                border: 0.5px solid rgba(255, 255, 255, 0.22);
                /* Fix: Ensure animation stays inside */
                overflow: hidden; 
                box-shadow:
                    0 15px 35px -5px rgba(0, 0, 0, 0.5),
                    0 5px 15px rgba(0, 0, 0, 0.2),
                    inset 0 1px 0.5px rgba(255, 255, 255, 0.25),
                    inset 0 -1px 0 rgba(0, 0, 0, 0.1);
            }

            .bottom-nav-switcher input[type="radio"] { display: none; }

            .bottom-nav-switcher label {
                position: relative;
                z-index: 10;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 12px 8px 10px;
                cursor: pointer;
                border-radius: 32px;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                color: rgba(255, 255, 255, 0.5);
                gap: 4px;
                -webkit-tap-highlight-color: transparent;
                user-select: none;
            }

            /* Indikator feedback saat ditekan (Bubbly effect) */
            .bottom-nav-switcher label:active {
                transform: scale(0.92);
            }

            .bottom-nav-switcher label .bnav-icon {
                font-size: 20px;
                line-height: 1;
                filter: grayscale(1) opacity(0.6);
                transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            }
            .bottom-nav-switcher label .bnav-text {
                font-size: 10px;
                font-weight: 800;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                font-family: 'Nunito', sans-serif;
                transition: all 0.3s ease;
                opacity: 0.6;
            }

            .bottom-nav-switcher label:has(input:checked) {
                color: #ffffff;
            }
            .bottom-nav-switcher label:has(input:checked) .bnav-icon {
                transform: scale(1.22) translateY(-2px);
                filter: grayscale(0) opacity(1);
            }
            .bottom-nav-switcher label:has(input:checked) .bnav-text {
                opacity: 1;
                transform: translateY(-1px);
            }

            /* Sliding Bubbly Layer - The "Fab" in FabBar */
            .bottom-nav-switcher .bnav-layer {
                position: absolute;
                top: 6px;
                bottom: 6px;
                left: 6px;
                width: calc(33.333% - 4px);
                background: linear-gradient(135deg, rgba(167, 139, 250, 0.45), rgba(124, 58, 237, 0.35));
                border: 1px solid rgba(167, 139, 250, 0.4);
                border-radius: 34px;
                box-shadow:
                    0 8px 20px -4px rgba(124, 58, 237, 0.4),
                    inset 0 1px 0 rgba(255, 255, 255, 0.3);
                transform: translateZ(0);
            }

            /* Draggable State Support */
            .bottom-nav-switcher.is-dragging .bnav-layer {
                transition: none !important;
                animation: none !important;
                left: var(--drag-offset, 6px) !important;
                transform: scaleX(var(--drag-scale, 1)) !important;
                transform-origin: center;
            }

            /* Position layer based on checked radio */
            .bottom-nav-switcher:has(label:nth-child(1) input:checked) .bnav-layer { left: 6px; }
            .bottom-nav-switcher:has(label:nth-child(2) input:checked) .bnav-layer { left: calc(33.333% + 2px); }
            .bottom-nav-switcher:has(label:nth-child(3) input:checked) .bnav-layer { left: calc(66.666% - 2px); }

            /* Squish animations - Membuat transisi terasa "Liquid" */
            .bottom-nav-switcher[data-prev="1"]:has(label:nth-child(2) input:checked) .bnav-layer { animation: fabSlide12 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
            .bottom-nav-switcher[data-prev="1"]:has(label:nth-child(3) input:checked) .bnav-layer { animation: fabSlide13 0.55s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
            .bottom-nav-switcher[data-prev="2"]:has(label:nth-child(1) input:checked) .bnav-layer { animation: fabSlide21 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
            .bottom-nav-switcher[data-prev="2"]:has(label:nth-child(3) input:checked) .bnav-layer { animation: fabSlide23 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
            .bottom-nav-switcher[data-prev="3"]:has(label:nth-child(1) input:checked) .bnav-layer { animation: fabSlide31 0.55s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
            .bottom-nav-switcher[data-prev="3"]:has(label:nth-child(2) input:checked) .bnav-layer { animation: fabSlide32 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }

            @keyframes fabSlide12 {
                0%   { left: 6px; width: calc(33.333% - 4px); }
                45%  { left: 6px; width: calc(66.666% - 4px); }
                100% { left: calc(33.333% + 2px); width: calc(33.333% - 4px); }
            }
            @keyframes fabSlide13 {
                0%   { left: 6px; width: calc(33.333% - 4px); }
                45%  { left: 6px; width: calc(100% - 14px); }
                100% { left: calc(66.666% - 2px); width: calc(33.333% - 4px); }
            }
            @keyframes fabSlide21 {
                0%   { left: calc(33.333% + 2px); width: calc(33.333% - 4px); }
                45%  { left: 6px; width: calc(33.333% * 2 - 4px); }
                100% { left: 6px; width: calc(33.333% - 4px); }
            }
            @keyframes fabSlide23 {
                0%   { left: calc(33.333% + 2px); width: calc(33.333% - 4px); }
                45%  { left: calc(33.333% + 2px); width: calc(66.666% - 10px); }
                100% { left: calc(66.666% - 2px); width: calc(33.333% - 4px); }
            }
            @keyframes fabSlide31 {
                0%   { left: calc(66.666% - 2px); width: calc(33.333% - 4px); }
                45%  { left: 8px; width: calc(100% - 14px); }
                100% { left: 6px; width: calc(33.333% - 4px); }
            }
            @keyframes fabSlide32 {
                0%   { left: calc(66.666% - 2px); width: calc(33.333% - 4px); }
                45%  { left: calc(33.333% + 2px); width: calc(66.666% - 10px); }
                100% { left: calc(33.333% + 2px); width: calc(33.333% - 4px); }
            }

            /* Responsive: HP - melayang lebih tinggi */
            @media (max-width: 767px) {
                .bottom-nav-bar {
                    bottom: env(safe-area-inset-bottom, 25px);
                    padding: 0 20px;
                }
                .bottom-nav-switcher {
                    width: 100%;
                    border-radius: 40px;
                    /* Bubbly entrance animation */
                    transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1),
                                opacity 0.4s ease;
                }
                .bottom-nav-switcher label .bnav-text {
                    font-size: 10px;
                }
            }

                /* State: nav tersembunyi saat ada notif */
                .bottom-nav-bar.bnav-hidden .bottom-nav-switcher {
                    transform: translateY(100%) scale(0.08);
                    opacity: 0;
                    border-radius: 50%;
                    pointer-events: none;
                    /* Animasi HILANG — smooth ease-in tanpa membal */
                    transition: transform 0.4s cubic-bezier(0.4, 0, 1, 1),
                                opacity 0.3s cubic-bezier(0.4, 0, 1, 1),
                                border-radius 0.35s cubic-bezier(0.4, 0, 1, 1);
                }

                /* MATIKAN SVG FILTER DI HP UNTUK PERFORMANCE */
                .btn-glass-primary {
                    backdrop-filter: blur(8px) saturate(120%) !important;
                    -webkit-backdrop-filter: blur(8px) saturate(120%) !important;
                }
                .custom-dropdown-menu::before {
                    backdrop-filter: blur(8px) !important;
                    -webkit-backdrop-filter: blur(8px) !important;
                }
            }

            /* Responsive: Laptop/Desktop - kecil tapi teks besar */
            @media (min-width: 768px) {
                .bottom-nav-bar {
                    padding: 0 0 16px 0;
                }
                .bottom-nav-switcher {
                    max-width: 380px;
                    border-radius: 22px;
                }
                .bottom-nav-switcher label {
                    padding: 10px 12px 8px;
                }
                .bottom-nav-switcher label .bnav-icon {
                    font-size: 20px;
                }
                .bottom-nav-switcher label .bnav-text {
                    font-size: 12px;
                    font-weight: 800;
                }
            }

            /* ==========================================
               🚀 PATCH PERFORMA: GPU TEXTURE SNAPSHOT
               (Visual 100% Asli, tapi dikunci sebagai gambar GPU agar tidak lag)
               ========================================== */

            /* 1. KUNCI EFEK KACA MENJADI TEKSTUR GAMBAR (SNAPSHOT) */
            .custom-dropdown-menu::before,
            .btn-glass-primary,
            #stats-grid .stat-card-inner,
            .toast-item,
            .modern-switcher .layer,
            .bottom-nav-switcher .bnav-layer {
                /* Paksa pindah ke chip grafis (GPU) */
                transform: translateZ(0);
                -webkit-transform: translateZ(0);
                backface-visibility: hidden;
                -webkit-backface-visibility: hidden;
                
                /* KUNCI RAHASIA: Hapus 'backdrop-filter' dari will-change. 
                   Ini akan memaksa Chrome/Safari HP memfoto efek kaca dan TIDAK 
                   menghitung ulang riak airnya setiap kali layar di-scroll! */
                will-change: transform; 
            }

            /* 2. ISOLASI KARTU AGAR SCROLL LICIN TANPA POP-IN */
            .glass-card, .anime-card, .waifu-card, .waifu-card-list {
                /* Menggantikan "contain: layout paint" dengan teknologi terbaru agar scroll enteng tapi kartu tidak telat muncul */
                content-visibility: auto;
                contain-intrinsic-size: 150px; 
                
                /* Kunci ke dalam memori grafis HP */
                transform: translateZ(0);
                -webkit-transform: translateZ(0);
                backface-visibility: hidden;
                -webkit-backface-visibility: hidden;
            }

            /* 3. KUNCI BACKGROUND WAIFU */
            #waifu-bg {
                will-change: opacity;
                transform: translateZ(0);
                -webkit-transform: translateZ(0);
            }

            /* 4. MATIKAN SENSOR JARI PADA EFEK CAHAYA */
            .glare-overlay,
            .star-movement-top,
            .star-movement-bottom {
                pointer-events: none !important;
                will-change: transform;
            }

            /* index.php — Paksa tampilkan tombol panah (spinner) pada input angka */
            input[type="number"]::-webkit-inner-spin-button,
            input[type="number"]::-webkit-outer-spin-button {
                opacity: 1 !important;
                display: block !important;
                cursor: pointer;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 4px;
            }
            /* Memberi ruang sedikit di kanan agar spinner tidak menumpuk dengan teks */
            input[type="number"] {
                padding-right: 30px !important;
            }


        </style>
    </head>

    <body>
        <svg style="display: none">
            <filter id="glass-distortion" x="0%" y="0%" width="100%" height="100%" filterUnits="objectBoundingBox">
                <feTurbulence type="fractalNoise" baseFrequency="0.001 0.005" numOctaves="1" seed="17" result="turbulence" />
                <feComponentTransfer in="turbulence" result="mapped">
                    <feFuncR type="gamma" amplitude="1" exponent="10" offset="0.5" />
                    <feFuncG type="gamma" amplitude="0" exponent="1" offset="0" />
                    <feFuncB type="gamma" amplitude="0" exponent="1" offset="0.5" />
                </feComponentTransfer>
                <feDisplacementMap in="SourceGraphic" in2="mapped" scale="15" xChannelSelector="R" yChannelSelector="R" />
            </filter>
        </svg>
        <svg class="filter" xmlns="http://www.w3.org/2000/svg" style="display:none; position:absolute; width:0; height:0;">
            <defs>
              <filter id="glass-displacement" x="-20%" y="-20%" width="140%" height="140%">
                <feTurbulence type="fractalNoise" baseFrequency="0.02" numOctaves="3" result="noise" />
                <feDisplacementMap in="SourceGraphic" in2="noise" scale="12" xChannelSelector="R" yChannelSelector="G" result="disp" />
                <feGaussianBlur in="disp" stdDeviation="0.4" result="blurred" />
                <feMerge>
                  <feMergeNode in="blurred" />
                </feMerge>
              </filter>
            </defs>
        </svg>

        <div id="app-init-loader" class="fixed inset-0 z-[900] flex items-center justify-center bg-[#0f0a1e]/95 transition-opacity duration-500">
            <div class="flex flex-col items-center gap-3 text-purple-200">
                <div class="h-10 w-10 rounded-full border-4 border-purple-300/30 border-t-purple-300 animate-spin"></div>
                <p class="text-xs tracking-wide">Memeriksa session akun...</p>
            </div>
        </div>

        <!-- Waifu background -->
        <img id="waifu-bg" src="" alt="" />

        <!-- Navbar -->
        <nav id="navbar">
            <div class="max-w-6xl mx-auto px-4 py-3 sm:py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
                
                <div id="navbar-brand-row" class="flex items-center justify-start w-full sm:w-auto gap-3">
                    <h1 id="navbar-title" class="shiny-text-vault font-extrabold text-xl sm:text-2xl whitespace-nowrap tracking-tight" style="font-family: 'Nunito', sans-serif;">
                        Anime & Waifu Vault
                    </h1>
                </div>
                
                <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-1 sm:gap-10 sm:mr-8 relative" style="display:none;">
                    <div id="nav-indicator"></div>
                    
                    <button id="install-pwa-btn" class="hidden sm:flex items-center gap-2 bg-purple-600/20 hover:bg-purple-600/40 text-purple-200 px-3 py-1.5 rounded-full text-[12px] font-bold border border-purple-500/30 transition-all mr-2" style="display: none;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        INSTALL APP
                    </button>

                    <button onclick="showPage('dashboard')" class="tab-btn active flex-1 sm:flex-none text-center px-1 py-2 rounded-lg text-gray-300 hover:text-purple-300 text-[14px] sm:text-base" id="tab-dashboard">Dashboard</button>
                    <button onclick="showPage('anime')" class="tab-btn flex-1 sm:flex-none text-center px-1 py-2 rounded-lg text-gray-300 hover:text-purple-300 text-[14px] sm:text-base" id="tab-anime">Anime</button>
                    <button onclick="showPage('waifu')" class="tab-btn flex-1 sm:flex-none text-center px-1 py-2 rounded-lg text-gray-300 hover:text-purple-300 text-[14px] sm:text-base" id="tab-waifu">Waifu</button>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main id="app-main" class="relative z-10 max-w-6xl mx-auto px-4 pt-12 pb-10 opacity-0 pointer-events-none transition-opacity duration-500">
            <!-- Bottom Nav Spacer - agar konten tidak tertutup -->
            <style>#app-main { padding-bottom: 100px !important; }</style>

            <!-- DASHBOARD PAGE -->
            <div id="page-dashboard" class="page active">
                <div class="py-6">
                    <h2 id="dashboard-user-name" class="text-3xl font-bold text-purple-200 mb-1" style="font-family: 'Nunito', sans-serif;">Yuuki</h2>
                    <p class="text-purple-400/70 text-sm">Anime and Waifu list.</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8" id="stats-grid">
                    <div class="star-border-container shadow-lg shadow-purple-500/10">
                        <div class="star-movement-bottom" style="background: linear-gradient(90deg, transparent, rgba(167, 139, 250, 1), transparent);"></div>
                        <div class="star-movement-top" style="background: linear-gradient(90deg, transparent, rgba(167, 139, 250, 1), transparent);"></div>
                        <div class="stat-card-inner">
                            <div class="text-3xl font-bold text-purple-300" id="stat-total">0</div>
                            <div class="text-xs text-gray-400 mt-1">Total Anime</div>
                        </div>
                    </div>
                    <div class="star-border-container shadow-lg shadow-green-500/10">
                        <div class="star-movement-bottom" style="background: linear-gradient(90deg, transparent, rgba(74, 222, 128, 1), transparent);"></div>
                        <div class="star-movement-top" style="background: linear-gradient(90deg, transparent, rgba(74, 222, 128, 1), transparent);"></div>
                        <div class="stat-card-inner">
                            <div class="text-3xl font-bold text-green-400" id="stat-completed">0</div>
                            <div class="text-xs text-gray-400 mt-1">Completed</div>
                        </div>
                    </div>
                    <div class="star-border-container shadow-lg shadow-blue-500/10">
                        <div class="star-movement-bottom" style="background: linear-gradient(90deg, transparent, rgba(96, 165, 250, 1), transparent);"></div>
                        <div class="star-movement-top" style="background: linear-gradient(90deg, transparent, rgba(96, 165, 250, 1), transparent);"></div>
                        <div class="stat-card-inner">
                            <div class="text-3xl font-bold text-blue-400" id="stat-watching">0</div>
                            <div class="text-xs text-gray-400 mt-1">Watching</div>
                        </div>
                    </div>
                    <div class="star-border-container shadow-lg shadow-pink-500/10">
                        <div class="star-movement-bottom" style="background: linear-gradient(90deg, transparent, rgba(244, 114, 182, 1), transparent);"></div>
                        <div class="star-movement-top" style="background: linear-gradient(90deg, transparent, rgba(244, 114, 182, 1), transparent);"></div>
                        <div class="stat-card-inner">
                            <div class="text-3xl font-bold text-pink-400" id="stat-waifus">0</div>
                            <div class="text-xs text-gray-400 mt-1">Waifus</div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-8 mb-8">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-pink-300 uppercase tracking-wider">💕 Waifu Favorit</h3>
                            <button onclick="showFavoritesList('waifu')" id="btn-more-waifu" class="dashboard-see-all text-pink-200 hidden">Lihat Semua Favorit →</button>
                        </div>
                        <div id="fav-waifu-list-dashboard" class="grid grid-cols-2 md:grid-cols-4 gap-3"></div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-purple-300 uppercase tracking-wider">⭐ Anime Favorit</h3>
                            <button onclick="showFavoritesList('anime')" id="btn-more-anime" class="dashboard-see-all text-purple-200 hidden">Lihat Semua Favorit →</button>
                        </div>
                        <div id="fav-anime-list-dashboard" class="grid grid-cols-2 md:grid-cols-4 gap-3"></div>
                    </div>
                </div>

                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-purple-200" style="font-family: 'Nunito', sans-serif;">Anime Terbaru</h3>
                    <button onclick="showPage('anime')" class="dashboard-see-all text-purple-200">Lihat semua →</button>
                </div>
                <div id="recent-anime-list" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3"></div>
            </div>

            <!-- ANIME PAGE -->
            <div id="page-anime" class="page">
                <div class="page-header-sticky py-6">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-purple-200" style="font-family: 'Nunito', sans-serif;">Koleksi Anime 🎌</h2>
                    <p class="text-sm text-purple-400/60 mt-1 font-medium">Anime List</p>
                </div>
                
                <div class="flex flex-col gap-2 mb-6 card-entrance" style="position: relative; z-index: 50;">
                    <div class="flex items-center justify-between gap-2">        
                        <form class="modern-switcher" id="switcher-anime">
                            <label>
                                <input type="radio" name="sw-anime" value="sort" checked onchange="setAnimeControlMode('sort')">
                                <span class="text text-xs sm:text-sm font-extrabold">✨ URUTAN</span>
                            </label>
                            <label>
                                <input type="radio" name="sw-anime" value="filter" onchange="setAnimeControlMode('filter')">
                                <span class="text text-xs sm:text-sm font-extrabold">📑 STATUS</span>
                            </label>
                            <div class="layer"></div>
                        </form>
                        <button onclick="openAnimeModal()" class="hidden sm:flex btn-glass-primary items-center gap-2 text-[11px] h-[38px] px-6 shadow-lg shadow-purple-500/20">
                            <span class="font-extrabold">+ TAMBAH ANIME</span>
                        </button>
                    </div>

                    <div id="anime-sub-controls" class="bg-white/5 p-2.5 rounded-2xl border border-white/10 flex items-center shadow-inner overflow-visible">
                        </div>

                    <button onclick="openAnimeModal()" class="flex sm:hidden btn-glass-primary items-center justify-center gap-2 text-[11px] py-3.5 w-full">
                        <span class="font-extrabold">+ TAMBAH ANIME</span>
                    </button>
                </div>

                <div id="anime-list" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3" style="position: relative; z-index: 1;"></div>
                
                <div id="anime-empty" class="text-center py-16 text-gray-500 hidden">
                    <div class="text-5xl mb-3">📭</div>
                    <p>Belum ada anime. Tambah sekarang!</p>
                </div>

                <div class="pagination-toolbar mt-10 border-t border-white/5 pt-6">
                    <div class="pagination-per-page flex items-center gap-2">
                        <span class="text-xs text-gray-500">Tampilkan:</span>
                        <select onchange="changePerPage('anime', this.value)" class="glass-input !py-1 !px-2 !w-20 text-xs">
                            <option value="10" selected>10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div id="anime-pagination-controls" class="pagination-controls-host"></div>
                </div>
            </div>

            <!-- WAIFU PAGE -->
            <div id="page-waifu" class="page">
                <div class="page-header-sticky py-6">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-purple-200" style="font-family: 'Nunito', sans-serif;">Waifu List 💕</h2>
                    <p class="text-sm text-purple-400/60 mt-1 font-medium">My Best Waifu</p>
                </div>

                <div class="flex flex-col gap-3 mb-6 card-entrance" style="position: relative; z-index: 50;">
                    
                    <div class="flex items-center justify-between gap-3">
                        <form class="modern-switcher" id="switcher-waifu">
                            <label>
                                <input type="radio" name="sw-waifu" value="grid" checked onchange="changeWaifuView('grid')">
                                <span class="text text-xs sm:text-sm font-extrabold">GRID</span>
                            </label>
                            <label>
                                <input type="radio" name="sw-waifu" value="list" onchange="changeWaifuView('list')">
                                <span class="text text-xs sm:text-sm font-extrabold">LIST</span>
                            </label>
                            <div class="layer"></div>
                        </form>

                        <button onclick="openWaifuModal()" class="hidden sm:flex btn-glass-primary items-center gap-2 text-[11px] h-[42px] px-6 shadow-lg shadow-purple-500/20">
                            <span class="font-extrabold">+ TAMBAH WAIFU</span>
                        </button>
                    </div>

                    <div id="waifu-sub-controls" class="bg-white/5 p-2.5 rounded-2xl border border-white/10 flex items-center shadow-inner overflow-visible">
                        </div>

                    <button onclick="openWaifuModal()" class="flex sm:hidden btn-glass-primary items-center justify-center gap-2 text-[11px] py-3.5 w-full shadow-lg shadow-purple-500/10">
                        <span class="font-extrabold">+ TAMBAH WAIFU</span>
                    </button>
                </div>

                <div id="waifu-list" class="grid gap-4 mt-4" style="position: relative; z-index: 1;"></div>
                
                <div id="waifu-empty" class="text-center py-16 text-gray-500 hidden">
                    <div class="text-5xl mb-3">💔</div>
                    <p>Belum ada waifu. Tambah sekarang!</p>
                </div>

                <div class="pagination-toolbar mt-10 border-t border-white/5 pt-6">
                    <div class="pagination-per-page flex items-center gap-2">
                        <span class="text-xs text-gray-500">Tampilkan:</span>
                        <select onchange="changePerPage('waifu', this.value)" class="glass-input !py-1 !px-2 !w-20 text-xs">
                            <option value="10" selected>10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div id="waifu-pagination-controls" class="pagination-controls-host"></div>
                </div>
            </div>

        </main>

        <!-- ===== ANIME MODAL ===== -->
        <div id="anime-modal" class="modal-overlay" onclick="closeAnimeModal(event)">
            <div class="modal-box">
                <h3 class="text-xl font-bold text-purple-200 mb-5" style="font-family: 'Nunito', sans-serif;" id="anime-modal-title">Tambah Anime</h3>

                <!-- API Search -->
                <div class="mb-5 p-4 rounded-xl" style="background: rgba(124,58,237,0.1); border: 1px solid rgba(124,58,237,0.2);">
                    <label class="label">🔍 Cari Anime</label>
                    <div class="flex gap-2">
                        <input type="text" id="api-search" class="glass-input text-sm" placeholder="Cari judul anime...">
                        <button type="button" onclick="searchAnimeAPI()" class="btn-primary text-sm whitespace-nowrap px-3">Cari</button>
                    </div>
                    <p class="text-[10px] text-pink-400/60 mt-2 italic">Tips: Jika karakter tidak muncul, bisa mengisi Nama & Asal secara manual di bawah.</p>
                    <div id="search-loading" class="text-xs text-purple-400 mt-2 hidden">⏳ Mencari...</div>
                    <div id="search-results-list" class="mt-3 space-y-1 hidden max-h-60 overflow-y-auto"></div>
                </div>

                <form id="anime-form" onsubmit="submitAnime(event)" class="space-y-4">
                    <input type="hidden" id="anime-id" value="">
                    <input type="hidden" id="anime-mal-id" value="">
                    <input type="hidden" id="anime-gambar-existing" value="">

                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2">
                            <label class="label">Judul Anime *</label>
                            <input type="text" id="anime-judul" class="glass-input" placeholder="Judul anime..." required>
                        </div>
                        <div class="col-span-2">
                            <label class="label">Genre (Pisahkan dengan koma)</label>
                            <input type="text" id="anime-genres" class="glass-input" placeholder="Action, Adventure, Fantasy...">
                        </div>
                        <div>
                            <label class="label">Eps Ditonton</label>
                            <input type="number" id="anime-eps-nonton" class="glass-input" placeholder="0" min="0">
                        </div>
                        <div>
                            <label class="label">Total Episode</label>
                            <input type="number" id="anime-eps-total" class="glass-input" placeholder="0" min="0">
                        </div>
                        <div class="col-span-2">
                            <label class="label">Status</label>
                            <select id="anime-status" class="glass-input">
                                <option value="plan_to_watch">Plan to Watch</option>
                                <option value="watching">Watching</option>
                                <option value="completed">Completed</option>
                                <option value="on_hold">On Hold</option>
                                <option value="dropped">Dropped</option>
                            </select>
                        </div>
                    </div>

                    <!-- Image section -->
                    <div>
                        <label class="label">Gambar</label>
                        <div class="flex items-center gap-3">
                            <img id="anime-img-preview" src="" alt="" class="img-preview hidden">
                            <div class="flex-1 space-y-2">
                                <input type="text" id="anime-gambar-url" class="glass-input text-sm" placeholder="URL gambar (dari API atau manual)">
                                <div class="text-xs text-gray-500 text-center">— atau —</div>
                                <input type="file" id="anime-gambar-file" class="text-xs text-gray-400" accept="image/*" onchange="previewAnimeImg(this)">
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="closeAnimeModal()" class="flex-1 py-2.5 rounded-xl text-gray-400 border border-white/10 hover:bg-white/5 text-sm font-semibold">Batal</button>
                        <button type="submit" class="flex-1 btn-primary py-2.5 rounded-xl text-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ===== WAIFU MODAL ===== -->
        <div id="waifu-modal" class="modal-overlay" onclick="closeWaifuModal(event)">
            <div class="modal-box">
                <h3 class="text-xl font-bold text-purple-200 mb-5" style="font-family: 'Nunito', sans-serif;" id="waifu-modal-title">Tambah Waifu</h3>

                <div class="mb-5 p-4 rounded-xl" style="background: rgba(236,72,153,0.1); border: 1px solid rgba(236,72,153,0.2);">
                    <label class="label">🔍 Cari Waifu</label>
                    <div class="flex gap-2">
                        <input type="text" id="api-search-waifu" class="glass-input text-sm" placeholder="Ketik nama waifu/karakter...">
                        <button type="button" onclick="searchWaifuAPI()" class="btn-primary text-sm whitespace-nowrap px-3" style="background: linear-gradient(135deg, #ec4899, #be185d);">Cari</button>
                    </div>
                    <p class="text-[10px] text-pink-400/60 mt-2 italic">Tips: Jika karakter tidak muncul, bisa mengisi Nama & Asal secara manual di bawah.</p>
                    <div id="search-waifu-loading" class="text-xs text-pink-400 mt-2 hidden">⏳ Mencari...</div>
                    <div id="search-waifu-results-list" class="mt-3 space-y-1 hidden max-h-60 overflow-y-auto"></div>
                </div>

                <form id="waifu-form" onsubmit="submitWaifu(event)" class="space-y-4">
                    <input type="hidden" id="waifu-id" value="">
                    <input type="hidden" id="waifu-pict-existing" value="">
                    <input type="hidden" id="waifu-art-existing" value="">

                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2">
                            <label class="label">Nama Karakter *</label>
                            <input type="text" id="waifu-nama" class="glass-input" placeholder="Contoh: Lilith" required>
                        </div>
                        <div class="col-span-2">
                            <label class="label">Asal (Anime / Game / Manga)</label>
                            <input type="text" id="waifu-anime" class="glass-input" placeholder="Contoh: The Nonexistence of You and Me">
                        </div>
                        <div>
                            <label class="label">Umur</label>
                            <input type="text" id="waifu-umur" class="glass-input" placeholder="17...">
                        </div>
                        <div class="col-span-2">
                            <label class="label">Bio</label>
                            <textarea id="waifu-bio" class="glass-input resize-none" rows="3" placeholder="Ceritakan tentang waifu ini..."></textarea>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="waifu-is-fav" class="w-4 h-4 accent-pink-500">
                        <label for="waifu-is-fav" class="text-sm text-pink-300 font-semibold cursor-pointer">💕 Jadikan Waifu Favorit</label>
                    </div>

                    <!-- Gallery uploads -->
                    <div class="space-y-4">
                        <div>
                            <label class="label">Foto Profil Utama</label>
                            <div class="flex items-center gap-3 mb-2">
                                <img id="waifu-pict-preview" src="" class="img-preview hidden" alt="">
                                <div class="flex-1 space-y-2">
                                    <input type="file" id="waifu-pict-file" class="text-xs text-gray-400" accept="image/*" onchange="previewWaifuPict(this)">
                                    <button type="button" id="btn-revert-official" onclick="revertToOfficial()" class="text-[10px] bg-pink-500/20 text-pink-300 border border-pink-400/30 px-2 py-1 rounded hidden">
                                        ⏪ Gunakan Gambar Official
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="gallery-management-section" class="border-t border-white/10 pt-4 hidden">
                            <label class="label">Galeri ART (Tanpa Limit)</label>
                            <div id="modal-gallery-list" class="grid grid-cols-4 gap-2 mb-3"></div>
                            <input type="file" id="add-art-file" class="hidden" accept="image/*" onchange="uploadToGallery()">
                            <button type="button" onclick="document.getElementById('add-art-file').click()" class="w-full py-2 border-2 border-dashed border-white/10 rounded-xl text-xs text-gray-500 hover:border-purple-500/50">
                                + Tambah Foto ART Baru
                            </button>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="closeWaifuModal()" class="flex-1 py-2.5 rounded-xl text-gray-400 border border-white/10 hover:bg-white/5 text-sm font-semibold">Batal</button>
                        <button type="submit" class="flex-1 btn-primary py-2.5 rounded-xl text-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

            <!-- Gallery Lightbox -->
            <div id="lightbox" class="modal-overlay" onclick="document.getElementById('lightbox').classList.remove('open')" style="cursor: zoom-out;">
                <img id="lightbox-img" src="" alt="" style="max-width: 90vw; max-height: 90vh; object-fit: contain; border-radius: 12px;">
            </div>

            <!-- Toast -->
            <div id="toast-container"></div>



            
            <!-- Service Worker Register -->
            <script> //Dari sini nambahnya
            
            // index.php — Tambahkan Logika Swipe Navigasi Sequential

            let touchstartX = 0;
            let touchendX = 0;
            let touchstartY = 0;
            let touchendY = 0;
            let ignorePageSwipeGesture = false;
                let modalScrollLockY = 0;
                let forceTopOnNextModalUnlock = false;

                function lockBodyScroll() {
                    if (document.body.classList.contains('modal-open')) return;
                    modalScrollLockY = window.scrollY || window.pageYOffset || 0;
                    document.documentElement.classList.add('modal-open');
                    document.body.classList.add('modal-open');
                    document.body.style.top = `-${modalScrollLockY}px`;
                }

                function unlockBodyScroll() {
                    if (!document.body.classList.contains('modal-open')) return;
                    
                    const mustGoTop = forceTopOnNextModalUnlock;
                    forceTopOnNextModalUnlock = false;
                    
                    const savedY = Math.abs(parseInt(document.body.style.top || '0', 10)) || modalScrollLockY || 0;
                    
                    document.documentElement.classList.remove('modal-open');
                    document.body.classList.remove('modal-open');
                    document.body.style.top = '';
                    
                    if (mustGoTop) {
                        window.scrollTo(0, 0);
                        // Double force untuk memastikan browser tidak restore posisi lama
                        setTimeout(() => window.scrollTo(0, 0), 0);
                        setTimeout(() => window.scrollTo(0, 0), 50);
                    } else {
                        window.scrollTo(0, savedY);
                    }
                }

                function syncModalScrollLock() {
                    const hasOpenModal = !!document.querySelector('.modal-overlay.open');
                    if (hasOpenModal) {
                        lockBodyScroll();
                    } else {
                        unlockBodyScroll();
                    }
                }

            // Urutan halaman agar navigasi berurutan (Sequential)
            const pageOrder = ['dashboard', 'anime', 'waifu'];

            function shouldIgnoreGlobalPageSwipe(target) {
                if (!target || typeof target.closest !== 'function') return false;
                return !!target.closest('[data-prevent-page-swipe], .pagination-numbers');
            }

            function handleGesture() {
                    if (document.querySelector('.modal-overlay.open')) return;
                const swipeDistance = touchendX - touchstartX;
                const verticalDistance = Math.abs(touchendY - touchstartY);
                
                // Syarat swipe: Jarak horizontal cukup jauh DAN tidak terlalu miring ke atas/bawah
                if (Math.abs(swipeDistance) > 70 && verticalDistance < 50) {
                    const currentPage = document.querySelector('.page.active').id.replace('page-', '');
                    const currentIndex = pageOrder.indexOf(currentPage);

                    if (swipeDistance < 0) {
                        // SWIPE KIRI -> Ke halaman berikutnya (Next)
                        if (currentIndex < pageOrder.length - 1) {
                            showPage(pageOrder[currentIndex + 1]);
                        }
                    } else {
                        // SWIPE KANAN -> Ke halaman sebelumnya (Prev)
                        if (currentIndex > 0) {
                            showPage(pageOrder[currentIndex - 1]);
                        }
                    }
                }
            }

            // Hanya pasang event listener jika di perangkat mobile
            if (window.innerWidth < 640) {
                document.addEventListener('touchstart', e => {
                        if (document.querySelector('.modal-overlay.open')) return;
                    ignorePageSwipeGesture = shouldIgnoreGlobalPageSwipe(e.target);
                    touchstartX = e.changedTouches[0].screenX;
                    touchstartY = e.changedTouches[0].screenY;
                }, {passive: true});

                document.addEventListener('touchend', e => {
                        if (document.querySelector('.modal-overlay.open')) return;
                    if (ignorePageSwipeGesture) {
                        ignorePageSwipeGesture = false;
                        return;
                    }
                    touchendX = e.changedTouches[0].screenX;
                    touchendY = e.changedTouches[0].screenY;
                    handleGesture();
                }, {passive: true});

                document.addEventListener('touchcancel', () => {
                    ignorePageSwipeGesture = false;
                }, {passive: true});
            }

                document.addEventListener('DOMContentLoaded', () => {
                    const modalObserver = new MutationObserver((mutations) => {
                        let shouldSync = false;
                        for (const mutation of mutations) {
                            const target = mutation.target;
                            if (target && target.nodeType === 1 && target.classList && target.classList.contains('modal-overlay')) {
                                shouldSync = true;
                                break;
                            }
                        }
                        if (shouldSync) syncModalScrollLock();
                    });

                    modalObserver.observe(document.body, { attributes: true, subtree: true, attributeFilter: ['class'] });

                    syncModalScrollLock();
                });

            // index.php — Tambahkan variabel state baru
            let animeSort = { field: 'id', order: 'desc' };       

            /* index.php — Tambahkan variabel state Waifu */
            let waifuSort = { field: 'id', order: 'desc' }; 
            let animeSearchQuery = '';
            let waifuSearchQuery = '';
            let animeFavTimestamps = {};
            let waifuFavTimestamps = {};
            let favOrderModes = { waifu: 'auto', anime: 'auto' };
            let favCustomOrders = { waifu: [], anime: [] };
            let favOrderMode = 'auto'; 
            let currentFavType = ''; 
            let sortableInstance = null;

            function loadFavTimestamps() {
                try {
                    const savedWaifu = localStorage.getItem('waifuFavTimestamps');
                    const savedAnime = localStorage.getItem('animeFavTimestamps');
                    waifuFavTimestamps = savedWaifu ? JSON.parse(savedWaifu) : {};
                    animeFavTimestamps = savedAnime ? JSON.parse(savedAnime) : {};
                } catch (e) {
                    waifuFavTimestamps = {};
                    animeFavTimestamps = {};
                }
            }

            function saveFavTimestamps() {
                localStorage.setItem('waifuFavTimestamps', JSON.stringify(waifuFavTimestamps));
                localStorage.setItem('animeFavTimestamps', JSON.stringify(animeFavTimestamps));
            }

            function getFavAddedTime(type, id) {
                const source = (type === 'anime') ? allAnimes : allWaifus;
                const item = Array.isArray(source) ? source.find(entry => String(entry.id) === String(id)) : null;
                if (item && item.fav_marked_at) {
                    const dbTime = new Date(item.fav_marked_at).getTime();
                    if (!Number.isNaN(dbTime)) return dbTime;
                }

                const key = String(id);
                if (type === 'anime') return Number(animeFavTimestamps[key] || 0);
                return Number(waifuFavTimestamps[key] || 0);
            }

            function getFavOrderMode(type) {
                return favOrderModes[type] === 'manual' ? 'manual' : 'auto';
            }

            async function loadFavModesFromServer() {
                try {
                    const res = await fetch('api.php?action=get_fav_modes');
                    const data = await res.json();
                    if (data && data.success && data.modes) {
                        favOrderModes = {
                            waifu: data.modes.waifu === 'manual' ? 'manual' : 'auto',
                            anime: data.modes.anime === 'manual' ? 'manual' : 'auto'
                        };
                        localStorage.setItem('favOrderModes', JSON.stringify(favOrderModes));
                    }
                } catch (e) {
                    // fallback ke localStorage yang sudah ada
                }
            }

            async function saveFavModeToServer(type, mode) {
                try {
                    await fetch(`api.php?action=set_fav_mode&type=${encodeURIComponent(type)}&mode=${encodeURIComponent(mode)}`);
                    markLocalSyncChange('settings');
                } catch (e) {
                    // biarkan local mode tetap jalan
                }
            }

            function sortByCustomOrder(items, type) {
                const dbSorted = [...items].sort((a, b) => {
                    const orderA = Number(a?.sort_order || 0);
                    const orderB = Number(b?.sort_order || 0);
                    const rankA = orderA > 0 ? orderA : Number.MAX_SAFE_INTEGER;
                    const rankB = orderB > 0 ? orderB : Number.MAX_SAFE_INTEGER;
                    if (rankA !== rankB) return rankA - rankB;
                    return Number(b?.id || 0) - Number(a?.id || 0);
                });

                if (dbSorted.some(item => Number(item?.sort_order || 0) > 0)) {
                    return dbSorted;
                }

                const currentIds = items.map(item => String(item.id));
                const currentSet = new Set(currentIds);
                const savedOrder = Array.isArray(favCustomOrders[type]) ? favCustomOrders[type].map(String) : [];

                const keptOrder = savedOrder.filter(id => currentSet.has(id));
                const keptSet = new Set(keptOrder);
                const newItems = currentIds.filter(id => !keptSet.has(id));
                const nextOrder = [...keptOrder, ...newItems];

                if (JSON.stringify(savedOrder) !== JSON.stringify(nextOrder)) {
                    favCustomOrders[type] = nextOrder;
                    localStorage.setItem('favCustomOrders', JSON.stringify(favCustomOrders));
                }

                const orderMap = new Map(nextOrder.map((id, idx) => [id, idx]));
                return [...items].sort((a, b) => {
                    const posA = orderMap.has(String(a.id)) ? orderMap.get(String(a.id)) : Number.MAX_SAFE_INTEGER;
                    const posB = orderMap.has(String(b.id)) ? orderMap.get(String(b.id)) : Number.MAX_SAFE_INTEGER;
                    if (posA !== posB) return posA - posB;
                    return Number(b.id || 0) - Number(a.id || 0);
                });
            }

            function initFavOrderMode() {
                try {
                    const savedModes = localStorage.getItem('favOrderModes');
                    const savedOrders = localStorage.getItem('favCustomOrders');
                    if (savedModes) {
                        const parsed = JSON.parse(savedModes);
                        favOrderModes = {
                            waifu: parsed?.waifu === 'manual' ? 'manual' : 'auto',
                            anime: parsed?.anime === 'manual' ? 'manual' : 'auto'
                        };
                    } else {
                        const legacyMode = localStorage.getItem('favOrderMode') === 'manual' ? 'manual' : 'auto';
                        favOrderModes = { waifu: legacyMode, anime: legacyMode };
                        localStorage.setItem('favOrderModes', JSON.stringify(favOrderModes));
                    }

                    if (savedOrders) {
                        const parsedOrders = JSON.parse(savedOrders);
                        favCustomOrders = {
                            waifu: Array.isArray(parsedOrders?.waifu) ? parsedOrders.waifu : [],
                            anime: Array.isArray(parsedOrders?.anime) ? parsedOrders.anime : []
                        };
                    }
                } catch (e) {
                    favOrderModes = { waifu: 'auto', anime: 'auto' };
                    favCustomOrders = { waifu: [], anime: [] };
                    localStorage.setItem('favOrderModes', JSON.stringify(favOrderModes));
                    localStorage.setItem('favCustomOrders', JSON.stringify(favCustomOrders));
                }

                favOrderMode = getFavOrderMode('waifu');
            }

            function setFavOrderMode(mode, shouldRefreshList = true, targetType = currentFavType) {
                if (!targetType) return;

                favOrderModes[targetType] = mode;
                favOrderMode = mode;
                localStorage.setItem('favOrderModes', JSON.stringify(favOrderModes)); 
                localStorage.setItem('favOrderMode', mode); 
                saveFavModeToServer(targetType, mode);

                // Trigger visual sinkronisasi radio
                const radio = document.querySelector(`input[name="sw-fav"][value="${mode}"]`);
                if (radio && !radio.checked) { radio.checked = true; radio.dispatchEvent(new Event('change')); }

                const ctrl = document.getElementById('manual-controls');
                if (mode === 'auto') {
                    ctrl?.classList.add('hidden');
                    toggleDragMode(false);
                } else {
                    ctrl?.classList.remove('hidden');
                }

                if (shouldRefreshList && currentFavType === targetType) showFavoritesList(targetType);
                loadDashboard(); 
            }

            function showFavoritesList(type) {
                currentFavType = type; // Kunci agar mode drag tahu kategori mana
                const modal = document.getElementById('favorites-list-modal');
                const title = document.getElementById('fav-modal-title');
                const content = document.getElementById('fav-modal-content');
                
                // Sinkronkan tombol opsi dengan pilihan di localStorage
                const currentMode = getFavOrderMode(type);
                setFavOrderMode(currentMode, false, type); 

                let favs = (type === 'waifu') ? allWaifus.filter(w => w.is_fav == 1) : allAnimes.filter(a => a.is_fav == 1);

                if (currentMode === 'auto') {
                    favs.sort((a, b) => {
                        const favTimeA = getFavAddedTime(type, a.id);
                        const favTimeB = getFavAddedTime(type, b.id);

                        if (favTimeA !== favTimeB) return favTimeB - favTimeA;

                        const idA = Number(a?.id || 0);
                        const idB = Number(b?.id || 0);
                        return idB - idA;
                    });
                } else {
                    favs = sortByCustomOrder(favs, type);
                }

                if (title) title.innerHTML = (type === 'waifu') ? '💕 Semua Waifu Favorit' : '⭐ Semua Anime Favorit';
                if (content) {
                    content.innerHTML = favs.length 
                        ? favs.map(item => renderSimpleFavCard(item, type)).join('')
                        : '<p class="text-gray-500 col-span-full text-center py-10">Belum ada favorit.</p>';
                }
                
                modal?.classList.add('open');
            }

            function renderSimpleFavCard(item, type) {
                const id = item.id;
                const nama = type === 'waifu' ? item.nama : item.judul;
                const rawPict = type === 'waifu' 
                    ? (item.pict_path || item.official_pict_url || item.art_path || '')
                    : (item.gambar_path || '');
                const pict = resolveMediaUrl(rawPict || '');
                
                const clickAction = type === 'waifu' ? `showWaifuDetail(${id})` : `showAnimeDetail(${id})`;

                return `
                <div data-id="${id}" class="glass-card p-3 cursor-pointer group hover:border-pink-500/50 relative" onclick="${clickAction}" onmouseenter="glareIn(this)" onmouseleave="glareOut(this)" ontouchstart="glareIn(this)" ontouchend="glareOut(this)" ontouchcancel="glareOut(this)">
                    <div class="glare-overlay"></div>
                    <div class="card-image-wrap aspect-square mb-2 overflow-hidden rounded-lg relative bg-black/20">
                        <img src="${pict}" data-base-src="${pict}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 opacity-0" loading="eager" decoding="async" onload="handleCardImageLoad(this)" onerror="handleCardImageError(this)">
                        <div class="card-image-loading absolute inset-0 flex items-center justify-center">
                            <div class="h-7 w-7 rounded-full border-4 border-purple-300/20 border-t-purple-300 animate-spin"></div>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-white truncate text-center">${escHtml(nama)}</p>
                </div>`;
            }

            // index.php — Ganti fungsi ini agar arah awal selalu benar
            // index.php — Update baris 585
            function changeSortField(field) {
                animeSort.field = field;
                
                if (field === 'judul') {
                    animeSort.order = 'asc'; // Nama defaultnya A-Z
                } else {
                    animeSort.order = 'desc'; // Tanggal & Favorit defaultnya Terbaru (↓)
                }

                const fieldLabel = field === 'judul'
                    ? 'Nama (A-Z)'
                    : (field === 'is_fav' ? 'Favorit Dahulu' : 'Terakhir Ditambah');
                showToast(`Urutan Anime: ${fieldLabel}`);
                
                updateSortVisual();
                renderAnimes();
            }

            function toggleSortOrder() {
                animeSort.order = animeSort.order === 'desc' ? 'asc' : 'desc';
                updateSortVisual();
                
                // Toast keterangan agar Senpai tidak bingung
                let msg = "";
                if (animeSort.field === 'judul') {
                    msg = animeSort.order === 'asc' ? "A ke Z" : "Z ke A";
                } else {
                    msg = animeSort.order === 'desc' ? "Terbaru ke Terlama" : "Terlama ke Terbaru";
                }
                showToast(`Urutan: ${msg}`);
                renderAnimes();
            }
            
            /* index.php — Perbaikan Sinkronisasi Visual Panah */
            function updateSortVisual() {
                const sortIcon = document.getElementById('sort-icon');
                if (!sortIcon) return;

                // Logic: Nama (A-Z=asc=↓), Lainnya (Newest=desc=↓)
                if (animeSort.field === 'judul') {
                    sortIcon.textContent = animeSort.order === 'asc' ? '↓' : '↑';
                } else {
                    sortIcon.textContent = animeSort.order === 'desc' ? '↓' : '↑';
                }
            }

    // Hanya ada satu deklarasi di sini

            function instantSearchAnime(query) {
                animeSearchQuery = query.toLowerCase();
                animeState.page = 1;
                renderAnimes();
            }

            function instantSearchWaifu(query) {
                waifuSearchQuery = query.toLowerCase();
                waifuState.page = 1;
                renderWaifus();
            }

            function setFavCardInteraction(activeDrag) {
                const content = document.getElementById('fav-modal-content');
                if (!content) return;

                const cards = content.querySelectorAll('[data-id]');
                cards.forEach(card => {
                    if (activeDrag) {
                        if (!card.dataset.onclickBackup) {
                            card.dataset.onclickBackup = card.getAttribute('onclick') || '';
                        }
                        card.removeAttribute('onclick');
                    } else {
                        const backup = card.dataset.onclickBackup || '';
                        if (backup) card.setAttribute('onclick', backup);
                        delete card.dataset.onclickBackup;
                    }
                });
            }

            function destroySortableInstance() {
                if (!sortableInstance) return;
                try {
                    sortableInstance.destroy();
                } catch (e) {
                    // Abaikan error destroy ganda
                }
                sortableInstance = null;
            }

            function applyCustomOrderToLocalData(type, orderedIds) {
                if (!Array.isArray(orderedIds) || !orderedIds.length) return;

                const orderMap = new Map(orderedIds.map((id, idx) => [String(id), idx + 1]));
                const target = type === 'anime' ? allAnimes : allWaifus;

                target.forEach(item => {
                    const key = String(item.id);
                    if (orderMap.has(key)) {
                        item.sort_order = orderMap.get(key);
                    }
                });
            }

            /**
             * ensureSortableLoaded — Guard Definitif untuk Bug "Sortable undefined setelah Cache"
             *
             * Masalah: Meski SW tidak intercept jsdelivr, ada edge case di mana script
             * gagal dieksekusi saat pertama kali (race condition saat SW baru mengambil alih,
             * atau browser cache corrupt, dll.). Fungsi ini memastikan Sortable SELALU tersedia
             * sebelum digunakan, dengan cara me-reload script secara dinamis jika perlu.
             */
            function ensureSortableLoaded() {
                // Jika Sortable sudah ada, langsung resolve
                if (typeof Sortable !== 'undefined') {
                    return Promise.resolve(true);
                }

                console.warn('[DAD] Sortable tidak ditemukan, memuat ulang secara dinamis...');

                return new Promise((resolve) => {
                    // Hapus script tag lama jika ada (untuk memaksa reload bersih)
                    const oldScript = document.querySelector('script[src*="sortablejs"]');
                    if (oldScript) oldScript.remove();

                    const script = document.createElement('script');
                    // Tambah timestamp agar browser tidak pakai cache HTTP yang rusak
                    script.src = `https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js?_r=${Date.now()}`;
                    script.onload = () => {
                        console.log('[DAD] Sortable berhasil dimuat ulang!');
                        resolve(typeof Sortable !== 'undefined');
                    };
                    script.onerror = () => {
                        console.error('[DAD] Gagal memuat Sortable. Periksa koneksi internet.');
                        resolve(false);
                    };
                    document.head.appendChild(script);
                });
            }

            function toggleDragMode(active) {
                const content = document.getElementById('fav-modal-content');
                const saveBtn = document.getElementById('btn-save-drag');
                const editBtn = document.getElementById('btn-edit-drag');

                if (active) {
                    saveBtn.classList.remove('hidden');
                    editBtn.classList.add('hidden');
                    content.classList.add('drag-active');
                    setFavCardInteraction(true);

                    // ── GUARD: Pastikan Sortable tersedia sebelum pakai ───────
                    ensureSortableLoaded().then(function(isReady) {
                        if (!isReady) {
                            toastManager.add({
                                title: 'Gagal memuat fitur Drag',
                                description: 'Hubungkan ke internet lalu coba lagi.',
                                type: 'error'
                            });
                            // Rollback UI
                            saveBtn.classList.add('hidden');
                            editBtn.classList.remove('hidden');
                            content.classList.remove('drag-active');
                            setFavCardInteraction(false);
                            return;
                        }

                        // ── Buat instance Sortable baru ───────────────────────
                        destroySortableInstance();
                        const _modalScrollBox = document.querySelector('#favorites-list-modal .modal-box');

                        sortableInstance = new Sortable(content, {
                            animation: 200,
                            ghostClass: 'sortable-ghost',
                            chosenClass: 'sortable-chosen',
                            dragClass: 'sortable-drag',

                            // forceFallback: wajib true — HTML5 DnD tidak support touch
                            forceFallback: true,
                            // fallbackOnBody: ghost di-append ke <body> → fix z-index di dalam modal
                            fallbackOnBody: true,
                            // fallbackTolerance: 3px — minimal movement sebelum drag tercatat
                            fallbackTolerance: 3,

                            // 150ms delay cukup untuk bedakan dari tap/scroll biasa
                            delay: 150,
                            delayOnTouchOnly: true,
                            touchStartThreshold: 5,

                            swapThreshold: 0.65,

                            scroll: true,
                            scrollSensitivity: 80,
                            scrollSpeed: 10,

                            // Bekukan scroll modal saat drag agar koordinat ghost akurat
                            onStart: function() {
                                if (_modalScrollBox) {
                                    _modalScrollBox._dadSavedOverflow = _modalScrollBox.style.overflowY;
                                    _modalScrollBox.style.overflowY = 'hidden';
                                }
                            },
                            onEnd: function() {
                                if (_modalScrollBox) {
                                    _modalScrollBox.style.overflowY = _modalScrollBox._dadSavedOverflow || '';
                                    delete _modalScrollBox._dadSavedOverflow;
                                }
                            }
                        });
                    });

                } else {
                    saveBtn.classList.add('hidden');
                    editBtn.classList.remove('hidden');
                    content.classList.remove('drag-active');
                    destroySortableInstance();
                    setFavCardInteraction(false);
                }
            }

            /* index.php — Perbaikan Fungsi Save */
            async function saveCustomOrder() {
                const content = document.getElementById('fav-modal-content');
                const cards = content.querySelectorAll('[data-id]');
                const orderedIds = Array.from(cards).map(card => card.dataset.id);

                if (!currentFavType) {
                    showToast("Kategori favorit tidak ditemukan!", true);
                    return;
                }

                if (!orderedIds.length) {
                    showToast("Tidak ada urutan yang bisa disimpan.", true);
                    return;
                }

                showToast("Menyimpan urutan...");
                
                try {
                    const res = await fetch('api.php?action=update_fav_order', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ type: currentFavType, ids: orderedIds })
                    });
                    const data = await res.json();
                    if (!data.success) throw new Error(data.message || 'Gagal menyimpan urutan');

                    favCustomOrders[currentFavType] = orderedIds;
                    localStorage.setItem('favCustomOrders', JSON.stringify(favCustomOrders));
                    applyCustomOrderToLocalData(currentFavType, orderedIds);
                    markLocalSyncChange(currentFavType);

                    showToast("Urutan berhasil disimpan!");
                    toggleDragMode(false);
                    showFavoritesList(currentFavType);
                    loadDashboard();
                } catch (e) {
                    showToast("Gagal menyimpan urutan!", true);
                }
            }

            // Fungsi cerdas untuk menggantikan confirm() bawaan browser
            function showConfirmDialog(title, description, okText = 'Hapus', cancelText = 'Batal') {
                return new Promise((resolve) => {
                    const modal = document.getElementById('confirm-modal');
                    document.getElementById('confirm-title').textContent = title;
                    document.getElementById('confirm-desc').textContent = description;
                    
                    const btnCancel = document.getElementById('confirm-cancel-btn');
                    const btnOk = document.getElementById('confirm-ok-btn');
                    
                    btnOk.textContent = okText;
                    btnCancel.textContent = cancelText;

                    // Fungsi bersih-bersih setelah tombol diklik
                    const cleanup = () => {
                        modal.classList.remove('open');
                        btnCancel.onclick = null;
                        btnOk.onclick = null;
                    };

                    btnCancel.onclick = () => { cleanup(); resolve(false); };
                    btnOk.onclick = () => { cleanup(); resolve(true); };

                    modal.classList.add('open');
                });
            }

            // --- TOAST MANAGER ---
            const toastManager = {
                updateStack: function(container) {
                    const activeToasts = Array.from(container.querySelectorAll('.toast-item:not(.hide)')).reverse();
                    activeToasts.forEach((item, index) => {
                        const stackIndex = Math.min(index, 4);
                        item.style.setProperty('--stack-index', String(stackIndex));
                        item.style.zIndex = String(100 - stackIndex);
                    });
                },
                add: function({ title, description, type = 'info', duration = 4000 }) {
                    const container = document.getElementById('toast-container');
                    if (!container) return;

                    const toast = document.createElement('div');
                    toast.className = `toast-item ${type}`;

                    // Tentukan Ikon berdasarkan Tipe
                    let icon = 'ℹ️';
                    if (type === 'success') icon = '✅';
                    if (type === 'error') icon = '❌';
                    if (type === 'warning') icon = '⚠️';

                    toast.innerHTML = `
                        <div class="toast-title">${icon} ${title}</div>
                        ${description ? `<div class="toast-desc">${description}</div>` : ''}
                    `;

                    container.appendChild(toast);
                    this.updateStack(container);

                    // Animasi Masuk (Slide In)
                    requestAnimationFrame(() => {
                        toast.classList.add('show');
                        this.updateStack(container);
                    });

                    // Animasi Keluar (Slide Out) lalu Hapus elemen
                    setTimeout(() => {
                        toast.classList.remove('show');
                        toast.classList.add('hide');
                        this.updateStack(container);
                        setTimeout(() => {
                            toast.remove();
                            this.updateStack(container);
                        }, 400);
                    }, duration);
                }
            };

            // Fallback untuk fungsi lama agar kodingan lain tidak error
            function showToast(msg) {
                toastManager.add({ title: "Info", description: msg, type: "info" });
            }


            // index.php — Fitur Scroll Mouse untuk mengubah angka Episode
            document.addEventListener('wheel', function(e) {
                // Hanya bekerja jika mouse berada di atas input tipe number (seperti Eps Ditonton)
                if (document.activeElement.type === 'number') {
                    e.preventDefault();
                    const input = document.activeElement;
                    const step = parseFloat(input.step) || 1;
                    const val = parseFloat(input.value) || 0;
                    
                    if (e.deltaY < 0) {
                        input.value = val + step; // Scroll ke atas = Tambah
                    } else {
                        input.value = Math.max(0, val - step); // Scroll ke bawah = Kurang
                    }
                    
                    // Beritahu sistem bahwa data berubah
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }, { passive: false });
            //batas penambahan Swipe Navigation dan Service Worker

        </script> 
        

        <!-- Main JS -->
        <script src="search_api.js?v=20260308f"></script>
        <script src="auth.js?v=20260307i"></script>
        
        <script>
        
        // index.php — Tambahkan variabel dan fungsi ini
        let lastKnownSync = { anime: null, waifu: null, settings: null };
        let autoSyncStarted = false;
        let autoSyncTimer = null;
        let isSyncChecking = false;
        let syncSnapshotReady = false;
        // index.php — Tambahkan fungsi ini
        function updateLocalSyncStatus(data) {
            if (data.last_anime !== undefined && data.last_anime !== null) lastKnownSync.anime = Number(data.last_anime);
            if (data.last_waifu !== undefined && data.last_waifu !== null) lastKnownSync.waifu = Number(data.last_waifu);
            if (data.last_settings !== undefined && data.last_settings !== null) lastKnownSync.settings = Number(data.last_settings);
        }

        function markLocalSyncChange(type) {
            if (!type) return;
            const current = Number(lastKnownSync[type] ?? 0);
            lastKnownSync[type] = current + 1;
        }

        const warmedImageSet = new Set();
        function warmImageCache(urls = []) {
            if (!Array.isArray(urls)) return;
            urls.forEach(raw => {
                const src = resolveMediaUrl(raw);
                if (!src || warmedImageSet.has(src)) return;
                warmedImageSet.add(src);
                const img = new Image();
                img.decoding = 'async';
                img.src = src;
            });
        }

        // Glare Hover Effect Functions
        function glareIn(el) {
            const overlay = el.querySelector('.glare-overlay');
            if (!overlay) return;
            overlay.style.transition = 'none';
            overlay.style.backgroundPosition = '-100% -100%, 0 0';
            void overlay.offsetWidth; // Force reflow
            overlay.style.transition = '1250ms ease';
            overlay.style.backgroundPosition = '100% 100%, 0 0';
        }

        function glareOut(el) {
            const overlay = el.querySelector('.glare-overlay');
            if (!overlay) return;
            overlay.style.transition = '1250ms ease';
            overlay.style.backgroundPosition = '-100% -100%, 0 0';
        }

        async function checkSyncNow() {
                if (document.hidden) return;
                if (document.body.classList.contains('auth-locked')) return;
                if (isSyncChecking) return;
                isSyncChecking = true;
                try {
                    const res = await fetch('api.php?action=get_sync_status', { cache: 'no-store' });
                    if (!res.ok) {
                        if (res.status === 401) {
                            window.dispatchEvent(new CustomEvent('vault-force-session-refresh'));
                        }
                        return;
                    }
                    const data = await res.json();
                    let shouldRefreshFavoritesModal = false;
                    const activePage = document.querySelector('.page.active')?.id || '';

                    const remoteAnimeVersion = Number(data.last_anime ?? 0);
                    const remoteWaifuVersion = Number(data.last_waifu ?? 0);
                    const remoteSettingsVersion = Number(data.last_settings ?? 0);

                    // Prevent initial double-render/flicker after first load.
                    if (!syncSnapshotReady) {
                        lastKnownSync.anime = remoteAnimeVersion;
                        lastKnownSync.waifu = remoteWaifuVersion;
                        lastKnownSync.settings = remoteSettingsVersion;
                        syncSnapshotReady = true;
                        return;
                    }

                    const localAnimeVersion = Number(lastKnownSync.anime ?? 0);
                    const localWaifuVersion = Number(lastKnownSync.waifu ?? 0);
                    const localSettingsVersion = Number(lastKnownSync.settings ?? 0);

                    const animeChanged = remoteAnimeVersion > localAnimeVersion;
                    const waifuChanged = remoteWaifuVersion > localWaifuVersion;
                    const settingsChanged = remoteSettingsVersion > localSettingsVersion;

                    // Cek perubahan Anime
                    if (animeChanged) {
                        console.log("🔄 Mendeteksi perubahan Anime, memperbarui...");
                        if (activePage === 'page-anime') await loadAnimes(true);
                        shouldRefreshFavoritesModal = true;
                    }

                    // Cek perubahan Waifu
                    if (waifuChanged) {
                        console.log("🔄 Mendeteksi perubahan Waifu, memperbarui...");
                        if (activePage === 'page-waifu') await loadWaifus(true);
                        shouldRefreshFavoritesModal = true;
                    }

                    // Cek perubahan mode favorit (BARU DITAMBAH / KUSTOM)
                    if (settingsChanged) {
                        console.log("🔄 Mendeteksi perubahan mode favorit, sinkronisasi...");
                        await loadFavModesFromServer();
                        // Ask auth layer to refresh username/profile state across devices.
                        window.dispatchEvent(new CustomEvent('vault-force-session-refresh'));
                        shouldRefreshFavoritesModal = true;
                    }

                    if ((animeChanged || waifuChanged || settingsChanged) && activePage === 'page-dashboard') {
                        loadDashboard();
                    }

                    const favModal = document.getElementById('favorites-list-modal');
                    if (shouldRefreshFavoritesModal && favModal && favModal.classList.contains('open') && currentFavType) {
                        showFavoritesList(currentFavType);
                    }

                    // Update state lokal
                    lastKnownSync.anime = remoteAnimeVersion;
                    lastKnownSync.waifu = remoteWaifuVersion;
                    lastKnownSync.settings = remoteSettingsVersion;
                } catch (e) {
                    // Abaikan error jika koneksi sedang tidak stabil
                } finally {
                    isSyncChecking = false;
                }
        }

        async function initAutoSync() {
            if (document.body.classList.contains('auth-locked')) return;
            if (autoSyncStarted) return;
            autoSyncStarted = true;

            await checkSyncNow();

            autoSyncTimer = setInterval(checkSyncNow, 2500);

            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') {
                    checkSyncNow();
                }
            });

            window.addEventListener('focus', checkSyncNow);
        }

        // Panggil fungsi ini saat halaman pertama kali dimuat
        window.addEventListener('load', () => {
            initAutoSync();
        });

        // ============ STATE ============
        let allAnimes = [];
        let allWaifus = [];
        let currentFilter = 'all';
        let currentOfficialUrl = "";

        function forceViewportTop() {
            window.scrollTo({ top: 0, left: 0, behavior: 'auto' });
        }

        // ============ NAVIGATION ============
        // index.php — Ganti fungsi showPage kamu dengan versi cerdas ini
        /* index.php — Ganti fungsi showPage (Baris 633-662) */
        // Paksa selalu tampil dari atas saat pindah tab tanpa animasi/jitter
        function _pinViewportTopOnTabSwitch() {
            forceViewportTop();
            requestAnimationFrame(forceViewportTop);
            setTimeout(forceViewportTop, 60);
        }

        function showPage(name) {
            const pages = document.querySelectorAll('.page');
            const buttons = document.querySelectorAll('.tab-btn');
            const targetPage = document.getElementById('page-' + name);
            const currentPage = document.querySelector('.page.active');

            if (targetPage === currentPage) return;

            const pageOrder = ['dashboard', 'anime', 'waifu'];
            const currentIndex = pageOrder.indexOf(currentPage.id.replace('page-', ''));
            const targetIndex = pageOrder.indexOf(name);
            const directionClass = targetIndex > currentIndex ? 'slide-next' : 'slide-prev';

            pages.forEach(p => p.classList.remove('active', 'slide-next', 'slide-prev'));
            buttons.forEach(b => b.classList.remove('active'));

            targetPage.classList.add('active', directionClass);
            const tabBtn = document.getElementById('tab-' + name);
            if (tabBtn) tabBtn.classList.add('active');

            // Reset posisi viewport ke atas saat dan setelah tab aktif berganti
            _pinViewportTopOnTabSwitch();

            updateNavIndicator(name);

            // Pemicu Load Data
            if (name === 'anime') {
                loadAnimes(); 
                setAnimeControlMode('sort'); // Aktifkan mode urutan otomatis saat masuk
            } else if (name === 'waifu') {
                loadWaifus();
                renderWaifuSubControls();
            } else if (name === 'dashboard') {
                loadDashboard();
            }

            // Jaga tetap di atas setelah render awal konten halaman baru
            _pinViewportTopOnTabSwitch();
        }

        function updateNavIndicator(name) {
            // Nav indicator sudah diganti oleh bottom nav switcher layer
            // Tetap ada agar tidak error jika dipanggil dari tempat lain
            const btn = document.getElementById('tab-' + name);
            const indicator = document.getElementById('nav-indicator');
            if (btn && indicator) {
                indicator.style.width = btn.offsetWidth + 'px';
                indicator.style.left = btn.offsetLeft + 'px';
            }
        }

        // Inisialisasi posisi awal saat halaman dimuat
        window.addEventListener('load', () => {
            setTimeout(() => updateNavIndicator('dashboard'), 100);
        });

        // Update posisi jika layar di-resize (penting untuk Web ke HP)
        window.addEventListener('resize', () => {
            const activeTab = document.querySelector('.tab-btn.active');
            if (activeTab) {
                const name = activeTab.id.replace('tab-', '');
                updateNavIndicator(name);
            }
        });

 

        function handleCardImageLoad(img) {
            if (!img) return;
            const wrapper = img.closest('.card-image-wrap');
            const loader = wrapper ? wrapper.querySelector('.card-image-loading') : null;
            if (loader) loader.classList.add('hidden');
            img.style.opacity = '1';
        }

        function handleCardImageError(img) {
            if (!img) return;
            const wrapper = img.closest('.card-image-wrap');
            const loader = wrapper ? wrapper.querySelector('.card-image-loading') : null;
            if (loader) loader.classList.remove('hidden');
            img.style.opacity = '0';

            if (img.dataset.retryPending === '1') return;
            const baseSrc = img.dataset.baseSrc || '';
            if (!baseSrc) return;

            img.dataset.retryPending = '1';
            setTimeout(() => {
                img.dataset.retryPending = '0';
                const sep = baseSrc.includes('?') ? '&' : '?';
                img.src = `${baseSrc}${sep}retry=${Date.now()}`;
            }, 1200);
        }

        // ============ DASHBOARD ============
        /* index.php — Versi loadDashboard yang sudah dibersihkan */
        async function loadDashboard() {
            try {
                const [aRes, wRes] = await Promise.all([
                    fetch('api.php?action=get_animes'),
                    fetch('api.php?action=get_waifus')
                ]);
                const aData = await aRes.json();
                const wData = await wRes.json();

                allAnimes = aData.data || [];
                allWaifus = wData.data || [];
                warmImageCache(allAnimes.slice(0, 20).map(item => item.gambar_path));
                warmImageCache(allWaifus.slice(0, 20).map(item => item.pict_path || item.official_pict_url || item.art_path));

                const getLatestValue = (item) => {
                    if (item && item.updated_at) {
                        const updatedAt = new Date(item.updated_at).getTime();
                        if (!Number.isNaN(updatedAt)) return updatedAt;
                    }
                    if (item && item.created_at) {
                        const createdAt = new Date(item.created_at).getTime();
                        if (!Number.isNaN(createdAt)) return createdAt;
                    }
                    return Number(item?.id || 0);
                };

                // 1. Update Statistik
                document.getElementById('stat-total').textContent = allAnimes.length;
                document.getElementById('stat-completed').textContent = allAnimes.filter(a => a.status === 'completed').length;
                document.getElementById('stat-watching').textContent = allAnimes.filter(a => a.status === 'watching').length;
                document.getElementById('stat-waifus').textContent = allWaifus.length;

                // 2. LOGIKA BACKGROUND & LIST WAIFU FAVORIT
                const favWaifus = allWaifus.filter(w => w.is_fav == 1);
                if (getFavOrderMode('waifu') === 'manual') {
                    const orderedWaifus = sortByCustomOrder(favWaifus, 'waifu');
                    favWaifus.splice(0, favWaifus.length, ...orderedWaifus);
                } else {
                    favWaifus.sort((a, b) => {
                        const favTimeA = getFavAddedTime('waifu', a.id);
                        const favTimeB = getFavAddedTime('waifu', b.id);
                        if (favTimeA !== favTimeB) return favTimeB - favTimeA;
                        return getLatestValue(b) - getLatestValue(a);
                    });
                }
                const bg = document.getElementById('waifu-bg');
                
                // Render Kartu Favorit
                const waifuFavContainer = document.getElementById('fav-waifu-list-dashboard');
                if (waifuFavContainer) {
                    waifuFavContainer.innerHTML = favWaifus.length 
                        ? favWaifus.slice(0, 4).map(renderWaifuCard).join('') 
                        : '<p class="text-gray-500 text-[10px] col-span-2 py-4 text-center">Belum ada favorit.</p>';
                }

                // FIX: Update Background (Cek pict_path ATAU official_pict_url)
                if (favWaifus.length > 0) {
                    const latestFav = favWaifus[0];
                    const imgPath = resolveMediaUrl(latestFav.pict_path || latestFav.official_pict_url);
                    
                    if (imgPath) {
                        // Hanya update kalau sumber gambarnya berubah
                        if (!bg.src.includes(imgPath)) {
                            bg.src = imgPath;
                            bg.onload = () => bg.classList.add('active');
                        } else {
                            bg.classList.add('active');
                        }
                    }
                } else {
                    bg.classList.remove('active');
                }

                // 3. Render 4 Anime Favorit
                const favAnimes = allAnimes.filter(a => a.is_fav == 1);
                if (getFavOrderMode('anime') === 'manual') {
                    const orderedAnimes = sortByCustomOrder(favAnimes, 'anime');
                    favAnimes.splice(0, favAnimes.length, ...orderedAnimes);
                } else {
                    favAnimes.sort((a, b) => {
                        const favTimeA = getFavAddedTime('anime', a.id);
                        const favTimeB = getFavAddedTime('anime', b.id);
                        if (favTimeA !== favTimeB) return favTimeB - favTimeA;
                        return getLatestValue(b) - getLatestValue(a);
                    });
                }
                const animeFavContainer = document.getElementById('fav-anime-list-dashboard');
                if (animeFavContainer) {
                    animeFavContainer.innerHTML = favAnimes.length 
                        ? favAnimes.slice(0, 4).map(renderAnimeCard).join('') 
                        : '<p class="text-gray-500 text-[10px] col-span-2 py-4 text-center">Belum ada favorit.</p>';
                }

                // 4. Render 5 Anime Terbaru
                const recent = [...allAnimes].sort((a, b) => getLatestValue(b) - getLatestValue(a)).slice(0, 5);
                document.getElementById('recent-anime-list').innerHTML = recent.map(renderAnimeCard).join('');

                // Sembunyikan/Tampilkan tombol "Lihat Semua"
                document.getElementById('btn-more-waifu')?.classList.toggle('hidden', favWaifus.length === 0);
                document.getElementById('btn-more-anime')?.classList.toggle('hidden', favAnimes.length === 0);

                // Randomize animasi kecepatan muter star border (antara 4s sampe 8s)
                randomizeStarBorderSpeed();

                // PENTING: Jangan tambah kod update background lagi di bawah sini!

            } catch (e) { console.error("Error load dashboard:", e); }
        }
        
        // Fungsi untuk mengubah speed durasi animasi menjadi random
        function randomizeStarBorderSpeed() {
            const containers = document.querySelectorAll('.star-border-container');
            containers.forEach(container => {
                // Random durasi ms antara 4000 (4s) dan 8500 (8.5s)
                const randomDuration = (Math.random() * (8.5 - 4.0) + 4.0).toFixed(2);
                const top = container.querySelector('.star-movement-top');
                const bottom = container.querySelector('.star-movement-bottom');
                if (top) top.style.animationDuration = `${randomDuration}s`;
                if (bottom) bottom.style.animationDuration = `${randomDuration}s`;
            });
        }

        let animeState = { page: 1, perPage: 10 };
        let waifuState = { page: 1, perPage: 10 };

        // Fungsi untuk mengganti jumlah item per halaman
        function changePerPage(type, value) {
            if (type === 'anime') {
                animeState.perPage = parseInt(value);
                animeState.page = 1;
                renderAnimes();
            } else {
                waifuState.perPage = parseInt(value);
                waifuState.page = 1;
                renderWaifus();
            }
        }

        function enableHorizontalDragScroll(scroller) {
            if (!scroller || scroller.dataset.dragReady === '1') return;
            scroller.dataset.dragReady = '1';

            let isDragging = false;
            let dragMoved = false;
            let startX = 0;
            let startScrollLeft = 0;

            const endDrag = () => {
                isDragging = false;
                scroller.classList.remove('is-dragging');
            };

            scroller.addEventListener('pointerdown', (e) => {
                if (e.pointerType === 'mouse' && e.button !== 0) return;
                isDragging = true;
                dragMoved = false;
                startX = e.clientX;
                startScrollLeft = scroller.scrollLeft;
                scroller.classList.add('is-dragging');
                // HAPUS setPointerCapture agar elemen anak (tombol) tetap bisa diklik
            });

            scroller.addEventListener('pointermove', (e) => {
                if (!isDragging) return;
                const deltaX = e.clientX - startX;
                if (Math.abs(deltaX) > 5) {
                    dragMoved = true;
                    // Hanya preventDefault jika benar-benar drag agar tidak blokir klik awal
                    e.preventDefault(); 
                }
                if (dragMoved) {
                    scroller.scrollLeft = startScrollLeft - deltaX;
                }
            });

            scroller.addEventListener('pointerup', endDrag);
            scroller.addEventListener('pointercancel', endDrag);
            scroller.addEventListener('pointerleave', () => {
                if (isDragging) endDrag();
            });

            scroller.addEventListener('click', (e) => {
                if (dragMoved) { 
                    e.preventDefault();
                    e.stopPropagation();
                    dragMoved = false;
                }
            }, true);
        }

        /* index.php — Ganti fungsi renderPaginationControls dengan ini */
        function renderPaginationControls(type, totalItems) {
            // Ambil data halaman saat ini
            const state = type === 'anime' ? animeState : waifuState;
            const totalPages = Math.ceil(totalItems / state.perPage);
            const containerId = type === 'anime' ? 'anime-pagination-controls' : 'waifu-pagination-controls';
            const container = document.getElementById(containerId);
            
            if (!container) return;
            container.innerHTML = ''; // Bersihkan kontainer lama

            if (totalPages <= 1) return; // Jika cuma 1 halaman, sembunyikan tombol

            // 1. Buat bungkus utama
            const wrapper = document.createElement('div');
            wrapper.className = 'pagination-wrapper';

            // 2. Buat tombol Prev (Mundur)
            const btnPrev = document.createElement('button');
            btnPrev.className = 'pagination-btn';
            btnPrev.innerHTML = '←'; 
            btnPrev.disabled = state.page === 1;
            btnPrev.onclick = () => changePage(type, state.page - 1);
            wrapper.appendChild(btnPrev);

            // 3. Buat bungkus khusus angka yang bisa di-scroll
            const numbersContainer = document.createElement('div');
            numbersContainer.className = 'pagination-numbers';
            numbersContainer.setAttribute('data-prevent-page-swipe', 'true');
            enableHorizontalDragScroll(numbersContainer);

            // 4. Looping pembuat angka halaman
            for (let i = 1; i <= totalPages; i++) {
                const btnPage = document.createElement('button');
                // Jika ini adalah halaman yang sedang dibuka, beri class 'active'
                btnPage.className = `pagination-btn ${i === state.page ? 'active' : ''}`;
                btnPage.textContent = i;
                btnPage.onclick = () => changePage(type, i);
                numbersContainer.appendChild(btnPage);
            }
            wrapper.appendChild(numbersContainer);

            // 5. Buat tombol Next (Maju)
            const btnNext = document.createElement('button');
            btnNext.className = 'pagination-btn';
            btnNext.innerHTML = '→';
            btnNext.disabled = state.page === totalPages;
            btnNext.onclick = () => changePage(type, state.page + 1);
            wrapper.appendChild(btnNext);

            // 6. Masukkan semua hasil rakitan ke dalam container asli di HTML
            container.appendChild(wrapper);

            // Auto-center angka aktif hanya secara horizontal (tanpa menggeser scroll vertikal halaman)
            setTimeout(() => {
                const activeBtn = numbersContainer.querySelector('.active');
                if (!activeBtn) return;

                const targetLeft = activeBtn.offsetLeft - ((numbersContainer.clientWidth - activeBtn.offsetWidth) / 2);
                const maxLeft = Math.max(0, numbersContainer.scrollWidth - numbersContainer.clientWidth);
                const nextLeft = Math.min(Math.max(0, targetLeft), maxLeft);

                numbersContainer.scrollTo({ left: nextLeft, behavior: 'smooth' });
            }, 50);
        }

        function changePage(type, newPage) {
            if (type === 'anime') {
                animeState.page = newPage;
                renderAnimes();
            } else {
                waifuState.page = newPage;
                renderWaifus();
            }
            // Animasi smooth scroll ke atas setelah render konten baru selesai
            requestAnimationFrame(() => {
                window.scrollTo({ top: 0, left: 0, behavior: 'smooth' });
                setTimeout(() => window.scrollTo({ top: 0, left: 0, behavior: 'smooth' }), 50);
            });
        }

        // ============ ANIME ============
        async function loadAnimes(forceFresh = false) {
            if (Array.isArray(allAnimes) && allAnimes.length) {
                warmImageCache(allAnimes.slice(0, 20).map(item => item.gambar_path));
                renderAnimes();
            }
            const animeUrl = forceFresh
                ? `api.php?action=get_animes&_sync=${Date.now()}`
                : 'api.php?action=get_animes';
            const res = await fetch(animeUrl, forceFresh ? { cache: 'no-store' } : undefined);
            const data = await res.json();
            allAnimes = data.data || [];
            warmImageCache(allAnimes.slice(0, 20).map(item => item.gambar_path));
            renderAnimes();
        }
        
        let animeControlMode = 'sort'; // 'sort' atau 'filter'

        /* index.php — Update fungsi setAnimeControlMode */
        function setAnimeControlMode(mode) {
            animeControlMode = mode;
            // Trigger visual sinkronisasi radio
            const radio = document.querySelector(`input[name="sw-anime"][value="${mode}"]`);
            if (radio && !radio.checked) { radio.checked = true; radio.dispatchEvent(new Event('change')); }
            
            currentFilter = 'all'; 
            renderAnimeSubControls(); 
            renderAnimes();
        }

        
    /* index.php — Update fungsi render agar lebih rapat */
        function renderAnimeSubControls() {
            const container = document.getElementById('anime-sub-controls');
            if (!container) return;
            
            container.innerHTML = ''; 

            if (animeControlMode === 'sort') {
                const calIcon = `<span class="text-red-400 text-sm">📅</span>`; 
                const sortLabels = { 
                    id: calIcon + '<span>TERAKHIR DITAMBAH</span>', 
                    judul: '<span class="text-sm">🔤</span><span>NAMA (A-Z)</span>', 
                    is_fav: '<span class="text-sm">⭐</span><span>FAVORIT DAHULU</span>' 
                };
                
                const arrow = (animeSort.field === 'judul') ? (animeSort.order === 'asc' ? '↓' : '↑') : (animeSort.order === 'desc' ? '↓' : '↑');

                container.innerHTML = `
                    <div class="flex flex-col sm:flex-row gap-2.5 items-center w-full filter-item-anim overflow-visible" style="position: relative; z-index: 200;">
                        <div class="flex items-center gap-2 w-full sm:w-auto relative" style="z-index: 300;"> 
                            <button onclick="toggleCustomDropdown()" class="filter-row-element glass-input !w-full sm:!w-64 flex justify-between items-center bg-white/5 border-purple-500/30">
                                <span id="current-sort-label" class="font-bold text-purple-100 flex items-center gap-3">
                                    ${sortLabels[animeSort.field]}
                                </span>
                                <span class="text-[8px] opacity-40">▼</span>
                            </button>
                            
                            <div id="custom-sort-menu" class="custom-dropdown-menu">
                                <div class="glass-inner">
                                    <div class="dropdown-item ${animeSort.field === 'id' ? 'active' : ''}" onclick="selectSort('id')">
                                        ${calIcon} <span>TERAKHIR DITAMBAH</span>
                                    </div>
                                    <div class="dropdown-item ${animeSort.field === 'judul' ? 'active' : ''}" onclick="selectSort('judul')">
                                        <span class="text-sm">🔤</span> <span>NAMA (A-Z)</span>
                                    </div>
                                    <div class="dropdown-item ${animeSort.field === 'is_fav' ? 'active' : ''}" onclick="selectSort('is_fav')">
                                        <span class="text-sm">⭐</span> <span>FAVORIT DAHULU</span>
                                    </div>
                                </div>
                            </div>
                            
                            <button onclick="toggleSortOrder()" class="filter-row-element glass-input !w-10 flex items-center justify-center border-purple-500/50">
                                <span id="sort-icon" class="text-purple-400 font-bold text-base">${arrow}</span>
                            </button>
                        </div>
                        
                        <div class="hidden sm:block h-6 w-[1px] bg-white/10 mx-0.5"></div>
                        
                        <div class="relative w-full">
                            <input type="text" oninput="instantSearchAnime(this.value)" value="${animeSearchQuery}" class="filter-row-element glass-input glass-input-search" placeholder="Cari anime...">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs opacity-40">🔍</span>
                        </div>
                    </div>
                `;
            } else {
                // Status Buttons (Simplified) -> Modern Switcher Filter
                container.innerHTML = `
                    <form class="modern-filter-switcher w-full" id="switcher-anime-status" style="touch-action: pan-x; overscroll-behavior-x: contain;">
                        ${['all', 'watching', 'completed', 'plan_to_watch', 'on_hold', 'dropped'].map((s, index) => {
                            const label = s === 'all' ? 'SEMUA' : s.replace(/_/g, ' ');
                            const isActive = currentFilter === s;
                            return `<label style="animation-delay: ${index * 25}ms">
                                <input type="radio" name="sw-anime-status" value="${s}" ${isActive ? 'checked' : ''} onchange="filterAnime('${s}')">
                                <span class="text text-[10px] font-extrabold">${label.toUpperCase()}</span>
                            </label>`;
                        }).join('')}
                        <div class="layer"></div>
                    </form>
                `;
                setTimeout(() => {
                    const form = document.getElementById('switcher-anime-status');
                    if(form) {
                        const layer = form.querySelector('.layer');
                        const checked = form.querySelector('input:checked');
                        if (layer && checked) {
                            const lbl = checked.parentElement;
                            layer.style.left = lbl.offsetLeft + 'px';
                            layer.style.width = lbl.offsetWidth + 'px';
                        }
                        // BUGFIX: Cegah swipe horizontal di filter ini memicu swipe halaman
                        form.addEventListener('touchstart', e => e.stopPropagation(), { passive: true });
                        form.addEventListener('touchmove',  e => e.stopPropagation(), { passive: true });
                        form.addEventListener('touchend',   e => e.stopPropagation(), { passive: true });
                    }
                }, 50);
            }
        }

        /* index.php — Fungsi Kontrol Urutan Waifu */

        /* index.php — Logika Kontrol Waifu */

        /* index.php — Pastikan fungsi renderWaifuSubControls seperti ini */
        /* index.php — Update fungsi agar ada Search Bar sesuai SS */
        function renderWaifuSubControls() {
            const container = document.getElementById('waifu-sub-controls');
            if (!container) return;
            
            container.style.overflow = 'visible';
            const calIcon = `<span class="text-red-400 text-sm">📅</span>`; 
            const sortLabels = { 
                id: calIcon + '<span>BARU DITAMBAHKAN</span>', 
                nama: '<span class="text-sm">🔤</span><span>NAMA (A-Z)</span>', 
                is_fav: '<span class="text-sm">❤️</span><span>FAVORIT DAHULU</span>' 
            };
            
            const arrow = (waifuSort.field === 'nama') 
                    ? (waifuSort.order === 'asc' ? '↓' : '↑') 
                    : (waifuSort.order === 'desc' ? '↓' : '↑');

            container.innerHTML = `
                <div class="flex flex-col sm:flex-row gap-2.5 items-center w-full filter-item-anim overflow-visible" style="position: relative; z-index: 200;">
                    <div class="flex items-center gap-2 w-full sm:w-auto relative" style="z-index: 300;"> 
                        <button onclick="toggleWaifuCustomDropdown()" class="filter-row-element glass-input !w-full sm:!w-72 flex justify-between items-center bg-white/5 border-purple-500/30">
                            <span id="current-waifu-sort-label" class="font-bold text-purple-100 flex items-center gap-3">
                                ${sortLabels[waifuSort.field]}
                            </span>
                            <span class="text-[8px] opacity-40">▼</span>
                        </button>
                        
                        <div id="waifu-custom-sort-menu" class="custom-dropdown-menu">
                            <div class="glass-inner">
                                <div class="dropdown-item ${waifuSort.field === 'id' ? 'active' : ''}" onclick="selectWaifuSort('id')">
                                    ${calIcon} <span>BARU DITAMBAHKAN</span>
                                </div>
                                <div class="dropdown-item ${waifuSort.field === 'nama' ? 'active' : ''}" onclick="selectWaifuSort('nama')">
                                    <span class="text-sm">🔤</span> <span>NAMA (A-Z)</span>
                                </div>
                                <div class="dropdown-item ${waifuSort.field === 'is_fav' ? 'active' : ''}" onclick="selectWaifuSort('is_fav')">
                                    <span class="text-sm">❤️</span> <span>FAVORIT DAHULU</span>
                                </div>
                            </div>
                        </div>
                        
                        <button onclick="toggleWaifuSortOrder()" class="filter-row-element glass-input !w-11 flex items-center justify-center border-purple-500/50">
                            <span id="waifu-sort-icon" class="text-purple-400 font-bold text-base">${arrow}</span>
                        </button>
                    </div>

                    <div class="hidden sm:block h-6 w-[1px] bg-white/10 mx-0.5"></div>

                    <div class="relative w-full">
                        <input type="text" oninput="instantSearchWaifu(this.value)" value="${waifuSearchQuery}" class="filter-row-element glass-input glass-input-search" placeholder="Cari waifu...">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs opacity-40">🔍</span>
                    </div>
                </div>
            `;
        }

        function toggleWaifuCustomDropdown() {
            document.getElementById('waifu-custom-sort-menu').classList.toggle('show');
        }

        function selectWaifuSort(field) {
            waifuSort.field = field;
            // Jika pilih nama, defaultnya A-Z (asc), lainnya Terbaru (desc)
            waifuSort.order = (field === 'nama') ? 'asc' : 'desc';

            const fieldLabel = field === 'nama'
                ? 'Nama (A-Z)'
                : (field === 'is_fav' ? 'Favorit Dahulu' : 'Terakhir Ditambah');
            showToast(`Urutan Waifu: ${fieldLabel}`);

            document.getElementById('waifu-custom-sort-menu').classList.remove('show');
            renderWaifuSubControls();
            renderWaifus();
        }

        function toggleWaifuSortOrder() {
            waifuSort.order = waifuSort.order === 'desc' ? 'asc' : 'desc';

            let msg = "";
            if (waifuSort.field === 'nama') {
                msg = waifuSort.order === 'asc' ? 'A ke Z' : 'Z ke A';
            } else {
                msg = waifuSort.order === 'desc' ? 'Terbaru ke Terlama' : 'Terlama ke Terbaru';
            }
            showToast(`Urutan Waifu: ${msg}`);

            renderWaifuSubControls();
            renderWaifus();
        }
        

        /* index.php — Ganti seluruh blok renderAnimes (Baris 681-764) */
        /* index.php — Perbaikan renderAnimes agar mencari di daftar Anime */
        function renderAnimes() {
            const container = document.getElementById('anime-list');
            const empty = document.getElementById('anime-empty');
            if (!container) return;        

            // PERBAIKAN 1: Murni gunakan waktu Dibuat/ID agar tidak terlempar saat di-edit/favorit
            const getAnimeTimeValue = (item) => {
                if (item && item.created_at) {
                    const createdAt = new Date(item.created_at).getTime();
                    if (!Number.isNaN(createdAt)) return createdAt;
                }
                return Number(item?.id || 0);
            };
        
            let filtered = allAnimes.filter(a => {
                const matchStatus = (currentFilter === 'all' || a.status === currentFilter);
                const matchSearch = a.judul.toLowerCase().includes(animeSearchQuery);
                return matchStatus && matchSearch;
            });

            filtered.sort((a, b) => {
                const favA = Number(a.is_fav || 0);
                const favB = Number(b.is_fav || 0);
                const timeA = getAnimeTimeValue(a);
                const timeB = getAnimeTimeValue(b);
                // PERBAIKAN 2: Gunakan getFavAddedTime agar membaca waktu dari Database, bukan cuma Cache Lokal!
                const favTimeA = getFavAddedTime('anime', a.id);
                const favTimeB = getFavAddedTime('anime', b.id);

                if (animeSort.field === 'is_fav') {
                    if (favA !== favB) return favB - favA;
                    if (favA === 1 && favB === 1 && favTimeA !== favTimeB) {
                        return animeSort.order === 'desc' ? favTimeB - favTimeA : favTimeA - favTimeB;
                    }
                    return animeSort.order === 'desc' ? timeB - timeA : timeA - timeB;
                }
                if (animeSort.field === 'judul') {
                    return animeSort.order === 'asc' ? a.judul.localeCompare(b.judul) : b.judul.localeCompare(a.judul);
                }
                return animeSort.order === 'desc' ? timeB - timeA : timeA - timeB;
            });

            const start = (animeState.page - 1) * animeState.perPage;
            const paginated = filtered.slice(start, start + animeState.perPage);

            if (!filtered.length) {
                container.innerHTML = '';
                empty.classList.remove('hidden');
                document.getElementById('anime-pagination-controls').innerHTML = '';
            } else {
                empty.classList.add('hidden');
                container.innerHTML = paginated.map(renderAnimeCard).join('');
                renderPaginationControls('anime', filtered.length);
            }
        }

        // Fungsi Render Kartu Anime (Tampilan Edit/Hapus Seperti Waifu)
        function renderAnimeCard(a) {
            const pct = a.eps_total > 0 ? Math.min(100, Math.round((a.eps_nonton / a.eps_total) * 100)) : 0;
            const img = resolveMediaUrl(a.gambar_path || '');
            const statusLabels = { watching: 'Watching', completed: 'Completed', on_hold: 'On Hold', dropped: 'Dropped', plan_to_watch: 'Plan' };

            // Tambahkan class 'group' pada container utama agar hover effect bekerja
            return `
            <div class="glass-card anime-card p-4 cursor-pointer group card-entrance relative" onclick="showAnimeDetail(${a.id})" onmouseenter="glareIn(this)" onmouseleave="glareOut(this)" ontouchstart="glareIn(this)" ontouchend="glareOut(this)" ontouchcancel="glareOut(this)">
                <div class="glare-overlay"></div>
                <div class="card-image-wrap relative mb-3 overflow-hidden rounded-lg bg-black/20">
                    <img src="${img}" alt="${escHtml(a.judul)}" loading="eager" decoding="async"
                        data-base-src="${img}"
                        class="w-full h-40 object-cover transition-transform duration-500 group-hover:scale-110 opacity-0"
                        onload="handleCardImageLoad(this)" onerror="handleCardImageError(this)">
                    <div class="card-image-loading absolute inset-0 flex items-center justify-center">
                        <div class="h-8 w-8 rounded-full border-4 border-purple-300/20 border-t-purple-300 animate-spin"></div>
                    </div>
                    <div class="absolute top-2 left-2">
                        <span class="status-badge status-${a.status}">${statusLabels[a.status]}</span>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <h4 class="text-sm font-bold text-white truncate flex-1" title="${escHtml(a.judul)}">${escHtml(a.judul)}</h4>
                        <button onclick="event.stopPropagation(); toggleAnimeFav(${a.id}, ${a.is_fav})" class="heart-btn ${a.is_fav == 1 ? 'fav' : ''} text-lg flex-shrink-0">
                            ${a.is_fav == 1 ? '❤️' : '🤍'}
                        </button>
                    </div>
                    
                    ${a.eps_total > 0 ? `
                    <div class="progress-bar mb-1">
                        <div class="progress-fill" style="width:${pct}%"></div>
                    </div>
                    <p class="text-[10px] text-gray-400 flex justify-between">
                        <span>${a.eps_nonton}/${a.eps_total} eps</span> 
                        <span>${pct}%</span>
                    </p>
                    ` : `<p class="text-[10px] text-gray-400">${a.eps_nonton > 0 ? a.eps_nonton + ' eps ditonton' : 'Belum ditonton'}</p>`}
                </div>

                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <button onclick="event.stopPropagation(); editAnime(${a.id})" class="btn-edit flex-1 py-2 text-[10px] font-bold tracking-widest uppercase">Edit</button>
                    <button onclick="event.stopPropagation(); deleteAnime(${a.id})" class="btn-danger flex-1 py-2 text-[10px] font-bold tracking-widest uppercase">Hapus</button>
                </div>
            </div>`;
        }

        async function toggleAnimeFav(id, currentFav) {
            await fetch(`api.php?action=toggle_anime_fav&id=${id}`);

            const nextFav = Number(currentFav) ? 0 : 1;
            const key = String(id);
            if (nextFav === 1) {
                animeFavTimestamps[key] = Date.now();
            } else {
                delete animeFavTimestamps[key];
            }
            saveFavTimestamps();
            markLocalSyncChange('anime');

            const anime = allAnimes.find(item => String(item.id) === key);
            if (anime) {
                anime.is_fav = nextFav;
                anime.fav_marked_at = nextFav ? new Date().toISOString() : null;
            }

            showToast('Status favorit anime diperbarui!');
            renderAnimes();
            loadDashboard(); // Refresh tampilan dashboard
            if(document.getElementById('page-anime').classList.contains('active')) loadAnimes();
        }

        function filterAnime(status) {
            currentFilter = status;
            
            const form = document.getElementById('switcher-anime-status');
            if(form) {
                const layer = form.querySelector('.layer');
                const checked = form.querySelector(`input[value="${status}"]`);
                if(layer && checked) {
                    const lbl = checked.parentElement;
                    const prevLeft = parseFloat(layer.style.left) || lbl.offsetLeft;
                    const prevWidth = parseFloat(layer.style.width) || lbl.offsetWidth;
                    const nextLeft = lbl.offsetLeft;
                    const nextWidth = lbl.offsetWidth;
                    
                    if (typeof layer.animate === 'function') {
                        layer.style.transition = 'none';
                        layer.animate([
                            { left: prevLeft + 'px', width: prevWidth + 'px' },
                            { left: Math.min(prevLeft, nextLeft) + 'px', width: (Math.abs(nextLeft - prevLeft) + Math.max(prevWidth, nextWidth)) + 'px' },
                            { left: nextLeft + 'px', width: nextWidth + 'px' }
                        ], {
                            duration: 400,
                            easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
                            fill: 'forwards'
                        }).onfinish = () => {
                            layer.style.left = nextLeft + 'px'; layer.style.width = nextWidth + 'px';
                        };
                    } else {
                        layer.style.transition = 'left 0.4s cubic-bezier(0.4, 0, 0.2, 1), width 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                        layer.style.left = nextLeft + 'px';
                        layer.style.width = nextWidth + 'px';
                    }
                }
            }

            const labelStr = status === 'all' ? 'Semua' : status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
            showToast(`Status Anime: ${labelStr}`);
            
            renderAnimes();
        }

        // Anime Modal
        function openAnimeModal(data = null) {
            document.getElementById('anime-modal').classList.add('open');
            resetAnimeForm();
            if (data) {
                document.getElementById('anime-modal-title').textContent = 'Edit Anime';
                document.getElementById('anime-id').value = data.id;
                document.getElementById('anime-mal-id').value = data.mal_id || '';
                document.getElementById('anime-judul').value = data.judul;
                document.getElementById('anime-genres').value = data.genres || '';
                document.getElementById('anime-eps-nonton').value = data.eps_nonton;
                document.getElementById('anime-eps-total').value = data.eps_total;
                document.getElementById('anime-status').value = data.status;
                document.getElementById('anime-gambar-existing').value = data.gambar_path || '';
                if (data.gambar_path) {
                    document.getElementById('anime-gambar-url').value = data.gambar_path;
                    showAnimeImgPreview(data.gambar_path);
                }
            } else {
                document.getElementById('anime-modal-title').textContent = 'Tambah Anime';
            }
        }

        function closeAnimeModal(e) {
            // Menutup hanya jika klik di luar box atau tombol tutup
            if (!e || e.target === document.getElementById('anime-modal')) {
                document.getElementById('anime-modal').classList.remove('open');
            }
        }

        function resetAnimeForm() {
            document.getElementById('anime-form').reset();
            document.getElementById('anime-id').value = '';
            document.getElementById('anime-mal-id').value = '';
            document.getElementById('anime-gambar-existing').value = '';
            document.getElementById('anime-img-preview').classList.add('hidden');
            document.getElementById('search-results-list').classList.add('hidden');
            document.getElementById('search-results-list').innerHTML = '';
            document.getElementById('api-search').value = '';
            document.getElementById('anime-genres').value = '';

            // PERBAIKAN: Paksa kembalikan status ke 'plan_to_watch' agar Custom Select UI ikut ter-reset
            const statusSelect = document.getElementById('anime-status');
            if (statusSelect) {
                statusSelect.value = 'plan_to_watch';
            }
        }

        async function editAnime(id) {
            const anime = allAnimes.find(a => a.id == id);
            if (anime) openAnimeModal(anime);
        }

        async function deleteAnime(id) {
            // Ganti alert bawaan dengan modal kustom kita
            const isConfirmed = await showConfirmDialog(
                'Hapus Anime Ini?',
                'Tindakan ini tidak dapat dibatalkan. Anime ini akan dihapus permanen dari daftar Vault-mu.'
            );
            if (!isConfirmed) return;

            await fetch(`api.php?action=delete_anime&id=${id}`);
            markLocalSyncChange('anime');
            allAnimes = allAnimes.filter(item => String(item.id) !== String(id));

            toastManager.add({
                title: "Terhapus!",
                description: "Anime telah dihapus permanen.",
                type: "success"
            });

            renderAnimes();
            loadAnimes(); 
            loadDashboard(); 
        }

        async function submitAnime(e) {
            e.preventDefault();
            const id = document.getElementById('anime-id').value;
            const action = id ? 'update_anime' : 'add_anime';
        
            const fd = new FormData();
            if (id) fd.append('id', id);
            fd.append('mal_id', document.getElementById('anime-mal-id').value);
            fd.append('judul', document.getElementById('anime-judul').value);
            fd.append('eps_nonton', document.getElementById('anime-eps-nonton').value || 0);
            fd.append('eps_total', document.getElementById('anime-eps-total').value || 0);
            fd.append('genres', document.getElementById('anime-genres').value);
            fd.append('status', document.getElementById('anime-status').value);
            fd.append('gambar_existing', document.getElementById('anime-gambar-existing').value);

            const urlInput = document.getElementById('anime-gambar-url').value;
            const fileInput = document.getElementById('anime-gambar-file');

            if (fileInput.files[0]) {
                fd.append('gambar', fileInput.files[0]);
            } else if (urlInput && !urlInput.startsWith('uploads/')) {
                fd.append('gambar_url', urlInput);
            }

            const res = await fetch(`api.php?action=${action}`, { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) {
                markLocalSyncChange('anime');
                if (id) {
                    const existing = allAnimes.find(item => String(item.id) === String(id));
                    if (existing) {
                        existing.judul = document.getElementById('anime-judul').value;
                        existing.eps_nonton = Number(document.getElementById('anime-eps-nonton').value || 0);
                        existing.eps_total = Number(document.getElementById('anime-eps-total').value || 0);
                        existing.genres = document.getElementById('anime-genres').value;
                        existing.status = document.getElementById('anime-status').value;
                        existing.gambar_path = fileInput.files[0]
                            ? URL.createObjectURL(fileInput.files[0])
                            : (urlInput || document.getElementById('anime-gambar-existing').value || existing.gambar_path);
                    }
                    renderAnimes();
                }
                toastManager.add({
                    title: "Success!",
                    description: id ? "Anime berhasil diperbarui✨" : "Anime berhasil ditambahkan✨",
                    type: "success"
                });
                forceTopOnNextModalUnlock = true;
                modalScrollLockY = 0;
                closeAnimeModal();
                forceViewportTop();
                setTimeout(forceViewportTop, 0);
                loadAnimes();
                loadDashboard();
            } else {
                toastManager.add({ title: "Gagal!", description: data.message || 'Gagal menyimpan!', type: "error" });
            }
        }

        function previewAnimeImg(input) {
            if (input.files[0]) {
                const url = URL.createObjectURL(input.files[0]);
                showAnimeImgPreview(url);
                document.getElementById('anime-gambar-url').value = '';
            }
        }

        function showAnimeImgPreview(url) {
            const el = document.getElementById('anime-img-preview');
            el.src = resolveMediaUrl(url);
            el.classList.remove('hidden');
        }

        // ============ WAIFU ============
        async function loadWaifus(forceFresh = false) {
            if (Array.isArray(allWaifus) && allWaifus.length) {
                warmImageCache(allWaifus.slice(0, 20).map(item => item.pict_path || item.official_pict_url || item.art_path));
                renderWaifus();
            }
            const waifuUrl = forceFresh
                ? `api.php?action=get_waifus&_sync=${Date.now()}`
                : 'api.php?action=get_waifus';
            const res = await fetch(waifuUrl, forceFresh ? { cache: 'no-store' } : undefined);
            const data = await res.json();
            allWaifus = data.data || [];
            warmImageCache(allWaifus.slice(0, 20).map(item => item.pict_path || item.official_pict_url || item.art_path));
            renderWaifus();
        }

        let waifuViewMode = 'grid'; 

        // index.php - Fungsi Sinkronisasi Switch
        function changeWaifuView(mode) {
            waifuViewMode = mode;
            // Trigger visual sinkronisasi radio
            const radio = document.querySelector(`input[name="sw-waifu"][value="${mode}"]`);
            if (radio && !radio.checked) { radio.checked = true; radio.dispatchEvent(new Event('change')); }
            
            renderWaifus();
        }

        /* index.php — Update renderWaifus dengan logika Sort */
        /* index.php — Ganti bagian sorting di dalam renderWaifus() */
        function renderWaifus() {
            const container = document.getElementById('waifu-list');
            const empty = document.getElementById('waifu-empty');
            if (!container) return;

            const getWaifuTimeValue = (item) => {
                if (item && item.created_at) {
                    const createdAt = new Date(item.created_at).getTime();
                    if (!Number.isNaN(createdAt)) return createdAt;
                }
                return Number(item?.id || 0);
            };

            let sorted = allWaifus.filter(w => {
                return w.nama.toLowerCase().includes(waifuSearchQuery) || 
                    (w.anime_asal && w.anime_asal.toLowerCase().includes(waifuSearchQuery));
            });
            
            sorted.sort((a, b) => {
                const favA = Number(a.is_fav || 0);
                const favB = Number(b.is_fav || 0);
                const timeA = getWaifuTimeValue(a);
                const timeB = getWaifuTimeValue(b);
                const favTimeA = getFavAddedTime('waifu', a.id);
                const favTimeB = getFavAddedTime('waifu', b.id);

                if (waifuSort.field === 'is_fav') {
                    if (favA !== favB) return favB - favA;
                    if (favA === 1 && favB === 1 && favTimeA !== favTimeB) {
                        return waifuSort.order === 'desc' ? favTimeB - favTimeA : favTimeA - favTimeB;
                    }
                    return waifuSort.order === 'desc' ? timeB - timeA : timeA - timeB;
                }
                if (waifuSort.field === 'nama') {
                    return waifuSort.order === 'asc' ? a.nama.localeCompare(b.nama) : b.nama.localeCompare(a.nama);
                }
                return waifuSort.order === 'desc' ? timeB - timeA : timeA - timeB;
            });
            
            const start = (waifuState.page - 1) * waifuState.perPage;
            const paginated = sorted.slice(start, start + waifuState.perPage);
            
            if (waifuViewMode === 'grid') {
                container.className = "grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4";
            } else {
                container.className = "grid grid-cols-1 gap-3";
            }

            if (!sorted.length) {
                container.innerHTML = '';
                empty.classList.remove('hidden');
                document.getElementById('waifu-pagination-controls').innerHTML = '';
            } else {
                empty.classList.add('hidden');
                container.innerHTML = paginated.map(w => waifuViewMode === 'grid' ? renderWaifuCard(w) : renderWaifuCardHorizontal(w)).join('');
                renderPaginationControls('waifu', sorted.length);
            }
        }

        // Desain 2: Memanjang ke Samping (Khusus Mode List)
        function renderWaifuCardHorizontal(w) {
            const pict = resolveMediaUrl(w.pict_path || w.official_pict_url || w.art_path || '');
            return `
            <div class="glass-card waifu-card-list overflow-hidden group cursor-pointer flex card-entrance relative" onclick="showWaifuDetail(${w.id})" onmouseenter="glareIn(this)" onmouseleave="glareOut(this)" ontouchstart="glareIn(this)" ontouchend="glareOut(this)" ontouchcancel="glareOut(this)">
                <div class="glare-overlay"></div>
                <div class="card-image-wrap img-container relative w-[100px] h-[120px] flex-shrink-0 bg-black/20">
                    <img src="${pict}" data-base-src="${pict}" class="w-full h-full object-cover opacity-0" loading="eager" decoding="async" onload="handleCardImageLoad(this)" onerror="handleCardImageError(this)">
                    <div class="card-image-loading absolute inset-0 flex items-center justify-center">
                        <div class="h-6 w-6 rounded-full border-4 border-pink-300/20 border-t-pink-300 animate-spin"></div>
                    </div>
                    <button onclick="event.stopPropagation(); toggleFav(${w.id}, ${w.is_fav})" class="absolute top-1 left-1 heart-btn ${w.is_fav ? 'fav' : ''} bg-black/40 p-1 rounded-full text-xs">
                        ${w.is_fav ? '❤️' : '🤍'}
                    </button>
                </div>
                
                <div class="p-4 flex-1 min-w-0 flex flex-col justify-center">
                    <h4 class="font-bold text-white text-base truncate leading-tight">${escHtml(w.nama)}</h4>
                    <p class="text-xs text-purple-300/70 italic truncate mt-1">${escHtml(w.anime_asal || 'Unknown Origin')}</p>
                </div>

                <div class="flex flex-col justify-center gap-1.5 pr-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300 w-auto">
                    <button onclick="event.stopPropagation(); editWaifu(${w.id})" 
                        class="btn-edit py-1.5 px-3 text-[10px] font-bold tracking-tighter uppercase rounded-lg w-full">
                        Edit
                    </button>
                    <button onclick="event.stopPropagation(); deleteWaifu(${w.id})" 
                        class="btn-danger py-1.5 px-3 text-[10px] font-bold tracking-tighter uppercase rounded-lg w-full">
                        Hapus
                    </button>
                </div>
            </div>`;
        }


        function renderWaifuCard(w) {
            const pict = resolveMediaUrl(w.pict_path || w.official_pict_url || w.art_path || '');
            const mainArt = resolveMediaUrl(w.art_path || w.pict_path || w.official_pict_url || '');
            
            return `
            <div class="glass-card waifu-card overflow-hidden group cursor-pointer card-entrance flex flex-col relative" onclick="showWaifuDetail(${w.id})" onmouseenter="glareIn(this)" onmouseleave="glareOut(this)" ontouchstart="glareIn(this)" ontouchend="glareOut(this)" ontouchcancel="glareOut(this)">
                <div class="glare-overlay"></div>
                <div class="card-image-wrap relative h-48 w-full overflow-hidden flex-shrink-0 bg-black/20">
                    <img src="${mainArt}" data-base-src="${mainArt}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 opacity-0" loading="eager" decoding="async" alt="${escHtml(w.nama)}" onload="handleCardImageLoad(this)" onerror="handleCardImageError(this)">
                    <div class="card-image-loading absolute inset-0 flex items-center justify-center">
                        <div class="h-8 w-8 rounded-full border-4 border-pink-300/20 border-t-pink-300 animate-spin"></div>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-[#1a1030] via-transparent to-transparent"></div>
                    
                    <div class="card-image-wrap absolute bottom-3 left-3 w-12 h-12 rounded-full border-2 border-pink-500 shadow-lg overflow-hidden bg-[#1a1030]/80">
                        <img src="${pict}" data-base-src="${pict}" class="w-full h-full object-cover opacity-0" loading="eager" decoding="async" onload="handleCardImageLoad(this)" onerror="handleCardImageError(this)">
                        <div class="card-image-loading absolute inset-0 flex items-center justify-center">
                            <div class="h-5 w-5 rounded-full border-4 border-pink-300/20 border-t-pink-300 animate-spin"></div>
                        </div>
                    </div>
                    
                    <button onclick="event.stopPropagation(); toggleFav(${w.id}, ${w.is_fav})" class="absolute top-3 right-3 heart-btn ${w.is_fav ? 'fav' : ''} bg-black/40 p-1.5 rounded-full backdrop-blur-md">
                        ${w.is_fav ? '❤️' : '🤍'}
                    </button>
                </div>

                <div class="p-4 flex-1 flex flex-col">
                    <div class="mb-3">
                        <h4 class="font-bold text-white text-lg leading-tight truncate">${escHtml(w.nama)}</h4>
                        <p class="text-xs text-purple-300/70 italic">${escHtml(w.anime_asal || 'Unknown Origin')}</p>
                    </div>
                    
                    <div class="mt-auto flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <button onclick="event.stopPropagation(); editWaifu(${w.id})" class="btn-edit flex-1 py-2 text-[10px] font-bold tracking-widest uppercase">Edit</button>
                        <button onclick="event.stopPropagation(); deleteWaifu(${w.id})" class="btn-danger flex-1 py-2 text-[10px] font-bold tracking-widest uppercase">Hapus</button>
                    </div>
                </div>
            </div>`;
        }

        async function toggleFav(id, currentFav) {
            // PERBAIKAN: Selalu gunakan 'toggle_fav' karena api.php Anda sudah cerdas 
            // bisa mendeteksi apakah harus menambah atau menghapus favorit
            const res = await fetch(`api.php?action=toggle_fav&id=${id}`);
            const data = await res.json();
            
            if (data.success) {
                const nextFav = Number(currentFav) ? 0 : 1;
                const key = String(id);

                if (nextFav === 1) {
                    waifuFavTimestamps[key] = Date.now();
                } else {
                    delete waifuFavTimestamps[key];
                }
                saveFavTimestamps();
                markLocalSyncChange('waifu');

                const waifu = allWaifus.find(item => String(item.id) === key);
                if (waifu) {
                    waifu.is_fav = nextFav;
                    waifu.fav_marked_at = nextFav ? new Date().toISOString() : null;
                }

                showToast("Status favorit diperbarui!");
                renderWaifus();
                // Segarkan data agar heart berubah warna dan dashboard terupdate
                loadWaifus();
                setTimeout(loadDashboard, 300);
            }
        }

        function openWaifuModal(data = null) {
            document.getElementById('waifu-modal').classList.add('open');
            resetWaifuForm();
            if (data) {
                document.getElementById('waifu-modal-title').textContent = 'Edit Waifu';
                document.getElementById('waifu-id').value = data.id;
                document.getElementById('waifu-nama').value = data.nama;
                document.getElementById('waifu-anime').value = data.anime_asal || '';
                document.getElementById('waifu-umur').value = data.umur || '';
                document.getElementById('waifu-bio').value = data.bio || '';
                document.getElementById('waifu-is-fav').checked = !!(data.is_fav == 1);
                
                // Simpan gambar lama (dari upload atau official) ke field existing
                document.getElementById('waifu-pict-existing').value = data.pict_path || data.official_pict_url || '';
                
                // Tampilkan preview foto profil utama
                const currentPict = data.pict_path || data.official_pict_url;
                if (currentPict) { 
                    const el = document.getElementById('waifu-pict-preview'); 
                    el.src = resolveMediaUrl(currentPict);
                    el.classList.remove('hidden'); 
                }
            } else {
                document.getElementById('waifu-modal-title').textContent = 'Tambah Waifu';
            }
        }

        function closeWaifuModal(e) {
            if (!e || e.target === document.getElementById('waifu-modal')) {
                document.getElementById('waifu-modal').classList.remove('open');
            }
        }

        function resetWaifuForm() {
            document.getElementById('waifu-form').reset();
            document.getElementById('waifu-id').value = '';
            document.getElementById('waifu-pict-existing').value = '';
            document.getElementById('waifu-art-existing').value = '';
            document.getElementById('waifu-pict-preview').classList.add('hidden');
        
        }

        async function editWaifu(id) {
            const res = await fetch(`api.php?action=get_waifu_details&id=${id}`);
            const data = await res.json();
            
            // Buka modal dulu
            openWaifuModal(data);
            
            // Simpan link official untuk tombol revert (Balik ke Official)
            currentOfficialUrl = data.official_pict_url || "";
            const revertBtn = document.getElementById('btn-revert-official');
            if (currentOfficialUrl) {
                revertBtn.classList.remove('hidden');
            } else {
                revertBtn.classList.add('hidden');
            }

            // Munculkan bagian manajemen galeri
            document.getElementById('gallery-management-section').classList.remove('hidden');
            renderModalGallery(data.gallery || []);
        }

        async function deleteWaifu(id) {
            // Ganti alert bawaan dengan modal kustom kita
            const isConfirmed = await showConfirmDialog(
                'Hapus Waifu Ini?',
                'Tindakan ini tidak dapat dibatalkan. Waifu dan semua foto galeri miliknya akan lenyap.'
            );
            if (!isConfirmed) return;

            await fetch(`api.php?action=delete_waifu&id=${id}`);
            markLocalSyncChange('waifu');
            allWaifus = allWaifus.filter(item => String(item.id) !== String(id));

            toastManager.add({
                title: "Terhapus!",
                description: "Waifu dan galerinya telah dihapus.",
                type: "success"
            });

            renderWaifus();
            loadWaifus(); 
            loadDashboard(); 
        }

        async function submitWaifu(e) {
            e.preventDefault();
            const id = document.getElementById('waifu-id').value;
            const action = id ? 'update_waifu' : 'add_waifu';
            const bioValue = document.getElementById('waifu-bio').value || '';
            const fd = new FormData();
            if (id) fd.append('id', id);
            fd.append('nama', document.getElementById('waifu-nama').value);
            fd.append('anime_asal', document.getElementById('waifu-anime').value);
            fd.append('umur', document.getElementById('waifu-umur').value);
            fd.append('bio', bioValue);
            fd.append('pict_existing', document.getElementById('waifu-pict-existing').value);
            if (document.getElementById('waifu-is-fav').checked) fd.append('is_fav', '1');

            const pictFile = document.getElementById('waifu-pict-file');
            if (pictFile.files[0]) fd.append('pict', pictFile.files[0]);

            const res = await fetch(`api.php?action=${action}`, { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) {
                markLocalSyncChange('waifu');
                if (id) {
                    const existing = allWaifus.find(item => String(item.id) === String(id));
                    if (existing) {
                        existing.nama = document.getElementById('waifu-nama').value;
                        existing.anime_asal = document.getElementById('waifu-anime').value;
                        existing.umur = document.getElementById('waifu-umur').value;
                        existing.bio = document.getElementById('waifu-bio').value;
                        existing.is_fav = document.getElementById('waifu-is-fav').checked ? 1 : 0;
                        existing.pict_path = pictFile.files[0]
                            ? URL.createObjectURL(pictFile.files[0])
                            : (document.getElementById('waifu-pict-existing').value || existing.pict_path);
                    }
                    renderWaifus();
                }
                toastManager.add({
                    title: "Success!",
                    description: id ? "Waifu berhasil diperbarui❤️" : "Waifu berhasil ditambahkan❤️",
                    type: "success"
                });
                forceTopOnNextModalUnlock = true;
                modalScrollLockY = 0;
                closeWaifuModal();
                forceViewportTop();
                setTimeout(forceViewportTop, 0);
                loadWaifus();
                loadDashboard(); // Update tampilan depan
            } else {
                toastManager.add({ title: "Gagal!", description: data.message || 'Gagal menyimpan!', type: "error" });
            }
        }

        // Fungsi untuk mengembalikan foto profil ke link asli API
        function revertToOfficial() {
            if (!currentOfficialUrl) return;
            document.getElementById('waifu-pict-existing').value = currentOfficialUrl;
            document.getElementById('waifu-pict-preview').src = resolveMediaUrl(currentOfficialUrl);
            showToast("Kembali ke foto Official!");
        }

        // Menampilkan list foto di dalam modal edit
        function renderModalGallery(gallery) {
            const container = document.getElementById('modal-gallery-list');
            container.innerHTML = gallery.map(img => `
                <div class="relative group aspect-square">
                    <div class="card-image-wrap relative w-full h-full rounded-lg overflow-hidden bg-black/20 border border-white/10">
                        <img src="${resolveMediaUrl(img.image_path)}" data-base-src="${resolveMediaUrl(img.image_path)}" class="w-full h-full object-cover rounded-lg opacity-0" loading="eager" decoding="async" onload="handleCardImageLoad(this)" onerror="handleCardImageError(this)">
                        <div class="card-image-loading absolute inset-0 flex items-center justify-center">
                            <div class="h-5 w-5 rounded-full border-4 border-pink-300/20 border-t-pink-300 animate-spin"></div>
                        </div>
                    </div>
                    <button type="button" onclick="deleteGalleryItem(${img.id})" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 text-[10px] flex items-center justify-center">✕</button>
                </div>
            `).join('');
        }

        let cropperInstance = null;

        // Fungsi pemicu saat tombol "+ Tambah Foto ART Baru" diklik
        function startArtCrop() {
            document.getElementById('add-art-file').click();
        }

        // Upload ART baru ke galeri tanpa limit
        async function uploadToGallery() {
            const fileInput = document.getElementById('add-art-file');
            const file = fileInput.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                const cropImg = document.getElementById('crop-preview-image');
                cropImg.src = e.target.result;
                
                // Tampilkan modal pemotong
                document.getElementById('crop-modal').classList.add('open');
                
                // Inisialisasi Cropper (Rasio bebas/disesuaikan)
                if (cropperInstance) cropperInstance.destroy();
                cropperInstance = new Cropper(cropImg, {
                    aspectRatio: NaN, // Bebas sesuaikan kotak potong
                    viewMode: 1,
                    dragMode: 'move',
                    background: false,
                    autoCropArea: 0.8
                });
            };
            reader.readAsDataURL(file);
        }

        async function executeCropAndUpload() {
            const waifuId = document.getElementById('waifu-id').value;
            if (!cropperInstance || !waifuId) return;

            // Ambil hasil potongan dalam kualitas tinggi
            cropperInstance.getCroppedCanvas({ maxWidth: 2048, maxHeight: 2048 }).toBlob(async (blob) => {
                const fd = new FormData();
                fd.append('waifu_id', waifuId);
                fd.append('art', blob, 'cropped_art.jpg'); // Kirim sebagai file 'art' ke PHP

                showToast("Sedang memproses upload...");
                
                const res = await fetch('api.php?action=add_gallery_item', { method: 'POST', body: fd });
                const data = await res.json();
                
                if (data.success) {
                    markLocalSyncChange('waifu');
                    showToast("Art baru berhasil disesuaikan & disimpan!");
                    closeCropModal();
                    editWaifu(waifuId); // Refresh tampilan galeri di modal
                } else {
                    showToast("Gagal menyimpan gambar!", true);
                }
            }, 'image/jpeg', 0.9);
        }

        function closeCropModal() {
            document.getElementById('crop-modal').classList.remove('open');
            document.getElementById('add-art-file').value = '';
            if (cropperInstance) cropperInstance.destroy();
        }

        // Hapus foto art
        async function deleteGalleryItem(gid) {
            const isConfirmed = await showConfirmDialog(
                'Hapus Foto ART?', 
                'Foto ini akan dihapus selamanya dari galeri waifu-mu.'
            );
            if (!isConfirmed) return;

            const waifuId = document.getElementById('waifu-id').value;
            await fetch(`api.php?action=delete_gallery_item&id=${gid}`);
            markLocalSyncChange('waifu');
            editWaifu(waifuId);
        }

        function previewWaifuPict(input) {
            if (input.files[0]) {
                const el = document.getElementById('waifu-pict-preview');
                el.src = URL.createObjectURL(input.files[0]);
                el.classList.remove('hidden');
            }
        }

        function resolveMediaUrl(url) {
            const raw = (url || '').toString().trim();
            if (!raw) return '';

            if (raw.startsWith('data:') || raw.startsWith('blob:')) return raw;
            if (raw.startsWith('uploads/')) return raw;
            if (raw.startsWith('/uploads/')) return raw;

            try {
                const parsed = new URL(raw, window.location.href);
                if (parsed.origin === window.location.origin) {
                    return parsed.pathname + parsed.search + parsed.hash;
                }

                // Route external image hosts through proxy to avoid mobile hotlink blocks.
                return `api.php?action=image_proxy&url=${encodeURIComponent(parsed.href)}`;
            } catch (_) {
                return raw;
            }
        }

    
        // Card sederhana khusus untuk list di dalam popup agar lebih rapi


        // KUNCI 2: Pastikan mesin Drag dimatikan saat ditutup agar tidak nyangkut saat dibuka lagi
        function forceCloseFavoritesModal() {
            document.getElementById('favorites-list-modal').classList.remove('open');
            if (document.getElementById('fav-modal-content').classList.contains('drag-active')) {
                toggleDragMode(false);
            }
        }

        function closeFavoritesModal(e) {
            if (e && e.target && e.target.id === 'favorites-list-modal') {
                forceCloseFavoritesModal();
            }
        }

        // ============ LIGHTBOX ============
        function openLightbox(src) {
            document.getElementById('lightbox-img').src = resolveMediaUrl(src);
            document.getElementById('lightbox').classList.add('open');
        }

        // ============ UTILS ============
        function escHtml(str) {
            if (!str) return '';
            return str.toString().replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        // ============ INIT ============
        loadFavTimestamps();
        initFavOrderMode();
        loadFavModesFromServer().finally(() => {
            loadDashboard();
        });

        window.addEventListener('vault-authenticated', () => {
            loadFavModesFromServer().finally(() => {
                loadDashboard();
                loadAnimes();
                loadWaifus();
                initAutoSync();
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const animeInput = document.getElementById('api-search');
            if (animeInput) {
                animeInput.addEventListener('input', () => {
                    // Jalankan langsung tanpa debounce agar list muncul per ketikan.
                    searchAnimeAPI();
                });
            }

            const waifuInput = document.getElementById('api-search-waifu');
            if (waifuInput) {
                waifuInput.addEventListener('input', () => {
                    // Jalankan langsung tanpa debounce agar list muncul per ketikan.
                    searchWaifuAPI();
                });
            }
        });
            // Fungsi Detail Anime
        // Fungsi Detail Anime: Sekarang menggunakan Fetch agar stabil di semua halaman
        let animeDetailRequestSeq = 0;
        let waifuDetailRequestSeq = 0;

        async function showAnimeDetail(id) {
            const requestSeq = ++animeDetailRequestSeq;
            const modal = document.getElementById('detail-modal-anime');
            const imgEl = document.getElementById('det-anime-img');
            const imgLoadingEl = document.getElementById('det-anime-loading');

            // Tampilkan loading dulu sampai gambar baru selesai dimuat.
            imgEl.style.opacity = '0';
            imgLoadingEl?.classList.remove('hidden');
            document.getElementById('det-anime-judul').textContent = '';
            document.getElementById('det-anime-status').textContent = '';
            document.getElementById('det-anime-genres').textContent = '';
            document.getElementById('det-anime-progress').style.width = '0%';
            document.getElementById('det-anime-eps').textContent = '';

            modal.classList.add('open');

            const res = await fetch(`api.php?action=get_anime_details&id=${id}`);
            const a = await res.json();
            if (!a || requestSeq !== animeDetailRequestSeq) return;

            const img = resolveMediaUrl(a.gambar_path || `https://api.dicebear.com/7.x/shapes/svg?seed=${a.id}`);
            const pct = a.eps_total > 0 ? Math.min(100, Math.round((a.eps_nonton / a.eps_total) * 100)) : 0;
            const labels = { watching: 'Watching', completed: 'Completed', on_hold: 'On Hold', dropped: 'Dropped', plan_to_watch: 'Plan' };

            imgEl.dataset.errorHandled = '0';
            imgEl.onload = () => {
                if (requestSeq !== animeDetailRequestSeq) return;
                imgLoadingEl?.classList.add('hidden');
                imgEl.style.opacity = '1';
            };
            imgEl.onerror = () => {
                if (requestSeq !== animeDetailRequestSeq) return;
                if (imgEl.dataset.errorHandled === '1') {
                    imgLoadingEl?.classList.add('hidden');
                    imgEl.style.opacity = '1';
                    return;
                }
                imgEl.dataset.errorHandled = '1';
                imgEl.src = `https://api.dicebear.com/7.x/shapes/svg?seed=${a.id}`;
            };
            imgEl.src = img;
            document.getElementById('det-anime-judul').textContent = a.judul;
            document.getElementById('det-anime-status').textContent = labels[a.status];
            
            // Tampilkan Genre di popup
            document.getElementById('det-anime-genres').textContent = a.genres || 'No genres listed';
            
            document.getElementById('det-anime-status').className = `status-badge status-${a.status} mb-3 inline-block`;
            document.getElementById('det-anime-progress').style.width = pct + '%';
            document.getElementById('det-anime-eps').textContent = `${a.eps_nonton} / ${a.eps_total || '?'} Episode (${pct}%)`;
            
        }

        // Fungsi Detail Waifu
        async function showWaifuDetail(id) {
            const requestSeq = ++waifuDetailRequestSeq;
            const modal = document.getElementById('detail-modal-waifu');
            const bannerEl = document.getElementById('det-waifu-banner');
            const pictEl = document.getElementById('det-waifu-pict');
            const pictLoadingEl = document.getElementById('det-waifu-loading');
            const galleryContainer = document.querySelector('#detail-modal-waifu .grid');

            // Bersihkan konten lama dulu agar tidak carry-over saat ganti item cepat.
            bannerEl.style.backgroundImage = '';
            pictEl.style.opacity = '0';
            pictLoadingEl?.classList.remove('hidden');
            document.getElementById('det-waifu-nama').textContent = '';
            document.getElementById('det-waifu-bio').textContent = '';
            if (galleryContainer) galleryContainer.innerHTML = '';

            modal.classList.add('open');

            const res = await fetch(`api.php?action=get_waifu_details&id=${id}`);
            const w = await res.json();
            if (!w || requestSeq !== waifuDetailRequestSeq) return;
            
            const pict = resolveMediaUrl(w.pict_path || w.official_pict_url || 'https://api.dicebear.com/7.x/adventurer/svg?seed=' + w.nama);
            document.getElementById('det-waifu-banner').style.backgroundImage = `url(${pict})`;
            pictEl.dataset.errorHandled = '0';
            pictEl.onload = () => {
                if (requestSeq !== waifuDetailRequestSeq) return;
                pictLoadingEl?.classList.add('hidden');
                pictEl.style.opacity = '1';
            };
            pictEl.onerror = () => {
                if (requestSeq !== waifuDetailRequestSeq) return;
                if (pictEl.dataset.errorHandled === '1') {
                    pictLoadingEl?.classList.add('hidden');
                    pictEl.style.opacity = '1';
                    return;
                }
                pictEl.dataset.errorHandled = '1';
                pictEl.src = `https://api.dicebear.com/7.x/adventurer/svg?seed=${encodeURIComponent(w.nama || 'waifu')}`;
            };
            pictEl.src = pict;
            document.getElementById('det-waifu-nama').textContent = w.nama;
            document.getElementById('det-waifu-bio').textContent = w.bio || 'No description.';
            
            // Tampilkan semua koleksi foto ART
            if (galleryContainer) {
                galleryContainer.className = "grid grid-cols-2 sm:grid-cols-3 gap-2 mt-4";
                galleryContainer.innerHTML = (w.gallery || []).map(img => `
                    <div class="card-image-wrap relative w-full h-24 rounded-xl overflow-hidden bg-black/20">
                        <img src="${resolveMediaUrl(img.image_path)}" data-base-src="${resolveMediaUrl(img.image_path)}" class="w-full h-24 object-cover rounded-xl cursor-zoom-in opacity-0" loading="eager" decoding="async" onload="handleCardImageLoad(this)" onerror="handleCardImageError(this)" onclick="openLightbox('${img.image_path}')">
                        <div class="card-image-loading absolute inset-0 flex items-center justify-center">
                            <div class="h-6 w-6 rounded-full border-4 border-pink-300/20 border-t-pink-300 animate-spin"></div>
                        </div>
                    </div>
                `).join('');
            }
        }

        // Handler klik luar modal untuk menutup
        function closeDetailAnime(e) {
            if (!e || e.target === document.getElementById('detail-modal-anime')) {
                document.getElementById('detail-modal-anime').classList.remove('open');
            }
        }

        function closeDetailWaifu(e) {
            if (!e || e.target === document.getElementById('detail-modal-waifu')) {
                document.getElementById('detail-modal-waifu').classList.remove('open');
            }
        }

        // Fallback helper untuk modal berbasis id agar kompatibel dengan markup lama/baru.
        function openModal(id) {
            const el = document.getElementById(id);
            if (el) el.classList.add('open');
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            if (el) el.classList.remove('open');
        }

        // index.php — Tambahkan fungsi kontrol baru

        function toggleCustomDropdown() {
            const menu = document.getElementById('custom-sort-menu');
            menu.classList.toggle('show');
        }

        function selectSort(field) {
            // Panggil fungsi changeSortField yang sudah Senpai punya
            changeSortField(field); 
            
            // Tutup menu
            document.getElementById('custom-sort-menu').classList.remove('show');
            
            // Gambar ulang sub-controls agar label tombol berubah
            renderAnimeSubControls();
        }

        // Menutup dropdown jika klik di luar menu
        window.addEventListener('click', function(e) {
            const menu = document.getElementById('custom-sort-menu');
            const btn = document.querySelector('[onclick="toggleCustomDropdown()"]');
            if (menu && !menu.contains(e.target) && !btn.contains(e.target)) {
                menu.classList.remove('show');
            }
        });

        
        // Efek animasi Squish
        function initModernSwitchers() {
            document.querySelectorAll('.modern-switcher').forEach(switcher => {
                const radios = switcher.querySelectorAll('input[type="radio"]');
                let previousIndex = '1';

                radios.forEach((radio, index) => {
                    if (radio.checked) previousIndex = (index + 1).toString();
                });
                switcher.setAttribute('c-previous', previousIndex);

                radios.forEach((radio, index) => {
                    radio.addEventListener('change', () => {
                        if (radio.checked) {
                            switcher.setAttribute('c-previous', previousIndex);
                            previousIndex = (index + 1).toString();
                        }
                    });
                });
            });
        }
        
        // Panggil setelah halaman dimuat
        document.addEventListener('DOMContentLoaded', initModernSwitchers);

        </script>

            <div id="detail-modal-anime" class="modal-overlay" onclick="closeDetailAnime(event)">
                <div class="modal-box p-0 overflow-hidden max-w-md">
                    <div class="relative">
                        <img id="det-anime-img" src="" class="w-full h-64 object-contain bg-black/20 transition-opacity duration-200">
                        <div id="det-anime-loading" class="absolute inset-0 flex items-center justify-center bg-black/25 hidden">
                            <div class="h-9 w-9 rounded-full border-4 border-purple-300/25 border-t-purple-300 animate-spin"></div>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-[#1a1030] via-transparent to-transparent"></div>
                        <button onclick="document.getElementById('detail-modal-anime').classList.remove('open')" class="absolute top-4 right-4 bg-black/50 p-2 rounded-full text-white">✕</button>
                    </div>
                    <div class="p-6 -mt-12 relative z-10">
                        <h3 id="det-anime-judul" class="text-2xl font-bold text-white mb-1 leading-tight"></h3>
                        
                        <span id="det-anime-status" class="status-badge mb-3 inline-block"></span>
                        <p id="det-anime-genres" class="text-xs text-purple-300/80 mb-3 italic"></p>
                        <div class="glass p-4 rounded-xl border-purple-500/20">
                            <p class="text-purple-300 text-sm mb-1">Progress Menonton</p>
                            <div class="progress-bar mb-2 h-2">
                                <div id="det-anime-progress" class="progress-fill"></div>
                            </div>
                            <p id="det-anime-eps" class="text-xs text-gray-400"></p>
                        </div>
                    </div>
                </div>
            </div>

        <div id="detail-modal-waifu" class="modal-overlay" onclick="closeDetailWaifu(event)">
            <div class="modal-box p-0 overflow-y-auto max-h-[90vh]">
                <div class="relative h-48 bg-purple-900/20">
                    <div id="det-waifu-banner" class="absolute inset-0 opacity-30 bg-cover bg-center blur-sm"></div>
                    <div id="det-waifu-loading" class="absolute -bottom-10 left-6 w-32 h-32 rounded-full border-4 border-[#1a1030] bg-[#1a1030]/80 flex items-center justify-center z-10 hidden">
                        <div class="h-8 w-8 rounded-full border-4 border-pink-300/25 border-t-pink-300 animate-spin"></div>
                    </div>
                    <img id="det-waifu-pict" src="" class="absolute -bottom-10 left-6 w-32 h-32 rounded-full border-4 border-[#1a1030] object-cover shadow-xl z-20 transition-opacity duration-200 cursor-pointer" onclick="openLightbox(this.src)">
                    <button onclick="document.getElementById('detail-modal-waifu').classList.remove('open')" class="absolute top-4 right-4 bg-black/50 p-2 rounded-full text-white z-20">✕</button>
                </div>
                <div class="p-6 pt-12">
                    <div class="flex items-center justify-between mb-2">
                        <h3 id="det-waifu-nama" class="text-2xl font-bold text-white"></h3>
                        <span id="det-waifu-umur" class="text-pink-400 font-semibold"></span>
                    </div>
                    <p id="det-waifu-anime" class="text-purple-300 text-sm mb-4"></p>
                    <p id="det-waifu-bio" class="text-gray-400 text-sm leading-relaxed mb-6 italic"></p>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-4"></div>
                </div>
            </div>
        </div>

        <div id="modal-detail" class="modal-overlay" onclick="if(event.target===this) closeModal('modal-detail')">
            <div class="modal-box p-0 overflow-hidden flex flex-col">
                <div class="relative w-full h-64 sm:h-80 bg-gray-900">
                    <img id="detail-pict" src="" alt="Detail" class="w-full h-full object-cover">
                    
                    <button onclick="closeModal('modal-detail')" class="absolute top-4 right-4 bg-black/50 hover:bg-black/80 text-white p-2 rounded-full backdrop-blur-sm transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6">
                    <h3 id="detail-title" class="text-3xl font-bold text-white mb-1"></h3>
                    <p id="detail-subtitle" class="text-sm font-semibold text-purple-400 mb-4"></p>
                    
                    <p id="detail-bio" class="text-sm text-gray-300 leading-relaxed whitespace-pre-wrap"></p>
                </div>
            </div>
        </div>

        <script>
            // Service Worker Registration
            const isLocalHost = ['localhost', '127.0.0.1', '::1'].includes(location.hostname);
            const canUsePwaFeatures = window.isSecureContext || location.protocol === 'https:' || isLocalHost;

            if ('serviceWorker' in navigator && canUsePwaFeatures) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('./service-worker.js?v=20260328b', { scope: './' })
                        .then(reg => {
                            console.log('SW Registered!');
                            reg.update();
                        })
                        .catch(err => console.log('SW Register Failed:', err));
                });
            } else if (!canUsePwaFeatures) {
                window.addEventListener('load', () => {
                    const msg = 'PWA install butuh HTTPS. Jika dibuka dari IP lokal HTTP di HP, tombol install tidak akan muncul.';
                    console.warn(msg);
                    if (typeof showToast === 'function') {
                        showToast(msg, true);
                    }
                });
            }

            // PWA Install Prompt Logic
            let deferredPrompt;
            const installBtn = document.getElementById('install-pwa-btn');

            window.addEventListener('beforeinstallprompt', (e) => {
                // Prevent Chrome 67 and earlier from automatically showing the prompt
                e.preventDefault();
                // Stash the event so it can be triggered later.
                deferredPrompt = e;
                // Update UI to show the install button
                if (installBtn) {
                    installBtn.style.display = 'flex';
                }
                console.log('PWA: Ready to install');
            });

            if (installBtn) {
                installBtn.addEventListener('click', async () => {
                    if (!deferredPrompt) return;
                    // Show the install prompt
                    deferredPrompt.prompt();
                    // Wait for the user to respond to the prompt
                    const { outcome } = await deferredPrompt.userChoice;
                    console.log(`User response to the install prompt: ${outcome}`);
                    // We've used the prompt, and can't use it again, throw it away
                    deferredPrompt = null;
                    // Hide the button
                    installBtn.style.display = 'none';
                });
            }

            window.addEventListener('appinstalled', (evt) => {
                console.log('PWA: Installed');
                if (installBtn) installBtn.style.display = 'none';
            });
            
            window.addEventListener('load', () => {
                initAutoSync();
                
                // Cek jika saat load kita berada di tab anime, langsung gambar filternya
                const activeTab = document.querySelector('.tab-btn.active');
                if (activeTab && activeTab.id === 'tab-anime') {
                    setAnimeControlMode('sort');
                }
            });
        </script>

        <div id="crop-modal" class="modal-overlay">
            <div class="modal-box max-w-2xl p-6">
                <h3 class="text-xl font-bold text-white mb-4">Sesuaikan Komposisi ART</h3>
                <div class="max-h-[60vh] overflow-hidden rounded-xl bg-black/40 mb-6">
                    <img id="crop-preview-image" src="" style="display: block; max-width: 100%;">
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeCropModal()" class="flex-1 py-3 text-gray-400 border border-white/10 rounded-xl hover:bg-white/5 font-semibold transition-all">Batal</button>
                    <button type="button" onclick="executeCropAndUpload()" class="flex-1 btn-primary py-3 rounded-xl font-bold">Potong & Upload</button>
                </div>
            </div>
        </div>

        <div id="favorites-list-modal" class="modal-overlay" onclick="closeFavoritesModal(event)">
            <div class="modal-box p-0 overflow-y-auto max-h-[85vh] border-purple-500/20">
                
                <div class="sticky-header-fix">
                    <div class="fav-top-row">
                        <h3 id="fav-modal-title" class="text-xl font-bold text-white flex items-center gap-2"></h3>
                        <button onclick="forceCloseFavoritesModal()" 
                            class="bg-white/5 hover:bg-white/10 p-2 rounded-full text-gray-400 transition-all">✕</button>
                    </div>

                    <div class="fav-toolbar">
                        <form class="modern-switcher w-full sm:w-auto" id="switcher-fav">
                            <label>
                                <input type="radio" name="sw-fav" value="auto" checked onchange="setFavOrderMode('auto')">
                                <span class="text text-[10px] font-extrabold flex gap-1 whitespace-nowrap"><span class="sm:hidden">✨ BARU</span><span class="hidden sm:inline">✨ BARU DITAMBAH</span></span>
                            </label>
                            <label>
                                <input type="radio" name="sw-fav" value="manual" onchange="setFavOrderMode('manual')">
                                <span class="text text-[10px] font-extrabold flex gap-1 whitespace-nowrap"><span class="sm:hidden">🎮 KUSTOM</span><span class="hidden sm:inline">🎮 KUSTOM (DRAG)</span></span>
                            </label>
                            <div class="layer"></div>
                        </form>

                        <div id="manual-controls" class="hidden flex gap-2">
                            <button id="btn-edit-drag" onclick="toggleDragMode(true)" class="btn-edit px-4 py-1.5 text-[10px] font-extrabold">EDIT</button>
                            <button id="btn-save-drag" onclick="saveCustomOrder()" class="btn-primary hidden px-4 py-1.5 text-[10px] font-extrabold">SIMPAN</button>
                        </div>
                    </div>
                </div>
                
                <div id="fav-modal-content" class="grid grid-cols-2 sm:grid-cols-3 gap-4 p-6 pt-4 pb-10"></div>
            </div>
        </div>
        
        <div id="confirm-modal" class="modal-overlay" style="z-index: 600;">
            <div class="modal-box max-w-sm p-6" style="border: 1px solid rgba(239, 68, 68, 0.3);">
                <h3 id="confirm-title" class="text-xl font-bold text-white mb-2">Apakah kamu yakin?</h3>
                <p id="confirm-desc" class="text-sm text-gray-400 mb-6">
                    Tindakan ini tidak bisa dibatalkan.
                </p>
                <div class="flex gap-3 justify-end mt-4">
                    <button type="button" id="confirm-cancel-btn" class="px-4 py-2 rounded-xl text-gray-300 hover:bg-white/10 font-semibold transition-all text-sm">
                        Batal
                    </button>
                    <button type="button" id="confirm-ok-btn" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all text-sm shadow-lg shadow-red-600/20">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
        
        <!-- BOTTOM NAVIGATION BAR (Liquid Glass iOS) -->
        <div class="bottom-nav-bar" id="bottom-nav-bar" data-prevent-page-swipe>
            <form class="bottom-nav-switcher" id="bottom-nav-switcher" data-prev="1">
                <label>
                    <input type="radio" name="bnav" value="dashboard" checked>
                    <span class="bnav-icon">🏠</span>
                    <span class="bnav-text">Dashboard</span>
                </label>
                <label>
                    <input type="radio" name="bnav" value="anime">
                    <span class="bnav-icon">🎌</span>
                    <span class="bnav-text">Anime</span>
                </label>
                <label>
                    <input type="radio" name="bnav" value="waifu">
                    <span class="bnav-icon">💖</span>
                    <span class="bnav-text">Waifu</span>
                </label>
                <div class="bnav-layer"></div>
            </form>
        </div>

        <script>

        // --- Bottom Nav Sync ---
        (function() {
            const switcher = document.getElementById('bottom-nav-switcher');
            if (!switcher) return;
            const radios = switcher.querySelectorAll('input[name="bnav"]');
            const pageMap = { 'dashboard': 1, 'anime': 2, 'waifu': 3 };

            let isDragging = false; 

            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) showPage(this.value);
                });
            });

            // Override showPage untuk sinkronisasi pintar
            const _origShowPage = window.showPage;
            window.showPage = function(name) {
                const targetRadio = switcher.querySelector(`input[name="bnav"][value="${name}"]`);
                if (targetRadio && !targetRadio.checked) {
                    targetRadio.checked = true;
                }

                const navBar = document.getElementById('bottom-nav-bar');
                if (navBar) navBar.classList.remove('bnav-hidden');

                const tContainer = document.getElementById('toast-container');
                if (tContainer) {
                    tContainer.querySelectorAll('.toast-item.show').forEach(t => {
                        t.classList.remove('show');
                        t.classList.add('hide');
                        setTimeout(() => t.remove(), 400); 
                    });
                }

                _origShowPage(name);
                
                if (isDragging) {
                    switcher.setAttribute('data-prev', pageMap[name]);
                } else {
                    setTimeout(() => {
                        switcher.setAttribute('data-prev', pageMap[name]);
                    }, 550);
                }
            };

            // --- PERBAIKAN DRAG ABSOLUT UNTUK HP (REVISED: PERCENTAGE LOGIC) ---
            let hasMoved = false; 
            let wasDragging = false; 
            let startX = 0;
            let initialLeft = 0; 
            let barWidth = 0;
            let pillWidth = 0;
            let lastSelectedIndex = -1; 

            switcher.style.touchAction = 'none';

            function startDrag(clientX, clientY) {
                if (switcher.closest('.bnav-hidden')) return;
                
                const layer = switcher.querySelector('.bnav-layer');
                const layerRect = layer.getBoundingClientRect();
                
                // Area grab pill (toleransi luas agar mudah di-grab HP)
                if (
                    clientX < layerRect.left - 40 || 
                    clientX > layerRect.right + 40 || 
                    clientY < layerRect.top - 40 || 
                    clientY > layerRect.bottom + 40
                ) {
                    return; 
                }
                
                isDragging = true;
                hasMoved = false; 
                wasDragging = false;
                barWidth = switcher.offsetWidth;
                pillWidth = layer.offsetWidth;
                
                startX = clientX;

                const currentChecked = switcher.querySelector('input[name="bnav"]:checked');
                lastSelectedIndex = currentChecked ? pageMap[currentChecked.value] - 1 : 0;

                // Gunakan posisi slot yang sebenarnya berdasarkan index aktif,
                // bukan layer.offsetLeft yang bisa salah saat animasi sedang berjalan di HP
                const slotPositions = [
                    6,
                    barWidth * (1/3) + 2,
                    barWidth * (2/3) - 2
                ];
                initialLeft = slotPositions[lastSelectedIndex] ?? 6;
                
                switcher.style.setProperty('--drag-offset', initialLeft + 'px');
            }

            function moveDrag(clientX, preventDefaultFn) {
                if (!isDragging) return;
                
                const deltaX = clientX - startX;
                
                if (!hasMoved && Math.abs(deltaX) > 4) {
                    hasMoved = true;
                    switcher.classList.add('is-dragging');
                    // Cegah label menerima klik saat sedang geser
                    switcher.querySelectorAll('label').forEach(l => l.style.pointerEvents = 'none');
                }
                
                if (hasMoved) {
                    if (preventDefaultFn) preventDefaultFn(); 
                    
                    let leftPos = initialLeft + deltaX;
                    const minLeft = 6;
                    const maxLeft = barWidth - pillWidth - 6;
                    leftPos = Math.max(minLeft, Math.min(leftPos, maxLeft));
                    
                    switcher.style.setProperty('--drag-offset', leftPos + 'px');

                    // Liquid stretch effect
                    const stretch = 1 + Math.min(Math.abs(deltaX) / 250, 0.2);
                    switcher.style.setProperty('--drag-scale', stretch);
                    
                    // --- LOGIKA PERSENTASE (Lebih akurat di semua layar HP) ---
                    // Cek posisi tengah pill relatif terhadap lebar bar
                    const pillCenter = leftPos + (pillWidth / 2);
                    const percent = (pillCenter / barWidth) * 100;
                    
                    let closestIndex = 0;
                    if (percent > 33.33 && percent < 66.66) closestIndex = 1;
                    else if (percent >= 66.66) closestIndex = 2;
                    
                    if (closestIndex !== lastSelectedIndex) {
                        lastSelectedIndex = closestIndex;
                        const labels = Array.from(switcher.querySelectorAll('label'));
                        const targetRadio = labels[closestIndex].querySelector('input');
                        if (targetRadio && !targetRadio.checked) {
                            targetRadio.checked = true;
                            showPage(targetRadio.value); 
                            // Feedback getar ringan jika didukung HP
                            if (navigator.vibrate) navigator.vibrate(5);
                        }
                    }
                }
            }

            function endDrag() {
                if (!isDragging) return;
                
                if (hasMoved) {
                    switcher.classList.remove('is-dragging');
                    switcher.style.removeProperty('--drag-scale');
                    switcher.querySelectorAll('label').forEach(l => l.style.removeProperty('pointer-events'));
                    
                    setTimeout(() => {
                        if (!isDragging) switcher.style.removeProperty('--drag-offset');
                    }, 40);
                    
                    wasDragging = true; 
                    setTimeout(() => { wasDragging = false; }, 300); // Shield klik lebih lama
                }
                
                isDragging = false;
                hasMoved = false;
            }

            // Shield klik global untuk switcher dan anak-anaknya (label)
            switcher.addEventListener('click', function(e) {
                if (wasDragging) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            }, { capture: true });

            // Support Touch (HP)
            switcher.addEventListener('touchstart', function(e) {
                startDrag(e.touches[0].clientX, e.touches[0].clientY);
            }, { passive: true });

            window.addEventListener('touchmove', function(e) {
                if (isDragging) {
                    moveDrag(e.touches[0].clientX, () => {
                        if (e.cancelable) e.preventDefault();
                    });
                }
            }, { passive: false });

            window.addEventListener('touchend', endDrag);
            window.addEventListener('touchcancel', endDrag);

            // Support Mouse (Laptop)
            switcher.addEventListener('mousedown', function(e) {
                if (e.button !== 0) return; 
                startDrag(e.clientX, e.clientY);
            });

            window.addEventListener('mousemove', function(e) {
                if (isDragging) {
                    moveDrag(e.clientX, () => { e.preventDefault(); });
                }
            });

            window.addEventListener('mouseup', endDrag);
            window.addEventListener('mouseleave', function(e) {
                if (e.target === window.document) endDrag();
            });

            switcher.addEventListener('dragstart', e => e.preventDefault());
        })();
        </script>

        <script>
        // --- Bottom Nav: Hide saat Toast muncul (Mobile only) ---
        (function() {
            const bottomNav = document.getElementById('bottom-nav-bar');
            const toastContainer = document.getElementById('toast-container');
            if (!bottomNav || !toastContainer) return;

            let hideTimer = null;

            function hasActiveToasts() {
                // Jangan hitung toast yang sedang dalam proses menghilang (class .hide)
                return toastContainer.querySelectorAll('.toast-item:not(.hide)').length > 0;
            }

            function isMobile() {
                return window.innerWidth <= 767;
            }

            function hideBottomNav() {
                if (!isMobile()) return;
                clearTimeout(hideTimer);
                bottomNav.classList.add('bnav-hidden');
            }

            function showBottomNav() {
                if (!isMobile()) {
                    bottomNav.classList.remove('bnav-hidden');
                    return;
                }
                // Delay sedikit agar animasi toast hide selesai dulu
                clearTimeout(hideTimer);
                hideTimer = setTimeout(() => {
                    if (!hasActiveToasts()) {
                        bottomNav.classList.remove('bnav-hidden');
                    }
                }, 450);
            }

            // Observasi perubahan pada toast-container
            const observer = new MutationObserver(() => {
                if (hasActiveToasts()) {
                    hideBottomNav();
                } else {
                    showBottomNav();
                }
            });

            observer.observe(toastContainer, { childList: true, subtree: true });
        })();
        </script>

        <!-- Custom JS Select untuk Mobile (Menggantikan bawaan Android Chrome Select Picker) -->
        <script>
// [BEGIN] REPLACEMENT SCRIPT
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('select').forEach(select => {
                const wrapper = document.createElement('div');
                // KUNCI PERBAIKAN 1: Tambahkan z-index: 99999 agar MUSTAHIL tertembus oleh Card
                wrapper.className = 'custom-select-wrapper relative';
                wrapper.style.zIndex = '99999';
                
                select.parentNode.insertBefore(wrapper, select);
                wrapper.appendChild(select);
                select.style.display = 'none';

                const btn = document.createElement('div');
                btn.className = 'custom-select-btn flex items-center justify-between gap-2 px-4 py-2 bg-white/5 border border-purple-500/30 rounded-xl cursor-pointer hover:bg-white/10 transition-colors text-sm font-semibold text-purple-200';
                
                const textSpan = document.createElement('span');
                textSpan.textContent = select.options[select.selectedIndex]?.textContent || select.options[0].textContent;
                
                const chevron = document.createElement('span');
                chevron.innerHTML = `<svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>`;
                
                btn.appendChild(textSpan);
                btn.appendChild(chevron);
                wrapper.appendChild(btn);

                const menu = document.createElement('div');
                // KUNCI PERBAIKAN 2: Hanya panggil class utama agar CSS Liquid Glass kita bisa bekerja
                menu.className = 'custom-dropdown-menu !w-auto !min-w-full'; 
                
                // --- TAMBAHAN BARU: Paksa menu terbuka ke ATAS ---
                menu.style.top = 'auto';
                menu.style.bottom = 'calc(100% + 10px)';
                // --------------------------------------------------

                // WADAH KACA (Ini yang sebelumnya terlewat di script-mu)
                const glassInner = document.createElement('div');
                glassInner.className = 'glass-inner';
                menu.appendChild(glassInner);

                Array.from(select.options).forEach(opt => {
                    const optionDiv = document.createElement('div');
                    optionDiv.className = 'dropdown-item';
                    optionDiv.innerHTML = `<span>${opt.textContent}</span>`;
                    
                    if (opt.selected) optionDiv.classList.add('active');

                    optionDiv.addEventListener('click', (e) => {
                        e.stopPropagation();
                        // Update original select
                        select.value = opt.value;
                        textSpan.textContent = opt.textContent;
                        select.dispatchEvent(new Event('change'));
                        
                        // Tutup menu
                        menu.classList.remove('show');
                        chevron.style.transform = 'rotate(0deg)';
                        
                        // Update warna aktif
                        glassInner.querySelectorAll('.dropdown-item').forEach(d => d.classList.remove('active'));
                        optionDiv.classList.add('active');
                    });
                    glassInner.appendChild(optionDiv);
                });

                wrapper.appendChild(menu);

                // Efek buka tutup
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isShowing = menu.classList.contains('show');
                    
                    // Tutup semua menu lain dulu agar tidak saling tumpuk
                    document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.remove('show'));
                    document.querySelectorAll('.custom-select-btn svg').forEach(svg => svg.style.transform = 'rotate(0deg)');

                    if (!isShowing) {
                        menu.classList.add('show');
                        chevron.style.transform = 'rotate(180deg)';
                    }
                });

                // Sinkronisasi fungsi jika diubah lewat sistem
                const origSetter = Object.getOwnPropertyDescriptor(HTMLSelectElement.prototype, 'value').set;
                Object.defineProperty(select, 'value', {
                    set(val) {
                        origSetter.call(this, val);
                        const selectedOpt = Array.from(select.options).find(o => o.value === val);
                        if (selectedOpt) {
                            textSpan.textContent = selectedOpt.textContent;
                            glassInner.querySelectorAll('.dropdown-item').forEach(d => d.classList.remove('active'));
                            const activeDiv = Array.from(glassInner.children).find(d => d.textContent.trim() === selectedOpt.textContent.trim());
                            if(activeDiv) activeDiv.classList.add('active');
                        }
                    },
                    get() {
                        return Object.getOwnPropertyDescriptor(HTMLSelectElement.prototype, 'value').get.call(this);
                    }
                });
            });

            // Deteksi klik area luar untuk menutup menu
            window.addEventListener('click', function(e) {
                document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
                    const menu = wrapper.querySelector('.custom-dropdown-menu');
                    const btn = wrapper.querySelector('.custom-select-btn');
                    if (menu && btn && !menu.contains(e.target) && !btn.contains(e.target)) {
                        menu.classList.remove('show');
                        const chevron = btn.querySelector('svg');
                        if (chevron) chevron.style.transform = 'rotate(0deg)';
                    }
                });
            });

            
        });
        // [END] REPLACEMENT SCRIPT
        </script>        

    </body>
    </html>