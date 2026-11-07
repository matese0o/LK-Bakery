<?php
require_once '../includes/auth_check.php';
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_sub_category'])) {
        $main_category_id = $_POST['main_category_id'];
        $name = trim($_POST['name']);
        if (!empty($name)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO sub_categories (main_category_id, name) VALUES (?, ?)");
                $stmt->execute([$main_category_id, $name]);
                $_SESSION['success'] = "Sub category added successfully!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Error adding sub category: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "Sub category name cannot be empty!";
        }
        header("Location: sub_categories.php");
        exit();
    }

    if (isset($_POST['edit_sub_category'])) {
        $id = $_POST['id'];
        $main_category_id = $_POST['main_category_id'];
        $name = trim($_POST['name']);
        if (!empty($name)) {
            try {
                $stmt = $pdo->prepare("UPDATE sub_categories SET main_category_id = ?, name = ? WHERE id = ?");
                $stmt->execute([$main_category_id, $name, $id]);
                $_SESSION['success'] = "Sub category updated successfully!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Error updating sub category: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "Sub category name cannot be empty!";
        }
        header("Location: sub_categories.php");
        exit();
    }

    if (isset($_POST['delete_sub_category'])) {
        $id = $_POST['id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM sub_categories WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['success'] = "Sub category deleted successfully!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error deleting sub category: " . $e->getMessage();
        }
        header("Location: sub_categories.php");
        exit();
    }
}


$main_categories = $pdo->query("SELECT * FROM main_categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);


$sub_categories = $pdo->query("
    SELECT sc.*, mc.name AS main_category_name 
    FROM sub_categories sc
    JOIN main_categories mc ON sc.main_category_id = mc.id
    ORDER BY mc.name, sc.name
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sub Categories</title>
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .category-container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        select.form-control {
            height: 38px;
        }
        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-primary {
            background: #4CAF50;
            color: white;
        }
        .btn-danger {
            background: #f44336;
            color: white;
        }
        .btn-warning {
            background: #ff9800;
            color: white;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-error {
            background: #ffdddd;
            border-left: 4px solid #f44336;
            color: #f44336;
        }
        .alert-success {
            background: #ddffdd;
            border-left: 4px solid #4CAF50;
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <?php include '../includes/admin_header.php'; ?>
    
    <div class="main-content">
        <div class="category-container">
            <h2>Manage Sub Categories</h2>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            

            <form method="POST" class="form-group">
                <h3>Add New Sub Category</h3>
                <select name="main_category_id" class="form-control" required>
                    <option value="">Select Main Category</option>
                    <?php foreach ($main_categories as $category): ?>
                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="name" class="form-control" placeholder="Sub Category Name" required>
                <button type="submit" name="add_sub_category" class="btn btn-primary">Add Sub Category</button>
            </form>
            

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Main Category</th>
                        <th>Sub Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sub_categories as $sub_category): ?>
                    <tr>
                        <td><?= htmlspecialchars($sub_category['id']) ?></td>
                        <td><?= htmlspecialchars($sub_category['main_category_name']) ?></td>
                        <td><?= htmlspecialchars($sub_category['name']) ?></td>
                        <td>
                       
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $sub_category['id'] ?>">
                                <select name="main_category_id" required>
                                    <?php foreach ($main_categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" <?= $category['id'] == $sub_category['main_category_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="text" name="name" value="<?= htmlspecialchars($sub_category['name']) ?>" required>
                                <button type="submit" name="edit_sub_category" class="btn btn-warning">Edit</button>
                            </form>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $sub_category['id'] ?>">
                                <button type="submit" name="delete_sub_category" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php include '../includes/admin_footer.php'; ?>
</body>
</html>