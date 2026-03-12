<?php

function get_all_products(PDO $pdo)
{
    $query = "SELECT products.*, categories.category_name
              FROM products
              LEFT JOIN categories ON products.category_id = categories.id
              WHERE products.is_deleted = 0
              ORDER BY products.created_at DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function add_stock(PDO $pdo, int $product_id, int $qty, int $user_id = null) {
    try {
        $pdo->beginTransaction();

        // 1. Get current stock (balance_before)
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = :id");
        $stmt->execute([':id' => $product_id]);
        $balance_before = (int) $stmt->fetchColumn();

        // 2. Update stock
        $stmt = $pdo->prepare("UPDATE products SET stock = stock + :qty WHERE id = :id");
        $stmt->execute([':qty' => $qty, ':id' => $product_id]);

        if ($stmt->rowCount() === 0) {
            throw new Exception("Product not found.");
        }

        // 3. Get new stock (balance_after)
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = :id");
        $stmt->execute([':id' => $product_id]);
        $balance_after = (int) $stmt->fetchColumn();

        // 4. Insert log
        $stmt = $pdo->prepare("
            INSERT INTO stock_logs 
            (product_id, action, quantity, balance_before, balance_after, user_id, remarks)
            VALUES (:product_id, 'IN', :qty, :balance_before, :balance_after, :user_id, 'Stock added')
        ");
        $stmt->execute([
            ':product_id' => $product_id,
            ':qty' => $qty,
            ':balance_before' => $balance_before,
            ':balance_after' => $balance_after,
            ':user_id' => $user_id
        ]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}
function decrease_stock(PDO $pdo, int $product_id, int $qty, string $reason, int $user_id = null) {
    $allowed_reasons = ['SALE','REPAIR','DAMAGED','LOST','RETURN','ADJUSTMENT'];

    if (!in_array($reason, $allowed_reasons)) {
        throw new Exception('Invalid stock-out reason.');
    }

    try {
        $pdo->beginTransaction();

        // Get stock before
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = :id");
        $stmt->execute([':id' => $product_id]);
        $balance_before = (int) $stmt->fetchColumn();

        // Update stock
        $stmt = $pdo->prepare("UPDATE products
            SET stock = stock - :qty
            WHERE id = :id AND stock >= :qty");
        $stmt->execute([':qty' => $qty, ':id' => $product_id]);

        if ($stmt->rowCount() === 0) {
            $pdo->rollBack();
            throw new Exception('Insufficient stock or product not found.');
        }

        // Get stock after
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = :id");
        $stmt->execute([':id' => $product_id]);
        $balance_after = (int) $stmt->fetchColumn();

        // Insert log
        $stmt = $pdo->prepare("
            INSERT INTO stock_logs
            (product_id, action, quantity, balance_before, balance_after, user_id, remarks)
            VALUES (:product_id, 'OUT', :qty, :balance_before, :balance_after, :user_id, :remarks)
        ");
        $stmt->execute([
            ':product_id' => $product_id,
            ':qty' => $qty,
            ':balance_before' => $balance_before,
            ':balance_after' => $balance_after,
            ':user_id' => $user_id,
            ':remarks' => $reason
        ]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}
