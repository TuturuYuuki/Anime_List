<?php
require_once 'db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const WEBAUTHN_RP_ID = 'localhost';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($action === 'image_proxy') {
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: public, max-age=604800');
    header('Referrer-Policy: no-referrer');
} else {
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0, no-transform');
    header('Pragma: no-cache');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

$allowedOrigins = [
    'https://yuuki-anime.rf.gd',
    'http://localhost',
    'http://127.0.0.1'
];
$requestOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';
$isLocalDevOrigin = false;
if ($requestOrigin !== '') {
    $isLocalDevOrigin = (bool)preg_match(
        '#^https?://(localhost|127\.0\.0\.1|(?:\d{1,3}\.){3}\d{1,3}|[a-z0-9\-]+\.test)(:\d+)?$#i',
        $requestOrigin
    );
}

if ($requestOrigin !== '' && (in_array($requestOrigin, $allowedOrigins, true) || $isLocalDevOrigin)) {
    header('Access-Control-Allow-Origin: ' . $requestOrigin);
} elseif ($requestOrigin !== '' && isset($_SERVER['HTTP_HOST']) && stripos($requestOrigin, $_SERVER['HTTP_HOST']) !== false) {
    header('Access-Control-Allow-Origin: ' . $requestOrigin);
} else {
    header('Access-Control-Allow-Origin: http://localhost');
}
header('Access-Control-Allow-Credentials: true');

function isPrivateOrLocalHost(string $host): bool {
    $hostLower = strtolower($host);
    if (in_array($hostLower, ['localhost', '127.0.0.1', '::1'], true)) {
        return true;
    }

    if (filter_var($hostLower, FILTER_VALIDATE_IP)) {
        return !filter_var(
            $hostLower,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }

    return false;
}

function hasColumn(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    $stmt->execute([$table, $column]);
    return ((int)$stmt->fetchColumn() > 0);
}

function ensureColumn(PDO $db, string $table, string $column, string $definition): void {
    if (hasColumn($db, $table, $column)) return;
    $db->exec("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}");
}

function ensureCoreSchema(PDO $db): void {
    static $ready = false;
    if ($ready) return;

    ensureColumn($db, 'animes', 'user_id', 'INT NULL');
    ensureColumn($db, 'animes', 'genres', 'TEXT NULL');
    ensureColumn($db, 'animes', 'is_fav', 'TINYINT(1) NOT NULL DEFAULT 0');
    ensureColumn($db, 'animes', 'sort_order', 'INT NOT NULL DEFAULT 0');
    ensureColumn($db, 'animes', 'fav_marked_at', 'DATETIME NULL DEFAULT NULL');

    ensureColumn($db, 'waifus', 'user_id', 'INT NULL');
    ensureColumn($db, 'waifus', 'official_pict_url', 'VARCHAR(500) NULL');
    ensureColumn($db, 'waifus', 'sort_order', 'INT NOT NULL DEFAULT 0');
    ensureColumn($db, 'waifus', 'fav_marked_at', 'DATETIME NULL DEFAULT NULL');

    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL,
        email VARCHAR(190) NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        profile_pict VARCHAR(500) DEFAULT NULL,
        bio TEXT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uq_users_username (username),
        UNIQUE KEY uq_users_email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    ensureColumn($db, 'users', 'password_changed_at', 'DATETIME NULL DEFAULT NULL');

    $db->exec("CREATE TABLE IF NOT EXISTS user_credentials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        credential_id VARCHAR(255) NOT NULL,
        public_key TEXT NULL,
        sign_count BIGINT NOT NULL DEFAULT 0,
        transports VARCHAR(255) DEFAULT NULL,
        aaguid VARCHAR(64) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_used_at TIMESTAMP NULL DEFAULT NULL,
        UNIQUE KEY uq_user_credentials_credential_id (credential_id),
        INDEX idx_user_credentials_user_id (user_id),
        CONSTRAINT fk_user_credentials_user FOREIGN KEY (user_id)
            REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS password_reset_tokens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        token VARCHAR(128) NOT NULL,
        expires_at DATETIME NOT NULL,
        used_at DATETIME NULL DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uq_password_reset_token (token),
        INDEX idx_password_reset_user_id (user_id),
        CONSTRAINT fk_password_reset_user FOREIGN KEY (user_id)
            REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $ready = true;
}

function ensureAppSettingsTable(PDO $db): void {
    static $ready = false;
    if ($ready) return;

    $db->exec("CREATE TABLE IF NOT EXISTS app_settings (
        setting_key VARCHAR(190) PRIMARY KEY,
        setting_value TEXT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $ready = true;
}

function ensureWaifuGalleryTable(PDO $db): void {
    static $ready = false;
    if ($ready) return;

    $db->exec("CREATE TABLE IF NOT EXISTS waifu_gallery (
        id INT AUTO_INCREMENT PRIMARY KEY,
        waifu_id INT NOT NULL,
        image_path VARCHAR(500) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_waifu_id (waifu_id),
        CONSTRAINT fk_waifu_gallery_waifu FOREIGN KEY (waifu_id)
            REFERENCES waifus(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $ready = true;
}

function normalizeUploadPath(?string $path): ?string {
    if (!$path) return null;

    $clean = parse_url($path, PHP_URL_PATH);
    if (!is_string($clean) || $clean === '') return null;

    $clean = ltrim(str_replace('\\', '/', $clean), '/');
    if (strpos($clean, UPLOAD_URL) !== 0) return null;

    return $clean;
}

function deleteLocalUploadFile(?string $path): void {
    $normalized = normalizeUploadPath($path);
    if (!$normalized) return;

    $base = realpath(UPLOAD_DIR);
    if ($base === false) return;

    $target = realpath(__DIR__ . '/' . $normalized);
    if ($target === false || strpos($target, $base) !== 0 || !is_file($target)) return;

    @unlink($target);
}

function getSetting(PDO $db, string $key, ?string $default = null): ?string {
    ensureAppSettingsTable($db);
    $stmt = $db->prepare("SELECT setting_value FROM app_settings WHERE setting_key = ? LIMIT 1");
    $stmt->execute([$key]);
    $value = $stmt->fetchColumn();
    return ($value === false || $value === null || $value === '') ? $default : (string)$value;
}

function setSetting(PDO $db, string $key, string $value): void {
    ensureAppSettingsTable($db);
    $stmt = $db->prepare("INSERT INTO app_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    $stmt->execute([$key, $value]);
}

function userSettingKey(int $userId, string $suffix): string {
    return 'user_' . $userId . '_' . $suffix;
}

function bumpSyncVersion(PDO $db, int $userId, string $type): void {
    ensureAppSettingsTable($db);
    $key = userSettingKey($userId, 'sync_version_' . $type);
    $stmt = $db->prepare("INSERT INTO app_settings (setting_key, setting_value) VALUES (?, '1') ON DUPLICATE KEY UPDATE setting_value = CAST(COALESCE(NULLIF(setting_value, ''), '0') AS UNSIGNED) + 1");
    $stmt->execute([$key]);
}

function getSyncVersion(PDO $db, int $userId, string $type): int {
    $value = getSetting($db, userSettingKey($userId, 'sync_version_' . $type), '0');
    return (int)$value;
}

function getCurrentUserId(): ?int {
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

function getPasswordVersion(PDO $db, int $userId): string {
    $stmt = $db->prepare("SELECT COALESCE(UNIX_TIMESTAMP(password_changed_at), UNIX_TIMESTAMP(created_at), 0) FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $value = $stmt->fetchColumn();
    return ($value === false || $value === null) ? '0' : (string)$value;
}

function attachSessionForUser(PDO $db, int $userId): void {
    $_SESSION['user_id'] = $userId;
    $_SESSION['password_version'] = getPasswordVersion($db, $userId);
}

function invalidateSessionState(): void {
    $_SESSION = [];
    session_unset();
}

function isSessionPasswordVersionValid(PDO $db, int $userId): bool {
    $current = getPasswordVersion($db, $userId);
    $sessionVersion = isset($_SESSION['password_version']) ? (string)$_SESSION['password_version'] : '';

    // Backward compatibility for existing sessions created before password_version was tracked.
    if ($sessionVersion === '') {
        $_SESSION['password_version'] = $current;
        return true;
    }

    return hash_equals($current, $sessionVersion);
}

function requireAuth(?PDO $db = null): int {
    $userId = getCurrentUserId();
    if (!$userId) {
        jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
    }

    $conn = $db ?: getDB();
    if (!isSessionPasswordVersionValid($conn, $userId)) {
        invalidateSessionState();
        jsonResponse([
            'success' => false,
            'message' => 'Password akun berubah. Silakan login ulang.',
            'reason' => 'password_changed'
        ], 401);
    }

    return $userId;
}

function getUserById(PDO $db, int $id): ?array {
    $stmt = $db->prepare("SELECT id, username, email, profile_pict, bio, created_at FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function getJsonBody(): array {
    $raw = file_get_contents('php://input');
    if (!$raw) return [];
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function b64url(string $bin): string {
    return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
}

function randomChallenge(): string {
    return b64url(random_bytes(32));
}

function normalizeCredentialId(string $credentialId): string {
    return trim($credentialId);
}

function normalizeResetToken(string $token): string {
    $normalized = strtoupper(trim($token));
    return preg_replace('/[^A-Z0-9]/', '', $normalized) ?? '';
}

function getWebAuthnRpId(): string {
    return WEBAUTHN_RP_ID;
}

function fetchExternalJson(string $url): array {
    $headers = [
        'Accept: application/json',
        'User-Agent: AnimeWaifuVault/1.0 (+https://yuuki-anime.rf.gd)'
    ];

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        $body = curl_exec($ch);
        $curlErr = curl_error($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false) {
            return ['ok' => false, 'status' => 0, 'message' => $curlErr !== '' ? $curlErr : 'Gagal menghubungi API eksternal'];
        }

        $decoded = json_decode($body, true);
        if (!is_array($decoded)) {
            return ['ok' => false, 'status' => $status, 'message' => 'Respons API eksternal tidak valid'];
        }

        if ($status >= 400) {
            $message = $decoded['message'] ?? 'API eksternal mengembalikan error';
            return ['ok' => false, 'status' => $status, 'message' => (string)$message, 'data' => $decoded];
        }

        return ['ok' => true, 'status' => $status, 'data' => $decoded];
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 15,
            'header' => implode("\r\n", $headers)
        ]
    ]);

    $body = @file_get_contents($url, false, $context);
    $status = 0;
    if (isset($http_response_header) && is_array($http_response_header)) {
        foreach ($http_response_header as $line) {
            if (preg_match('/HTTP\/\S+\s+(\d{3})/', $line, $m)) {
                $status = (int)$m[1];
                break;
            }
        }
    }

    if ($body === false) {
        return ['ok' => false, 'status' => $status, 'message' => 'Gagal menghubungi API eksternal'];
    }

    $decoded = json_decode($body, true);
    if (!is_array($decoded)) {
        return ['ok' => false, 'status' => $status, 'message' => 'Respons API eksternal tidak valid'];
    }

    if ($status >= 400) {
        $message = $decoded['message'] ?? 'API eksternal mengembalikan error';
        return ['ok' => false, 'status' => $status, 'message' => (string)$message, 'data' => $decoded];
    }

    return ['ok' => true, 'status' => $status, 'data' => $decoded];
}

$bootstrapDb = getDB();
ensureCoreSchema($bootstrapDb);

$publicActions = [
    'signup',
    'login',
    'forgot_password',
    'reset_password',
    'session_status',
    'search_anime',
    'search_waifu',
    'search_character_full',
    'image_proxy',
    'webauthn_begin_login',
    'webauthn_finish_login'
];

if (!in_array($action, $publicActions, true)) {
    requireAuth();
}

switch ($action) {
    case 'image_proxy':
        $rawUrl = trim((string)($_GET['url'] ?? ''));
        if ($rawUrl === '') {
            http_response_code(400);
            exit;
        }

        $decodedUrl = urldecode($rawUrl);
        if (!filter_var($decodedUrl, FILTER_VALIDATE_URL)) {
            http_response_code(400);
            exit;
        }

        $parts = parse_url($decodedUrl);
        $scheme = strtolower((string)($parts['scheme'] ?? ''));
        $host = (string)($parts['host'] ?? '');
        if (!in_array($scheme, ['http', 'https'], true) || $host === '' || isPrivateOrLocalHost($host)) {
            http_response_code(403);
            exit;
        }

        $cacheDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'cache';
        $cacheTtl = 7 * 24 * 60 * 60;
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0775, true);
        }

        // Bersihkan file cache yang kadaluarsa.
        if (is_dir($cacheDir)) {
            $staleFiles = glob($cacheDir . DIRECTORY_SEPARATOR . '*');
            if (is_array($staleFiles)) {
                $staleBefore = time() - $cacheTtl;
                foreach ($staleFiles as $staleFile) {
                    if (is_file($staleFile) && @filemtime($staleFile) < $staleBefore) {
                        @unlink($staleFile);
                    }
                }
            }
        }

        $cacheKey = hash('sha256', $decodedUrl);
        $cachedCandidates = glob($cacheDir . DIRECTORY_SEPARATOR . $cacheKey . '.*') ?: [];
        foreach ($cachedCandidates as $cachedFile) {
            if (!is_file($cachedFile)) continue;
            if (@filemtime($cachedFile) < (time() - $cacheTtl)) {
                @unlink($cachedFile);
                continue;
            }

            $cachedMime = @mime_content_type($cachedFile);
            if (!is_string($cachedMime) || stripos($cachedMime, 'image/') !== 0) {
                $cachedMime = 'image/jpeg';
            }

            header('X-Proxy-Cache: HIT');
            header('Content-Type: ' . $cachedMime);
            readfile($cachedFile);
            exit;
        }

        $body = false;
        $contentType = '';

        if (function_exists('curl_init')) {
            $ch = curl_init($decodedUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CONNECTTIMEOUT => 8,
                CURLOPT_TIMEOUT => 20,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_HTTPHEADER => [
                    'Accept: image/*,*/*;q=0.8',
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123 Safari/537.36',
                    'Referer: https://myanimelist.net/'
                ],
            ]);
            $body = curl_exec($ch);
            $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = (string)curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            curl_close($ch);

            if ($body === false || $status >= 400 || $status === 0) {
                http_response_code(502);
                exit;
            }
        } else {
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'timeout' => 20,
                    'header' => "Accept: image/*,*/*;q=0.8\r\nUser-Agent: AnimeWaifuVault/1.0\r\nReferer: https://myanimelist.net/"
                ]
            ]);
            $body = @file_get_contents($decodedUrl, false, $context);
            if ($body === false) {
                http_response_code(502);
                exit;
            }

            if (isset($http_response_header) && is_array($http_response_header)) {
                foreach ($http_response_header as $line) {
                    if (stripos($line, 'Content-Type:') === 0) {
                        $contentType = trim(substr($line, strlen('Content-Type:')));
                        break;
                    }
                }
            }
        }

        if (!is_string($body) || $body === '') {
            http_response_code(502);
            exit;
        }

        $normalizedType = strtolower(trim(explode(';', $contentType)[0] ?? ''));
        if ($normalizedType === '' || stripos($normalizedType, 'image/') !== 0) {
            $finfoMime = '';
            if (function_exists('finfo_open') && function_exists('finfo_buffer') && function_exists('finfo_close')) {
                $finfo = @finfo_open(FILEINFO_MIME_TYPE);
                if ($finfo) {
                    $finfoMime = @finfo_buffer($finfo, $body);
                    @finfo_close($finfo);
                }
            }
            if (is_string($finfoMime) && stripos($finfoMime, 'image/') === 0) {
                $normalizedType = strtolower(trim($finfoMime));
            } else {
                $normalizedType = 'image/jpeg';
            }
        }

        $mimeToExt = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'image/avif' => 'avif'
        ];
        $ext = $mimeToExt[$normalizedType] ?? 'jpg';
        $cacheFile = $cacheDir . DIRECTORY_SEPARATOR . $cacheKey . '.' . $ext;
        @file_put_contents($cacheFile, $body, LOCK_EX);

        header('X-Proxy-Cache: MISS');
        header('Content-Type: ' . $normalizedType);
        echo $body;
        exit;

    case 'session_status':
        header('Cache-Control: no-store, no-cache, must-revalidate');
        $db = getDB();
        $userId = getCurrentUserId();
        if (!$userId) {
            jsonResponse(['authenticated' => false]);
        }

        if (!isSessionPasswordVersionValid($db, $userId)) {
            invalidateSessionState();
            jsonResponse([
                'authenticated' => false,
                'reason' => 'password_changed',
                'message' => 'Password akun telah diganti di perangkat lain. Silakan login ulang.'
            ]);
        }

        $user = getUserById($db, $userId);
        if (!$user) {
            invalidateSessionState();
            jsonResponse(['authenticated' => false]);
        }

        $stmt = $db->prepare("SELECT COUNT(*) FROM user_credentials WHERE user_id = ?");
        $stmt->execute([$userId]);
        $hasBiometric = ((int)$stmt->fetchColumn()) > 0;

        jsonResponse([
            'authenticated' => true,
            'user' => $user,
            'has_biometric' => $hasBiometric
        ]);
        break;

    case 'signup':
        if ($method !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        $db = getDB();
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if (mb_strlen($username) < 3) {
            jsonResponse(['success' => false, 'message' => 'Username minimal 3 karakter'], 400);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(['success' => false, 'message' => 'Format email tidak valid'], 400);
        }
        if (strlen($password) < 8) {
            jsonResponse(['success' => false, 'message' => 'Password minimal 8 karakter'], 400);
        }

        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1");
        $stmt->execute([$email, $username]);
        if ($stmt->fetch()) {
            jsonResponse(['success' => false, 'message' => 'Email atau username sudah terpakai'], 400);
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $defaultPict = 'https://api.dicebear.com/7.x/adventurer/svg?seed=' . rawurlencode($username);

        $stmt = $db->prepare("INSERT INTO users (username, email, password_hash, profile_pict, bio) VALUES (?,?,?,?,?)");
        $stmt->execute([$username, $email, $passwordHash, $defaultPict, '']);

        $userId = (int)$db->lastInsertId();
        session_regenerate_id(true);
        attachSessionForUser($db, $userId);

        jsonResponse(['success' => true, 'user' => getUserById($db, $userId)]);
        break;

    case 'login':
        if ($method !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        $db = getDB();
        $identifier = trim($_POST['identifier'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if ($identifier === '' || $password === '') {
            jsonResponse(['success' => false, 'message' => 'Email/username dan password wajib diisi'], 400);
        }

        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? OR username = ? LIMIT 1");
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, (string)$user['password_hash'])) {
            jsonResponse(['success' => false, 'message' => 'Login gagal. Cek kredensial.'], 401);
        }

        session_regenerate_id(true);
        attachSessionForUser($db, (int)$user['id']);

        jsonResponse(['success' => true, 'user' => getUserById($db, (int)$user['id'])]);
        break;

    case 'logout':
        invalidateSessionState();

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                (bool)$params['secure'],
                (bool)$params['httponly']
            );
        }

        session_destroy();
        jsonResponse(['success' => true]);
        break;

    case 'forgot_password':
        if ($method !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        $db = getDB();
        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(['success' => true, 'message' => 'Jika email terdaftar, token reset telah dibuat.']);
        }

        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $userId = $stmt->fetchColumn();

        if (!$userId) {
            jsonResponse(['success' => true, 'message' => 'Jika email terdaftar, token reset telah dibuat.']);
        }

        // Keep only one active reset token per user to avoid confusion during testing.
        $db->prepare("UPDATE password_reset_tokens SET used_at = NOW() WHERE user_id = ? AND used_at IS NULL")
            ->execute([(int)$userId]);

        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $token = '';
        for ($i = 0; $i < 8; $i++) {
            $token .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        // Use DB clock for expiry to avoid PHP/MySQL timezone mismatch on shared hosting/local stacks.
        $stmt = $db->prepare("INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))");
        $stmt->execute([(int)$userId, $token]);

        jsonResponse([
            'success' => true,
            'message' => 'Jika email telah terdaftar, token reset telah dikirim.'
        ]);
        break;

    case 'reset_password':
        if ($method !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        $db = getDB();
        $token = normalizeResetToken((string)($_POST['token'] ?? ''));
        $newPassword = (string)($_POST['new_password'] ?? '');

        if ($token === '' || strlen($newPassword) < 8) {
            jsonResponse(['success' => false, 'message' => 'Token dan password baru wajib valid'], 400);
        }

        $stmt = $db->prepare("SELECT * FROM password_reset_tokens WHERE UPPER(token) = ? AND used_at IS NULL AND expires_at > NOW() ORDER BY id DESC LIMIT 1");
        $stmt->execute([$token]);
        $reset = $stmt->fetch();

        if (!$reset) {
            jsonResponse(['success' => false, 'message' => 'Token reset tidak valid / kadaluarsa'], 400);
        }

        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $targetUserId = (int)$reset['user_id'];
        $db->prepare("UPDATE users SET password_hash = ?, password_changed_at = NOW() WHERE id = ?")
            ->execute([$passwordHash, $targetUserId]);
        $db->prepare("UPDATE password_reset_tokens SET used_at = NOW() WHERE id = ?")->execute([(int)$reset['id']]);
        bumpSyncVersion($db, $targetUserId, 'settings');

        jsonResponse(['success' => true, 'message' => 'Password berhasil direset']);
        break;

    case 'search_anime':
        $query = trim((string)($_GET['q'] ?? ''));
        if (mb_strlen($query) < 2) {
            jsonResponse(['success' => true, 'data' => []]);
        }

        $url = 'https://api.jikan.moe/v4/anime?q=' . rawurlencode($query) . '&limit=25';
        $result = fetchExternalJson($url);
        if (!$result['ok']) {
            $status = (int)($result['status'] ?? 502);
            $message = ($status === 429)
                ? 'API sedang limit. Coba lagi beberapa detik.'
                : (string)($result['message'] ?? 'Gagal mengambil data anime');
            jsonResponse(['success' => false, 'message' => $message], $status > 0 ? $status : 502);
        }

        $payload = $result['data'] ?? [];
        jsonResponse(['success' => true, 'data' => $payload['data'] ?? []]);
        break;

    case 'search_waifu':
        $query = trim((string)($_GET['q'] ?? ''));
        if (mb_strlen($query) < 2) {
            jsonResponse(['success' => true, 'data' => []]);
        }

        $url = 'https://api.jikan.moe/v4/characters?q=' . rawurlencode($query) . '&limit=25';
        $result = fetchExternalJson($url);
        if (!$result['ok']) {
            $status = (int)($result['status'] ?? 502);
            $message = ($status === 429)
                ? 'API sedang limit. Coba lagi beberapa detik.'
                : (string)($result['message'] ?? 'Gagal mengambil data waifu/karakter');
            jsonResponse(['success' => false, 'message' => $message], $status > 0 ? $status : 502);
        }

        $payload = $result['data'] ?? [];
        jsonResponse(['success' => true, 'data' => $payload['data'] ?? []]);
        break;

    case 'search_character_full':
        $charId = (int)($_GET['id'] ?? 0);
        if ($charId <= 0) {
            jsonResponse(['success' => false, 'message' => 'ID karakter tidak valid'], 400);
        }

        $url = 'https://api.jikan.moe/v4/characters/' . $charId . '/full';
        $result = fetchExternalJson($url);
        if (!$result['ok']) {
            $status = (int)($result['status'] ?? 502);
            $message = ($status === 429)
                ? 'API sedang limit. Coba lagi beberapa detik.'
                : (string)($result['message'] ?? 'Gagal mengambil detail karakter');
            jsonResponse(['success' => false, 'message' => $message], $status > 0 ? $status : 502);
        }

        $payload = $result['data'] ?? [];
        jsonResponse(['success' => true, 'data' => $payload['data'] ?? null]);
        break;

    case 'get_profile':
        $db = getDB();
        $userId = requireAuth($db);

        $user = getUserById($db, $userId);
        if (!$user) {
            jsonResponse(['success' => false, 'message' => 'User tidak ditemukan'], 404);
        }

        $stmt = $db->prepare("SELECT COUNT(*) FROM user_credentials WHERE user_id = ?");
        $stmt->execute([$userId]);
        $hasBiometric = ((int)$stmt->fetchColumn()) > 0;

        jsonResponse(['success' => true, 'user' => $user, 'has_biometric' => $hasBiometric]);
        break;

    case 'update_profile':
        if ($method !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        $db = getDB();
        $userId = requireAuth();

        $current = getUserById($db, $userId);
        if (!$current) jsonResponse(['success' => false, 'message' => 'User tidak ditemukan'], 404);

        $username = trim($_POST['username'] ?? (string)$current['username']);
        $bio = trim($_POST['bio'] ?? (string)$current['bio']);
        $profilePict = $current['profile_pict'] ?? null;

        if (mb_strlen($username) < 3) {
            jsonResponse(['success' => false, 'message' => 'Username minimal 3 karakter'], 400);
        }

        $usernameCheck = $db->prepare("SELECT id FROM users WHERE username = ? AND id <> ? LIMIT 1");
        $usernameCheck->execute([$username, $userId]);
        if ($usernameCheck->fetch()) {
            jsonResponse(['success' => false, 'message' => 'Username sudah dipakai user lain'], 400);
        }

        if (!empty($_FILES['profile_pict']['name'])) {
            $uploaded = uploadFile($_FILES['profile_pict'], 'profile');
            if (!$uploaded) {
                jsonResponse(['success' => false, 'message' => 'Gagal upload foto profil'], 400);
            }
            $profilePict = $uploaded;
        } elseif (!empty($_POST['profile_pict_data_url']) && str_starts_with($_POST['profile_pict_data_url'], 'data:image/')) {
            $payload = $_POST['profile_pict_data_url'];
            $parts = explode(',', $payload, 2);
            if (count($parts) === 2) {
                $bin = base64_decode($parts[1], true);
                if ($bin !== false) {
                    $filename = uniqid('profile_', true) . '.png';
                    $dir = UPLOAD_DIR . 'profile/';
                    if (!is_dir($dir)) mkdir($dir, 0755, true);
                    file_put_contents($dir . $filename, $bin);
                    $profilePict = UPLOAD_URL . 'profile/' . $filename;
                }
            }
        }

        $stmt = $db->prepare("UPDATE users SET username = ?, bio = ?, profile_pict = ? WHERE id = ?");
        $stmt->execute([$username, $bio, $profilePict, $userId]);
        bumpSyncVersion($db, $userId, 'settings');

        jsonResponse(['success' => true, 'user' => getUserById($db, $userId)]);
        break;

    case 'change_password':
        if ($method !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        $db = getDB();
        $userId = requireAuth();
        $currentPassword = (string)($_POST['current_password'] ?? '');
        $newPassword = (string)($_POST['new_password'] ?? '');

        if (strlen($newPassword) < 8) {
            jsonResponse(['success' => false, 'message' => 'Password baru minimal 8 karakter'], 400);
        }

        $stmt = $db->prepare("SELECT password_hash FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $storedHash = $stmt->fetchColumn();

        if (!$storedHash || !password_verify($currentPassword, (string)$storedHash)) {
            jsonResponse(['success' => false, 'message' => 'Password lama salah'], 400);
        }

        $db->prepare("UPDATE users SET password_hash = ?, password_changed_at = NOW() WHERE id = ?")
            ->execute([password_hash($newPassword, PASSWORD_DEFAULT), $userId]);
        bumpSyncVersion($db, $userId, 'settings');
        $_SESSION['password_version'] = getPasswordVersion($db, $userId);

        jsonResponse(['success' => true, 'message' => 'Password berhasil diganti']);
        break;

    case 'webauthn_begin_register':
        $db = getDB();
        $userId = requireAuth();
        $user = getUserById($db, $userId);
        if (!$user) jsonResponse(['success' => false, 'message' => 'User tidak ditemukan'], 404);

        $challenge = randomChallenge();
        $_SESSION['webauthn_register_challenge'] = $challenge;

        jsonResponse([
            'success' => true,
            'challenge' => $challenge,
            'user' => [
                'id' => (string)$userId,
                'name' => $user['email'],
                'displayName' => $user['username']
            ],
            'rp' => [
                'name' => 'Anime & Waifu Vault',
                'id' => getWebAuthnRpId()
            ]
        ]);
        break;

    case 'webauthn_finish_register':
        if ($method !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        $db = getDB();
        $userId = requireAuth();
        $body = getJsonBody();

        $challenge = $body['challenge'] ?? '';
        $credentialId = normalizeCredentialId((string)($body['credentialId'] ?? ''));
        $publicKey = (string)($body['publicKey'] ?? '');
        $signCount = (int)($body['signCount'] ?? 0);
        $transports = isset($body['transports']) && is_array($body['transports']) ? implode(',', $body['transports']) : null;

        if ($challenge === '' || $credentialId === '') {
            jsonResponse(['success' => false, 'message' => 'Payload WebAuthn tidak lengkap'], 400);
        }
        if (!isset($_SESSION['webauthn_register_challenge']) || $_SESSION['webauthn_register_challenge'] !== $challenge) {
            jsonResponse(['success' => false, 'message' => 'Challenge register tidak valid'], 400);
        }

        $stmt = $db->prepare("INSERT INTO user_credentials (user_id, credential_id, public_key, sign_count, transports) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE user_id = VALUES(user_id), public_key = VALUES(public_key), sign_count = VALUES(sign_count), transports = VALUES(transports)");
        $stmt->execute([$userId, $credentialId, $publicKey, $signCount, $transports]);

        unset($_SESSION['webauthn_register_challenge']);
        jsonResponse(['success' => true, 'message' => 'Biometrik berhasil diaktifkan']);
        break;

    case 'webauthn_begin_login':
        $db = getDB();
        $email = strtolower(trim((string)($_GET['email'] ?? '')));
        $challenge = randomChallenge();
        $_SESSION['webauthn_login_challenge'] = $challenge;

        $allowCredentials = [];
        if ($email !== '') {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                jsonResponse(['success' => false, 'message' => 'Isi email valid untuk login biometrik.'], 400);
            }

            $stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $targetUserId = (int)$stmt->fetchColumn();
            if (!$targetUserId) {
                jsonResponse(['success' => false, 'message' => 'Email tidak ditemukan.'], 404);
            }

            $stmt = $db->prepare("SELECT credential_id FROM user_credentials WHERE user_id = ?");
            $stmt->execute([$targetUserId]);
            $credentialRows = $stmt->fetchAll();
            if (!$credentialRows) {
                jsonResponse(['success' => false, 'message' => 'Biometrik belum diaktifkan untuk email ini.'], 400);
            }

            foreach ($credentialRows as $row) {
                $id = normalizeCredentialId((string)($row['credential_id'] ?? ''));
                if ($id !== '') {
                    $allowCredentials[] = ['type' => 'public-key', 'id' => $id];
                }
            }

            if (!$allowCredentials) {
                jsonResponse(['success' => false, 'message' => 'Kredensial biometrik tidak ditemukan untuk email ini.'], 400);
            }

            $_SESSION['webauthn_login_expected_user_id'] = $targetUserId;
        } else {
            unset($_SESSION['webauthn_login_expected_user_id']);
        }

        jsonResponse([
            'success' => true,
            'challenge' => $challenge,
            'rpId' => getWebAuthnRpId(),
            'allow_credentials' => $allowCredentials
        ]);
        break;

    case 'webauthn_finish_login':
        if ($method !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        $db = getDB();
        $body = getJsonBody();
        $challenge = $body['challenge'] ?? '';
        $credentialId = normalizeCredentialId((string)($body['credentialId'] ?? ''));

        if ($challenge === '' || $credentialId === '') {
            jsonResponse(['success' => false, 'message' => 'Payload WebAuthn tidak lengkap'], 400);
        }
        if (!isset($_SESSION['webauthn_login_challenge']) || $_SESSION['webauthn_login_challenge'] !== $challenge) {
            jsonResponse(['success' => false, 'message' => 'Challenge login tidak valid'], 400);
        }

        $stmt = $db->prepare("SELECT user_id FROM user_credentials WHERE credential_id = ? LIMIT 1");
        $stmt->execute([$credentialId]);
        $userId = (int)$stmt->fetchColumn();

        $expectedUserId = isset($_SESSION['webauthn_login_expected_user_id']) ? (int)$_SESSION['webauthn_login_expected_user_id'] : 0;
        if ($expectedUserId > 0 && $userId > 0 && $userId !== $expectedUserId) {
            unset($_SESSION['webauthn_login_challenge'], $_SESSION['webauthn_login_expected_user_id']);
            jsonResponse(['success' => false, 'message' => 'Kredensial biometrik tidak cocok dengan email yang diisi.'], 401);
        }

        if (!$userId) {
            jsonResponse(['success' => false, 'message' => 'Kredensial biometrik tidak terdaftar'], 401);
        }

        $db->prepare("UPDATE user_credentials SET last_used_at = NOW() WHERE credential_id = ?")
            ->execute([$credentialId]);

        session_regenerate_id(true);
        attachSessionForUser($db, $userId);
        unset($_SESSION['webauthn_login_challenge'], $_SESSION['webauthn_login_expected_user_id']);

        jsonResponse(['success' => true, 'user' => getUserById($db, $userId)]);
        break;

    case 'get_fav_modes':
        $db = getDB();
        $userId = requireAuth();
        $waifuMode = getSetting($db, userSettingKey($userId, 'fav_mode_waifu'), 'auto');
        $animeMode = getSetting($db, userSettingKey($userId, 'fav_mode_anime'), 'auto');
        jsonResponse([
            'success' => true,
            'modes' => [
                'waifu' => ($waifuMode === 'manual' ? 'manual' : 'auto'),
                'anime' => ($animeMode === 'manual' ? 'manual' : 'auto')
            ]
        ]);
        break;

    case 'set_fav_mode':
        $db = getDB();
        $userId = requireAuth();
        $type = ($_GET['type'] ?? '') === 'anime' ? 'anime' : 'waifu';
        $mode = ($_GET['mode'] ?? '') === 'manual' ? 'manual' : 'auto';
        setSetting($db, userSettingKey($userId, 'fav_mode_' . $type), $mode);
        bumpSyncVersion($db, $userId, 'settings');
        jsonResponse(['success' => true]);
        break;

    case 'get_sync_status':
        $db = getDB();
        $userId = requireAuth();
        ensureAppSettingsTable($db);

        jsonResponse([
            'last_anime' => getSyncVersion($db, $userId, 'anime'),
            'last_waifu' => getSyncVersion($db, $userId, 'waifu'),
            'last_settings' => getSyncVersion($db, $userId, 'settings')
        ]);
        break;

    case 'get_animes':
        $db = getDB();
        $userId = requireAuth();
        $status = $_GET['status'] ?? '';
        $hasSortOrder = hasColumn($db, 'animes', 'sort_order');
        $hasUpdatedAt = hasColumn($db, 'animes', 'updated_at');
        $sql = "SELECT * FROM animes WHERE user_id = ?";
        $params = [$userId];
        if ($status) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        $orderParts = ["is_fav DESC"];
        if ($hasSortOrder) {
            $orderParts[] = "sort_order ASC";
        }
        if ($hasUpdatedAt) {
            $orderParts[] = "updated_at DESC";
        } else {
            $orderParts[] = "id DESC";
        }
        $sql .= " ORDER BY " . implode(', ', $orderParts);
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        jsonResponse(['data' => $stmt->fetchAll()]);
        break;

    case 'get_anime_details':
        $db = getDB();
        $userId = requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $stmt = $db->prepare("SELECT * FROM animes WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        jsonResponse($stmt->fetch());
        break;

    case 'add_anime':
        $db = getDB();
        $userId = requireAuth();

        $stmtCheck = $db->prepare("SELECT id FROM animes WHERE judul = ? AND user_id = ? LIMIT 1");
        $stmtCheck->execute([$_POST['judul'], $userId]);
        if ($stmtCheck->fetch()) {
            jsonResponse(['success' => false, 'message' => 'Judul anime ini sudah ada di koleksimu!'], 400);
        }

        $gambar_path = null;
        if (!empty($_FILES['gambar']['name'])) {
            $gambar_path = uploadFile($_FILES['gambar'], 'anime');
        } elseif (!empty($_POST['gambar_url'])) {
            $gambar_path = $_POST['gambar_url'];
        }

        $stmt = $db->prepare("INSERT INTO animes (user_id, mal_id, judul, eps_nonton, eps_total, genres, gambar_path, status) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $userId,
            $_POST['mal_id'] ?: null,
            $_POST['judul'],
            (int)$_POST['eps_nonton'],
            (int)$_POST['eps_total'],
            $_POST['genres'] ?? null,
            $gambar_path,
            $_POST['status'] ?? 'plan_to_watch'
        ]);

        bumpSyncVersion($db, $userId, 'anime');
        jsonResponse(['success' => true, 'id' => $db->lastInsertId()]);
        break;

    case 'update_anime':
        $db = getDB();
        $userId = requireAuth();
        $id = (int)$_POST['id'];
        $gambar_path = $_POST['gambar_existing'] ?? null;
        if (!empty($_FILES['gambar']['name'])) {
            $gambar_path = uploadFile($_FILES['gambar'], 'anime');
        } elseif (!empty($_POST['gambar_url'])) {
            $gambar_path = $_POST['gambar_url'];
        }

        $stmt = $db->prepare("UPDATE animes SET judul=?, eps_nonton=?, eps_total=?, genres=?, gambar_path=?, status=? WHERE id=? AND user_id=?");
        $stmt->execute([
            $_POST['judul'],
            (int)$_POST['eps_nonton'],
            (int)$_POST['eps_total'],
            $_POST['genres'] ?? null,
            $gambar_path,
            $_POST['status'],
            $id,
            $userId
        ]);
        bumpSyncVersion($db, $userId, 'anime');
        jsonResponse(['success' => true]);
        break;

    case 'delete_anime':
        $db = getDB();
        $userId = requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $stmtAnime = $db->prepare("SELECT gambar_path FROM animes WHERE id = ? AND user_id = ? LIMIT 1");
        $stmtAnime->execute([$id, $userId]);
        $gambarPath = $stmtAnime->fetchColumn();

        $stmtDelete = $db->prepare("DELETE FROM animes WHERE id=? AND user_id=?");
        $stmtDelete->execute([$id, $userId]);

        if ($stmtDelete->rowCount() > 0) {
            deleteLocalUploadFile(is_string($gambarPath) ? $gambarPath : null);
        }

        bumpSyncVersion($db, $userId, 'anime');
        jsonResponse(['success' => true]);
        break;

    case 'toggle_anime_fav':
        $db = getDB();
        $userId = requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $hasFavMarkedAt = hasColumn($db, 'animes', 'fav_marked_at');
        $hasSortOrder = hasColumn($db, 'animes', 'sort_order');

        $stmt = $db->prepare("SELECT is_fav FROM animes WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        $current = $stmt->fetchColumn();
        $new_val = $current ? 0 : 1;

        if ($hasFavMarkedAt) {
            if ($hasSortOrder && $new_val == 1) {
                $db->prepare("UPDATE animes SET is_fav = ?, fav_marked_at = CURRENT_TIMESTAMP, sort_order = (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM (SELECT sort_order FROM animes WHERE user_id = ?) t), updated_at = updated_at WHERE id = ? AND user_id = ?")
                   ->execute([$new_val, $userId, $id, $userId]);
            } else {
                $db->prepare("UPDATE animes SET is_fav = ?, fav_marked_at = CASE WHEN ? = 1 THEN CURRENT_TIMESTAMP ELSE NULL END, updated_at = updated_at WHERE id = ? AND user_id = ?")
                   ->execute([$new_val, $new_val, $id, $userId]);
            }
        } else {
            if ($hasSortOrder && $new_val == 1) {
                $db->prepare("UPDATE animes SET is_fav = ?, sort_order = (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM (SELECT sort_order FROM animes WHERE user_id = ?) t) WHERE id = ? AND user_id = ?")
                   ->execute([$new_val, $userId, $id, $userId]);
            } else {
                $db->prepare("UPDATE animes SET is_fav = ? WHERE id = ? AND user_id = ?")
                   ->execute([$new_val, $id, $userId]);
            }
        }
        bumpSyncVersion($db, $userId, 'anime');
        jsonResponse(['success' => true]);
        break;

    case 'get_waifus':
        $db = getDB();
        $userId = requireAuth();
        $hasSortOrder = hasColumn($db, 'waifus', 'sort_order');
        $hasUpdatedAt = hasColumn($db, 'waifus', 'updated_at');
        $orderParts = ["is_fav DESC"];
        if ($hasSortOrder) {
            $orderParts[] = "sort_order ASC";
        }
        if ($hasUpdatedAt) {
            $orderParts[] = "updated_at DESC";
        } else {
            $orderParts[] = "id DESC";
        }
        $stmt = $db->prepare("SELECT * FROM waifus WHERE user_id = ? ORDER BY " . implode(', ', $orderParts));
        $stmt->execute([$userId]);
        jsonResponse(['data' => $stmt->fetchAll()]);
        break;

    case 'get_waifu_details':
        $db = getDB();
        $userId = requireAuth();
        ensureWaifuGalleryTable($db);
        $id = (int)($_GET['id'] ?? 0);
        $waifu = $db->prepare("SELECT * FROM waifus WHERE id = ? AND user_id = ?");
        $waifu->execute([$id, $userId]);
        $data = $waifu->fetch();

        if (!$data) {
            jsonResponse(null);
        }

        $gallery = $db->prepare("SELECT * FROM waifu_gallery WHERE waifu_id = ? ORDER BY id DESC");
        $gallery->execute([$id]);
        $data['gallery'] = $gallery->fetchAll();
        jsonResponse($data);
        break;

    case 'add_waifu':
        $db = getDB();
        $userId = requireAuth();
        ensureWaifuGalleryTable($db);
        $hasFavMarkedAt = hasColumn($db, 'waifus', 'fav_marked_at');

        $stmtCheck = $db->prepare("SELECT id FROM waifus WHERE nama = ? AND user_id = ? LIMIT 1");
        $stmtCheck->execute([$_POST['nama'], $userId]);
        if ($stmtCheck->fetch()) {
            jsonResponse(['success' => false, 'message' => 'Nama waifu ini sudah ada di list!'], 400);
        }

        $pict_path = !empty($_FILES['pict']['name']) ? uploadFile($_FILES['pict'], 'waifu') : null;
        $official_url = $_POST['pict_existing'] ?? null;

        $isFav = isset($_POST['is_fav']) ? 1 : 0;
        if ($hasFavMarkedAt) {
            $stmt = $db->prepare("INSERT INTO waifus (user_id, nama, anime_asal, umur, bio, official_pict_url, is_fav, fav_marked_at, pict_path) VALUES (?,?,?,?,?,?,?,?,?)");
            $stmt->execute([
                $userId,
                $_POST['nama'],
                $_POST['anime_asal'],
                $_POST['umur'],
                $_POST['bio'],
                $official_url,
                $isFav,
                $isFav ? date('Y-m-d H:i:s') : null,
                $pict_path
            ]);
        } else {
            $stmt = $db->prepare("INSERT INTO waifus (user_id, nama, anime_asal, umur, bio, official_pict_url, is_fav, pict_path) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->execute([
                $userId,
                $_POST['nama'],
                $_POST['anime_asal'],
                $_POST['umur'],
                $_POST['bio'],
                $official_url,
                $isFav,
                $pict_path
            ]);
        }

        $new_id = $db->lastInsertId();

        if ($official_url) {
            $db->prepare("INSERT INTO waifu_gallery (waifu_id, image_path) VALUES (?,?)")->execute([$new_id, $official_url]);
        }

        bumpSyncVersion($db, $userId, 'waifu');

        jsonResponse(['success' => true]);
        break;

    case 'delete_waifu':
        $db = getDB();
        $userId = requireAuth();
        ensureWaifuGalleryTable($db);
        $id = (int)($_GET['id'] ?? 0);

        $stmtWaifu = $db->prepare("SELECT pict_path FROM waifus WHERE id = ? AND user_id = ? LIMIT 1");
        $stmtWaifu->execute([$id, $userId]);
        $waifuPictPath = $stmtWaifu->fetchColumn();

        $stmtGallery = $db->prepare("SELECT g.image_path FROM waifu_gallery g JOIN waifus w ON w.id = g.waifu_id WHERE g.waifu_id = ? AND w.user_id = ?");
        $stmtGallery->execute([$id, $userId]);
        $galleryPaths = $stmtGallery->fetchAll(PDO::FETCH_COLUMN);

        $stmtDelete = $db->prepare("DELETE FROM waifus WHERE id=? AND user_id=?");
        $stmtDelete->execute([$id, $userId]);

        if ($stmtDelete->rowCount() > 0) {
            deleteLocalUploadFile(is_string($waifuPictPath) ? $waifuPictPath : null);
            foreach ($galleryPaths as $galleryPath) {
                deleteLocalUploadFile(is_string($galleryPath) ? $galleryPath : null);
            }
        }

        bumpSyncVersion($db, $userId, 'waifu');
        jsonResponse(['success' => true]);
        break;

    case 'update_waifu':
        $db = getDB();
        $userId = requireAuth();
        $id = (int)$_POST['id'];
        $hasFavMarkedAt = hasColumn($db, 'waifus', 'fav_marked_at');
        $pict_path = $_POST['pict_existing'] ?? null;
        if (!empty($_FILES['pict']['name'])) $pict_path = uploadFile($_FILES['pict'], 'waifu');
        $isFav = isset($_POST['is_fav']) ? 1 : 0;

        if ($hasFavMarkedAt) {
            $stmt = $db->prepare("UPDATE waifus SET nama=?, anime_asal=?, umur=?, bio=?, is_fav=?, fav_marked_at = CASE WHEN ? = 1 THEN COALESCE(fav_marked_at, CURRENT_TIMESTAMP) ELSE NULL END, pict_path=? WHERE id=? AND user_id=?");
            $stmt->execute([$_POST['nama'], $_POST['anime_asal'], $_POST['umur'], $_POST['bio'], $isFav, $isFav, $pict_path, $id, $userId]);
        } else {
            $stmt = $db->prepare("UPDATE waifus SET nama=?, anime_asal=?, umur=?, bio=?, is_fav=?, pict_path=? WHERE id=? AND user_id=?");
            $stmt->execute([$_POST['nama'], $_POST['anime_asal'], $_POST['umur'], $_POST['bio'], $isFav, $pict_path, $id, $userId]);
        }
        bumpSyncVersion($db, $userId, 'waifu');
        jsonResponse(['success' => true]);
        break;

    case 'add_gallery_item':
        $db = getDB();
        $userId = requireAuth();
        ensureWaifuGalleryTable($db);

        $waifuId = (int)($_POST['waifu_id'] ?? 0);
        $ownStmt = $db->prepare("SELECT id FROM waifus WHERE id = ? AND user_id = ? LIMIT 1");
        $ownStmt->execute([$waifuId, $userId]);
        if (!$ownStmt->fetch()) {
            jsonResponse(['success' => false, 'message' => 'Waifu tidak ditemukan'], 404);
        }

        $path = !empty($_FILES['art']['name']) ? uploadFile($_FILES['art'], 'fanart') : null;
        if ($path) {
            $db->prepare("INSERT INTO waifu_gallery (waifu_id, image_path) VALUES (?,?)")->execute([$waifuId, $path]);
        }
        bumpSyncVersion($db, $userId, 'waifu');
        jsonResponse(['success' => true]);
        break;

    case 'delete_gallery_item':
        $db = getDB();
        $userId = requireAuth();
        ensureWaifuGalleryTable($db);

        $galleryId = (int)($_GET['id'] ?? 0);
        $stmt = $db->prepare("SELECT g.id, g.image_path FROM waifu_gallery g JOIN waifus w ON w.id = g.waifu_id WHERE g.id = ? AND w.user_id = ? LIMIT 1");
        $stmt->execute([$galleryId, $userId]);
        $gallery = $stmt->fetch();
        if (!$gallery) {
            jsonResponse(['success' => false, 'message' => 'Item galeri tidak ditemukan'], 404);
        }

        $db->prepare("DELETE FROM waifu_gallery WHERE id = ?")->execute([$galleryId]);
        deleteLocalUploadFile($gallery['image_path'] ?? null);
        bumpSyncVersion($db, $userId, 'waifu');
        jsonResponse(['success' => true]);
        break;

    case 'toggle_fav':
        $db = getDB();
        $userId = requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $hasFavMarkedAt = hasColumn($db, 'waifus', 'fav_marked_at');
        $hasSortOrder = hasColumn($db, 'waifus', 'sort_order');

        $stmt = $db->prepare("SELECT is_fav FROM waifus WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        $current = $stmt->fetchColumn();
        $new_val = $current ? 0 : 1;

        if ($hasFavMarkedAt) {
            if ($hasSortOrder && $new_val == 1) {
                $db->prepare("UPDATE waifus SET is_fav = ?, fav_marked_at = CURRENT_TIMESTAMP, sort_order = (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM (SELECT sort_order FROM waifus WHERE user_id = ?) t), updated_at = updated_at WHERE id = ? AND user_id = ?")
                   ->execute([$new_val, $userId, $id, $userId]);
            } else {
                $db->prepare("UPDATE waifus SET is_fav = ?, fav_marked_at = CASE WHEN ? = 1 THEN CURRENT_TIMESTAMP ELSE NULL END, updated_at = updated_at WHERE id = ? AND user_id = ?")
                   ->execute([$new_val, $new_val, $id, $userId]);
            }
        } else {
            if ($hasSortOrder && $new_val == 1) {
                $db->prepare("UPDATE waifus SET is_fav = ?, sort_order = (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM (SELECT sort_order FROM waifus WHERE user_id = ?) t), updated_at = updated_at WHERE id = ? AND user_id = ?")
                   ->execute([$new_val, $userId, $id, $userId]);
            } else {
                $db->prepare("UPDATE waifus SET is_fav = ?, updated_at = updated_at WHERE id = ? AND user_id = ?")
                   ->execute([$new_val, $id, $userId]);
            }
        }
        bumpSyncVersion($db, $userId, 'waifu');
        jsonResponse(['success' => true]);
        break;

    case 'update_fav_order':
        $db = getDB();
        $userId = requireAuth();

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (isset($data['type']) && isset($data['ids']) && is_array($data['ids'])) {
            $type = $data['type'];
            $ids = $data['ids'];
            $table = ($type === 'anime') ? 'animes' : 'waifus';

            foreach ($ids as $index => $id) {
                $orderValue = $index + 1;
                $stmt = $db->prepare("UPDATE $table SET sort_order = ? WHERE id = ? AND user_id = ?");
                $stmt->execute([$orderValue, (int)$id, $userId]);
            }

            bumpSyncVersion($db, $userId, $type === 'anime' ? 'anime' : 'waifu');
            jsonResponse(['success' => true]);
        } else {
            jsonResponse(['success' => false, 'message' => 'Data tidak lengkap']);
        }
        break;

    default:
        jsonResponse(['error' => 'Action tidak dikenal'], 404);
}
?>