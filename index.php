<?php 
include 'config/database.php';

// --- LOGIKA HERO SECTION (TOP 1) ---
$hero_title = "Anime List";
$hero_synopsis = "Tunggu sebentar, sedang memuat data...";
$media_html = ""; 

$result_hero = mysqli_query($conn, "SELECT * FROM top_anime ORDER BY updated_at DESC LIMIT 1");
if ($result_hero && mysqli_num_rows($result_hero) > 0) {
    $hero_data = mysqli_fetch_assoc($result_hero);
    $hero_title = $hero_data['title'];
    $hero_synopsis = $hero_data['synopsis'];
    $db_media_url = $hero_data['trailer_url'];
    
    if (strpos($db_media_url, '.mp4') !== false) {
         $media_html = "<video autoplay loop muted playsinline class='hero-video'><source src='{$db_media_url}' type='video/mp4'></video>";
    } else {
         $media_html = "<img src='{$db_media_url}' alt='Background' class='hero-video'>";
    }
}

$api_hero_url = "https://api.jikan.moe/v4/top/anime?limit=1";
$response_hero = @file_get_contents($api_hero_url);

if ($response_hero !== FALSE) {
    $data_hero = json_decode($response_hero, true);
    if (!empty($data_hero['data'])) {
        $top_anime = $data_hero['data'][0];
        $mal_id = $top_anime['mal_id'];
        $title_api = mysqli_real_escape_string($conn, $top_anime['title']);
        $hero_title = $top_anime['title'];
        $hero_synopsis = substr($top_anime['synopsis'], 0, 150) . "...";
        $synopsis_api = mysqli_real_escape_string($conn, $hero_synopsis);
        
        $expected_video = "assets/videos/" . $mal_id . ".mp4";
        $expected_image = "assets/images/covers/" . $mal_id . ".jpg";
        
        if (file_exists($expected_video)) {
            $db_media_url = $expected_video;
            $media_html = "<video autoplay loop muted playsinline class='hero-video'><source src='{$db_media_url}' type='video/mp4'></video>";
        } elseif (file_exists($expected_image)) {
            $db_media_url = $expected_image;
            $media_html = "<img src='{$db_media_url}' alt='{$hero_title}' class='hero-video'>";
        } else {
            $db_media_url = $top_anime['trailer']['images']['maximum_image_url'] 
                            ?? $top_anime['trailer']['images']['large_image_url'] 
                            ?? $top_anime['images']['jpg']['large_image_url'];
            $media_html = "<img src='{$db_media_url}' alt='{$hero_title}' class='hero-video'>";
        }   
        
        mysqli_query($conn, "CALL SyncTopAnime($mal_id, '$title_api', '$synopsis_api', '$db_media_url')");
    }
}

