<?php
require '../includes/auth_check.php';
require '../includes/db_connect.php';

$type = isset($_GET['type']) && in_array($_GET['type'], ['main', 'sub']) ? $_GET['type'] : '';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$type || !$id) {
    $_SESSION['error'] = "Invalid request parameters!";
    header("Location: manage_categories.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    
    if (empty($name)) {
        $_SESSION['error'] = "Category name cannot be empty!";
        header("Location: edit_category.php?type=$type&id=$id");
        exit();
    }

    try {
        if ($type === 'main') {
            $stmt = $pdo->prepare("UPDATE main_categories SET name = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$name, $id]);
            $_SESSION['success'] = "Main category updated successfully!";
        } elseif ($type === 'sub') {
            $main_category_id = filter_input(INPUT_POST, 'main_category_id', FILTER_VALIDATE_INT);
            if (!$main_category_id) {
                $_SESSION['error'] = "Invalid main category selection";
                header("Location: edit_category.php?type=$type&id=$id");
                exit();
            }
            
            $stmt = $pdo->prepare("UPDATE sub_categories SET main_category_id = ?, name = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$main_category_id, $name, $id]);
            $_SESSION['success'] = "Sub category updated successfully!";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating category: " . $e->getMessage();
    }
    
    header("Location: manage_categories.php");
    exit();
}

try {
    if ($type === 'main') {
        $stmt = $pdo->prepare("SELECT * FROM main_categories WHERE id = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch();
        $title = "Edit Main Category";
    } elseif ($type === 'sub') {
        $stmt = $pdo->prepare("SELECT * FROM sub_categories WHERE id = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch();
        $title = "Edit Sub Category";
        $main_categories = $pdo->query("SELECT * FROM main_categories ORDER BY name")->fetchAll();
    }

    if (!$category) {
        $_SESSION['error'] = "Category not found!";
        header("Location: manage_categories.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: manage_categories.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $title ?> - LK Bakery</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #285670;
            --primary-hover: #1d4255;
            --secondary-color: #9c785b;
            --secondary-hover: #b08a6a;
            --success-color: #2e7d32;
            --warning-color: #ff9800;
            --danger-color: #d32f2f;
            --light-gray: #f5f5f5;
            --border-color: #e0e0e0;
            --card-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .admin-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 2rem;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
        }

        .form-section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--primary-color);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(40, 86, 112, 0.1);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23285670'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 15px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            border: none;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: var(--card-shadow);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: var(--card-shadow);
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            position: relative;
            animation: fadeIn 0.3s ease-in;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert i {
            font-size: 1.2rem;
        }

        .alert-success {
            background-color: rgba(46, 125, 50, 0.1);
            border-left: 4px solid var(--success-color);
            color: var(--success-color);
        }

        .alert-error {
            background-color: rgba(211, 47, 47, 0.1);
            border-left: 4px solid var(--danger-color);
            color: var(--danger-color);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="admin-container">
        <h1><?= $title ?></h1>
        
        <?php if (isset($_SESSION['error']) && !empty($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success']) && !empty($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <div class="form-section">
            <form method="POST">
                <div class="form-group">
                    <label for="name">Category Name</label>
                    <input type="text" id="name" name="name" class="form-control" 
                           value="<?= htmlspecialchars($category['name']) ?>" required>
                </div>
                
                <?php if ($type === 'sub'): ?>
                <div class="form-group">
                    <label for="main_category_id">Main Category</label>
                    <select id="main_category_id" name="main_category_id" class="form-control" required>
                        <option value="">Select Main Category</option>
                        <?php foreach ($main_categories as $main): ?>
                            <option value="<?= $main['id'] ?>" <?= $main['id'] == $category['main_category_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($main['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <div class="form-actions">
                    <a href="manage_categories.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
    

</body>
</html>