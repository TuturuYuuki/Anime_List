// search_api.js  Jikan API v4 (Versi Stabil & Akurat)
const JIKAN_BASE = 'https://api.jikan.moe/v4';
const LOCAL_API_BASE = 'api.php';
let animeTimer = null;
let waifuTimer = null;
let animeSearchController = null;
let waifuSearchController = null;
let animeSearchSeq = 0;
let waifuSearchSeq = 0;
const SEARCH_CACHE_TTL = 10 * 60 * 1000;
const SEARCH_CACHE_MAX_ENTRIES = 60;
const ANIME_CACHE_SESSION_KEY = 'vault_anime_search_cache_v1';
const WAIFU_CACHE_SESSION_KEY = 'vault_waifu_search_cache_v1';
const animeSearchCache = new Map();
const waifuSearchCache = new Map();
let lastAnimeQueryKey = '';
let lastWaifuQueryKey = '';
let prevAnimeQueryKey = '';
let prevWaifuQueryKey = '';

// Variabel penampung sementara agar data sinkron
let waifuSearchResults = [];
let animeSearchResults = [];

function getSearchImageUrl(url) {
    if (!url) return '';
    if (typeof window.resolveMediaUrl === 'function') {
        return window.resolveMediaUrl(url);
    }
    return url;
}

function restoreCacheFromSession(cache, storageKey) {
    try {
        const raw = sessionStorage.getItem(storageKey);
        if (!raw) return;

        const entries = JSON.parse(raw);
        if (!Array.isArray(entries)) return;

        const now = Date.now();
        entries.forEach((entry) => {
            if (!Array.isArray(entry) || entry.length !== 2) return;
            const key = entry[0];
            const payload = entry[1];
            if (!key || !payload || !Array.isArray(payload.data)) return;
            if ((now - Number(payload.time || 0)) > SEARCH_CACHE_TTL) return;

            cache.set(String(key), {
                time: Number(payload.time || now),
                data: payload.data
            });
        });
    } catch (_) {
        // Abaikan storage rusak agar pencarian tetap jalan.
    }
}

function persistCacheToSession(cache, storageKey) {
    try {
        const now = Date.now();
        const entries = Array.from(cache.entries())
            .filter(([, payload]) => payload && Array.isArray(payload.data) && ((now - Number(payload.time || 0)) <= SEARCH_CACHE_TTL))
            .sort((a, b) => Number(b[1].time || 0) - Number(a[1].time || 0))
            .slice(0, SEARCH_CACHE_MAX_ENTRIES);

        // Sinkronkan map memori agar tidak membesar terus.
        cache.clear();
        entries.forEach(([key, payload]) => cache.set(key, payload));
        sessionStorage.setItem(storageKey, JSON.stringify(entries));
    } catch (_) {
        // Abaikan error quota/private mode.
    }
}

function initSearchCaches() {
    restoreCacheFromSession(animeSearchCache, ANIME_CACHE_SESSION_KEY);
    restoreCacheFromSession(waifuSearchCache, WAIFU_CACHE_SESSION_KEY);
}

function getCachedSearch(cache, key) {
    const entry = cache.get(key);
    if (!entry) return null;
    if ((Date.now() - entry.time) > SEARCH_CACHE_TTL) {
        cache.delete(key);
        return null;
    }
    return entry.data;
}

function setCachedSearch(cache, key, data, storageKey = '') {
    cache.set(key, { time: Date.now(), data });
    if (storageKey) persistCacheToSession(cache, storageKey);
}

function warmThumbs(urls = []) {
    if (!Array.isArray(urls)) return;
    urls.forEach((src) => {
        if (!src) return;
        const img = new Image();
        img.decoding = 'async';
        img.src = src;
    });
}

function filterLocalResults(list = [], query = '', key = '') {
    const q = query.toLowerCase();
    if (!q || !key) return list;
    return list.filter(item => {
        const value = (item?.[key] || '').toString().toLowerCase();
        return value.includes(q);
    });
}

function getNearestCachedResults(cache, queryKey) {
    if (!queryKey) return null;

    const exact = getCachedSearch(cache, queryKey);
    if (exact) return exact;

    let bestKey = '';
    for (const key of cache.keys()) {
        if (!queryKey.startsWith(key) && !key.startsWith(queryKey)) continue;
        if (key.length > bestKey.length) bestKey = key;
    }

    if (!bestKey) return null;
    return getCachedSearch(cache, bestKey);
}

