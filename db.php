<?php
// db.php - Koneksi Database dengan PDO

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'anime_waifu_vault');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_CHARSET', 'utf8mb4');

define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', 'uploads/');

// Buat folder uploads jika belum ada
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
    mkdir(UPLOAD_DIR . 'anime/', 0755, true);
    mkdir(UPLOAD_DIR . 'waifu/', 0755, true);
    mkdir(UPLOAD_DIR . 'fanart/', 0755, true);
    mkdir(UPLOAD_DIR . 'profile/', 0755, true);
} elseif (!is_dir(UPLOAD_DIR . 'profile/')) {
    mkdir(UPLOAD_DIR . 'profile/', 0755, true);
}

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $hosts = [DB_HOST];
        if (DB_HOST === 'localhost') {
            $hosts[] = '127.0.0.1';
        } elseif (DB_HOST === '127.0.0.1') {
            $hosts[] = 'localhost';
        }

        $ports = [DB_PORT];
        if (DB_PORT === '3306') {
            $ports[] = '3307';
        }

        $errors = [];
        foreach (array_unique($hosts) as $host) {
            foreach (array_unique($ports) as $port) {
                $dsn = "mysql:host={$host};port={$port};dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                try {
                    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                    break 2;
                } catch (PDOException $e) {
                    $errors[] = "{$host}:{$port} => " . $e->getMessage();
                }
            }
        }

        if ($pdo === null) {
            http_response_code(500);
            die(json_encode([
                'error' => 'Koneksi database gagal. Cek service MySQL Laragon, nama database, username/password, atau set environment DB_PORT.',
                'detail' => $errors
            ]));
        }
    }
    return $pdo;
}

function uploadFile(array $file, string $subdir = ''): ?string {
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) return null;
    if ($file['size'] > 5 * 1024 * 1024) return null; // max 5MB
    
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $dir = UPLOAD_DIR . ($subdir ? $subdir . '/' : '');
    
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    
    if (move_uploaded_file($file['tmp_name'], $dir . $filename)) {
        return UPLOAD_URL . ($subdir ? $subdir . '/' : '') . $filename;
    }
    return null;
}

function jsonResponse(mixed $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
?>
