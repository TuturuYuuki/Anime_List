// search_api.js — Jikan API v4 (Versi Stabil & Akurat)
const JIKAN_BASE = 'https://api.jikan.moe/v4';
let animeTimer = null;
let waifuTimer = null;

// Variabel penampung sementara agar data sinkron
let waifuSearchResults = [];
let animeSearchResults = [];

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
    const resultsEl = document.getElementById('search-results-list');
    const loadingEl = document.getElementById('search-loading');

    if (query.length < 2) {
        resultsEl.classList.add('hidden');
        return;
    }
    
    if (loadingEl) loadingEl.classList.remove('hidden');
    resultsEl.classList.add('hidden');
    
    try {
        const res = await fetch(`${JIKAN_BASE}/anime?q=${encodeURIComponent(query)}&limit=25`);
        
        // Cek jika terkena limit (Error 429)
        if (res.status === 429) {
            console.warn("API Rate Limited. Tunggu sebentar...");
            return;
        }

        const json = await res.json();
        let rawData = json.data || [];
        
        // Urutkan: Judul yang paling pas muncul pertama
        animeSearchResults = sortResults(rawData, query, 'title');
        
        resultsEl.innerHTML = animeSearchResults.map((a, index) => {
            const genres = a.genres ? a.genres.map(g => g.name).join(', ') : '';
            return `
            <div class="search-item" onclick="selectAnimeFromAPI(${index})">
                <img src="${a.images.jpg.image_url}" class="w-10 h-14 rounded object-cover">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate">${a.title}</p>
                    <p class="text-[10px] text-purple-400 truncate">${genres}</p>
                    <p class="text-[10px] text-gray-400">${a.type} • ${a.episodes || '?'} Eps</p>
                </div>
            </div>`;
        }).join('');
        
        resultsEl.classList.remove('hidden');
    } catch (e) { 
        console.error("Anime Search Error:", e); 
    } finally {
        if (loadingEl) loadingEl.classList.add('hidden');
    }
}

async function searchWaifuAPI() {
    const query = document.getElementById('api-search-waifu').value.trim();
    const resultsEl = document.getElementById('search-waifu-results-list');
    const loadingEl = document.getElementById('search-waifu-loading');

    if (query.length < 2) {
        resultsEl.classList.add('hidden');
        return;
    }
    
    if (loadingEl) loadingEl.classList.remove('hidden');
    resultsEl.classList.add('hidden');
    
    try {
        const res = await fetch(`${JIKAN_BASE}/characters?q=${encodeURIComponent(query)}&limit=25`);
        
        if (res.status === 429) return;

        const json = await res.json();
        let rawData = json.data || []; 
        
        // Urutkan: Nama karakter yang paling pas muncul pertama
        waifuSearchResults = sortResults(rawData, query, 'name');
        
        resultsEl.innerHTML = waifuSearchResults.map((c, index) => `
            <div class="search-item" onclick="selectWaifuFromAPI(${index})">
                <img src="${c.images.jpg.image_url}" class="w-10 h-10 rounded-full object-cover">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate">${c.name}</p>
                    <p class="text-[10px] text-gray-400">Pilih karakter</p>
                </div>
            </div>`).join('');
            
        resultsEl.classList.remove('hidden');
    } catch (e) { 
        console.error("Waifu Search Error:", e); 
    } finally {
        if (loadingEl) loadingEl.classList.add('hidden');
    }
}

function selectAnimeFromAPI(index) {
    const data = animeSearchResults[index];
    if (!data) return;

    document.getElementById('anime-judul').value = data.title;
    document.getElementById('anime-eps-total').value = data.episodes || 0;
    document.getElementById('anime-gambar-url').value = data.images.jpg.image_url;
    
    const genres = data.genres ? data.genres.map(g => g.name).join(', ') : '';
    document.getElementById('anime-genres').value = genres;
    
    document.getElementById('search-results-list').classList.add('hidden');
}

async function selectWaifuFromAPI(index) {
    const charSummary = waifuSearchResults[index];
    if (!charSummary) return;

    const charId = charSummary.mal_id;
    const loadingEl = document.getElementById('search-waifu-loading');
    if (loadingEl) loadingEl.classList.remove('hidden');

    try {
        const res = await fetch(`${JIKAN_BASE}/characters/${charId}/full`);
        const json = await res.json();
        const data = json.data;

        document.getElementById('waifu-nama').value = data.name;
        
        if (data.anime && data.anime.length > 0) {
            document.getElementById('waifu-anime').value = data.anime[0].anime.title;
        } else {
            document.getElementById('waifu-anime').value = '';
        }
        
        if (data.about) {
            let cleanBio = data.about
                .replace(/\\r/g, "")
                .replace(/\n\n+/g, "\n\n")
                .replace(/\[Written by MAL Rewrite\]/g, "")
                .trim();
            document.getElementById('waifu-bio').value = cleanBio;
        }

        document.getElementById('waifu-pict-existing').value = data.images.jpg.image_url;
        const prev = document.getElementById('waifu-pict-preview');
        if (prev) {
            prev.src = data.images.jpg.image_url; 
            prev.classList.remove('hidden');
        }

    } catch (e) {
        console.error("Gagal mengambil detail karakter:", e);
        document.getElementById('waifu-nama').value = charSummary.name;
    } finally {
        if (loadingEl) loadingEl.classList.add('hidden');
        document.getElementById('search-waifu-results-list').classList.add('hidden');
    }
}