function renderSearchSkeleton(resultsEl, tone = 'purple') {
    const toneClass = tone === 'pink' ? 'search-skeleton-pink' : 'search-skeleton-purple';
    resultsEl.innerHTML = Array.from({ length: 4 }).map(() => `
        <div class="search-item search-skeleton-item ${toneClass}">
            <div class="search-skeleton-thumb"></div>
            <div class="search-skeleton-lines">
                <div class="search-skeleton-line"></div>
                <div class="search-skeleton-line short"></div>
                <div class="search-skeleton-line tiny"></div>
            </div>
        </div>
    `).join('');
    resultsEl.classList.remove('hidden');
}

function handleSearchThumbLoad(img) {
    if (!img) return;
    img.classList.add('loaded');
    const wrap = img.parentElement;
    if (wrap) wrap.classList.add('loaded');
}

function handleSearchThumbError(img, fallback = '') {
    if (!img) return;
    if (fallback && img.dataset.fallbackApplied !== '1') {
        img.dataset.fallbackApplied = '1';
        img.src = fallback;
        return;
    }
    const wrap = img.parentElement;
    if (wrap) wrap.classList.remove('loaded');
}

function renderAnimeSearchResults(resultsEl, list) {
    resultsEl.innerHTML = list.map((a, index) => {
        const genres = a.genres ? a.genres.map(g => g.name).join(', ') : '';
        const thumb = getSearchImageUrl(a.images?.jpg?.image_url || '');
        const fallback = 'https://api.dicebear.com/7.x/shapes/svg?seed=anime-search';
        return `
        <div class="search-item" onclick="selectAnimeFromAPI(${index})">
            <div class="search-thumb-wrap anime">
                <img src="${thumb}" class="search-thumb-img anime" loading="eager" decoding="async" fetchpriority="high" onload="handleSearchThumbLoad(this)" onerror="handleSearchThumbError(this, '${fallback}')">
                <div class="search-thumb-loading">
                    <div class="h-4 w-4 rounded-full border-2 border-purple-300/25 border-t-purple-300 animate-spin"></div>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white truncate">${a.title}</p>
                <p class="text-[10px] text-purple-400 truncate">${genres}</p>
                <p class="text-[10px] text-gray-400">${a.type}  ${a.episodes || '?'} Eps</p>
            </div>
        </div>`;
    }).join('');
    resultsEl.classList.remove('hidden');
}

function renderWaifuSearchResults(resultsEl, list) {
    resultsEl.innerHTML = list.map((c, index) => {
        const thumb = getSearchImageUrl(c.images?.jpg?.image_url || '');
        const fallback = 'https://api.dicebear.com/7.x/adventurer/svg?seed=waifu-search';
        return `
        <div class="search-item" onclick="selectWaifuFromAPI(${index})">
            <div class="search-thumb-wrap waifu">
                <img src="${thumb}" class="search-thumb-img waifu" loading="eager" decoding="async" fetchpriority="high" onload="handleSearchThumbLoad(this)" onerror="handleSearchThumbError(this, '${fallback}')">
                <div class="search-thumb-loading">
                    <div class="h-4 w-4 rounded-full border-2 border-pink-300/25 border-t-pink-300 animate-spin"></div>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white truncate">${c.name}</p>
                <p class="text-[10px] text-gray-400">Pilih karakter</p>
            </div>
        </div>`;
    }).join('');
    resultsEl.classList.remove('hidden');
}

/**
 * Fungsi pembantu untuk mengurutkan hasil pencarian.
 * Prioritas: 1. Nama yang persis sama, 2. Nama yang diawali kata kunci.
 */
function sortResults(results, query, key) {
    const q = query.toLowerCase();
    return results.sort((a, b) => {
        const valA = a[key].toLowerCase();
        const valB = b[key].toLowerCase();

        if (valA === q && valB !== q) return -1;
        if (valB === q && valA !== q) return 1;
        if (valA.startsWith(q) && !valB.startsWith(q)) return -1;
        if (valB.startsWith(q) && !valA.startsWith(q)) return 1;
        return 0;
    });
}

