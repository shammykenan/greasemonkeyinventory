<?php
// ajax_get_products.php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

require_once __DIR__ . '/../../config/connection.php'; // adjust path to your DB connection

$search = trim($_GET['search'] ?? '');
$category_id = trim($_GET['category_id'] ?? '');

$query = "SELECT products.*, categories.category_name, categories.id as category_id
          FROM products
          LEFT JOIN categories ON products.category_id = categories.id
          WHERE products.is_deleted = 0";

$params = [];

if ($category_id !== '') {
    $query .= " AND products.category_id = :category_id";
    $params[':category_id'] = $category_id;
}

if ($search !== '') {
    $query .= " AND (
        products.product_name LIKE :search OR
        products.sku LIKE :search OR
        products.part_number LIKE :search OR
        products.applicable_models LIKE :search OR
        products.description LIKE :search
    )";
    $params[':search'] = '%' . $search . '%';
}

$query .= " ORDER BY products.id DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode([
    'count' => count($products),
    'products' => $products
]);