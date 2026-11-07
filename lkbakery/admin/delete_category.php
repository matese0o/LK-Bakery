<?php
require '../includes/auth_check.php';
require '../includes/db_connect.php';

$type = $_GET['type'] ?? '';
$id = (int)$_GET['id'];

try {
    if ($type === 'main') {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM menu_items WHERE main_category_id = ?");
        $stmt->execute([$id]);
        $direct_items = $stmt->fetchColumn();
        
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM menu_items mi
            JOIN sub_categories sc ON mi.sub_category_id = sc.id
            WHERE sc.main_category_id = ?
        ");
        $stmt->execute([$id]);
        $subcategory_items = $stmt->fetchColumn();
        
        if ($direct_items > 0 || $subcategory_items > 0) {
            $_SESSION['error'] = "Cannot delete category - it contains menu items. " . 
                               "Please remove or reassign them first.";
        } else {
            $pdo->beginTransaction();
            
            $pdo->prepare("DELETE FROM sub_categories WHERE main_category_id = ?")
               ->execute([$id]);
            
            $pdo->prepare("DELETE FROM main_categories WHERE id = ?")
               ->execute([$id]);
            
            $pdo->commit();
            $_SESSION['success'] = "Main category deleted successfully!";
        }
        
    } elseif ($type === 'sub') {

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM menu_items WHERE sub_category_id = ?");
        $stmt->execute([$id]);
        $item_count = $stmt->fetchColumn();
        
        if ($item_count > 0) {
            $_SESSION['error'] = "Cannot delete subcategory - it contains menu items. " . 
                               "Please remove or reassign them first.";
        } else {

            $pdo->prepare("DELETE FROM sub_categories WHERE id = ?")
               ->execute([$id]);
            $_SESSION['success'] = "Sub category deleted successfully!";
        }
        
    } else {
        $_SESSION['error'] = "Invalid category type!";
    }
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['error'] = "Error deleting category: " . $e->getMessage();
}

header("Location: manage_categories.php");
exit();
?>