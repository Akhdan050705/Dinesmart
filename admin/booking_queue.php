<?php 
include '../config.php'; 

// Helper function untuk mencari tanggal berdasarkan nama hari (misal: Next Friday)
function get_date_by_day_name($day_name) {
    return date('Y-m-d', strtotime("next " . $day_name));
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
            <h2 class="page-title">Booking Queue</h2>
            
            <div class="queue-grid">
                <?php
                // Daftar hari yang ingin ditampilkan (Sesuai desain)
                $target_days = ['Friday', 'Saturday', 'Sunday', 'Monday'];
                // Daftar slot waktu
                $times = ['10:00', '12:00', '14:00', '16:00'];

                foreach($target_days as $day_name) {
                    // Cari tanggal asli (Y-m-d) untuk hari tersebut agar bisa dicocokkan ke DB
                    // Jika hari ini adalah hari yg dimaksud, pakai hari ini. Jika tidak, cari next day.
                    if(date('l') == $day_name) {
                        $date_db = date('Y-m-d');
                    } else {
                        $date_db = date('Y-m-d', strtotime("next " . $day_name));
                    }

                    echo "<div class='queue-col'>";
                    echo "<div class='queue-header'>$day_name <br><span style='font-size:12px; font-weight:normal; color:#888'>$date_db</span></div>";
                    
                    foreach($times as $time_slot) {
                        // Query Cek Reservasi
                        // Kita pakai LIKE '$time_slot%' karena di db formatnya 10:00:00
                        $sql = "SELECT * FROM reservations 
                                WHERE reservation_date = '$date_db' 
                                AND reservation_time LIKE '$time_slot%' 
                                AND status = 'Reserved' LIMIT 1";
                        
                        $result = $conn->query($sql);
                        $data = $result->fetch_assoc();

                        echo "<div class='queue-slot'>";
                        echo "<div class='slot-time'>$time_slot</div>";
                        
                        if($data) {
                            // Jika Ada Data di Database
                            echo "<div class='slot-info'>
                                    <b>".htmlspecialchars($data['customer_name'])."</b><br>
                                    <small><i class='fas fa-user'></i> ".$data['guests']." &nbsp; <i class='fas fa-table'></i> ".$data['table_number']."</small>
                                  </div>";
                            echo "<button class='slot-btn'>Release</button>";
                        } else {
                            // Jika KOSONG (Sesuai request Anda)
                             echo "<div class='slot-info' style='color:#ccc; font-size:12px;'>No Reservation</div>";
                        }
                        echo "</div>"; // End slot
                        
                        // Tombol Reserve Table di sela-sela jam 10
                        if($time_slot == '10:00') {
                             echo "<div style='padding:10px;'><button class='slot-btn reserve' style='width:100%'>+ Reserve Table</button></div>";
                        }
                    }
                    echo "</div>"; // End Col
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>