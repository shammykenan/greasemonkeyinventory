<?php
session_start();
if (!isset($_SESSION['user_id'])) { http_response_code(403); exit; }

require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../model/manage_products_model.php';

$products = get_all_products($pdo);
$total_products = count($products);
$total_inventory_value = 0;
$total_items_in_stock = 0;
$low_stock_count = 0;
$out_of_stock_count = 0;
$low_stock_products = [];
$out_of_stock_products = [];

foreach ($products as $product) {
    $total_inventory_value += $product['price'] * $product['stock'];
    $total_items_in_stock  += $product['stock'];
    if ($product['stock'] == 0) {
        $out_of_stock_count++;
        $out_of_stock_products[] = $product;
    } elseif ($product['stock'] < 10) {
        $low_stock_count++;
        $low_stock_products[] = $product;
    }
}

// Category stats
$stmt = $pdo->query("SELECT c.id, c.category_name,
    COUNT(p.id) AS product_count,
    SUM(p.stock) AS total_stock,
    SUM(p.stock * p.price) AS total_value
    FROM categories c
    LEFT JOIN products p ON c.id = p.category_id AND p.is_deleted = 0
    GROUP BY c.id, c.category_name ORDER BY c.category_name ASC");
$category_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recent stock logs
$stmt = $pdo->query("SELECT sl.*, p.product_name, u.username
    FROM stock_logs sl
    LEFT JOIN products p ON sl.product_id = p.id
    LEFT JOIN users u ON sl.user_id = u.id
    WHERE sl.is_deleted = 0 AND sl.created_at >= NOW() - INTERVAL 7 DAY
    ORDER BY sl.created_at DESC LIMIT 7");
$recent_stock_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Attention products (out + low stock)
$attention_products = array_merge($out_of_stock_products, $low_stock_products);
usort($attention_products, function($a, $b) {
    if ($a['stock'] == 0 && $b['stock'] > 0) return -1;
    if ($a['stock'] > 0 && $b['stock'] == 0) return 1;
    return $a['stock'] - $b['stock'];
});


header('Content-Type: application/json');
echo json_encode([
    'total_products'        => $total_products,
    'total_items_in_stock'  => $total_items_in_stock,
    'total_inventory_value' => number_format($total_inventory_value, 2),
    'out_of_stock_count'    => $out_of_stock_count,
    'low_stock_count'       => $low_stock_count,
    'category_products'     => $category_products,
    'attention_products'    => array_slice($attention_products, 0, 5),
    'attention_total'       => count($attention_products),
    'recent_stock_logs'     => $recent_stock_logs,
]);