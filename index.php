<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#7c3aed">
    <meta name="description" content="Anime & Waifu Vault - Koleksi Anime dan Waifu Favoritmu">
    <title>Anime & Waifu Vault ✨</title>
    <link rel="manifest" href="manifest.json">
    <link rel="apple-touch-icon" href="icons/icon-192.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Nunito:wght@400;600;700;800&display=swap');

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

        /* Waifu Background */
        #waifu-bg {
            position: fixed;
            right: -20px;
            bottom: 0;
            height: 85vh;
            max-width: 320px;
            opacity: 0.12;
            z-index: 0;
            pointer-events: none;
            transition: opacity 0.5s ease;
            filter: blur(1px);
            object-fit: contain;
        }
        #waifu-bg.active { opacity: 0.18; filter: blur(0px); }

        .glass {
            background: var(--glass);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
        }

        .glass-card {
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 16px;
            transition: all 0.3s ease;
        }
        .glass-card:hover { 
            background: rgba(255,255,255,0.1);
            border-color: rgba(167,139,250,0.4);
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(124,58,237,0.2);
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
            transition: all 0.2s ease;
            position: relative;
            font-family: 'Nunito', sans-serif;
            font-weight: 600;
        }
        .tab-btn.active {
            color: #c4b5fd;
        }
        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -1px; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, #7c3aed, #ec4899);
            border-radius: 2px;
        }

        /* Page sections */
        .page { display: none; }
        .page.active { display: block; }

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
        .btn-danger {
            background: rgba(239,68,68,0.15);
            color: #fca5a5;
            border: 1px solid rgba(239,68,68,0.25);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-danger:hover { background: rgba(239,68,68,0.3); }
        .btn-edit {
            background: rgba(167,139,250,0.15);
            color: #c4b5fd;
            border: 1px solid rgba(167,139,250,0.25);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-edit:hover { background: rgba(167,139,250,0.3); }

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

        /* Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(4px);
            z-index: 200;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .modal-overlay.open { display: flex; }
        .modal-box {
            background: #1a1030;
            border: 1px solid rgba(167,139,250,0.25);
            border-radius: 20px;
            max-width: 540px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            padding: 24px;
            position: relative;
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

        /* Stats cards */
        .stat-card {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px;
            padding: 16px;
            text-align: center;
        }

        @media (max-width: 640px) {
            .modal-box { padding: 16px; }
            #waifu-bg { display: none; }
        }
    </style>
</head>
<body>
    <!-- Waifu background -->
    <img id="waifu-bg" src="" alt="" />

    <!-- Navbar -->
    <nav id="navbar">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-2xl">🌸</span>
                <h1 class="font-bold text-lg text-purple-300" style="font-family: 'Nunito', sans-serif;">
                    Anime & Waifu Vault
                </h1>
            </div>
            <div class="flex items-center gap-1 text-sm">
                <button onclick="showPage('dashboard')" class="tab-btn active px-3 py-2 rounded-lg text-gray-300 hover:text-purple-300" id="tab-dashboard">Dashboard</button>
                <button onclick="showPage('anime')" class="tab-btn px-3 py-2 rounded-lg text-gray-300 hover:text-purple-300" id="tab-anime">Anime</button>
                <button onclick="showPage('waifu')" class="tab-btn px-3 py-2 rounded-lg text-gray-300 hover:text-purple-300" id="tab-waifu">Waifu</button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="relative z-10 max-w-6xl mx-auto px-4 pt-20 pb-10">

        <!-- DASHBOARD PAGE -->
        <div id="page-dashboard" class="page active">
            <div class="py-6">
                <h2 class="text-3xl font-bold text-purple-200 mb-1" style="font-family: 'Nunito', sans-serif;">Ohayou! 👋</h2>
                <p class="text-purple-400/70 text-sm">Selamat datang di vaultmu yang personal dan aesthetic.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8" id="stats-grid">
                <div class="stat-card">
                    <div class="text-3xl font-bold text-purple-300" id="stat-total">0</div>
                    <div class="text-xs text-gray-400 mt-1">Total Anime</div>
                </div>
                <div class="stat-card">
                    <div class="text-3xl font-bold text-green-400" id="stat-completed">0</div>
                    <div class="text-xs text-gray-400 mt-1">Completed</div>
                </div>
                <div class="stat-card">
                    <div class="text-3xl font-bold text-blue-400" id="stat-watching">0</div>
                    <div class="text-xs text-gray-400 mt-1">Watching</div>
                </div>
                <div class="stat-card">
                    <div class="text-3xl font-bold text-pink-400" id="stat-waifus">0</div>
                    <div class="text-xs text-gray-400 mt-1">Waifus</div>
                </div>
            </div>

            <div class="flex flex-col gap-8 mb-8">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-pink-300 uppercase tracking-wider">💕 Waifu Favorit</h3>
                        <div class="flex items-center gap-3">
                            <button onclick="showPage('waifu')" id="btn-more-waifu" class="text-[10px] text-pink-400 hover:underline hidden">Lihat Semua Favorit →</button>
                            <button onclick="showPage('waifu')" class="text-[10px] text-pink-400 hover:underline">Koleksi Lengkap →</button>
                        </div>
                    </div>
                    <div id="fav-waifu-list-dashboard" class="grid grid-cols-2 md:grid-cols-4 gap-3"></div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-purple-300 uppercase tracking-wider">⭐ Anime Favorit</h3>
                        <div class="flex items-center gap-3">
                            <button onclick="showPage('anime')" id="btn-more-anime" class="text-[10px] text-purple-400 hover:underline hidden">Lihat Semua Favorit →</button>
                            <button onclick="showPage('anime')" class="text-[10px] text-purple-400 hover:underline">Koleksi Lengkap →</button>
                        </div>
                    </div>
                    <div id="fav-anime-list-dashboard" class="grid grid-cols-2 md:grid-cols-4 gap-3"></div>
                </div>
            </div>

            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-purple-200" style="font-family: 'Nunito', sans-serif;">Anime Terbaru</h3>
                <button onclick="showPage('anime')" class="text-xs text-purple-400 hover:text-purple-300">Lihat semua →</button>
            </div>
            <div id="recent-anime-list" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3"></div>
        </div>

        <!-- ANIME PAGE -->
        <div id="page-anime" class="page">
            <div class="py-6 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-purple-200" style="font-family: 'Nunito', sans-serif;">Koleksi Anime 🎌</h2>
                    <p class="text-xs text-purple-400/60 mt-1">Kelola dan lacak anime favoritmu</p>
                </div>
                <button onclick="openAnimeModal()" class="btn-primary flex items-center gap-2 text-sm">
                    <span>+ Tambah</span>
                </button>
            </div>

            <!-- Filter -->
            <div class="flex gap-2 flex-wrap mb-5">
                <button onclick="filterAnime('all')" class="filter-btn active px-3 py-1.5 rounded-full text-xs font-semibold glass border border-purple-500/30 text-purple-300" data-filter="all">Semua</button>
                <button onclick="filterAnime('watching')" class="filter-btn px-3 py-1.5 rounded-full text-xs font-semibold glass border border-white/10 text-gray-400" data-filter="watching">Watching</button>
                <button onclick="filterAnime('completed')" class="filter-btn px-3 py-1.5 rounded-full text-xs font-semibold glass border border-white/10 text-gray-400" data-filter="completed">Completed</button>
                <button onclick="filterAnime('plan_to_watch')" class="filter-btn px-3 py-1.5 rounded-full text-xs font-semibold glass border border-white/10 text-gray-400" data-filter="plan_to_watch">Plan</button>
                <button onclick="filterAnime('on_hold')" class="filter-btn px-3 py-1.5 rounded-full text-xs font-semibold glass border border-white/10 text-gray-400" data-filter="on_hold">On Hold</button>
                <button onclick="filterAnime('dropped')" class="filter-btn px-3 py-1.5 rounded-full text-xs font-semibold glass border border-white/10 text-gray-400" data-filter="dropped">Dropped</button>
            </div>

            <div id="anime-list" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3"></div>
            <div id="anime-empty" class="text-center py-16 text-gray-500 hidden">
                <div class="text-5xl mb-3">📭</div>
                <p>Belum ada anime. Tambah sekarang!</p>
            </div>
        </div>

        <!-- WAIFU PAGE -->
        <div id="page-waifu" class="page">
            <div class="py-6 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-purple-200" style="font-family: 'Nunito', sans-serif;">Waifu List 💕</h2>
                    <p class="text-xs text-purple-400/60 mt-1">Koleksi dan atur waifu favoritmu</p>
                </div>
                <button onclick="openWaifuModal()" class="btn-primary flex items-center gap-2 text-sm">
                    <span>+ Tambah</span>
                </button>
            </div>

            <div id="waifu-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"></div>
            <div id="waifu-empty" class="text-center py-16 text-gray-500 hidden">
                <div class="text-5xl mb-3">💔</div>
                <p>Belum ada waifu. Tambah sekarang!</p>
            </div>
        </div>

    </main>

    <!-- ===== ANIME MODAL ===== -->
    <div id="anime-modal" class="modal-overlay" onclick="closeAnimeModal(event)">
        <div class="modal-box">
            <h3 class="text-xl font-bold text-purple-200 mb-5" style="font-family: 'Nunito', sans-serif;" id="anime-modal-title">Tambah Anime</h3>

            <!-- API Search -->
            <div class="mb-5 p-4 rounded-xl" style="background: rgba(124,58,237,0.1); border: 1px solid rgba(124,58,237,0.2);">
                <label class="label">🔍 Cari via Jikan API (opsional)</label>
                <div class="flex gap-2">
                    <input type="text" id="api-search" class="glass-input text-sm" placeholder="Cari judul anime...">
                    <button type="button" onclick="searchAnimeAPI()" class="btn-primary text-sm whitespace-nowrap px-3">Cari</button>
                </div>
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
                <label class="label">🔍 Cari Karakter via Jikan API (opsional)</label>
                <div class="flex gap-2">
                    <input type="text" id="api-search-waifu" class="glass-input text-sm" placeholder="Ketik nama waifu...">
                    <button type="button" onclick="searchWaifuAPI()" class="btn-primary text-sm whitespace-nowrap px-3" style="background: linear-gradient(135deg, #ec4899, #be185d);">Cari</button>
                </div>
                <div id="search-waifu-loading" class="text-xs text-pink-400 mt-2 hidden">⏳ Mencari...</div>
                <div id="search-waifu-results-list" class="mt-3 space-y-1 hidden max-h-60 overflow-y-auto"></div>
            </div>

            <form id="waifu-form" onsubmit="submitWaifu(event)" class="space-y-4">
                <input type="hidden" id="waifu-id" value="">
                <input type="hidden" id="waifu-pict-existing" value="">
                <input type="hidden" id="waifu-art-existing" value="">

                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="label">Nama Waifu *</label>
                        <input type="text" id="waifu-nama" class="glass-input" placeholder="Nama waifu..." required>
                    </div>
                    <div>
                        <label class="label">Asal Anime</label>
                        <input type="text" id="waifu-anime" class="glass-input" placeholder="Judul anime...">
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
    <div id="toast" class="glass px-4 py-3 rounded-xl border border-purple-500/30 text-sm font-semibold text-purple-200"></div>

    <!-- Service Worker Register -->
    <script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js').catch(() => {});
    }
    </script>

    <!-- Main JS -->
    <script src="search_api.js"></script>
    <script>
    // ============ STATE ============
    let allAnimes = [];
    let allWaifus = [];
    let currentFilter = 'all';
    let currentOfficialUrl = "";

    // ============ NAVIGATION ============
    function showPage(name) {
        document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('page-' + name).classList.add('active');
        document.getElementById('tab-' + name).classList.add('active');

        if (name === 'anime') loadAnimes();
        if (name === 'waifu') loadWaifus();
        if (name === 'dashboard') loadDashboard();
    }

    // ============ TOAST ============
    function showToast(msg, isError = false) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.style.borderColor = isError ? 'rgba(239,68,68,0.4)' : 'rgba(167,139,250,0.4)';
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
    }

    // ============ DASHBOARD ============
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

            // 1. Update Stats
            document.getElementById('stat-total').textContent = allAnimes.length;
            document.getElementById('stat-completed').textContent = allAnimes.filter(a => a.status === 'completed').length;
            document.getElementById('stat-watching').textContent = allAnimes.filter(a => a.status === 'watching').length;
            document.getElementById('stat-waifus').textContent = allWaifus.length;

            // 2. Render 4 Waifu Favorit
            const favWaifus = allWaifus.filter(w => w.is_fav == 1);
            const waifuContainer = document.getElementById('fav-waifu-list-dashboard');
            waifuContainer.innerHTML = favWaifus.length 
                ? favWaifus.slice(0, 4).map(w => renderWaifuCard(w)).join('') 
                : '<p class="text-gray-500 text-[10px] col-span-2 py-4 text-center">Belum ada favorit.</p>';

            // 3. Render 4 Anime Favorit
            const favAnimes = allAnimes.filter(a => a.is_fav == 1);
            const animeFavContainer = document.getElementById('fav-anime-list-dashboard');
            animeFavContainer.innerHTML = favAnimes.length 
                ? favAnimes.slice(0, 4).map(renderAnimeCard).join('') 
                : '<p class="text-gray-500 text-[10px] col-span-2 py-4 text-center">Belum ada favorit.</p>';

            // 4. Render 5 Anime Terbaru (Limit 5)
            const recent = allAnimes.slice(0, 5);
            document.getElementById('recent-anime-list').innerHTML = recent.map(renderAnimeCard).join('');

            // Logika sembunyi/tampil tombol "Lihat Semua"
            document.getElementById('btn-more-waifu')?.classList.toggle('hidden', favWaifus.length <= 4);
            document.getElementById('btn-more-anime')?.classList.toggle('hidden', favAnimes.length <= 4);
            // 5. Update Background dari Waifu Favorit Pertama
            if (favWaifus.length > 0) {
                const bg = document.getElementById('waifu-bg');
                bg.src = favWaifus[0].pict_path || '';
                bg.classList.add('active');
            }

        } catch (e) { console.error("Error load dashboard:", e); }
    }

    // ============ ANIME ============
    async function loadAnimes() {
        const res = await fetch('api.php?action=get_animes');
        const data = await res.json();
        allAnimes = data.data || [];
        renderAnimes();
    }

    function renderAnimes() {
        const filtered = currentFilter === 'all' ? allAnimes : allAnimes.filter(a => a.status === currentFilter);
        const container = document.getElementById('anime-list');
        const empty = document.getElementById('anime-empty');
        if (!filtered.length) {
            container.innerHTML = '';
            empty.classList.remove('hidden');
        } else {
            empty.classList.add('hidden');
            container.innerHTML = filtered.map(renderAnimeCard).join('');
        }
    }

    // Fungsi Render Kartu Anime (Tampilan Edit/Hapus Seperti Waifu)
    function renderAnimeCard(a) {
        const pct = a.eps_total > 0 ? Math.min(100, Math.round((a.eps_nonton / a.eps_total) * 100)) : 0;
        const img = a.gambar_path || `https://api.dicebear.com/7.x/shapes/svg?seed=${a.id}`;
        const statusLabels = { watching: 'Watching', completed: 'Completed', on_hold: 'On Hold', dropped: 'Dropped', plan_to_watch: 'Plan' };

        // TAMBAHKAN onclick="showAnimeDetail(${a.id})" dan cursor-pointer di sini
        return `
        <div class="glass-card anime-card p-4 cursor-pointer" onclick="showAnimeDetail(${a.id})"> 
            <div class="relative mb-3">
                <img src="${img}" alt="${escHtml(a.judul)}" class="w-full h-40 object-cover rounded-lg" onerror="this.src='https://api.dicebear.com/7.x/shapes/svg?seed=${a.id}'">
                <div class="absolute top-2 left-2">
                    <span class="status-badge status-${a.status}">${statusLabels[a.status]}</span>
                </div>
            </div>

            <div class="mb-3">
                <div class="flex items-center justify-between gap-2 mb-1">
                    <h4 class="text-sm font-bold text-white truncate flex-1" title="${escHtml(a.judul)}">${escHtml(a.judul)}</h4>
                    <button onclick="event.stopPropagation(); toggleAnimeFav(${a.id})" class="heart-btn ${a.is_fav == 1 ? 'fav' : ''} text-lg flex-shrink-0">
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

            <div class="flex gap-2">
                <button onclick="event.stopPropagation(); editAnime(${a.id})" class="btn-edit flex-1 text-center py-1.5 rounded-lg transition-all">Edit</button>
                <button onclick="event.stopPropagation(); deleteAnime(${a.id})" class="btn-danger flex-1 text-center py-1.5 rounded-lg transition-all">Hapus</button>
            </div>
        </div>`;
    }

    async function toggleAnimeFav(id) {
        await fetch(`api.php?action=toggle_anime_fav&id=${id}`);
        showToast('Status favorit anime diperbarui!');
        loadDashboard(); // Refresh tampilan dashboard
        if(document.getElementById('page-anime').classList.contains('active')) loadAnimes();
    }

    function filterAnime(status) {
        currentFilter = status;
        document.querySelectorAll('.filter-btn').forEach(b => {
            const isActive = b.dataset.filter === status;
            b.classList.toggle('active', isActive);
            b.classList.toggle('border-purple-500/30', isActive);
            b.classList.toggle('text-purple-300', isActive);
            b.classList.toggle('border-white/10', !isActive);
            b.classList.toggle('text-gray-400', !isActive);
        });
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
    }

    async function editAnime(id) {
        const anime = allAnimes.find(a => a.id == id);
        if (anime) openAnimeModal(anime);
    }

    async function deleteAnime(id) {
        if (!confirm('Hapus anime ini?')) return;
        await fetch(`api.php?action=delete_anime&id=${id}`);
        showToast('Anime dihapus!');
        loadAnimes(); // Update halaman anime
        loadDashboard(); // Update dashboard otomatis
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
            showToast(id ? 'Anime diperbarui!' : 'Anime ditambahkan!');
            closeAnimeModal();
            loadAnimes();
            loadDashboard();
        } else {
            showToast('Gagal menyimpan!', true);
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
        el.src = url;
        el.classList.remove('hidden');
    }

    // ============ WAIFU ============
    async function loadWaifus() {
        const res = await fetch('api.php?action=get_waifus');
        const data = await res.json();
        allWaifus = data.data || [];
        renderWaifus();
    }

    function renderWaifus() {
        const container = document.getElementById('waifu-list');
        const empty = document.getElementById('waifu-empty');
        if (!allWaifus.length) {
            container.innerHTML = '';
            empty.classList.remove('hidden');
        } else {
            empty.classList.add('hidden');
            container.innerHTML = allWaifus.map(renderWaifuCard).join('');
        }
    }

    function renderWaifuCard(w) {
    const defaultImg = `https://api.dicebear.com/7.x/adventurer/svg?seed=${encodeURIComponent(w.nama)}`;
    const pict = w.pict_path || w.official_pict_url || defaultImg;
    
    // Gunakan Fan Art sebagai latar kartu jika ada untuk kesan artistik
    const mainArt = w.art_path || pict;

    return `
    <div class="glass-card waifu-card overflow-hidden group cursor-pointer" onclick="showWaifuDetail(${w.id})">
        <div class="relative h-48 w-full overflow-hidden">
            <img src="${mainArt}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" alt="${escHtml(w.nama)}">
            <div class="absolute inset-0 bg-gradient-to-t from-[#1a1030] via-transparent to-transparent"></div>
            
            <img src="${pict}" class="absolute bottom-3 left-3 w-12 h-12 rounded-full border-2 border-pink-500 shadow-lg object-cover">
            
            <button onclick="event.stopPropagation(); toggleFav(${w.id}, ${w.is_fav})" class="absolute top-3 right-3 heart-btn ${w.is_fav ? 'fav' : ''} bg-black/40 p-1.5 rounded-full backdrop-blur-md">
                ${w.is_fav ? '❤️' : '🤍'}
            </button>
        </div>

        <div class="p-4">
            <div class="mb-3">
                <h4 class="font-bold text-white text-lg leading-tight truncate">${escHtml(w.nama)}</h4>
                <p class="text-xs text-purple-300/70 italic">${escHtml(w.anime_asal || 'Unknown Origin')}</p>
            </div>
            
            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
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
            showToast("Status favorit diperbarui!");
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
                el.src = currentPict; 
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
        if (!confirm('Hapus waifu ini?')) return;
        await fetch(`api.php?action=delete_waifu&id=${id}`);
        showToast('Waifu dihapus!');
        loadWaifus(); // Update halaman waifu
        loadDashboard(); // Update dashboard otomatis
    }

    async function submitWaifu(e) {
        e.preventDefault();
        const id = document.getElementById('waifu-id').value;
        const action = id ? 'update_waifu' : 'add_waifu';

        const fd = new FormData();
        if (id) fd.append('id', id);
        fd.append('nama', document.getElementById('waifu-nama').value);
        fd.append('anime_asal', document.getElementById('waifu-anime').value);
        fd.append('umur', document.getElementById('waifu-umur').value);
        fd.append('bio', document.getElementById('waifu-bio').value);
        fd.append('pict_existing', document.getElementById('waifu-pict-existing').value);
        if (document.getElementById('waifu-is-fav').checked) fd.append('is_fav', '1');

        const pictFile = document.getElementById('waifu-pict-file');
        if (pictFile.files[0]) fd.append('pict', pictFile.files[0]);

        const res = await fetch(`api.php?action=${action}`, { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
            showToast(id ? 'Waifu diperbarui!' : 'Waifu ditambahkan! 💕');
            closeWaifuModal();
            loadWaifus();
            loadDashboard(); // Update tampilan depan
        } else {
            showToast('Gagal menyimpan!', true);
        }
    }

    // Fungsi untuk mengembalikan foto profil ke link asli API
    function revertToOfficial() {
        if (!currentOfficialUrl) return;
        document.getElementById('waifu-pict-existing').value = currentOfficialUrl;
        document.getElementById('waifu-pict-preview').src = currentOfficialUrl;
        showToast("Kembali ke foto Official!");
    }

    // Menampilkan list foto di dalam modal edit
    function renderModalGallery(gallery) {
        const container = document.getElementById('modal-gallery-list');
        container.innerHTML = gallery.map(img => `
            <div class="relative group aspect-square">
                <img src="${img.image_path}" class="w-full h-full object-cover rounded-lg border border-white/10">
                <button type="button" onclick="deleteGalleryItem(${img.id})" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 text-[10px] flex items-center justify-center">✕</button>
            </div>
        `).join('');
    }

    // Upload ART baru ke galeri tanpa limit
    async function uploadToGallery() {
        const waifuId = document.getElementById('waifu-id').value;
        const fileInput = document.getElementById('add-art-file');
        if (!fileInput.files[0] || !waifuId) return;

        const fd = new FormData();
        fd.append('waifu_id', waifuId);
        fd.append('art', fileInput.files[0]);

        const res = await fetch('api.php?action=add_gallery_item', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
            showToast("Art baru ditambahkan!");
            editWaifu(waifuId); // Refresh modal galeri
        }
    }

    // Hapus foto art
    async function deleteGalleryItem(gid) {
        if (!confirm('Hapus foto art ini?')) return;
        const waifuId = document.getElementById('waifu-id').value;
        await fetch(`api.php?action=delete_gallery_item&id=${gid}`);
        editWaifu(waifuId);
    }

    function previewWaifuPict(input) {
        if (input.files[0]) {
            const el = document.getElementById('waifu-pict-preview');
            el.src = URL.createObjectURL(input.files[0]);
            el.classList.remove('hidden');
        }
    }

    // ============ LIGHTBOX ============
    function openLightbox(src) {
        document.getElementById('lightbox-img').src = src;
        document.getElementById('lightbox').classList.add('open');
    }

    // ============ UTILS ============
    function escHtml(str) {
        if (!str) return '';
        return str.toString().replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // ============ INIT ============
    loadDashboard();
    document.addEventListener('DOMContentLoaded', () => {
        const animeInput = document.getElementById('api-search');
        if (animeInput) {
            animeInput.addEventListener('input', () => {
                clearTimeout(animeTimer);
                // PERBAIKAN: Waktu dikurangi ke 300ms agar terasa lebih "synchronous"
                animeTimer = setTimeout(() => {
                    searchAnimeAPI();
                }, 300); 
            });
        }

        const waifuInput = document.getElementById('api-search-waifu');
        if (waifuInput) {
            waifuInput.addEventListener('input', () => {
                clearTimeout(waifuTimer);
                // PERBAIKAN: Waktu dikurangi ke 300ms
                waifuTimer = setTimeout(() => {
                    searchWaifuAPI();
                }, 300);
            });
        }
    });
        // Fungsi Detail Anime
    // Fungsi Detail Anime: Sekarang menggunakan Fetch agar stabil di semua halaman
    async function showAnimeDetail(id) {
        const res = await fetch(`api.php?action=get_anime_details&id=${id}`);
        const a = await res.json();
        if (!a) return;

        const img = a.gambar_path || `https://api.dicebear.com/7.x/shapes/svg?seed=${a.id}`;
        const pct = a.eps_total > 0 ? Math.min(100, Math.round((a.eps_nonton / a.eps_total) * 100)) : 0;
        const labels = { watching: 'Watching', completed: 'Completed', on_hold: 'On Hold', dropped: 'Dropped', plan_to_watch: 'Plan' };

        document.getElementById('det-anime-img').src = img;
        document.getElementById('det-anime-judul').textContent = a.judul;
        document.getElementById('det-anime-status').textContent = labels[a.status];
        
        // Tampilkan Genre di popup
        document.getElementById('det-anime-genres').textContent = a.genres || 'No genres listed';
        
        document.getElementById('det-anime-status').className = `status-badge status-${a.status} mb-3 inline-block`;
        document.getElementById('det-anime-progress').style.width = pct + '%';
        document.getElementById('det-anime-eps').textContent = `${a.eps_nonton} / ${a.eps_total || '?'} Episode (${pct}%)`;
        
        document.getElementById('detail-modal-anime').classList.add('open');
    }

    // Fungsi Detail Waifu
    async function showWaifuDetail(id) {
        const res = await fetch(`api.php?action=get_waifu_details&id=${id}`);
        const w = await res.json();
        
        const pict = w.pict_path || w.official_pict_url || 'https://api.dicebear.com/7.x/adventurer/svg?seed=' + w.nama;
        document.getElementById('det-waifu-banner').style.backgroundImage = `url(${pict})`;
        document.getElementById('det-waifu-pict').src = pict;
        document.getElementById('det-waifu-nama').textContent = w.nama;
        document.getElementById('det-waifu-bio').textContent = w.bio || 'No description.';
        
        // Tampilkan semua koleksi foto ART
        const galleryContainer = document.querySelector('#detail-modal-waifu .grid');
        galleryContainer.className = "grid grid-cols-2 sm:grid-cols-3 gap-2 mt-4";
        galleryContainer.innerHTML = (w.gallery || []).map(img => `
            <img src="${img.image_path}" class="w-full h-24 object-cover rounded-xl cursor-zoom-in" onclick="openLightbox('${img.image_path}')">
        `).join('');

        document.getElementById('detail-modal-waifu').classList.add('open');
    }

    // Handler klik luar modal untuk menutup
    function closeDetailAnime(e) { if (e.target.id === 'detail-modal-anime') e.target.classList.remove('open'); }
    function closeDetailWaifu(e) { if (e.target.id === 'detail-modal-waifu') e.target.classList.remove('open'); }

    
    </script>

        <div id="detail-modal-anime" class="modal-overlay" onclick="closeDetailAnime(event)">
            <div class="modal-box p-0 overflow-hidden max-w-md">
                <div class="relative">
                    <img id="det-anime-img" src="" class="w-full h-64 object-contain bg-black/20">
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
        <div class="modal-box p-0 overflow-hidden">
            <div class="relative h-48 bg-purple-900/20">
                <div id="det-waifu-banner" class="absolute inset-0 opacity-30 bg-cover bg-center blur-sm"></div>
                <img id="det-waifu-pict" src="" class="absolute -bottom-10 left-6 w-32 h-32 rounded-full border-4 border-[#1a1030] object-cover shadow-xl">
                <button onclick="document.getElementById('detail-modal-waifu').classList.remove('open')" class="absolute top-4 right-4 bg-black/50 p-2 rounded-full text-white">✕</button>
            </div>
            <div class="p-6 pt-12">
                <div class="flex items-center justify-between mb-2">
                    <h3 id="det-waifu-nama" class="text-2xl font-bold text-white"></h3>
                    <span id="det-waifu-umur" class="text-pink-400 font-semibold"></span>
                </div>
                <p id="det-waifu-anime" class="text-purple-300 text-sm mb-4"></p>
                <p id="det-waifu-bio" class="text-gray-400 text-sm leading-relaxed mb-6 italic"></p>
                
                <div class="grid grid-cols-2 gap-4">
                    <div id="det-waifu-off-box">
                        <p class="text-[10px] uppercase text-gray-500 mb-2 tracking-widest">Official Art</p>
                        <img id="det-waifu-off-img" src="" class="w-full h-32 object-cover rounded-xl border border-white/10 cursor-zoom-in" onclick="openLightbox(this.src)">
                    </div>
                    <div id="det-waifu-fan-box">
                        <p class="text-[10px] uppercase text-gray-500 mb-2 tracking-widest">Fan Art</p>
                        <img id="det-waifu-fan-img" src="" class="w-full h-32 object-cover rounded-xl border border-white/10 cursor-zoom-in" onclick="openLightbox(this.src)">
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
