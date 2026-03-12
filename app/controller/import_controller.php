<?php
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }

require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../model/logs_model.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['sql_file'])) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
    exit;
}

$file = $_FILES['sql_file'];

// ── Validate ───────────────────────────────────────────────────
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Upload error code: ' . $file['error']]);
    exit;
}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($ext !== 'sql') {
    echo json_encode(['success' => false, 'message' => 'Only .sql files are allowed.']);
    exit;
}

if ($file['size'] > 50 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 50MB.']);
    exit;
}

// ── Read SQL ───────────────────────────────────────────────────
$sql = file_get_contents($file['tmp_name']);
if ($sql === false || empty(trim($sql))) {
    echo json_encode(['success' => false, 'message' => 'File is empty or unreadable.']);
    exit;
}

// ── Sanitize SQL ───────────────────────────────────────────────
// Unwrap MySQL conditional comments /*!XXXXX ... */ → inner content
$sql = preg_replace('/\/\*!\d+\s+(.*?)\*\//s', '$1', $sql);

// Remove any SET statements where the value is NULL (causes PDO errors)
$sql = preg_replace('/^\s*SET\s+\S+\s*=\s*NULL\s*;?\s*$/im', '', $sql);

// Remove comment-only lines and blank lines to keep statement splitting clean
$sql = preg_replace('/^--.*$/m', '', $sql);
$sql = preg_replace('/^\s*[\r\n]/m', '', $sql);

// ── Execute ────────────────────────────────────────────────────
try {
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

    // Split on semicolons followed by whitespace/newlines
    $statements = array_filter(
        array_map('trim', preg_split('/;\s*[\r\n]+/', $sql)),
        fn($s) => !empty($s)
    );

    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

    $failed = [];
    foreach ($statements as $statement) {
        $trimmed = trim($statement);
        if (empty($trimmed)) continue;

        try {
            $pdo->exec($trimmed);
        } catch (PDOException $e) {
            // Collect non-fatal errors instead of aborting the whole import
            $failed[] = [
                'statement' => substr($trimmed, 0, 120) . (strlen($trimmed) > 120 ? '...' : ''),
                'error'     => $e->getMessage(),
            ];
        }
    }

    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');

    // ── Log Activity ───────────────────────────────────────────
    $user_id = (int) $_SESSION['user_id'];
    $check = $pdo->prepare("SELECT id FROM users WHERE id = :id LIMIT 1");
    $check->execute([':id' => $user_id]);
    if ($check->fetch()) {
        try {
            add_activity_log($pdo, $user_id, null, "Imported Database Backup: " . basename($file['name']));
        } catch (Exception $e) { /* silent */ }
    }

    // Return success — include any non-fatal warnings if they occurred
    $response = [
        'success'  => true,
        'message'  => 'Database imported successfully.',
        'filename' => htmlspecialchars(basename($file['name'])),
    ];

    if (!empty($failed)) {
        $response['warnings'] = $failed;
        $response['message']  = 'Database imported with ' . count($failed) . ' warning(s). Most data was restored successfully.';
    }

    echo json_encode($response);

} catch (PDOException $e) {
    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    echo json_encode(['success' => false, 'message' => 'SQL Error: ' . $e->getMessage()]);
}
exit;