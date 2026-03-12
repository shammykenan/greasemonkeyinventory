<?php
require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../model/logs_model.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=landing_page");
    exit();
}

$user_id  = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';

/* =========================
   FILTER INPUTS
========================= */
$search          = $_GET['search'] ?? '';
$date_from       = $_GET['date_from'] ?? '';
$date_to         = $_GET['date_to'] ?? '';
$action_type     = $_GET['action_type'] ?? '';
$remarks_search  = $_GET['remarks_search'] ?? '';
$per_page = max(1, (int)($_GET['per_page'] ?? 50));
$page_num    = max(1, (int)($_GET['page_num'] ?? 1));
$offset      = ($page_num - 1) * $per_page;

$totalStock_logs  = countFilteredStockLogs($pdo, $user_id, $search, $date_from, $date_to, $action_type, $remarks_search);
$totalStock_pages = (int)ceil($totalStock_logs / $per_page);
$stock_logs  = getFilteredStockLogs($pdo, $user_id, $search, $date_from, $date_to, $action_type, $remarks_search, $per_page, $offset);
$totalActivity_logs     = countFilteredActivityLogs($pdo, $user_id, $search, $date_from, $date_to);
$totalActivity_pages    = (int)ceil($totalActivity_logs / $per_page);
$activity_logs  = getFilteredActivityLogs($pdo, $user_id, $search, $date_from, $date_to, $per_page, $offset);
$deleted_stock_logs  = show_deleted_stock_log($pdo, $user_id);
$deleted_activity_logs = show_deleted_activity_logs($pdo, $user_id);

/* =========================
   HELPERS
========================= */
function getProductId(PDO $pdo, int $log_id): ?int
{
    $stmt = $pdo->prepare("SELECT product_id FROM stock_logs WHERE id = ?");
    $stmt->execute([$log_id]);
    return $stmt->fetchColumn() ?: null;
}

function redirect(string $url)
{
    header("Location: $url");
    exit();
}

/* =========================
   SINGLE STOCK LOG ACTIONS
========================= */

