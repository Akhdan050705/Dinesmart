<?php
include '../config.php'; // Pastikan path config benar

if(isset($_POST['save_menu'])) {
    // 1. Ambil Data dari Form & Amankan String
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']); // Input Kategori Baru
    $price = $_POST['price'];
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Input Nutrisi Baru
    $nutrition_tag = mysqli_real_escape_string($conn, $_POST['nutrition']); // Contoh: "Best Seller", "Spicy"
    $calories = mysqli_real_escape_string($conn, $_POST['calories']);
    $protein = mysqli_real_escape_string($conn, $_POST['protein']);
    $fat = mysqli_real_escape_string($conn, $_POST['fat']);
    $carbs = mysqli_real_escape_string($conn, $_POST['carbs']);
    
    // 2. Image Upload Logic
    $target_dir = "../uploads/"; 
    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
    
    $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    $db_image_path = "uploads/" . $new_filename; // Path untuk database

    if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        
        // 3. Query Insert Lengkap (Sesuai kolom database Anda)
        $sql = "INSERT INTO menu_items 
                (name, category, nutrition, price, calories, protein, fat, carbs, description, image_path) 
                VALUES 
                ('$name', '$category', '$nutrition_tag', '$price', '$calories', '$protein', '$fat', '$carbs', '$desc', '$db_image_path')";
        
        if($conn->query($sql)){
            echo "<script>alert('Menu Berhasil Ditambahkan!'); window.location='menu_input.php';</script>";
        } else {
            echo "<script>alert('Error Database: ". $conn->error ."');</script>";
        }
    } else {
        echo "<script>alert('Gagal upload gambar.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Tambahan Style Khusus Form ini agar rapi */
        .form-row { display: flex; gap: 20px; margin-bottom: 15px; }
        .form-col { flex: 1; }
        .nutri-grid { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px; }
        
        /* Style untuk Select Option agar terlihat modern */
        select {
            width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; background: #fff;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <div class="top-header"><div>Admin Powered By <br><span>DineSmart</span></div></div>
        
        <div class="container">
            <h2 class="page-title">Add New Menu</h2>
            
            <form method="POST" enctype="multipart/form-data" class="card">
                
                <div class="form-row">
                    <div class="form-col">
                        <label>Menu Name</label>
                        <input type="text" name="name" placeholder="e.g. Grilled Salmon" required>
                    </div>
                    <div class="form-col">
                        <label>Category</label>
                        <select name="category" required>
                            <option value="" disabled selected>Select Category</option>
                            <option value="Appetizer">Appetizer</option>
                            <option value="Main Course">Main Course</option>
                            <option value="Dessert">Dessert</option>
                            <option value="Snack">Snack</option>
                            <option value="Drink">Drink</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <label>Price (IDR)</label>
                        <input type="number" name="price" placeholder="e.g. 75000" required>
                    </div>
                    <div class="form-col">
                        <label>Nutrition Tag (Optional)</label>
                        <input type="text" name="nutrition" placeholder="e.g. Best Seller, Spicy, Vegan">
                    </div>
                </div>

                <label style="margin-top: 10px; display:block; border-bottom:1px solid #eee; padding-bottom:5px; margin-bottom:10px; font-weight:bold; color:#F2994A;">Nutrition Details</label>
                <div class="nutri-grid">
                    <div>
                        <label style="font-size:12px;">Calories</label>
                        <input type="text" name="calories" placeholder="e.g. 350 kcal">
                    </div>
                    <div>
                        <label style="font-size:12px;">Protein</label>
                        <input type="text" name="protein" placeholder="e.g. 32 g">
                    </div>
                    <div>
                        <label style="font-size:12px;">Fat</label>
                        <input type="text" name="fat" placeholder="e.g. 15 g">
                    </div>
                    <div>
                        <label style="font-size:12px;">Carbs</label>
                        <input type="text" name="carbs" placeholder="e.g. 45 g">
                    </div>
                </div>

                <label>Description</label>
                <textarea name="description" rows="4" placeholder="Describe the taste and ingredients..."></textarea>

                <label>Add Images</label>
                <div style="position: relative; border: 2px dashed #ddd; padding: 40px; text-align: center; cursor: pointer; background: #f9f9f9;" onclick="document.getElementById('fileInput').click();">
                    <span id="fileNameDisplay" style="color:#888;">Drop Images or Click to Upload</span>
                    <input type="file" name="image" id="fileInput" required 
                           style="opacity: 0; position: absolute; top: 0; left: 0; width: 100%; height: 100%; cursor: pointer;"
                           onchange="document.getElementById('fileNameDisplay').innerText = this.files[0].name;">
                </div>

                <button type="submit" name="save_menu" class="btn" style="margin-top:20px; width:100%;">Publish Menu</button>
            </form>
        </div>
    </div>
</body>
</html>