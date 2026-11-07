<?php
require '../includes/auth_check.php';
require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $name = trim($_POST['name']);
    
    if (empty($name)) {
        $_SESSION['error'] = "Category name cannot be empty!";
        header("Location: manage_categories.php");
        exit();
    }

    try {
        if ($type === 'main') {
            $stmt = $pdo->prepare("INSERT INTO main_categories (name) VALUES (?)");
            $stmt->execute([$name]);
            $_SESSION['success'] = "Main category added successfully!";
        } elseif ($type === 'sub') {
            $main_category_id = (int)$_POST['main_category_id'];
            $stmt = $pdo->prepare("INSERT INTO sub_categories (main_category_id, name) VALUES (?, ?)");
            $stmt->execute([$main_category_id, $name]);
            $_SESSION['success'] = "Sub category added successfully!";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error saving category: " . $e->getMessage();
    }
}

header("Location: manage_categories.php");
exit();
?>