<?php
session_start();
if (!isset($_SESSION['user_id'])) { http_response_code(403); exit; }

require_once __DIR__ . '/../../config/connection.php'; // adjust path

$search      = trim($_GET['search'] ?? '');
$stock_level = trim($_GET['stock_level'] ?? '');

$query  = "SELECT products.*, categories.category_name
           FROM products
           LEFT JOIN categories ON products.category_id = categories.id
           WHERE products.is_deleted = 0";
$params = [];

if ($search !== '') {
    $query .= " AND (products.product_name LIKE :search
                OR products.sku LIKE :search
                OR products.part_number LIKE :search
                OR products.applicable_models LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

if ($stock_level === 'low')       $query .= " AND products.stock > 0 AND products.stock <= 10";
elseif ($stock_level === 'out')   $query .= " AND products.stock = 0";
elseif ($stock_level === 'available') $query .= " AND products.stock > 0";

$query .= " ORDER BY products.id DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);

header('Content-Type: application/json');
echo json_encode(['products' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);