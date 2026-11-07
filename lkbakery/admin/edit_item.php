<?php
require_once '../includes/auth_check.php';
require_once '../includes/db_connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid item ID";
    header("Location: manage_menu.php");
    exit();
}

$item_id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
    $stmt->execute([$item_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        $_SESSION['error'] = "Item not found";
        header("Location: manage_menu.php");
        exit();
    }
    $main_categories = $pdo->query("SELECT * FROM main_categories ORDER BY name")->fetchAll();
    $sub_categories = $pdo->query("SELECT * FROM sub_categories ORDER BY name")->fetchAll();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required = ['main_category_id', 'sub_category_id', 'item_name', 'price'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error'] = "Please fill all required fields";
                header("Location: edit_item.php?id=" . $item_id);
                exit();
            }
        }

        $imagePath = $item['image_path'];
        if (!empty($_FILES["image"]["tmp_name"])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($_FILES['image']['type'], $allowedTypes)) {
                $_SESSION['error'] = "Only JPG, PNG & GIF files are allowed";
                header("Location: edit_item.php?id=" . $item_id);
                exit();
            }

            $targetDir = "../imgs/";
            $fileName = uniqid() . '_' . basename($_FILES["image"]["name"]);
            $targetFile = $targetDir . $fileName;
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                if (file_exists($targetDir . $item['image_path'])) {
                    unlink($targetDir . $item['image_path']);
                }
                $imagePath = $fileName;
            }
        }

        $stmt = $pdo->prepare("UPDATE menu_items SET 
            main_category_id = :main_category_id,
            sub_category_id = :sub_category_id,
            item_name = :item_name,
            description = :description,
            image_path = :image_path,
            price = :price,
            is_available = :is_available
            WHERE id = :id");
        
        $stmt->execute([
            ':main_category_id' => $_POST['main_category_id'],
            ':sub_category_id' => $_POST['sub_category_id'],
            ':item_name' => $_POST['item_name'],
            ':description' => $_POST['description'],
            ':image_path' => $imagePath,
            ':price' => $_POST['price'],
            ':is_available' => isset($_POST['is_available']) ? 1 : 0,
            ':id' => $item_id
        ]);

        $_SESSION['success'] = "Item updated successfully!";
        header("Location: manage_menu.php");
        exit();
    }

} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: edit_item.php?id=" . $item_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu Item</title>
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
            max-width: 1200px;
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

        .success {
            background-color: rgba(46, 125, 50, 0.1);
            border-left: 4px solid var(--success-color);
            color: var(--success-color);
        }

        .error {
            background-color: rgba(211, 47, 47, 0.1);
            border-left: 4px solid var(--danger-color);
            color: var(--danger-color);
        }

        .form-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }

        .form-section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
        }

        .form-section h2 {
            color: var(--primary-color);
            margin-top: 0;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
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

        textarea.form-control {
            min-height: 120px;
        }

        .image-preview {
            text-align: center;
            margin-bottom: 20px;
        }

        .current-image {
            max-width: 100%;
            height: 200px;
            object-fit: contain;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            margin-bottom: 10px;
        }

        .form-actions {
            grid-column: span 2;
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
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: var(--card-shadow);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: var(--card-shadow);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }

        @media (max-width: 768px) {
            .form-container {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                grid-column: span 1;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="admin-container">
        <h1>Edit Menu Item</h1>
        
        <?php if (isset($_SESSION['error']) && !empty($_SESSION['error'])): ?>
            <div class="alert error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success']) && !empty($_SESSION['success'])): ?>
            <div class="alert success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-container">
                <div class="form-section">
                    <h2>Basic Information</h2>
                    
                    <div class="form-group">
                        <label for="main_category">Main Category</label>
                        <select name="main_category_id" id="main_category" class="form-control" required>
                            <option value="">Select Main Category</option>
                            <?php foreach ($main_categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= $category['id'] == $item['main_category_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="sub_category">Sub Category</label>
                        <select name="sub_category_id" id="sub_category" class="form-control" required>
                            <option value="">Select Sub Category</option>
                            <?php foreach ($sub_categories as $sub_category): ?>
                                <option value="<?= $sub_category['id'] ?>" 
                                        data-main="<?= $sub_category['main_category_id'] ?>"
                                        <?= $sub_category['id'] == $item['sub_category_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sub_category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="item_name">Item Name</label>
                        <input type="text" name="item_name" id="item_name" class="form-control" 
                               value="<?= htmlspecialchars($item['item_name']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control"><?= htmlspecialchars($item['description']) ?></textarea>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2>Media & Pricing</h2>
                    
                    <div class="image-preview">
                        <?php if ($item['image_path']): ?>
                            <img src="../imgs/<?= htmlspecialchars($item['image_path']) ?>" class="current-image" alt="Current Item Image">
                            <p>Current image: <?= htmlspecialchars($item['image_path']) ?></p>
                        <?php else: ?>
                            <div style="height:200px; background:var(--light-gray); display:flex; align-items:center; justify-content:center; border-radius:8px;">
                                <p>No image currently set</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Update Image</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        <small class="text-muted">Leave blank to keep current image</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" step="0.01" name="price" id="price" class="form-control" 
                               value="<?= htmlspecialchars($item['price']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" name="is_available" id="is_available" value="1" <?= $item['is_available'] ? 'checked' : '' ?>>
                            <label for="is_available">Available for purchase</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="manage_menu.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Item
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <script>
        document.getElementById('main_category').addEventListener('change', function() {
            const mainId = this.value;
            const subSelect = document.getElementById('sub_category');
            
            subSelect.disabled = !mainId;
        
            Array.from(subSelect.options).forEach(option => {
                if (option.value === '') return; 
                option.style.display = option.dataset.main === mainId ? '' : 'none';
            });
            
            if (subSelect.value && subSelect.querySelector(`option[value="${subSelect.value}"]`).dataset.main !== mainId) {
                subSelect.value = '';
            }
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            const mainSelect = document.getElementById('main_category');
            if (mainSelect.value) {
                mainSelect.dispatchEvent(new Event('change'));
            }
            
            const imageInput = document.getElementById('image');
            if (imageInput) {
                imageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            const preview = document.querySelector('.current-image');
                            if (preview) {
                                preview.src = event.target.result;
                            } else {
                                const imagePreview = document.querySelector('.image-preview');
                                const img = document.createElement('img');
                                img.src = event.target.result;
                                img.className = 'current-image';
                                imagePreview.insertBefore(img, imagePreview.firstChild);
                            }
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>