// Archive
if (isset($_POST['delete_stock_log'])) {
    $id = (int) $_POST['id'];

    try {
        $product_id = getProductId($pdo, $id);
        delete_stock_log($pdo, $id);

        add_activity_log($pdo, $user_id, $product_id, "Archived stock log #{$id}");
        redirect("index.php?page=stock_logs&deleted={$id}");
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
}
if (isset($_POST['delete_activity_log'])) {
    $id = (int) $_POST['id'];

    try {
        $product_id = getProductId($pdo, $id);
        delete_activity_log($pdo, $id);
        redirect("index.php?page=activity_logs&archive={$id}");
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
}

// Restore
if (isset($_POST['restore_stock_log'])) {
    $id = (int) $_POST['id'];

    try {
        $product_id = getProductId($pdo, $id);
        restore_stock_log($pdo, $id);

        add_activity_log($pdo, $user_id, $product_id, "Restored stock log #{$id}");
        redirect("index.php?page=stock_logs&restored={$id}");
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
}

// Permanent delete
if (isset($_POST['permanent_delete_stock_log'])) {
    $id = (int) $_POST['id'];

    try {
        $product_id = getProductId($pdo, $id);
        permanent_delete_stock_log($pdo, $id);

        add_activity_log($pdo, $user_id, $product_id, "Permanently deleted stock log #{$id}");
        redirect("index.php?page=stock_logs&permanently_deleted={$id}");
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
}

/* =========================
   ACTIVITY LOG ACTIONS
========================= */

if (isset($_POST['permanent_delete_activity_log'])) {
    permanent_delete_activity_log($pdo, (int) $_POST['id']);
    redirect("index.php?page=activity_logs&deleted=1");
}

if (isset($_POST['restore_activity_log'])) {
    restore_activity_log($pdo, (int) $_POST['id']);
    redirect("index.php?page=activity_logs&restored=1");
}

/* =========================
   BULK STOCK LOG ACTIONS
========================= */

// Permanently delete ALL archived stock logs
if (isset($_POST['permanent_delete_all_stock_logs'])) {
    try {
        $pdo->beginTransaction();

        // Fetch IDs to be permanently deleted
        $stmt = $pdo->query("
            SELECT id 
            FROM stock_logs 
            WHERE is_deleted = 1");
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $count = count($ids);

        if ($count > 0) {
            // Permanently delete the logs
            permanent_delete_all_stock_logs($pdo);

            // Limit ID list for logging
            $maxIdsToShow = 10;
            $shownIds = array_slice($ids, 0, $maxIdsToShow);
            $idText = implode(', ', $shownIds);
            if ($count > $maxIdsToShow) {
                $idText .= '...';
            }

            add_activity_log(
                $pdo,
                $user_id,
                null,
                "Permanently deleted {$count} stock logs (IDs: {$idText})"
            );
        }

        $pdo->commit();
        redirect("index.php?page=stock_logs&all_stock_logs_deleted=1");

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log($e->getMessage());
    }
}
if (isset($_POST['permanent_delete_all_activity_logs'])) {
    try {
        $pdo->beginTransaction();

        // Fetch IDs to be permanently deleted
        $stmt = $pdo->query("
            SELECT id 
            FROM activity_logs 
            WHERE is_deleted = 1");
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $count = count($ids);

        if ($count > 0) {
            // Permanently delete the logs
            permanent_delete_all_activity_logs($pdo);

            // Limit ID list for logging
            $maxIdsToShow = 10;
            $shownIds = array_slice($ids, 0, $maxIdsToShow);
            $idText = implode(', ', $shownIds);
            if ($count > $maxIdsToShow) {
                $idText .= '...';
            }

            add_activity_log(
                $pdo,
                $user_id,
                null,
                "Permanently deleted {$count} activity logs (IDs: {$idText})"
            );
        }

        $pdo->commit();
        redirect("index.php?page=activity_logs&all_activity_logs_deleted=1");

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log($e->getMessage());
    }
}
// Restore ALL stock logs
if (isset($_POST['restore_all_stock_logs'])) {
    try {
        $pdo->beginTransaction();

        // Fetch IDs to be restored (before update)
        $stmt = $pdo->query("
            SELECT id 
            FROM stock_logs 
            WHERE is_deleted = 1
        ");
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $count = count($ids);

        if ($count > 0) {
            restore_all_stock_logs($pdo);

            // Limit ID list to avoid huge logs
            $maxIdsToShow = 10;
            $shownIds = array_slice($ids, 0, $maxIdsToShow);

            $idText = implode(', ', $shownIds);
            if ($count > $maxIdsToShow) {
                $idText .= '...';
            }

            add_activity_log(
                $pdo,
                $user_id,
                null,
                "Restored {$count} stock logs (IDs: {$idText})"
            );
        }

        $pdo->commit();
        redirect("index.php?page=stock_logs&all_stock_logs_restored=1");

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log($e->getMessage());
    }
}
if (isset($_POST['restore_all_activity_logs'])) {
    try {
        $pdo->beginTransaction();

        // Fetch IDs to be restored (before update)
        $stmt = $pdo->query("SELECT id FROM activity_logs WHERE is_deleted = 1");
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $count = count($ids);

        if ($count > 0) {
            restore_all_activity_logs($pdo);

            // Limit ID list to avoid huge logs
            $maxIdsToShow = 10;
            $shownIds = array_slice($ids, 0, $maxIdsToShow);

            $idText = implode(', ', $shownIds);
            if ($count > $maxIdsToShow) {
                $idText .= '...';
            }

            add_activity_log(
                $pdo,
                $user_id,
                null,
                "Restored {$count} activity logs (IDs: {$idText})"
            );
        }

        $pdo->commit();
        redirect("index.php?page=activity_logs&all_activity_logs_restored=1");

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log($e->getMessage());
    }
}
/* =========================
   BULK VISIBLE STOCK LOGS
========================= */

if (isset($_POST['archive_visible_stock_logs'], $_POST['visible_log_ids'])) {

    $ids = array_filter(array_map('intval', explode(',', $_POST['visible_log_ids'])));

    if (!$ids) {
        redirect("index.php?page=stock_logs&error=no_ids");
    }

    $archived = [];

    foreach ($ids as $id) {
        delete_stock_log($pdo, $id);
        $archived[] = $id;
    }

    add_activity_log(
        $pdo,
        $user_id,
        null,
        "Archived " . count($archived) . " stock logs (IDs: " . implode(', ', $archived) . ")"
    );

    redirect("index.php?page=stock_logs&bulk_archived=" . count($archived));
}

/* =========================
   BULK ACTIVITY LOGS
========================= */

if (isset($_POST['archive_visible_activity_logs'], $_POST['visible_log_ids'])) {
    try {
        $pdo->beginTransaction();

        $ids = array_unique(array_filter(array_map('intval', explode(',', $_POST['visible_log_ids']))));

        if (!$ids) {
            redirect("index.php?page=activity_logs&error=no_ids");
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $stmt = $pdo->prepare("
            UPDATE activity_logs
            SET is_deleted = 1, deleted_at = NOW()
            WHERE id IN ($placeholders) AND is_deleted = 0
        ");
        $stmt->execute($ids);

        $count = $stmt->rowCount();

        add_activity_log(
            $pdo,
            $user_id,
            null,
            "Archived {$count} activity logs"
        );

        $pdo->commit();
        redirect("index.php?page=activity_logs&bulk_archived={$count}");
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log($e->getMessage());
    }
}
if (isset($_POST['archive_selected_stock_logs']) && isset($_POST['selected_log_ids'])) {
    try {
        $pdo->beginTransaction();
        
        $log_ids = trim($_POST['selected_log_ids']);
        
        if (empty($log_ids)) {
            header("Location: index.php?page=stock_logs&error=no_ids");
            exit();
        }
        
        // Convert to array and sanitize
        $log_ids_array = explode(',', $log_ids);
        $log_ids_array = array_filter($log_ids_array, function($id) {
            return is_numeric(trim($id)) && $id > 0;
        });
        
        if (empty($log_ids_array)) {
            header("Location: index.php?page=stock_logs&error=invalid_ids");
            exit();
        }
        
        // Remove duplicates
        $log_ids_array = array_unique($log_ids_array);
        
        // Create placeholders for IN clause
        $placeholders = implode(',', array_fill(0, count($log_ids_array), '?'));
        
        // Count logs before archiving (only visible ones)
        $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM stock_logs WHERE id IN ($placeholders) AND is_deleted = 0");
        $count_stmt->execute($log_ids_array);
        $count = $count_stmt->fetchColumn();
        
        if ($count > 0) {
            // Archive the logs
            $archive_stmt = $pdo->prepare("UPDATE stock_logs SET deleted_at = NOW(), is_deleted = 1 WHERE id IN ($placeholders) AND is_deleted = 0");
            $archive_stmt->execute($log_ids_array);
            $affected_rows = $archive_stmt->rowCount();
            
            // Add activity log
            add_activity_log(
                $pdo,
                $user_id,
                null,
                "Archived {$affected_rows} stock logs (IDs: " . implode(', ', array_slice($log_ids_array, 0, 10)) . (count($log_ids_array) > 10 ? '...' : '') . ")"
            );
        }
        
        $pdo->commit();
        
        // Redirect with success message
        header("Location: index.php?page=stock_logs&bulk_archived=" . ($count ?? 0));
        exit();
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // Log the error
        error_log("Error archiving selected stock logs: " . $e->getMessage());
        header("Location: index.php?page=stock_logs&error=archive_failed");
        exit();
    }
}
