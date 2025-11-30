<?php
include 'config.php';
include 'header.php';

// Cek apakah ada ID di URL
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM menu_items WHERE id = '$id'";
    $result = $conn->query($sql);
    
    if($result->num_rows > 0) {
        $item = $result->fetch_assoc();
    } else {
        echo "<script>window.location='menu.php';</script>"; // Redirect jika ID salah
        exit();
    }
} else {
    echo "<script>window.location='menu.php';</script>"; // Redirect jika tidak ada ID
    exit();
}
?>

<div style="background-color: #fff; min-height: 100vh;"> <div class="detail-wrapper">
        
        <a href="menu.php" class="back-btn"><i class="fas fa-chevron-left"></i></a>
        
        <div class="detail-image-container">
            <img src="<?php echo $item['image_path']; ?>" alt="<?php echo $item['name']; ?>">
        </div>

        <h1 class="detail-title"><?php echo $item['name']; ?></h1>
        <div class="detail-price">RP <?php echo number_format($item['price'], 0, ',', '.'); ?></div>

        <div class="detail-desc">
            <?php echo nl2br(htmlspecialchars($item['description'])); ?>
        </div>

        <div class="nutrition-info">
            <?php if($item['calories']): ?>
            <div class="nutrition-item">
                <span class="nutrition-label">Calories:</span>
                <span class="nutrition-value">±<?php echo $item['calories']; ?></span>
            </div>
            <?php endif; ?>

            <?php if($item['protein']): ?>
            <div class="nutrition-item">
                <span class="nutrition-label">Protein:</span>
                <span class="nutrition-value">±<?php echo $item['protein']; ?></span>
            </div>
            <?php endif; ?>

            <?php if($item['fat']): ?>
            <div class="nutrition-item">
                <span class="nutrition-label">Fat:</span>
                <span class="nutrition-value">±<?php echo $item['fat']; ?></span>
            </div>
            <?php endif; ?>

            <?php if($item['carbs']): ?>
            <div class="nutrition-item">
                <span class="nutrition-label">Carbohydrates:</span>
                <span class="nutrition-value">±<?php echo $item['carbs']; ?></span>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>