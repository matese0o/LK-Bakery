<?php
session_start();
require_once '../includes/auth_check.php';
require_once '../includes/db_connect.php';


if (!isset($_SESSION['user_role'])) {  
    $_SESSION['error'] = "Unauthorized access!";
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION['user_role'] !== 'admin') {
    $_SESSION['error'] = "You don't have permission to perform this action!";
    header("Location: manage_menu.php");
    exit();
}


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid item ID!";
    header("Location: manage_menu.php");
    exit();
}

$item_id = (int)$_GET['id'];

try {

    $stmt = $conn->prepare("SELECT image_path FROM menu_items WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Item not found!";
        header("Location: manage_menu.php");
        exit();
    }
    
    $item = $result->fetch_assoc();
    $image_path = "../imgs/" . $item['image_path'];
    

    $deleteStmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
    $deleteStmt->bind_param("i", $item_id);
    $deleteStmt->execute();
    

    if (file_exists($image_path)) {
        if (!unlink($image_path)) {
            $_SESSION['warning'] = "Item deleted but image removal failed!";
        }
    }
    
    if ($deleteStmt->affected_rows > 0) {
        $_SESSION['success'] = "Item deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete item!";
    }
    
    $deleteStmt->close();
    $conn->close();
    
} catch (Exception $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
}

header("Location: manage_menu.php");
exit();
?>