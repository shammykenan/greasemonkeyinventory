<?php

// ── CREATE CATEGORY ────────────────────────────────────────────
function createCategory(PDO $pdo, string $category_name, string $sku_prefix, int $requires_oem): int
{
    $category_name = trim($category_name);
    $sku_prefix    = strtoupper(trim($sku_prefix));

    if (empty($category_name)) {
        throw new Exception("Category name cannot be empty.");
    }

    if (empty($sku_prefix)) {
        throw new Exception("SKU prefix cannot be empty.");
    }

    // Check duplicate category name
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE LOWER(category_name) = LOWER(:name) LIMIT 1");
    $stmt->execute([':name' => $category_name]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("Category '{$category_name}' already exists.");
    }

    // Check duplicate SKU prefix
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE UPPER(sku_prefix) = UPPER(:prefix) LIMIT 1");
    $stmt->execute([':prefix' => $sku_prefix]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("SKU prefix '{$sku_prefix}' is already used by another category.");
    }

    $stmt = $pdo->prepare("
        INSERT INTO categories (category_name, sku_prefix, requires_oem)
        VALUES (:name, :sku_prefix, :requires_oem)
    ");
    $stmt->execute([
        ':name'         => $category_name,
        ':sku_prefix'   => $sku_prefix,
        ':requires_oem' => $requires_oem,
    ]);

    return (int) $pdo->lastInsertId();
}

function getAllCategories(PDO $pdo): array
{
    $stmt = $pdo->prepare("
        SELECT 
            c.id,
            c.category_name,
            c.sku_prefix,
            c.requires_oem,
            c.created_at,
            (
                SELECT COUNT(*)
                FROM products p
                WHERE p.category_id = c.id
                  AND p.is_deleted = 0
            ) AS product_count
        FROM categories c
        ORDER BY c.category_name ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ── GET SINGLE CATEGORY ────────────────────────────────────────
function getCategoryById(PDO $pdo, int $id)
{
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ── GET CATEGORY NAME ──────────────────────────────────────────
function getCategoryName(PDO $pdo, int $id): string
{
    $stmt = $pdo->prepare("SELECT category_name FROM categories WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $result = $stmt->fetchColumn();
    return $result !== false ? $result : '';
}

// ── UPDATE CATEGORY ────────────────────────────────────────────
function updateCategory(PDO $pdo, int $id, string $category_name, string $sku_prefix, int $requires_oem): bool
{
    $category_name = trim($category_name);
    $sku_prefix    = strtoupper(trim($sku_prefix));

    if (empty($category_name)) {
        throw new Exception("Category name cannot be empty.");
    }

    if (empty($sku_prefix)) {
        throw new Exception("SKU prefix cannot be empty.");
    }

    // Check duplicate name excluding self
    $stmt = $pdo->prepare("
        SELECT id FROM categories
        WHERE LOWER(category_name) = LOWER(:name) AND id != :id LIMIT 1
    ");
    $stmt->execute([':name' => $category_name, ':id' => $id]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("Category '{$category_name}' already exists.");
    }

    // Check duplicate prefix excluding self
    $stmt = $pdo->prepare("
        SELECT id FROM categories
        WHERE UPPER(sku_prefix) = UPPER(:prefix) AND id != :id LIMIT 1
    ");
    $stmt->execute([':prefix' => $sku_prefix, ':id' => $id]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("SKU prefix '{$sku_prefix}' is already used by another category.");
    }

    $stmt = $pdo->prepare("
        UPDATE categories
        SET category_name = :name,
            sku_prefix    = :sku_prefix,
            requires_oem  = :requires_oem
        WHERE id = :id
    ");
    $stmt->execute([
        ':name'         => $category_name,
        ':sku_prefix'   => $sku_prefix,
        ':requires_oem' => $requires_oem,
        ':id'           => $id,
    ]);

    return $stmt->rowCount() > 0;
}

// ── DELETE CATEGORY ────────────────────────────────────────────
function deleteCategory(PDO $pdo, int $id): bool
{
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM products
        WHERE category_id = :id AND is_deleted = 0
    ");
    $stmt->execute([':id' => $id]);
    $count = (int) $stmt->fetchColumn();

    if ($count > 0) {
        throw new Exception("Cannot delete: {$count} active product(s) are using this category.");
    }

    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount() > 0;
}