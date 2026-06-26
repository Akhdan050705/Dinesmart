<?php 
// PERBAIKAN UTAMA: Session harus dimulai di sini
session_start();

include 'config.php'; 

// PERBAIKAN: Timezone
date_default_timezone_set('Asia/Jakarta'); 

// --- LOGIC 1: CEK STATUS TOKO ---
$store_status = 'open'; 
$status_sql = "SELECT setting_value FROM system_settings WHERE setting_key = 'store_status'";
$status_result = $conn->query($status_sql);
if ($status_result->num_rows > 0) {
    $store_status = $status_result->fetch_assoc()['setting_value'];
}

// --- LOGIC 2: CEK RESERVASI HARI INI (REMINDER) ---
$has_reservation = false;
$res_time_str = "";
$booking_code = "";
$time_diff_seconds = 0; 

// Sekarang $_SESSION['user_id'] sudah bisa dibaca karena session_start() ada di atas
if(isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $today = date('Y-m-d'); 
    
    $res_sql = "SELECT * FROM reservations 
                WHERE customer_id = '$uid' 
                AND reservation_date = '$today' 
                AND status = 'Reserved' 
                ORDER BY reservation_time ASC LIMIT 1";
                
    $res_query = $conn->query($res_sql);
    
    if($res_query->num_rows > 0) {
        $res_data = $res_query->fetch_assoc();
        $res_time_str = $res_data['reservation_time']; 
        $booking_code = $res_data['booking_code'] ? $res_data['booking_code'] : $res_data['id'];
        
        $now = time(); 
        $res_timestamp = strtotime($today . ' ' . $res_time_str); 
        
        $time_diff_seconds = $res_timestamp - $now; 
        
        // Cek Logika Waktu (0 detik s/d 5 Jam)
        if($time_diff_seconds > 0 && $time_diff_seconds <= (5 * 3600)) {
            $has_reservation = true;
        }
    }
}
?>

<?php 
// Include Header
// NOTE: Jika di dalam header.php ada session_start() lagi, mungkin akan muncul Notice.
// Jika muncul error "Session already started", hapus session_start() di dalam file header.php
include 'header.php'; 
?>

