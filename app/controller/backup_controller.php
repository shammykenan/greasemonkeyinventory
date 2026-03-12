<?php
// ── ERROR HANDLING ──────────────────────────────────────────────
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Manila');

// ── CHECK LOGIN ────────────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=landing_page");
    exit;
}

require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../model/logs_model.php';

// ── BACKUP FOLDER ─────────────────────────────────────────────
$backupDir = __DIR__ . '/../../GreaseMonkeyBackups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

// ── BACKUP FILENAME ───────────────────────────────────────────
$filename = "greasemonkey_backup_" . date("Y-m-d_h-i-s-A") . ".sql";
$filepath = $backupDir . '/' . $filename;

// ── INITIALIZE OUTPUT ─────────────────────────────────────────
$output  = "-- Grease Monkey Database Backup\n";
$output .= "-- Generated on: " . date("Y-m-d h:i:s A") . "\n";
$output .= "-- ---------------------------------------------------------\n\n";
$output .= "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
$output .= "/*!40101 SET NAMES utf8mb4 */;\n";
$output .= "/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;\n";
$output .= "/*!40103 SET TIME_ZONE='+08:00' */;\n";
$output .= "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n";
$output .= "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n\n";

// ── GET ALL TABLES ─────────────────────────────────────────────
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

// ── AUTOMATIC TABLE ORDERING BASED ON FOREIGN KEYS ────────────
$orderedTables   = [];
$remainingTables = $tables;
$maxIterations   = count($tables) * count($tables);
$iterations      = 0;

while (!empty($remainingTables)) {
    $iterations++;

    if ($iterations > $maxIterations) {
        $orderedTables = array_merge($orderedTables, array_values($remainingTables));
        break;
    }

    $progress = false;
    foreach ($remainingTables as $key => $table) {
        $refs = $pdo->query("
            SELECT REFERENCED_TABLE_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = '$table'
              AND REFERENCED_TABLE_NAME IS NOT NULL
              AND REFERENCED_TABLE_NAME != '$table'
        ")->fetchAll(PDO::FETCH_COLUMN);

        $allResolved = true;
        foreach ($refs as $ref) {
            if (in_array($ref, $remainingTables)) {
                $allResolved = false;
                break;
            }
        }

        if ($allResolved) {
            $orderedTables[] = $table;
            unset($remainingTables[$key]);
            $progress = true;
        }
    }

    if (!$progress) {
        $orderedTables = array_merge($orderedTables, array_values($remainingTables));
        break;
    }
}

// ── REVERSE ORDER FOR DROPS (children first) ──────────────────
$reversedTables = array_reverse($orderedTables);

// ── PHASE 1: DROP ALL TABLES (children first) ─────────────────
$output .= "-- ---------------------------------------------------------\n";
$output .= "-- Drop all tables in safe order (children first)\n";
$output .= "-- ---------------------------------------------------------\n\n";
$output .= "/*!40014 SET FOREIGN_KEY_CHECKS=0 */;\n";
foreach ($reversedTables as $table) {
    $output .= "DROP TABLE IF EXISTS `$table`;\n";
}
$output .= "/*!40014 SET FOREIGN_KEY_CHECKS=1 */;\n\n";

// ── PHASE 2: CREATE + INSERT (parents first) ──────────────────
foreach ($orderedTables as $table) {

    $createStmt = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);

    $output .= "\n-- ---------------------------------------------------------\n";
    $output .= "-- Table structure for `$table`\n";
    $output .= "-- ---------------------------------------------------------\n\n";
    $output .= $createStmt['Create Table'] . ";\n\n";

    $stmt = $pdo->prepare("SELECT * FROM `$table`");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($rows)) {
        $output .= "-- Data for `$table`\n\n";
        $chunks = array_chunk($rows, 100);
        foreach ($chunks as $chunk) {
            $output .= "INSERT INTO `$table` VALUES\n";
            $rowValues = [];
            foreach ($chunk as $row) {
                $values = array_map(function ($value) use ($pdo) {
                    return is_null($value) ? "NULL" : $pdo->quote($value);
                }, array_values($row));
                $rowValues[] = "  (" . implode(", ", $values) . ")";
            }
            $output .= implode(",\n", $rowValues) . ";\n";
        }
    } else {
        $output .= "-- No data in `$table`\n";
    }

    $output .= "\n";
}

// ── RESTORE SETTINGS ──────────────────────────────────────────
$output .= "-- ---------------------------------------------------------\n";
$output .= "-- Restore original settings\n";
$output .= "-- ---------------------------------------------------------\n\n";
$output .= "/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;\n";
$output .= "/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;\n";
$output .= "/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;\n";
$output .= "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n";

// ── SAVE TO FOLDER ─────────────────────────────────────────────
$saved = file_put_contents($filepath, $output);

// ── LOG ACTIVITY ──────────────────────────────────────────────
if ($saved !== false) {
    $user_id = (int) $_SESSION['user_id'];

    $check = $pdo->prepare("SELECT id FROM users WHERE id = :id LIMIT 1");
    $check->execute([':id' => $user_id]);

    if ($check->fetch()) {
        try {
            add_activity_log($pdo, $user_id, null, "Generated Database Backup: {$filename}");
        } catch (Exception $e) {
            // Silently fail — don't break the download
        }
    }
}

// ── OUTPUT FILE FOR DOWNLOAD ───────────────────────────────────
header('Content-Type: application/sql');
header("Content-Disposition: attachment; filename=\"{$filename}\"");
header('Pragma: no-cache');
header('Expires: 0');

echo $output;
exit;