async function searchAnimeAPI() {
    const query = document.getElementById('api-search').value.trim();
    const queryKey = query.toLowerCase();
    const resultsEl = document.getElementById('search-results-list');
    const loadingEl = document.getElementById('search-loading');

    if (query.length < 1) {
        clearTimeout(animeTimer);
        if (animeSearchController) animeSearchController.abort();
        resultsEl.classList.add('hidden');
        lastAnimeQueryKey = '';
        prevAnimeQueryKey = '';
        animeSearchResults = [];
        return;
    }

    const shouldFilterFromPrevious = prevAnimeQueryKey
        && (queryKey.startsWith(prevAnimeQueryKey) || prevAnimeQueryKey.startsWith(queryKey));

    const localBase = (shouldFilterFromPrevious && animeSearchResults.length ? animeSearchResults : null)
        || getNearestCachedResults(animeSearchCache, queryKey)
        || animeSearchResults;

    if (localBase && localBase.length) {
        animeSearchResults = sortResults(filterLocalResults(localBase, queryKey, 'title'), query, 'title');
        warmThumbs(animeSearchResults.slice(0, 10).map(a => getSearchImageUrl(a.images?.jpg?.image_url || '')));
        renderAnimeSearchResults(resultsEl, animeSearchResults);
    } else {
        renderSearchSkeleton(resultsEl, 'purple');
    }

    lastAnimeQueryKey = queryKey;
    prevAnimeQueryKey = queryKey;

    const hasExactCache = !!getCachedSearch(animeSearchCache, queryKey);
    const debounceMs = hasExactCache ? 150 : 300;

    clearTimeout(animeTimer);
    animeTimer = setTimeout(async () => {
        if (queryKey !== lastAnimeQueryKey) return;

        if (animeSearchController) animeSearchController.abort();
        animeSearchController = new AbortController();
        const seq = ++animeSearchSeq;

        if (loadingEl) loadingEl.classList.remove('hidden');

        try {
            const res = await fetch(`${LOCAL_API_BASE}?action=search_anime&q=${encodeURIComponent(query)}`, {
                cache: 'no-store',
                signal: animeSearchController.signal
            });
            if (seq !== animeSearchSeq) return;

            const json = await res.json();
            if (!res.ok || !json.success) {
                throw new Error(json.message || 'Gagal mengambil hasil pencarian anime');
            }

            const rawData = json.data || [];
            animeSearchResults = sortResults(rawData, query, 'title');
            setCachedSearch(animeSearchCache, queryKey, animeSearchResults, ANIME_CACHE_SESSION_KEY);
            warmThumbs(animeSearchResults.slice(0, 8).map(a => getSearchImageUrl(a.images?.jpg?.image_url || '')));
            renderAnimeSearchResults(resultsEl, animeSearchResults);
        } catch (e) {
            if (e.name === 'AbortError') return;
            console.error('Anime Search Error:', e);
            resultsEl.innerHTML = `<div class="text-xs text-red-300 py-2">${e.message || 'Gagal mencari anime'}</div>`;
            resultsEl.classList.remove('hidden');
        } finally {
            if (loadingEl) loadingEl.classList.add('hidden');
        }
    }, debounceMs);
}

async function searchWaifuAPI() {
    const query = document.getElementById('api-search-waifu').value.trim();
    const queryKey = query.toLowerCase();
    const resultsEl = document.getElementById('search-waifu-results-list');
    const loadingEl = document.getElementById('search-waifu-loading');

    if (query.length < 1) {
        clearTimeout(waifuTimer);
        if (waifuSearchController) waifuSearchController.abort();
        resultsEl.classList.add('hidden');
        lastWaifuQueryKey = '';
        prevWaifuQueryKey = '';
        waifuSearchResults = [];
        return;
    }

    const shouldFilterFromPrevious = prevWaifuQueryKey
        && (queryKey.startsWith(prevWaifuQueryKey) || prevWaifuQueryKey.startsWith(queryKey));

    const localBase = (shouldFilterFromPrevious && waifuSearchResults.length ? waifuSearchResults : null)
        || getNearestCachedResults(waifuSearchCache, queryKey)
        || waifuSearchResults;

    if (localBase && localBase.length) {
        waifuSearchResults = sortResults(filterLocalResults(localBase, queryKey, 'name'), query, 'name');
        warmThumbs(waifuSearchResults.slice(0, 10).map(c => getSearchImageUrl(c.images?.jpg?.image_url || '')));
        renderWaifuSearchResults(resultsEl, waifuSearchResults);
    } else {
        renderSearchSkeleton(resultsEl, 'pink');
    }

    lastWaifuQueryKey = queryKey;
    prevWaifuQueryKey = queryKey;

    const hasExactCache = !!getCachedSearch(waifuSearchCache, queryKey);
    const debounceMs = hasExactCache ? 150 : 300;

    clearTimeout(waifuTimer);
    waifuTimer = setTimeout(async () => {
        if (queryKey !== lastWaifuQueryKey) return;

        if (waifuSearchController) waifuSearchController.abort();
        waifuSearchController = new AbortController();
        const seq = ++waifuSearchSeq;

        if (loadingEl) loadingEl.classList.remove('hidden');

        try {
            const res = await fetch(`${LOCAL_API_BASE}?action=search_waifu&q=${encodeURIComponent(query)}`, {
                cache: 'no-store',
                signal: waifuSearchController.signal
            });
            if (seq !== waifuSearchSeq) return;

            const json = await res.json();
            if (!res.ok || !json.success) {
                throw new Error(json.message || 'Gagal mengambil hasil pencarian waifu');
            }

            const rawData = json.data || [];
            waifuSearchResults = sortResults(rawData, query, 'name');
            setCachedSearch(waifuSearchCache, queryKey, waifuSearchResults, WAIFU_CACHE_SESSION_KEY);
            warmThumbs(waifuSearchResults.slice(0, 8).map(c => getSearchImageUrl(c.images?.jpg?.image_url || '')));
            renderWaifuSearchResults(resultsEl, waifuSearchResults);
        } catch (e) {
            if (e.name === 'AbortError') return;
            console.error('Waifu Search Error:', e);
            resultsEl.innerHTML = `<div class="text-xs text-red-300 py-2">${e.message || 'Gagal mencari waifu/karakter'}</div>`;
            resultsEl.classList.remove('hidden');
        } finally {
            if (loadingEl) loadingEl.classList.add('hidden');
        }
    }, debounceMs);
}

