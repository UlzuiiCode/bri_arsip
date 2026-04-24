<?php
/**
 * API Endpoint: Ambil notifikasi (activity logs terbaru)
 * Mengembalikan JSON untuk dropdown notifikasi.
 */

require_once __DIR__ . '/../config/app.php';
require_once BASE_PATH . '/config/database.php';

header('Content-Type: application/json; charset=utf-8');

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $db = getDBConnection();

    $limit    = 10;
    $isAdmin  = (($_SESSION['user_role'] ?? '') === 'admin');
    $userId   = (int) ($_SESSION['user_id'] ?? 0);

    if ($isAdmin) {
        // Admin: tampilkan semua aktivitas
        $stmt = $db->prepare(
            "SELECT al.id, al.action, al.description, al.created_at, u.nama AS nama_pengguna, u.role AS role_pengguna
             FROM activity_logs al
             LEFT JOIN users u ON al.user_id = u.id
             ORDER BY al.created_at DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    } else {
        // Pegawai: hanya aktivitas milik sendiri
        $stmt = $db->prepare(
            "SELECT al.id, al.action, al.description, al.created_at, u.nama AS nama_pengguna, u.role AS role_pengguna
             FROM activity_logs al
             LEFT JOIN users u ON al.user_id = u.id
             WHERE al.user_id = :user_id
             ORDER BY al.created_at DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    }
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format data
    $notifications = [];
    foreach ($logs as $log) {
        // Tentukan icon berdasarkan action
        $icon = 'activity';
        $color = 'blue';
        switch ($log['action']) {
            case 'LOGIN':
                $icon = 'log-in';
                $color = 'green';
                break;
            case 'LOGOUT':
                $icon = 'log-out';
                $color = 'slate';
                break;
            case 'LOGIN_FAILED':
                $icon = 'alert-triangle';
                $color = 'red';
                break;
            case 'UPLOAD_DOCUMENT':
            case 'CREATE_DOCUMENT':
                $icon = 'upload';
                $color = 'blue';
                break;
            case 'DELETE_DOCUMENT':
                $icon = 'trash-2';
                $color = 'red';
                break;
            case 'RESTORE_DOCUMENT':
                $icon = 'rotate-ccw';
                $color = 'green';
                break;
            case 'BULK_DELETE_DOCUMENT':
                $icon = 'trash';
                $color = 'red';
                break;
            case 'FORCE_DELETE_DOCUMENT':
                $icon = 'x-circle';
                $color = 'red';
                break;
            case 'BULK_DOWNLOAD':
                $icon = 'download';
                $color = 'blue';
                break;
            case 'EXPORT_CSV':
            case 'EXPORT_PDF':
                $icon = 'file-text';
                $color = 'emerald';
                break;
            case 'UPDATE_PROFILE':
                $icon = 'user';
                $color = 'amber';
                break;
            case 'CREATE_USER':
                $icon = 'user-plus';
                $color = 'green';
                break;
            case 'DELETE_USER':
                $icon = 'user-minus';
                $color = 'red';
                break;
            case 'UPDATE_USER':
                $icon = 'edit';
                $color = 'amber';
                break;
            case 'REGISTER':
                $icon = 'user-check';
                $color = 'emerald';
                break;
        }

        $notifications[] = [
            'id'          => (int) $log['id'],
            'action'      => $log['action'],
            'description' => $log['description'],
            'user'        => $log['nama_pengguna'] ?? 'Sistem',
            'role'        => $log['role_pengguna'] ?? '',
            'icon'        => $icon,
            'color'       => $color,
            'time'        => $log['created_at'],
            'time_ago'    => timeAgo($log['created_at']),
        ];
    }

    echo json_encode(['notifications' => $notifications]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Gagal memuat notifikasi.']);
}

/**
 * Helper: Format waktu relatif (misal "5 menit lalu")
 */
function timeAgo(string $datetime): string
{
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
