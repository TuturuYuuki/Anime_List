<?php
header('Content-Type: application/manifest+json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0, no-transform');
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/manifest.php';
$basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
if ($basePath === '.' || $basePath === '/') {
    $basePath = '';
}

$toAppUrl = static function (string $path) use ($basePath): string {
    if ($path === '') {
        return $basePath === '' ? '/' : $basePath . '/';
    }
    if ($path[0] !== '/') {
        $path = '/' . $path;
    }
    return $basePath === '' ? $path : $basePath . $path;
};

$manifest = [
    'name' => 'Anime & Waifu Vault',
    'short_name' => 'AnimeVault',
    'description' => 'Koleksi Anime dan Waifu Favoritmu yang Personal dan Aesthetic',
    'id' => $toAppUrl(''),
    'start_url' => $toAppUrl('/index.php'),
    'display' => 'standalone',
    'orientation' => 'portrait-primary',
    'background_color' => '#0f0a1e',
    'theme_color' => '#7c3aed',
    'lang' => 'id',
    'scope' => $toAppUrl(''),
    'categories' => ['entertainment', 'lifestyle'],
    'icons' => [
        [
            'src' => $toAppUrl('/icons/icon-192.png'),
            'sizes' => '192x192',
            'type' => 'image/png',
            'purpose' => 'any maskable'
        ],
        [
            'src' => $toAppUrl('/icons/icon-512.png'),
            'sizes' => '512x512',
            'type' => 'image/png',
            'purpose' => 'any maskable'
        ]
    ],
    'screenshots' => [
        [
            'src' => $toAppUrl('/screenshots/dashboard.png'),
            'sizes' => '390x844',
            'type' => 'image/png',
            'form_factor' => 'narrow',
            'label' => 'Dashboard Anime & Waifu Vault'
        ]
    ],
    'shortcuts' => [
        [
            'name' => 'Tambah Anime',
            'short_name' => 'Add Anime',
            'url' => $toAppUrl('/index.php?page=anime'),
            'icons' => [[
                'src' => $toAppUrl('/icons/icon-192.png'),
                'sizes' => '192x192'
            ]]
        ],
        [
            'name' => 'List Waifu',
            'short_name' => 'Waifus',
            'url' => $toAppUrl('/index.php?page=waifu'),
            'icons' => [[
                'src' => $toAppUrl('/icons/icon-192.png'),
                'sizes' => '192x192'
            ]]
        ]
    ]
];

echo json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
