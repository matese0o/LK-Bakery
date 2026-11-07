<?php
require_once '../includes/auth_check.php';
require_once '../includes/db_connect.php';

$error = '';
$success = '';

$main_categories = $pdo->query("SELECT * FROM main_categories ORDER BY name")->fetchAll();
$sub_categories = $pdo->query("SELECT * FROM sub_categories ORDER BY name")->fetchAll();

$active_items = [];
if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    $active_items = $pdo->prepare("SELECT * FROM menu_items WHERE main_category_id = ? LIMIT 6");
    $active_items->execute([$category_id]);
    $active_items = $active_items->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required = ['main_category_id', 'sub_category_id', 'item_name', 'price'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $error = "Please fill all required fields";
            break;
        }
    }

    if (empty($error)) {
        if (empty($_FILES['image']['tmp_name'])) {
            $error = "Please select an image";
        } else {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = $_FILES['image']['type'];
            if (!in_array($fileType, $allowedTypes)) {
                $error = "Only JPG, PNG & GIF files are allowed";
            }
        }
    }

    if (empty($error)) {
        $targetDir = "../imgs/";
        $fileName = uniqid() . '_' . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO menu_items 
                                      (main_category_id, sub_category_id, item_name, description, image_path, price) 
                                      VALUES (:main_category_id, :sub_category_id, :item_name, :description, :image_path, :price)");
                
                $stmt->execute([
                    ':main_category_id' => $_POST['main_category_id'],
                    ':sub_category_id' => $_POST['sub_category_id'],
                    ':item_name' => $_POST['item_name'],
                    ':description' => $_POST['description'],
                    ':image_path' => $fileName,
                    ':price' => $_POST['price']
                ]);
                
                $_SESSION['success'] = "Item added successfully!";
                header("Location: manage_menu.php");
                exit();
            } catch (PDOException $e) {
                if (file_exists($targetFile)) {
                    unlink($targetFile);
                }
                $error = "Error saving item to database: " . $e->getMessage();
            }
        } else {
            $error = "Error uploading image";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Menu Item</title>
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .form-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .form-section {
            grid-column: 1;
        }
        
        .preview-section {
            grid-column: 2;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #285670;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-family: inherit;
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        .btn {
            background: #285670;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #1e4254;
        }
        
        .btn-cancel {
            background: #6c757d;
            margin-left: 10px;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
        }
        
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: 500;
        }
        
        .alert-error {
            background: #ffebee;
            border-left: 4px solid #f44336;
            color: #f44336;
        }
        
        .alert-success {    
            background: #e8f5e9;
            border-left: 4px solid #4CAF50;
            color: #4CAF50;
        }
        
        .preview-title {
            color: #285670;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .item-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .item-card:hover {
            transform: translateY(-5px);
        }
        
        .item-image {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }
        
        .item-details {
            padding: 10px;
        }
        
        .item-name {
            font-weight: bold;
            margin: 0 0 5px 0;
            color: #333;
            font-size: 14px;
        }
        
        .item-price {
            color: #285670;
            font-weight: bold;
            margin: 0;
            font-size: 14px;
        }
        
        .no-items {
            color: #666;
            font-style: italic;
            text-align: center;
            padding: 20px;
        }
        
        @media (max-width: 768px) {
            .form-container {
                grid-template-columns: 1fr;
            }
            
            .form-section, .preview-section {
                grid-column: auto;
            }
            
            .items-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="main-content">
        <div class="form-container">
            <div class="form-section">
                <h2>Add New Menu Item</h2>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success']) && !empty($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="main_category">Main Category</label>
                        <select name="main_category_id" id="main_category" class="form-control" required>
                            <option value="">Select Main Category</option>
                            <?php foreach ($main_categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= isset($_GET['category_id']) && $_GET['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="sub_category">Sub Category</label>
                        <select name="sub_category_id" id="sub_category" class="form-control" required disabled>
                            <option value="">Select Sub Category</option>
                            <?php foreach ($sub_categories as $sub_category): ?>
                                <option value="<?= $sub_category['id'] ?>" data-main="<?= $sub_category['main_category_id'] ?>">
                                    <?= htmlspecialchars($sub_category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="item_name">Item Name</label>
                        <input type="text" name="item_name" id="item_name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
                        <small>Only JPG, PNG, or GIF files allowed (Max 2MB)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" step="0.01" name="price" id="price" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn">Add Item</button>
                        <a href="manage_menu.php" class="btn btn-cancel">Cancel</a>
                    </div>
                </form>
            </div>
            
            <div class="preview-section">
                <h3 class="preview-title">Active Items in This Category</h3>
                <div class="items-grid" id="items-preview">
                    <?php if (!empty($active_items)): ?>
                        <?php foreach ($active_items as $item): ?>
                            <div class="item-card">
                                <img src="../imgs/<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['item_name']) ?>" class="item-image">
                                <div class="item-details">
                                    <p class="item-name"><?= htmlspecialchars($item['item_name']) ?></p>
                                    <p class="item-price">$<?= number_format($item['price'], 2) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-items">Select a category to view active items</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
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
            
            subSelect.value = '';
            
            if (mainId) {
                fetch(`add_item.php?category_id=${mainId}`)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const preview = doc.getElementById('items-preview');
                        if (preview) {
                            document.getElementById('items-preview').innerHTML = preview.innerHTML;
                        }
                    })
                    .catch(error => console.error('Error loading preview:', error));
            } else {
                document.getElementById('items-preview').innerHTML = 
                    '<p class="no-items">Select a category to view active items</p>';
            }
        });
        
        const mainSelect = document.getElementById('main_category');
        if (mainSelect.value) {
            mainSelect.dispatchEvent(new Event('change'));
        }
    </script>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>