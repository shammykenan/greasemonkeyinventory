<?php
require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../model/manage_products_model.php';
require_once __DIR__ . '/../model/category_model.php';
require_once __DIR__ . '/../model/logs_model.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=landing_page");
    exit();
}
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';
$role = $_SESSION['role'] ?? 'staff';
$error = '';
$success = '';

if (isset($_POST['add_product'])) {

    $name              = trim($_POST['product_name']);
    $part_number_input = trim($_POST['part_number']);
    $part_number       = $part_number_input !== '' ? $part_number_input : null;
    $applicable_models = trim($_POST['applicable_models']) ?: null;
    $description       = trim($_POST['description']);
    $price             = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    $category_id       = (int) $_POST['category_id'];

    /* ================================
       Basic validation
    ================================= */
    if (empty($name)) {
        $error = "Product name is required.";
    } elseif ($price === false || $price <= 0) {
        $error = "Price must be greater than 0.";
    }

    /* ================================
       Image upload
    ================================= */
    $imageName = null;

    if (empty($error) && !empty($_FILES['product_image']['name'])) {

        $uploadDir = __DIR__ . '/../../assets/images/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ];

        $fileType = mime_content_type($_FILES['product_image']['tmp_name']);

        if (!in_array($fileType, $allowedTypes, true)) {
            $error = "Invalid image type. Only JPG, PNG, GIF, and WEBP are allowed.";
        } else {
            $imageName  = time() . '_' . basename($_FILES['product_image']['name']);
            $targetPath = $uploadDir . $imageName;

            if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $targetPath)) {
                $error = "Failed to upload image.";
                $imageName = null;
            }
        }
    }

    /* ================================
       Insert product
    ================================= */
    if (empty($error)) {
        try {
            $newId = add_product(
                $pdo,
                $category_id,
                $name,
                $part_number,          // NULL for non-OEM
                $applicable_models,
                $description,
                $price,
                $imageName
            );

            add_activity_log(
                $pdo,
                $user_id,
                $newId,
                "Added product #$newId - $name" .
                ($part_number ? " (OEM: $part_number)" : "")
            );

            header("Location: index.php?page=manage_products&added=1");
            exit;

        } catch (Exception $e) {
            $error = "Failed to add product: " . $e->getMessage();
        }
    }
}
if (isset($_POST['update_product'])) {

    // ===========================
    // 1. Sanitize inputs
    // ===========================
    $id                = (int) ($_POST['product_id'] ?? 0);
    $name              = trim($_POST['product_name'] ?? '');
    $part_number_input = trim($_POST['part_number'] ?? '');
    $applicable_models = trim($_POST['applicable_models'] ?? '') ?: null;
    $description       = trim($_POST['description'] ?? '');
    $price             = filter_var($_POST['price'] ?? null, FILTER_VALIDATE_FLOAT);
    $category_id       = (int) ($_POST['category_id'] ?? 0);

    $error = '';

    // ===========================
    // 2. Basic validation
    // ===========================
    if (empty($name)) {
        $error = "Product name is required.";
    } elseif ($price === false || $price <= 0) {
        $error = "Price must be greater than 0.";
    }

    // ===========================
    // 3. Fetch current product
    // ===========================
    if (empty($error)) {
        $stmt = $pdo->prepare("SELECT category_id, sku, product_image, part_number FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $currentProduct = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$currentProduct) {
            $error = "Product not found.";
        }
    }

    // ===========================
    // 4. Check for duplicate part number BEFORE updating
    // ===========================
    if (empty($error) && !empty($part_number_input)) {
        // Check if this part number already exists in another product
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE part_number = :part_number AND id != :id");
        $stmt->execute([
            ':part_number' => $part_number_input,
            ':id' => $id
        ]);
        
        if ($stmt->fetchColumn() > 0) {
            $error = "⚠️ Update failed! Part number '$part_number_input' already exists. Each OEM part number must be unique.";
        }
    }

    // ===========================
    // 5. Determine SKU and validate part number requirements
    // ===========================
    if (empty($error)) {
        $old_category_id = (int)$currentProduct['category_id'];
        $category_changed = $category_id !== $old_category_id;

        // Fetch category name
        $stmt = $pdo->prepare("SELECT category_name FROM categories WHERE id = :id");
        $stmt->execute([':id' => $category_id]);
        $category_name = $stmt->fetchColumn() ?? 'Product';

        // OEM required categories
        $oemRequiredCategories = [
            'ABS Module',
            'Transmission',
            'Water Pump'
        ];

        // Enforce OEM part number if required
        if (in_array($category_name, $oemRequiredCategories, true) && empty($part_number_input)) {
            $error = "⚠️ Update failed! OEM Part Number is REQUIRED for " . htmlspecialchars($category_name) . " category.";
        }

        // Only proceed if no error
        if (empty($error)) {
            // Keep part_number as user entered
            $part_number = $part_number_input ?: null;

            // SKU prefix mapping
            $prefixes = [
                'ABS Module'                  => 'ABS',
                'Transmission'                => 'TRANS',
                'Water Pump'                  => 'WP',
                'Fluids'                      => 'FLUID',
                'Timing Components'           => 'TIM',
                'Brake Components'            => 'BRK',
                'Cooling System'              => 'COOL'
            ];
            $prefix = $prefixes[$category_name] ?? 'PROD';

            // Generate new SKU if category changed
            if ($category_changed) {
                $stmt = $pdo->prepare("
                    SELECT MAX(CAST(SUBSTRING(sku, LENGTH(:prefix) + 2) AS UNSIGNED)) AS max_num
                    FROM products
                    WHERE sku LIKE :like_prefix
                ");
                $stmt->execute([
                    ':prefix' => $prefix,
                    ':like_prefix' => $prefix . '-%'
                ]);
                $max = (int)($stmt->fetchColumn() ?? 0);

                $sku = $prefix . '-' . str_pad($max + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $sku = $currentProduct['sku']; // keep existing SKU
            }
        }
    }

    // ===========================
    // 6. Handle image upload
    // ===========================
    $imageName = $currentProduct['product_image'] ?? null;

    // Handle remove image flag
        if (empty($error) && ($_POST['remove_image'] ?? '0') === '1') {
            $uploadDir = __DIR__ . '/../../assets/images/';
            if ($imageName && file_exists($uploadDir . $imageName)) {
                unlink($uploadDir . $imageName);
            }
            $imageName = null;
        }

        if (empty($error) && !empty($_FILES['product_image']['name'])) {
        $uploadDir = __DIR__ . '/../../assets/images/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $allowedTypes = ['image/jpeg','image/png','image/gif','image/webp'];
        $fileType = mime_content_type($_FILES['product_image']['tmp_name']);

        if (!in_array($fileType, $allowedTypes, true)) {
            $error = "Invalid image type. Only JPG, PNG, GIF, and WEBP are allowed.";
        } else {
            $newImageName = time() . '_' . basename($_FILES['product_image']['name']);
            $targetPath = $uploadDir . $newImageName;

            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetPath)) {
                if ($imageName && file_exists($uploadDir . $imageName)) {
                    unlink($uploadDir . $imageName); // delete old image
                }
                $imageName = $newImageName;
            } else {
                $error = "Failed to upload new image.";
            }
        }
    }

    // ===========================
    // 7. Update product in DB
    // ===========================
    if (empty($error)) {
        try {
            update_product(
                $pdo,
                $id,
                $category_id,
                $name,
                $part_number,  // user-entered OEM part number
                $sku,          // generated SKU
                $applicable_models,
                $description,
                $price,
                $imageName
            );

            $partNumberDisplay = $currentProduct['part_number'] ? " (Part Number: {$currentProduct['part_number']})" : "";
            add_activity_log($pdo, $user_id, $id, "Updated product #$id - $name$partNumberDisplay");

            // Store success in session and redirect
            $_SESSION['success'] = "Product #$id updated successfully!";
            header("Location: index.php?page=manage_products");
            exit;

        } catch (PDOException $e) {
            // Catch database errors including duplicate entries
            if ($e->getCode() == '23000' && strpos($e->getMessage(), 'part_number') !== false) {
                $error = "⚠️ Update failed! Part number '$part_number_input' already exists. Each OEM part number must be unique.";
            } else {
                $error = "⚠️ Update failed: " . $e->getMessage();
            }
        } catch (Exception $e) {
            $error = "⚠️ Update failed: " . $e->getMessage();
        }
    }
    
    // If there was an error, store it in session and redirect
    if (!empty($error)) {
        $_SESSION['error'] = $error;
        header("Location: index.php?page=manage_products");
        exit;
    }
}