<style>
    /* Style Popup Toko Tutup */
    .blur-content { filter: blur(8px); pointer-events: none; user-select: none; }
    .closed-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 99999; display: flex; justify-content: center; align-items: center; }
    .closed-card { background: white; padding: 40px; border-radius: 15px; text-align: center; width: 90%; max-width: 500px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); animation: popIn 0.5s; }
    @keyframes popIn { from { transform: scale(0.5); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .closed-icon { font-size: 60px; color: #e74c3c; margin-bottom: 20px; }
    .closed-title { font-size: 28px; font-weight: 800; color: #333; margin-bottom: 10px; }
    
    /* STYLE REMINDER BAR */
    .reservation-alert {
        background: linear-gradient(90deg, #F2994A, #F2C94C); /* Oranye */
        color: white;
        padding: 12px 20px;
        position: fixed;
        top: 0; 
        left: 0;
        width: 100%;
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Poppins', sans-serif;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        animation: slideDown 0.5s;
    }
    @keyframes slideDown { from { transform: translateY(-100%); } to { transform: translateY(0); } }
    
    .alert-content { display: flex; align-items: center; gap: 15px; font-size: 14px; }
    .countdown-box {
        background: rgba(255,255,255,0.2);
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: bold;
        font-family: monospace;
        font-size: 16px;
        border: 1px solid rgba(255,255,255,0.4);
    }
    .btn-view-ticket {
        background: white; color: #F2994A; text-decoration: none; padding: 5px 12px; border-radius: 20px; font-weight: bold; font-size: 12px; transition: 0.2s;
    }
    .btn-view-ticket:hover { background: #fffbf5; transform: scale(1.05); }
    
    <?php if($has_reservation): ?>
    body { padding-top: 50px; } 
    <?php endif; ?>
</style>

<?php if($store_status == 'closed'): ?>
    <div class="closed-overlay">
        <div class="closed-card">
            <div class="closed-icon"><i class="fas fa-store-slash"></i></div>
            <div class="closed-title">SORRY, WE'RE CLOSED</div>
            <p style="color:#666; margin-bottom:20px;">Restaurant is currently closed.</p>
        </div>
    </div>
<?php endif; ?>

<?php if($has_reservation): ?>
    <div class="reservation-alert">
        <div class="alert-content">
            <i class="fas fa-bell"></i>
            <span>
                Hi <b><?php echo $_SESSION['user']; ?></b>, you have a reservation today at <b><?php echo date('H:i', strtotime($res_time_str)); ?></b> 
                (Code: #<?php echo $booking_code; ?>)
            </span>
            
            <div class="countdown-box" id="countdownTimer">
                --:--:--
            </div>
            
            <a href="profile.php" class="btn-view-ticket">View Ticket</a>
        </div>
    </div>
<?php endif; ?>


<div class="<?php echo ($store_status == 'closed') ? 'blur-content' : ''; ?>">

    <section class="hero-section">
        <div style="color:var(--primary-orange); font-size: 40px; margin-bottom: 10px;"><i class="fas fa-cloud-meatball"></i></div>
        <h1 class="hero-title">Welcome To Our Restaurant</h1>
        <p class="hero-subtitle">Enjoy An Exclusive Dining Experience With Effortless Reservations, Personalized Service, And Trusted Menu Information.</p>
        <a href="reservation.php" class="btn-orange btn-hero" style="text-decoration:none;">Reserve Now</a>
    </section>

    <section class="section-container">
        <h2 class="section-title">View Our Menu</h2>
        <div class="menu-grid">
            <a href="menu.php#Main Course" class="menu-card" style="text-decoration: none; display: block; color: inherit;">
                <img src="https://images.unsplash.com/photo-1544025162-d76694265947?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" alt="Main Courses">
                <div class="menu-label">Main Courses</div>
            </a>
            <a href="menu.php#Drink" class="menu-card" style="text-decoration: none; display: block; color: inherit;">
                <img src="https://images.unsplash.com/photo-1551024709-8f23befc6f87?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" alt="Drinks">
                <div class="menu-label">Drinks</div>
            </a>
        </div>
    </section>

    <section class="section-container">
        <div class="bestseller-card">
            <div class="bs-content">
                <h2>Our Best Seller</h2>
                <p>Grilled Lemon Salmon Served With Fresh Side Salad, Combining Smoky Flavor And Zesty Freshness For A Healthy, Delicious Meal.</p>
                <a href="menu.php" class="btn-orange" style="text-decoration:none; display:inline-block; width:auto; padding: 12px 30px;">View Menu</a>
            </div>
            <div class="bs-image">
                <img src="https://images.unsplash.com/photo-1467003909585-2f8a72700288?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" alt="Grilled Salmon">
            </div>
        </div>
    </section>

    <section class="section-container" style="margin-bottom: 80px;">
        <h2 class="section-title">CUSTOMER FEEDBACKS</h2>
        <div class="feedback-grid">
            <?php
            $fb_sql = "SELECT * FROM feedbacks WHERE rating >= 4 ORDER BY created_at DESC LIMIT 3";
            if ($conn) {
                $fb_res = $conn->query($fb_sql);
                if($fb_res && $fb_res->num_rows > 0) {
                    while($row = $fb_res->fetch_assoc()) {
                        ?>
                        <div class="feedback-card">
                            <div class="fb-name"><?php echo htmlspecialchars(strtoupper($row['customer_name'])); ?></div>
                            <p class="fb-text">"<?php echo htmlspecialchars($row['content']); ?>"</p>
                            <div class="fb-stars">
                                <?php for($i=0; $i < $row['rating']; $i++) { echo '<i class="fas fa-star"></i>'; } ?>
                            </div>
                            <i class="fas fa-quote-right fb-quote"></i>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p style='color:#888; width:100%; text-align:center;'>No feedbacks yet.</p>";
                }
            } else {
                echo "Koneksi Database Gagal.";
            }
            ?>
        </div>
    </section>

</div> 

<?php if($has_reservation): ?>
<script>
    var remainingSeconds = <?php echo $time_diff_seconds; ?>;

    function startCountdown() {
        var timerDisplay = document.getElementById('countdownTimer');
        var interval = setInterval(function() {
            if (remainingSeconds <= 0) {
                clearInterval(interval);
                timerDisplay.innerHTML = "It's Time!";
                return;
            }
            var h = Math.floor(remainingSeconds / 3600);
            var m = Math.floor((remainingSeconds % 3600) / 60);
            var s = Math.floor(remainingSeconds % 60);
            h = (h < 10) ? "0" + h : h;
            m = (m < 10) ? "0" + m : m;
            s = (s < 10) ? "0" + s : s;
            timerDisplay.innerHTML = h + ":" + m + ":" + s;
            remainingSeconds--; 
        }, 1000);
    }
    startCountdown();
</script>
<?php endif; ?>

<?php include 'footer.php'; ?>