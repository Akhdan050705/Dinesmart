<?php
include 'config.php';
include 'header.php';

// Daftar Kategori
$categories = ['Appetizer', 'Main Course', 'Dessert', 'Snack', 'Drink'];

// 1. LOGIKA UTAMA: Cek apakah ada menu sama sekali di database?
$check_sql = "SELECT * FROM menu_items LIMIT 1"; 
$check_result = $conn->query($check_sql);
?>

<div style="background-color: var(--light-bg); min-height: 100vh; padding-bottom: 50px;">
    
    <div class="menu-page-header">
        <h1 style="font-size: 36px; font-weight: 800; color: #333;">Our Menu</h1>
    </div>

    <?php if($check_result->num_rows == 0): ?>
        
        <div class="empty-state-container">
            <div class="empty-state-icon">
                <i class="fas fa-utensils"></i>
                <i class="fas fa-times" style="font-size: 40px; position: absolute; margin-left: -30px; margin-top: 40px; color: var(--primary-orange); background: #fff; border-radius: 50%;"></i>
            </div>
            
            <h3 class="empty-state-title">Menu Belum Tersedia</h3>
            <p class="empty-state-text">
                Mohon maaf, saat ini kami sedang memperbarui daftar menu kami.<br>
                Silakan kembali lagi nanti untuk mencicipi hidangan lezat kami.
            </p>
            
            <a href="index.php" class="btn-orange" style="text-decoration:none; margin-top: 20px; display:inline-block;">Back to Home</a>
        </div>

    <?php else: ?>

        <?php foreach($categories as $cat): ?>
            <?php 
                $sql = "SELECT * FROM menu_items WHERE category = '$cat'";
                $result = $conn->query($sql);
                
                if($result->num_rows > 0): 
            ?>
                <h2 class="menu-category-title" id="<?php echo $cat; ?>"><?php echo $cat; ?>s</h2>
                
                <div class="menu-items-grid">
                    <?php while($row = $result->fetch_assoc()): ?>
                    <div class="menu-item-card">
                        <img src="<?php echo $row['image_path']; ?>" alt="<?php echo $row['name']; ?>">
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p><?php echo substr(htmlspecialchars($row['description']), 0, 100) . '...'; ?></p>
                        <a href="menu_detail.php?id=<?php echo $row['id']; ?>" class="btn-orange" style="text-decoration:none; padding: 10px 30px;">Read more</a>
                    </div>
                    <?php endwhile; ?>
                </div>

            <?php endif; ?>
        <?php endforeach; ?>

    <?php endif; ?> </div>

<?php include 'footer.php'; ?>