if (empty($media_html)) $media_html = "<div class='hero-video' style='background-color: #1a1a1a;'></div>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>YAMENIME - Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time(); ?>">
</head>
<body>

    <nav class="navbar">
        <div class="nav-left">
            <div class="logo">やめにめ</div>
            <ul class="nav-links">
                <li><a href="#" class="active">Home</a></li>
                <li><a href="#">Catalog</a></li>
                <li><a href="#">News</a></li>
                <li><a href="#">Collections</a></li>
            </ul>
        </div>
        <div class="nav-right">
            <div class="search-bar">
                <input type="text" placeholder="Search...">
            </div>
            <button class="btn-log">Log In</button>
            <button class="btn-start">Get Started</button>
        </div>
    </nav>

    <header class="hero">
        <?= $media_html ?>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1><?= $hero_title ?></h1>
            <p><?= $hero_synopsis ?></p>
            <div class="hero-btns">
                <button class="btn-start">Learn More</button>
                <button class="btn-watch">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: text-bottom; margin-right: 5px;">
                        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                    </svg>
                    To Watch
                </button>
            </div>
        </div>
    </header>

    <main class="main-content">
        <h3 class="section-title">Special For You</h3>
        <div class="slider-container" id="sliderContainer">
            <button class="slider-arrow left" onclick="scrollSlider(-1)">&#10094;</button>
            <div class="anime-grid" id="animeSlider">
                <?php 
                $api_rec_url = "https://api.jikan.moe/v4/recommendations/anime";
                $response_list = @file_get_contents($api_rec_url);
                if ($response_list !== FALSE) {
                    $list_data = json_decode($response_list, true);
                    if (!empty($list_data['data'])) {
                        mysqli_query($conn, "TRUNCATE TABLE anime_list");
                        $count = 0;
                        foreach ($list_data['data'] as $rec) {
                            foreach ($rec['entry'] as $anime) {
                                if ($count >= 12) break 2;
                                $m_id = $anime['mal_id'];
                                $m_title = mysqli_real_escape_string($conn, $anime['title']);
                                $m_img = $anime['images']['jpg']['large_image_url'];
                                $m_year = 'Rec'; 
                                $m_genre = 'Community';
                                mysqli_query($conn, "CALL SyncAnimeList($m_id, '$m_title', '$m_img', '$m_year', '$m_genre')");
                                $count++;
                            }
                        }
                    }
                }
                $local_list = mysqli_query($conn, "SELECT * FROM anime_list ORDER BY (release_year = 'Rec' OR release_year = 'N/A') ASC, release_year DESC, updated_at DESC");
                while ($row = mysqli_fetch_assoc($local_list)) : ?>
                    <div class="anime-card">
                        <img src="<?= $row['image_url'] ?>" alt="<?= $row['title'] ?>">
                        <div class="info">
                            <h4><?= $row['title'] ?></h4>
                            <p><?= $row['release_year'] ?>, <?= $row['main_genre'] ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <button class="slider-arrow right" onclick="scrollSlider(1)">&#10095;</button>
        </div>

        <div class="section-header-inline">
            <h3 class="section-title">Featured Collections</h3>
        </div>
        <div class="collections-grid">
            <?php 
            // Konfigurasi Genre ID yang Akurat
// Cukup ganti bagian konfigurasi ini di index.php kamu
            $collections_config = [
                ['name' => 'The Best Mystical Anime', 'filter' => 'genres=14'], // Genre ID 14 = Horror (Junji Ito dkk)
                ['name' => 'Top 20 Romance Anime', 'filter' => 'genres=22'],    // Genre ID 22 = Romance
                ['name' => 'The Best Classic Animes', 'filter' => 'start_date=1980-01-01&end_date=2000-12-31']
            ];

            $check_coll = mysqli_query($conn, "SELECT id FROM anime_collections");
            
            // Lakukan sinkronisasi SP jika tabel kosong
            if (mysqli_num_rows($check_coll) == 0) {
                foreach ($collections_config as $config) {
                    $api_url = "https://api.jikan.moe/v4/anime?" . $config['filter'] . "&limit=3&order_by=score&sort=desc";
                    $res = @file_get_contents($api_url);
                    if ($res !== FALSE) {
                        $data = json_decode($res, true);
                        if (!empty($data['data'])) {
                            $img1 = $data['data'][0]['images']['jpg']['large_image_url'] ?? '';
                            $img2 = $data['data'][1]['images']['jpg']['large_image_url'] ?? '';
                            $img3 = $data['data'][2]['images']['jpg']['large_image_url'] ?? '';
                            $c_name = mysqli_real_escape_string($conn, $config['name']);
                            mysqli_query($conn, "CALL SyncCollections('$c_name', '$img1', '$img2', '$img3')");
                        }
                    }
                    usleep(500000); // Jeda rate limit API
                }
            }

            // Tampilkan hasil sinkronisasi dari database
            $db_coll = mysqli_query($conn, "SELECT * FROM anime_collections LIMIT 3");
            while ($col = mysqli_fetch_assoc($db_coll)) : ?>
                <div class="collection-card">
                    <div class="coll-info">
                        <h4><?= $col['collection_name'] ?></h4>
                    </div>
                    <div class="coll-stack">
                        <img src="<?= $col['image_url_1'] ?>" class="stack-1" onerror="this.src='https://via.placeholder.com/100x150'">
                        <img src="<?= $col['image_url_2'] ?>" class="stack-2" onerror="this.src='https://via.placeholder.com/100x150'">
                        <img src="<?= $col['image_url_3'] ?>" class="stack-3" onerror="this.src='https://via.placeholder.com/100x150'">
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="section-header-inline">
            <h3 class="section-title">Trending Now</h3>
            <a href="#" class="view-all">View All</a>
        </div>
