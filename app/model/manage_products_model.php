<?php

function add_product(
    PDO $pdo,
    int $category_id,
    string $name,
    ?string $part_number,
    ?string $applicable_models,
    string $description,
    float $price,
    ?string $product_image
) {
    // Get category with prefix and OEM flag
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $category_id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$category) {
        throw new Exception('Invalid category.');
    }

    $prefix      = $category['sku_prefix'];
    $requiresOem = (bool) $category['requires_oem'];

    // OEM validation
    if ($requiresOem && empty($part_number)) {
        throw new Exception('OEM Part Number is REQUIRED for this category.');
    }

    // Safe SKU generation
    $stmt = $pdo->prepare("
        SELECT MAX(CAST(SUBSTRING(sku, LENGTH(:prefix) + 2) AS UNSIGNED)) AS max_num
        FROM products
        WHERE sku LIKE :like_prefix
    ");
    $stmt->execute([
        ':prefix'      => $prefix,
        ':like_prefix' => $prefix . '-%'
    ]);

    $maxNumber  = (int) ($stmt->fetch(PDO::FETCH_ASSOC)['max_num'] ?? 0);
    $nextNumber = $maxNumber + 1;
    $sku        = $prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

    $normalizedPartNumber = !empty($part_number) ? trim($part_number) : null;

    $stmt = $pdo->prepare("
        INSERT INTO products (sku, category_id, product_name, part_number, applicable_models, description, price, product_image)
        VALUES (:sku, :category_id, :product_name, :part_number, :applicable_models, :description, :price, :product_image)
    ");

    try {
        $stmt->execute([
            ':sku'               => $sku,
            ':category_id'       => $category_id,
            ':product_name'      => $name,
            ':part_number'       => $normalizedPartNumber,
            ':applicable_models' => $applicable_models,
            ':description'       => $description,
            ':price'             => $price,
            ':product_image'     => $product_image
        ]);
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            throw new Exception('Duplicate SKU or OEM Part Number detected.');
        }
        throw $e;
    }

    return (int) $pdo->lastInsertId();
}

function get_product_by_sku(PDO $pdo, string $sku)
{
    $query = "SELECT * FROM products WHERE sku = :sku AND is_deleted = 0";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':sku' => $sku]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_product_by_id(PDO $pdo, int $id)
{
    $query = "SELECT * FROM products WHERE id = :id AND is_deleted = 0";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// NEW: Get product by ID including deleted ones (for restore/permanent delete operations)
function get_product_by_id_including_deleted(PDO $pdo, int $id)
{
    $query = "SELECT * FROM products WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

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

function update_product(
    PDO $pdo,
    int $id,
    int $category_id,
    string $name,
    ?string $part_number,
    ?string $sku,
    ?string $applicable_models,
    string $description,
    float $price,
    ?string $product_image
) {
    $query = "UPDATE products 
              SET category_id = :category_id,
                  product_name = :name,
                  part_number = :part_number,
                  sku = :sku,
                  applicable_models = :applicable_models,
                  description = :description,
                  price = :price,
                  product_image = :product_image
              WHERE id = :id";

    $stmt = $pdo->prepare($query);
    return $stmt->execute([
        ':id'                => $id,
        ':category_id'       => $category_id,
        ':name'              => $name,
        ':part_number'       => $part_number,  // NULL allowed
        ':sku'               => $sku,
        ':applicable_models' => $applicable_models,
        ':description'       => $description,
        ':price'             => $price,
        ':product_image'     => $product_image
    ]);
}


function delete_product(PDO $pdo, int $id, int $user_id)
{
    $product = get_product_by_id($pdo, $id);
    if (!$product) return false;

    $pdo->beginTransaction();
    
    try {
        // Record stock log for archiving (stock remains the same)
        $balance = (int) $product['stock'];
        $stmt = $pdo->prepare("
            INSERT INTO stock_logs 
            (product_id, action, quantity, balance_before, balance_after, user_id, remarks)
            VALUES (:product_id, 'ARCHIVE', :quantity, :balance_before, :balance_after, :user_id, 'Product archived')
        ");
        $stmt->execute([
            ':product_id' => $id,
            ':quantity' => $balance,
            ':balance_before' => $balance,
            ':balance_after'  => 0,
            ':user_id' => $user_id
        ]);

        // Soft delete the product
        $stmt = $pdo->prepare("
            UPDATE products
            SET is_deleted = 1,
                deleted_at = NOW()
            WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);

        $pdo->commit();
        return $stmt->rowCount() > 0;

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function permanent_delete_product(PDO $pdo, int $id)
{
    // Delete all stock logs associated with this product
    $stmt = $pdo->prepare("DELETE FROM stock_logs WHERE product_id = :id");
    $stmt->execute([':id' => $id]);
    
    // Permanently delete the product
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    
    return $stmt->rowCount() > 0;
}

function restore_product(PDO $pdo, int $id, int $user_id)
{
    // Get the archived product
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) return false;

    $pdo->beginTransaction();

    try {
        // Restore the product
        $stmt = $pdo->prepare("
            UPDATE products
            SET is_deleted = 0,
                deleted_at = NULL
            WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);

        // Record stock log as Stock IN
        if ((int)$product['stock'] > 0) {
            $stock = (int)$product['stock'];
            $stmt = $pdo->prepare("
                INSERT INTO stock_logs
                (product_id, action, quantity, balance_before, balance_after, user_id, remarks)
                VALUES (:product_id, 'IN', :quantity, :balance_before, :balance_after, :user_id, 'Product restored - stock added')
            ");
            $stmt->execute([
                ':product_id'    => $id,
                ':quantity'      => $stock,
                ':balance_before'=> 0,
                ':balance_after' => $stock,
                ':user_id'       => $user_id
            ]);
        }

        $pdo->commit();
        return true;

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}
function get_all_categories(PDO $pdo)
{
    return getAllCategories($pdo);
}
// Add this function to manage_products_model.php

function get_filtered_products_for_print($pdo, $category_id = null, $search_term = '') {
    $query = "SELECT p.*, c.category_name 
              FROM products p
              LEFT JOIN categories c ON p.category_id = c.id
              WHERE p.is_deleted = 0";
    
    $params = [];
    
    if ($category_id) {
        $query .= " AND p.category_id = ?";
        $params[] = $category_id;
    }
    
    if (!empty($search_term)) {
        $query .= " AND (p.product_name LIKE ? 
                        OR p.sku LIKE ? 
                        OR p.part_number LIKE ? 
                        OR p.applicable_models LIKE ?)";
        $search_param = "%{$search_term}%";
        array_push($params, $search_param, $search_param, $search_param, $search_param);
    }
    
    $query .= " ORDER BY c.category_name, p.product_name";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}