function selectAnimeFromAPI(index) {
    const data = animeSearchResults[index];
    if (!data) return;

    document.getElementById('anime-judul').value = data.title;
    document.getElementById('anime-eps-total').value = data.episodes || 0;
    document.getElementById('anime-gambar-url').value = data.images.jpg.image_url;

    const genres = data.genres ? data.genres.map(g => g.name).join(', ') : '';
    document.getElementById('anime-genres').value = genres;

    if (typeof window.showAnimeImgPreview === 'function') {
        window.showAnimeImgPreview(data.images.jpg.image_url);
    }

    document.getElementById('search-results-list').classList.add('hidden');
}

/* search_api.js  Update selectWaifuFromAPI */
async function selectWaifuFromAPI(index) {
    const charSummary = waifuSearchResults[index];
    if (!charSummary) return;

    // Isi kolom secara langsung dari data summary (tanpa tunggu fetch) — sama seperti anime
    document.getElementById('waifu-nama').value = charSummary.name;
    document.getElementById('waifu-anime').value = '';
    document.getElementById('waifu-bio').value = '';

    const imgUrl = charSummary.images?.jpg?.image_url || '';
    document.getElementById('waifu-pict-existing').value = imgUrl;
    const prev = document.getElementById('waifu-pict-preview');
    if (prev && imgUrl) {
        const safeImg = (typeof window.resolveMediaUrl === 'function')
            ? window.resolveMediaUrl(imgUrl)
            : imgUrl;
        prev.src = safeImg;
        prev.classList.remove('hidden');
    }

    // Tutup hasil pencarian & kosongkan kolom search langsung
    document.getElementById('search-waifu-results-list').classList.add('hidden');
    const searchInput = document.getElementById('api-search-waifu');
    if (searchInput) searchInput.value = '';
    lastWaifuQueryKey = '';
    prevWaifuQueryKey = '';
    waifuSearchResults = [];

    // Fetch detail (bio & anime asal) di background — tidak blokir UI
    const charId = charSummary.mal_id;
    const loadingEl = document.getElementById('search-waifu-loading');
    if (loadingEl) loadingEl.classList.remove('hidden');

    try {
        const res = await fetch(`${LOCAL_API_BASE}?action=search_character_full&id=${charId}`, { cache: 'no-store' });
        const json = await res.json();
        if (!res.ok || !json.success || !json.data) {
            throw new Error(json.message || 'Gagal mengambil detail karakter');
        }
        const data = json.data;

        // Cek Anime, jika kosong cek Manga (banyak karakter game ada di entri Manga/VN)
        if (data.anime && data.anime.length > 0) {
            document.getElementById('waifu-anime').value = data.anime[0].anime.title;
        } else if (data.manga && data.manga.length > 0) {
            document.getElementById('waifu-anime').value = data.manga[0].manga.title;
        }

        // Bio
        if (data.about) {
            let cleanBio = data.about.replace(/\\r/g, '').replace(/\n\n+/g, '\n\n').replace(/\[Written by MAL Rewrite\]/g, '').trim();
            document.getElementById('waifu-bio').value = cleanBio;
        }

        // Perbarui gambar jika ada versi lebih lengkap dari detail
        if (data.images?.jpg?.image_url) {
            document.getElementById('waifu-pict-existing').value = data.images.jpg.image_url;
            if (prev) {
                const safeImg = (typeof window.resolveMediaUrl === 'function')
                    ? window.resolveMediaUrl(data.images.jpg.image_url)
                    : data.images.jpg.image_url;
                prev.src = safeImg;
                prev.classList.remove('hidden');
            }
        }

    } catch (e) {
        console.error('Gagal mengambil detail karakter:', e);
    } finally {
        if (loadingEl) loadingEl.classList.add('hidden');
    }
}

initSearchCaches();