<div class="trending-grid">
            <?php 
            // 1. Endpoint khusus anime musim ini (Trending Seasonal)
            $api_trending_url = "https://api.jikan.moe/v4/seasons/now?limit=6";
            $res_trending = @file_get_contents($api_trending_url);

            if ($res_trending !== FALSE) {
                $trend_data = json_decode($res_trending, true);
                if (!empty($trend_data['data'])) {
                    // Truncate biar data lama musim lalu ilang
                    mysqli_query($conn, "TRUNCATE TABLE anime_trending");
                    foreach ($trend_data['data'] as $item) {
                        $t_id = $item['mal_id'];
                        $t_title = mysqli_real_escape_string($conn, $item['title']);
                        $t_img = $item['images']['jpg']['large_image_url'];
                        $t_score = $item['score'] ?? 0.00;
                        $t_eps = $item['episodes'] ?? 0;
                        
                        // Panggil SP kamu yang sudah ada
                        mysqli_query($conn, "CALL SyncTrendingAnime($t_id, '$t_title', '$t_img', $t_score, $t_eps)");
                    }
                }
            }

            // 2. Tampilkan dari DB (Order by score biar yang paling bagus di depan)
            $db_trending = mysqli_query($conn, "SELECT * FROM anime_trending ORDER BY score DESC LIMIT 6");
            while ($row = mysqli_fetch_assoc($db_trending)) : ?>
                <div class="trend-card">
                    <div class="trend-img-container">
                        <img src="<?= $row['image_url'] ?>" alt="<?= $row['title'] ?>">
                        <div class="trend-score">⭐ <?= $row['score'] ?></div>
                    </div>
                    <div class="trend-info">
                        <h4><?= (strlen($row['title']) > 22) ? substr($row['title'], 0, 22) . '...' : $row['title'] ?></h4>
                        <p><?= $row['episodes'] ?> Episodes</p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <footer class="main-footer">
        <div class="footer-left">
            <span class="brand">Yamenime.com</span>
            <ul class="footer-links">
                <li><a href="#">Terms & Privacy</a></li>
                <li><a href="#">Contacts</a></li>
            </ul>
        </div>
        <div class="footer-right">
            <div class="social-icons">
                <a href="#"><img src="assets/images/icons/telegram.png" alt="Telegram"></a>
                <a href="#"><img src="assets/images/icons/discord.png" alt="Discord"></a>
                <a href="#"><img src="assets/images/icons/youtube.png" alt="YouTube"></a>
                <a href="#"><img src="assets/images/icons/instagram.png" alt="Instagram"></a>
            </div>
        </div>
    </footer>
    </main>

    <script>
    const slider = document.getElementById('animeSlider');
    const container = document.getElementById('sliderContainer');

    function updateShadows() {
        const scrollLeft = slider.scrollLeft;
        const maxScroll = slider.scrollWidth - slider.clientWidth;
        if (scrollLeft > 5) container.classList.add('show-left-dim');
        else container.classList.remove('show-left-dim');
        if (scrollLeft < maxScroll - 5) container.classList.add('show-right-dim');
        else container.classList.remove('show-right-dim');
    }

    function scrollSlider(direction) {
        const scrollAmount = 450; 
        slider.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
    }

    slider.addEventListener('scroll', updateShadows);
    window.addEventListener('load', updateShadows);
    </script>
</body>
</html>