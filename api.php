<?php
// api.php - Handler API untuk Anime & Waifu

require_once 'db.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($action) {

    // ============ ANIME ============
    // ============ ANIME ============
    case 'get_animes':
        $db = getDB();
        $status = $_GET['status'] ?? '';
        $sql = "SELECT * FROM animes";
        $params = [];
        if ($status) {
            $sql .= " WHERE status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY updated_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        jsonResponse(['data' => $stmt->fetchAll()]);
        break; // Pastikan ada break di sini

    case 'get_anime_details':
        $db = getDB();
        $id = (int)$_GET['id'];
        $stmt = $db->prepare("SELECT * FROM animes WHERE id = ?");
        $stmt->execute([$id]);
        jsonResponse($stmt->fetch());
        break;

    case 'add_anime':
        $db = getDB();
        $gambar_path = null;
        // KODE YANG HILANG: Proses upload atau URL gambar
        if (!empty($_FILES['gambar']['name'])) {
            $gambar_path = uploadFile($_FILES['gambar'], 'anime');
        } elseif (!empty($_POST['gambar_url'])) {
            $gambar_path = $_POST['gambar_url'];
        }

        $stmt = $db->prepare("INSERT INTO animes (mal_id, judul, eps_nonton, eps_total, genres, gambar_path, status) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([
            $_POST['mal_id'] ?: null,
            $_POST['judul'],
            (int)$_POST['eps_nonton'],
            (int)$_POST['eps_total'],
            $_POST['genres'] ?? null,
            $gambar_path,
            $_POST['status'] ?? 'plan_to_watch'
        ]);
        jsonResponse(['success' => true, 'id' => $db->lastInsertId()]);
        break;

    case 'update_anime':
        $db = getDB();
        $id = (int)$_POST['id'];
        $gambar_path = $_POST['gambar_existing'] ?? null;
        if (!empty($_FILES['gambar']['name'])) {
            $gambar_path = uploadFile($_FILES['gambar'], 'anime');
        } elseif (!empty($_POST['gambar_url'])) {
            $gambar_path = $_POST['gambar_url'];
        }

        $stmt = $db->prepare("UPDATE animes SET judul=?, eps_nonton=?, eps_total=?, genres=?, gambar_path=?, status=? WHERE id=?");
        $stmt->execute([$_POST['judul'], (int)$_POST['eps_nonton'], (int)$_POST['eps_total'], $_POST['genres'] ?? null, $gambar_path, $_POST['status'], $id]);
        jsonResponse(['success' => true]);
        break;

    case 'delete_anime':
        $db = getDB();
        $id = (int)($_GET['id'] ?? 0);
        $db->prepare("DELETE FROM animes WHERE id=?")->execute([$id]);
        jsonResponse(['success' => true]);
        break; // TAMBAHKAN BREAK INI AGAR TIDAK ERROR
    
    
    case 'toggle_anime_fav':
        $db = getDB();
        $id = (int)($_GET['id'] ?? 0);
        // Cek status favorit saat ini
        $stmt = $db->prepare("SELECT is_fav FROM animes WHERE id = ?");
        $stmt->execute([$id]);
        $current = $stmt->fetchColumn();
        $new_val = $current ? 0 : 1;
        
        // Update ke status baru
        $db->prepare("UPDATE animes SET is_fav = ? WHERE id = ?")->execute([$new_val, $id]);
        jsonResponse(['success' => true]);
        break;
        
    // ============ WAIFU (VERSI BERSIH) ============
    case 'get_waifus':
        $db = getDB();
        $stmt = $db->query("SELECT * FROM waifus ORDER BY is_fav DESC, updated_at DESC");
        jsonResponse(['data' => $stmt->fetchAll()]);
        break;

    case 'get_waifu_details':
        $db = getDB();
        $id = (int)$_GET['id'];
        $waifu = $db->prepare("SELECT * FROM waifus WHERE id = ?");
        $waifu->execute([$id]);
        $data = $waifu->fetch();
        
        $gallery = $db->prepare("SELECT * FROM waifu_gallery WHERE waifu_id = ? ORDER BY id DESC");
        $gallery->execute([$id]);
        $data['gallery'] = $gallery->fetchAll();
        jsonResponse($data);
        break;

    case 'add_waifu':
        $db = getDB();
        $pict_path = !empty($_FILES['pict']['name']) ? uploadFile($_FILES['pict'], 'waifu') : null;
        $official_url = $_POST['pict_existing'] ?? null; 

        // Pastikan urutan parameter (7 kolom) sesuai dengan struktur tabelmu
        $stmt = $db->prepare("INSERT INTO waifus (nama, anime_asal, umur, bio, official_pict_url, is_fav, pict_path) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$_POST['nama'], $_POST['anime_asal'], $_POST['umur'], $_POST['bio'], $official_url, isset($_POST['is_fav']) ? 1 : 0, $pict_path]);
        
        $new_id = $db->lastInsertId();
        
        // Simpan ke galeri secara otomatis jika ada URL official
        if ($official_url) {
            $db->prepare("INSERT INTO waifu_gallery (waifu_id, image_path) VALUES (?,?)")->execute([$new_id, $official_url]);
        }
        jsonResponse(['success' => true]);
        break;

    case 'delete_waifu':
        $db = getDB();
        $id = (int)($_GET['id'] ?? 0);
        $db->prepare("DELETE FROM waifus WHERE id=?")->execute([$id]);
        jsonResponse(['success' => true]);
        break;

    case 'update_waifu':
        $db = getDB();
        $id = (int)$_POST['id'];
        $pict_path = $_POST['pict_existing'] ?? null;
        if (!empty($_FILES['pict']['name'])) $pict_path = uploadFile($_FILES['pict'], 'waifu');

        // Hapus art_path dari sini karena sudah pakai tabel galeri
        $stmt = $db->prepare("UPDATE waifus SET nama=?, anime_asal=?, umur=?, bio=?, is_fav=?, pict_path=? WHERE id=?");
        $stmt->execute([$_POST['nama'], $_POST['anime_asal'], $_POST['umur'], $_POST['bio'], isset($_POST['is_fav']) ? 1 : 0, $pict_path, $id]);
        jsonResponse(['success' => true]);
        break;

    case 'add_gallery_item':
        $db = getDB();
        $path = !empty($_FILES['art']['name']) ? uploadFile($_FILES['art'], 'fanart') : null;
        if ($path) {
            $db->prepare("INSERT INTO waifu_gallery (waifu_id, image_path) VALUES (?,?)")->execute([(int)$_POST['waifu_id'], $path]);
        }
        jsonResponse(['success' => true]);
        break;

    case 'delete_gallery_item':
        getDB()->prepare("DELETE FROM waifu_gallery WHERE id = ?")->execute([(int)$_GET['id']]);
        jsonResponse(['success' => true]);
        break;

    case 'toggle_fav': // Toggle favorit waifu spesifik
        $db = getDB();
        $id = (int)($_GET['id'] ?? 0);
        // Ambil status saat ini
        $stmt = $db->prepare("SELECT is_fav FROM waifus WHERE id = ?");
        $stmt->execute([$id]);
        $current = $stmt->fetchColumn();
        $new_val = $current ? 0 : 1;
        
        // Update hanya waifu ini
        $db->prepare("UPDATE waifus SET is_fav = ? WHERE id = ?")->execute([$new_val, $id]);
        jsonResponse(['success' => true]);
        break;

    default:
        jsonResponse(['error' => 'Action tidak dikenal'], 404);
}
?>