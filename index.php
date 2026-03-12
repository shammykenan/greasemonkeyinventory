<?php
//Start session
ini_set('session.gc_maxlifetime', 900);
session_set_cookie_params(900);
session_start();

//Include session check and database connection
require_once __DIR__ . '/config/session-check.php';
require_once __DIR__ . '/config/connection.php';

//Default page
$page = $_GET['page'] ?? 'home';

//Define routes (your existing code)
$routes = [
    'dashboard' => ['view' => 'app/view/dashboard.php'],
    'landing_page' => ['controller' => 'app/controller/login_controller.php', 'view' => 'app/view/landing_page.php'],
    'reset_notice' => ['view' => 'app/view/reset_notice.php'],
    'backup' => ['controller' => 'app/controller/backup_controller.php'],
    'import' => ['controller' => 'app/controller/import_controller.php'],
    'manage_products' => ['controller' => 'app/controller/manage_products_controller.php', 'view' => 'app/view/manage_products.php'],
    'manage_stocks' => ['controller' => 'app/controller/manage_stocks_controller.php', 'view' => 'app/view/manage_stocks.php'],
    'stock_logs' => ['controller' => 'app/controller/logs_controller.php', 'view' => 'app/view/stock_logs.php'],
    'activity_logs' => ['controller' => 'app/controller/logs_controller.php', 'view' => 'app/view/activity_logs.php'],
    'print_stock_logs' => ['view' => 'app/view/print_stock_logs.php'],
    'print_activity_logs' => ['view' => 'app/view/print_activity_logs.php'],
    'print_inventory' => ['view' => 'app/view/print_inventory.php'],
    'reset_password' => ['view' => 'reset_password.php'],
    'forgot_password' => ['controller' => 'app/controller/forgot_password_process.php'],
    'logout' => ['view' => 'config/logout.php']
];

//Check if page exists
if (!array_key_exists($page, $routes)) {
    echo "<h1>🚨 Page not found</h1>";
    exit;
}

//Include controller if it exists
if (isset($routes[$page]['controller'])) {
    $controllerPath = __DIR__ . '/' . $routes[$page]['controller'];
    if (file_exists($controllerPath)) {
        require_once $controllerPath;
    }
}

//Include view if it exists
if (isset($routes[$page]['view'])) {
    $viewPath = __DIR__ . '/' . $routes[$page]['view'];
    if (file_exists($viewPath)) {
        require_once $viewPath;
    }
}