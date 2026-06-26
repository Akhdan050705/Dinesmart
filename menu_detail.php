<?php
include 'config.php';
include 'header.php';

// Cek apakah ada ID di URL
if(isset($_GET['id'])) {
    // Amankan input ID
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // --- LOGIKA TAMBAHAN: UPDATE VIEW COUNT ---
    // Setiap kali halaman ini dibuka, tambah 1 view ke database
    // Ini agar fitur "Most Discovered Eats" di Dashboard Admin bekerja
    $conn->query("UPDATE menu_items SET views = views + 1 WHERE id = '$id'");

    // Ambil Data Menu
    $sql = "SELECT * FROM menu_items WHERE id = '$id'";
    $result = $conn->query($sql);
    
    if($result->num_rows > 0) {
        $item = $result->fetch_assoc();
    } else {
        echo "<script>window.location='menu.php';</script>"; // Redirect jika ID tidak ditemukan di DB
        exit();
    }
} else {
    echo "<script>window.location='menu.php';</script>"; // Redirect jika parameter ID tidak ada
    exit();
}
?>

<div style="background-color: #fff; min-height: 100vh;"> 
    <div class="detail-wrapper">
        
        <a href="menu.php" class="back-btn"><i class="fas fa-chevron-left"></i></a>
        
        <div class="detail-image-container">
            <img src="<?php echo $item['image_path']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
        </div>

        <h1 class="detail-title"><?php echo htmlspecialchars($item['name']); ?></h1>
        
        <div class="detail-price">RP <?php echo number_format($item['price'], 0, ',', '.'); ?></div>

        <div class="detail-desc">
            <?php echo nl2br(htmlspecialchars($item['description'])); ?>
        </div>

        <div class="nutrition-info">
            
            <?php if(!empty($item['calories'])): ?>
            <div class="nutrition-item">
                <span class="nutrition-label">Calories:</span>
                <span class="nutrition-value">±<?php echo htmlspecialchars($item['calories']); ?></span>
            </div>
            <?php endif; ?>

            <?php if(!empty($item['protein'])): ?>
            <div class="nutrition-item">
                <span class="nutrition-label">Protein:</span>
                <span class="nutrition-value">±<?php echo htmlspecialchars($item['protein']); ?></span>
            </div>
            <?php endif; ?>

            <?php if(!empty($item['fat'])): ?>
            <div class="nutrition-item">
                <span class="nutrition-label">Fat:</span>
                <span class="nutrition-value">±<?php echo htmlspecialchars($item['fat']); ?></span>
            </div>
            <?php endif; ?>

            <?php if(!empty($item['carbs'])): ?>
            <div class="nutrition-item">
                <span class="nutrition-label">Carbohydrates:</span>
                <span class="nutrition-value">±<?php echo htmlspecialchars($item['carbs']); ?></span>
            </div>
            <?php endif; ?>
            
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>