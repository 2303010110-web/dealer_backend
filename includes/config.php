<?php
// ============================================================
// DEALER MITSUBISHI — Config & Database Connection
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dealer_mitsubishi');
define('SITE_NAME', 'Dealer Mitsubishi');
define('SITE_TAGLINE', 'Showroom Mitsubishi Premium');
define('SITE_URL', 'http://localhost/mitsubishi');
define('WA_NUMBER', '6281234567890');

// ============================================================
// Database Connection (MySQLi)
// ============================================================
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die('<div style="font-family:sans-serif;padding:40px;text-align:center;background:#fef2f2;color:#b91c1c;">
        <h2>⚠ Koneksi Database Gagal</h2>
        <p>Pastikan database <strong>' . DB_NAME . '</strong> sudah dibuat dan konfigurasi benar.</p>
        <small>' . $conn->connect_error . '</small>
    </div>');
}
$conn->set_charset('utf8mb4');

// ============================================================
// Session
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
// Helpers
// ============================================================
function rupiah(int $angka): string {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function sanitize(string $str): string {
    return htmlspecialchars(strip_tags(trim($str)));
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

function isAdminLoggedIn(): bool {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function requireAdmin(): void {
    if (!isAdminLoggedIn()) {
        redirect('/mitsubishi/admin/login.php');
    }
}

function getSetting(mysqli $conn, string $key, string $default = ''): string {
    $stmt = $conn->prepare("SELECT nilai FROM pengaturan WHERE kunci = ?");
    $stmt->bind_param('s', $key);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    return $res ? $res['nilai'] : $default;
}

/**
 * Menghasilkan URL gambar yang benar.
 * - Jika gambar berupa URL eksternal (http/https), langsung kembalikan.
 * - Jika path lokal (assets/uploads/...), tambahkan base path.
 * - Jika kosong, kembalikan placeholder SVG.
 */
function imgUrl(string $gambar, string $basePath = '/mitsubishi/'): string {
    $gambar = trim($gambar);
    if (!$gambar) {
        return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='600' height='400'%3E%3Crect fill='%23f1f5f9' width='600' height='400'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' fill='%239ca3af' dy='.3em' font-size='18' font-family='sans-serif'%3ENo Image%3C/text%3E%3C/svg%3E";
    }
    // URL eksternal
    if (str_starts_with($gambar, 'http://') || str_starts_with($gambar, 'https://')) {
        return $gambar;
    }
    // Path lokal — buang slash awal jika ada
    $gambar = ltrim($gambar, '/');
    return $basePath . $gambar;
}

function generateKodeBooking(): string {
    return 'MIT-' . strtoupper(substr(md5(uniqid()), 0, 8));
}

function timeAgo(string $datetime): string {
    $now  = new DateTime();
    $past = new DateTime($datetime);
    $diff = $now->diff($past);
    if ($diff->y > 0) return $diff->y . ' tahun lalu';
    if ($diff->m > 0) return $diff->m . ' bulan lalu';
    if ($diff->d > 0) return $diff->d . ' hari lalu';
    if ($diff->h > 0) return $diff->h . ' jam lalu';
    if ($diff->i > 0) return $diff->i . ' menit lalu';
    return 'Baru saja';
}
