<?php
include '../config.php';

if(isset($_POST['save_menu'])) {
    $name = $_POST['name'];
    $nutrition = $_POST['nutrition'];
    $price = $_POST['price'];
    $desc = $_POST['description'];
    
    // Image Upload Logic
    $target_dir = "uploads/";
    // Pastikan folder uploads ada
    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
    
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

    $sql = "INSERT INTO menu_items (name, nutrition, price, description, image_path) 
            VALUES ('$name', '$nutrition', '$price', '$desc', '$target_file')";
    $conn->query($sql);
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="top-header"><div>Admin Powered By <br><span>DineSmart</span></div></div>
        <div class="container">
            <h2 class="page-title">Menu Details</h2>
            <form method="POST" enctype="multipart/form-data" class="card">
                <label>Menu Name</label>
                <input type="text" name="name" required>

                <div style="display:flex; gap:20px;">
                    <div style="flex:1">
                        <label>Nutrition</label>
                        <input type="text" name="nutrition">
                    </div>
                    <div style="flex:1">
                        <label>Price</label>
                        <input type="number" name="price" required>
                    </div>
                </div>

                <label>Description</label>
                <textarea name="description" rows="4"></textarea>

                <label>Add Images</label>
                <div style="border: 2px dashed #ddd; padding: 40px; text-align: center; cursor: pointer;">
                    Drop Images or Click to Upload
                    <input type="file" name="image" style="opacity:0; position:absolute; left:0; height:100%; width:100%;">
                </div>

                <button type="submit" name="save_menu" class="btn" style="margin-top:20px; width:100%;">Save</button>
            </form>
        </div>
    </div>
</body>
</html>