if (isset($_POST['delete_product'])) {
    $id = (int) $_POST['product_id'];
    
    try {
        $product = get_product_by_id($pdo, $id);

        $partNumberDisplay = $product['part_number'] ? " (Part Number: {$product['part_number']})" : "";
            add_activity_log($pdo, $user_id, $id, "Archived  product #$id - {$product['product_name']}$partNumberDisplay");
        delete_product($pdo, $id, $user_id);
        header("Location: index.php?page=manage_products&deleted=" . $id);
        exit;
    } catch (Exception $e) {
        $error = "Failed to delete product: " . $e->getMessage();
    }
}

// Permanent delete handler
if (isset($_POST['permanent_delete_product'])) {
    $id = (int) $_POST['product_id'];
    
    try {
        // FIXED: Use the new function that includes deleted products
        $product = get_product_by_id_including_deleted($pdo, $id);
        
        if (!$product) {
            $error = "Product not found.";
        } else {
            // Delete the product image if it exists
            if ($product['product_image']) {
                $imagePath = __DIR__ . '/../../assets/images/' . $product['product_image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $partNumberDisplay = $product['part_number'] ? " (Part Number: {$product['part_number']})" : "";
            add_activity_log($pdo, $user_id, $id, "Deleted product #$id - {$product['product_name']}$partNumberDisplay");

            
            // FIXED: Use the function that properly deletes stock logs first
            permanent_delete_product($pdo, $id);
            
            header("Location: index.php?page=manage_products&permanently_deleted=" . $id);
            exit;
        }
    } catch (Exception $e) {
        $error = "Failed to permanently delete product: " . $e->getMessage();
    }
}

// Restore product handler
if (isset($_POST['restore_product'])) {
    $id = (int) $_POST['product_id'];
    
    try {
        // FIXED: Get product details from database first
        $product = get_product_by_id_including_deleted($pdo, $id);
        
        if (!$product) {
            $error = "Product not found.";
        } else {
            // Restore product FIRST, then add activity log
            restore_product($pdo, $id, $user_id);
            $partNumberDisplay = $product['part_number'] ? " (Part Number: {$product['part_number']})" : "";
            add_activity_log($pdo, $user_id, $id, "Restored product #$id - {$product['product_name']}$partNumberDisplay");
            header("Location: index.php?page=manage_products&restored=" . $id);
            exit;
        }
    } catch (Exception $e) {
        $error = "Failed to restore product: " . $e->getMessage();
    }
}
if (isset($_POST['add_category'])) {
    try {
        createCategory(
            $pdo,
            $_POST['category_name'],
            $_POST['sku_prefix'],
            isset($_POST['requires_oem']) ? 1 : 0
        );
        $success = "Category added successfully.";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

if (isset($_POST['edit_category'])) {
    try {
        updateCategory(
            $pdo,
            (int) $_POST['category_id'],
            $_POST['category_name'],
            $_POST['sku_prefix'],
            isset($_POST['requires_oem']) ? 1 : 0
        );
        $success = "Category updated successfully.";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
if (isset($_POST['delete_category'])) {
    try {
        $catName = getCategoryName($pdo, (int) $_POST['category_id']);

        if (empty($catName)) {
            $error = "Category not found.";
        } else {
            deleteCategory($pdo, (int) $_POST['category_id']);

            if (function_exists('add_activity_log')) {
                add_activity_log($pdo, $user_id, null, "Deleted Category: {$catName}");
            }

            $success = "Category '{$catName}' deleted successfully.";
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
// Always fetch categories for the view
$categories = getAllCategories($pdo);
$products = get_all_products($pdo);
$categories = get_all_categories($pdo);
$editItem = null;
// Check for session messages first (these take priority)
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

// Only check for URL success messages if there's no session message
if (empty($success) && empty($error)) {
    if (isset($_GET['added'])) {
        $success = "Product added successfully!";
    }
    if (isset($_GET['deleted'])) {
        $itemId = (int) $_GET['deleted'];
        $success = "Product #" . $itemId . " archived successfully!";
    }
    if (isset($_GET['permanently_deleted'])) {
        $itemId = (int) $_GET['permanently_deleted'];
        $success = "Product #" . $itemId . " permanently deleted!";
    }
    if (isset($_GET['restored'])) {
        $itemId = (int) $_GET['restored'];
        $success = "Product #" . $itemId . " restored successfully!";
    }
    // REMOVED: Don't check for $_GET['updated'] anymore - we use session
}
    
require_once __DIR__ . '/../view/manage_products.php';