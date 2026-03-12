<?php

function showstock_logs(PDO $pdo, int $user_id)
{
    $stmt = $pdo->prepare("SELECT 
            s.id,
            s.product_id,
            s.action,
            s.quantity,
            s.remarks,
            s.created_at,
            s.user_id,
            u.username, 
            u.id as user_table_id,
            p.product_name as product_name,
            p.part_number,
            p.applicable_models
        FROM stock_logs as s 
        JOIN users as u ON u.id = s.user_id 
        LEFT JOIN products as p ON p.id = s.product_id
        WHERE s.user_id = :user_id 
        ORDER BY s.created_at DESC
    ");

    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ── PAGINATED: count total matching rows ─────────────────────────────────────
function countFilteredStockLogs(PDO $pdo, int $user_id, string $search = '', string $date_from = '', string $date_to = '', string $action_type = '', string $remarks_search = ''): int
{
    $sql = "SELECT COUNT(*)
        FROM stock_logs AS s
        JOIN users AS u ON u.id = s.user_id
        LEFT JOIN products AS p ON p.id = s.product_id
        WHERE s.user_id = :user_id
          AND s.is_deleted = 0
    ";

    $params = [':user_id' => $user_id];

    if (!empty($search)) {
        $sql .= " AND (
            p.product_name LIKE :search 
            OR p.part_number LIKE :search
            OR p.applicable_models LIKE :search
            OR u.username LIKE :search 
            OR s.action LIKE :search
        )";
        $params[':search'] = '%' . $search . '%';
    }

    if (!empty($remarks_search)) {
        $sql .= " AND s.remarks LIKE :remarks_search";
        $params[':remarks_search'] = '%' . $remarks_search . '%';
    }

    if (!empty($date_from)) {
        $sql .= " AND DATE(s.created_at) >= :date_from";
        $params[':date_from'] = $date_from;
    }

    if (!empty($date_to)) {
        $sql .= " AND DATE(s.created_at) <= :date_to";
        $params[':date_to'] = $date_to;
    }

    if (!empty($action_type)) {
        $sql .= " AND s.action = :action_type";
        $params[':action_type'] = $action_type;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

// ── PAGINATED: fetch one page of results ────────────────────────────────────
function getFilteredStockLogs(PDO $pdo, int $user_id, string $search = '', string $date_from = '', string $date_to = '', $action_type = '', string $remarks_search = '', int $limit = 50, int $offset = 0)
{
    $sql = "SELECT 
            s.id,
            s.product_id,
            s.action,
            s.quantity,
            s.balance_before,
            s.balance_after,
            s.remarks,
            s.created_at,
            s.user_id,
            u.username, 
            u.id AS user_table_id,
            p.product_name AS product_name,
            p.part_number,
            p.applicable_models
        FROM stock_logs AS s
        JOIN users AS u ON u.id = s.user_id
        LEFT JOIN products AS p ON p.id = s.product_id
        WHERE s.user_id = :user_id
          AND s.is_deleted = 0
    ";

    $params = [':user_id' => $user_id];

    if (!empty($search)) {
        $sql .= " AND (
            p.product_name LIKE :search 
            OR p.part_number LIKE :search
            OR p.applicable_models LIKE :search
            OR u.username LIKE :search 
            OR s.action LIKE :search
        )";
        $params[':search'] = '%' . $search . '%';
    }

    if (!empty($remarks_search)) {
        $sql .= " AND s.remarks LIKE :remarks_search";
        $params[':remarks_search'] = '%' . $remarks_search . '%';
    }

    if (!empty($date_from)) {
        $sql .= " AND DATE(s.created_at) >= :date_from";
        $params[':date_from'] = $date_from;
    }

    if (!empty($date_to)) {
        $sql .= " AND DATE(s.created_at) <= :date_to";
        $params[':date_to'] = $date_to;
    }

    if (!empty($action_type)) {
        $sql .= " AND s.action = :action_type";
        $params[':action_type'] = $action_type;
    }

    $sql .= " ORDER BY s.created_at DESC";
    $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function delete_stock_log(PDO $pdo, int $id)
{
    $stmt = $pdo->prepare("UPDATE stock_logs SET is_deleted = 1, deleted_at = NOW() WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

function archive_all_stock_logs(PDO $pdo)
{
    $stmt = $pdo->prepare("UPDATE stock_logs SET is_deleted = 1, deleted_at = NOW() WHERE is_deleted = 0");
    $stmt->execute();
}

function archive_all_activity_logs(PDO $pdo)
{
    $stmt = $pdo->prepare("UPDATE activity_logs SET is_deleted = 1, deleted_at = NOW() WHERE is_deleted = 0");
    $stmt->execute();
}

function permanent_delete_stock_log(PDO $pdo, int $id)
{
    $stmt = $pdo->prepare("DELETE FROM stock_logs WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount() > 0;
}

function restore_stock_log(PDO $pdo, int $id)
{
    $stmt = $pdo->prepare("UPDATE stock_logs SET is_deleted = 0, deleted_at = NULL WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount() > 0;
}

function show_deleted_stock_log(PDO $pdo, int $user_id)
{
    $stmt = $pdo->prepare("SELECT 
            s.id,
            s.product_id,
            s.action,
            s.quantity,
            s.balance_before,
            s.balance_after,
            s.remarks,
            s.created_at,
            s.deleted_at,
            s.user_id,
            u.username, 
            u.id as user_table_id,
            p.product_name as product_name,
            p.part_number,
            p.applicable_models
        FROM stock_logs as s 
        JOIN users as u ON u.id = s.user_id 
        LEFT JOIN products as p ON p.id = s.product_id
        WHERE s.user_id = :user_id 
          AND s.is_deleted = 1
        ORDER BY s.deleted_at DESC
        LIMIT 200
    ");

    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ACTIVITY LOGS ──────────────────────────────────────────────────────────────

function add_activity_log(PDO $pdo, $user_id, $product_id, $activity)
{
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, product_id, activity) VALUES (:user_id, :product_id, :activity)");
    $stmt->execute([
        ':user_id'    => $user_id,
        ':product_id' => $product_id,
        ':activity'   => $activity,
    ]);
}

function show_activity_logs(PDO $pdo, int $user_id)
{
    $stmt = $pdo->prepare("SELECT 
            a.id,
            a.activity,
            a.created_at,
            a.user_id,
            u.username,
            u.id AS user_table_id
        FROM activity_logs AS a
        JOIN users AS u ON u.id = a.user_id
        WHERE a.user_id = :user_id
          AND a.is_deleted = 0
        ORDER BY a.created_at DESC
    ");
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ── PAGINATED: count total matching rows ─────────────────────────────────────
function countFilteredActivityLogs(PDO $pdo, int $user_id, string $search = '', string $date_from = '', string $date_to = ''): int
{
    $sql = "SELECT COUNT(*)
        FROM activity_logs AS a
        JOIN users AS u ON u.id = a.user_id
        WHERE a.user_id = :user_id
          AND a.is_deleted = 0
    ";

    $params = [':user_id' => $user_id];

    if (!empty($search)) {
        $sql .= " AND (a.activity LIKE :search OR u.username LIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    if (!empty($date_from)) {
        $sql .= " AND DATE(a.created_at) >= :date_from";
        $params[':date_from'] = $date_from;
    }

    if (!empty($date_to)) {
        $sql .= " AND DATE(a.created_at) <= :date_to";
        $params[':date_to'] = $date_to;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

// ── PAGINATED: fetch one page of results ────────────────────────────────────
function getFilteredActivityLogs(PDO $pdo, int $user_id, string $search = '', string $date_from = '', string $date_to = '', int $limit = 50, int $offset = 0)
{
    $sql = "SELECT 
            a.id,
            a.activity,
            a.created_at,
            a.user_id,
            u.username,
            u.id AS user_table_id
        FROM activity_logs AS a
        JOIN users AS u ON u.id = a.user_id
        WHERE a.user_id = :user_id
          AND a.is_deleted = 0
    ";

    $params = [':user_id' => $user_id];

    if (!empty($search)) {
        $sql .= " AND (a.activity LIKE :search OR u.username LIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    if (!empty($date_from)) {
        $sql .= " AND DATE(a.created_at) >= :date_from";
        $params[':date_from'] = $date_from;
    }

    if (!empty($date_to)) {
        $sql .= " AND DATE(a.created_at) <= :date_to";
        $params[':date_to'] = $date_to;
    }

    $sql .= " ORDER BY a.created_at DESC";
    $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function delete_activity_log(PDO $pdo, int $id)
{
    $stmt = $pdo->prepare("UPDATE activity_logs SET is_deleted = 1, deleted_at = NOW() WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

function permanent_delete_all_stock_logs(PDO $pdo)
{
    $stmt = $pdo->prepare("DELETE FROM stock_logs WHERE is_deleted = 1");
    $stmt->execute();
}

function restore_all_stock_logs(PDO $pdo)
{
    $stmt = $pdo->prepare("UPDATE stock_logs SET is_deleted = 0, deleted_at = NULL WHERE is_deleted = 1");
    return $stmt->execute();
}

function permanent_delete_all_activity_logs(PDO $pdo)
{
    $stmt = $pdo->prepare("DELETE FROM activity_logs WHERE is_deleted = 1");
    $stmt->execute();
}

function permanent_delete_activity_log(PDO $pdo, int $id)
{
    $stmt = $pdo->prepare("DELETE FROM activity_logs WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount() > 0;
}

function restore_all_activity_logs(PDO $pdo)
{
    $stmt = $pdo->prepare("UPDATE activity_logs SET is_deleted = 0, deleted_at = NULL WHERE is_deleted = 1");
    return $stmt->execute();
}

function restore_activity_log(PDO $pdo, int $id)
{
    $stmt = $pdo->prepare("UPDATE activity_logs SET is_deleted = 0, deleted_at = NULL WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount() > 0;
}

function show_deleted_activity_logs(PDO $pdo, int $user_id)
{
    $stmt = $pdo->prepare("SELECT 
            a.id,
            a.activity,
            a.created_at,
            a.deleted_at,
            a.user_id,
            u.username,
            a.product_id,
            p.product_name,
            u.id AS user_table_id
        FROM activity_logs AS a
        JOIN users AS u ON u.id = a.user_id
        LEFT JOIN products AS p ON a.product_id = p.id
        WHERE a.user_id = :user_id
          AND a.is_deleted = 1
        ORDER BY a.deleted_at DESC
        LIMIT 200
    ");

    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_activity_logs_by_date($pdo, $from = null, $to = null, $search = '')
{
    $sql = "SELECT al.*, u.username 
            FROM activity_logs al
            LEFT JOIN users u ON al.user_id = u.id
            WHERE al.is_deleted = 0";

    $params = [];

    if ($from && $to) {
        $sql .= " AND al.created_at BETWEEN :from AND :to";
        $params[':from'] = $from;
        $params[':to']   = $to;
    }

    if ($search !== '') {
        $sql .= " AND (al.activity LIKE :search OR u.username LIKE :search)";
        $params[':search'] = "%$search%";
    }

    $sql .= " ORDER BY al.created_at ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_stock_logs_by_date(PDO $pdo, $from = null, $to = null, $search = '', $action_type = '', $remarks_search = '')
{
    $sql = "SELECT s.id, s.product_id, s.action, s.quantity, s.balance_before, s.balance_after, s.remarks, s.created_at,
                   p.product_name, p.part_number
            FROM stock_logs AS s
            LEFT JOIN products AS p ON p.id = s.product_id
            WHERE s.is_deleted = 0";

    $params = [];

    if ($from) {
        $sql .= " AND s.created_at >= :from";
        $params[':from'] = $from;
    }
    if ($to) {
        $sql .= " AND s.created_at <= :to";
        $params[':to'] = $to;
    }

    if (!empty($search)) {
        $sql .= " AND (p.product_name LIKE :search OR p.part_number LIKE :search OR s.action LIKE :search)";
        $params[':search'] = "%$search%";
    }

    if (!empty($remarks_search)) {
        $sql .= " AND s.remarks LIKE :remarks_search";
        $params[':remarks_search'] = "%$remarks_search%";
    }

    if (!empty($action_type)) {
        $sql .= " AND s.action = :action_type";
        $params[':action_type'] = strtoupper($action_type);
    }

    $sql .= " ORDER BY s.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}