<?php 
include '../config.php'; 

// --- PENTING: SET TIMEZONE ---
date_default_timezone_set('Asia/Jakarta'); 

// Variabel untuk SweetAlert
$swal_icon = "";
$swal_title = "";
$swal_text = "";

// --- LOGIKA 1: UPDATE STATUS MANUAL (TOMBOL DONE) ---
if(isset($_POST['mark_completed'])) {
    $res_id = $_POST['reservation_id'];
    $conn->query("UPDATE reservations SET status = 'Completed' WHERE id = '$res_id'");
}

// --- LOGIKA 2: TAMBAH RESERVASI MANUAL (WALK-IN) ---
if(isset($_POST['manual_reserve'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $guests = $_POST['guests'];
    $table = $_POST['table_no'];
    $date = $_POST['date']; // Format Y-m-d
    $time = $_POST['time']; // Format H:i
    
    // --- VALIDASI KETERSEDIAAN MEJA ---
    $check_sql = "SELECT id FROM reservations 
                  WHERE reservation_date = '$date' 
                  AND reservation_time = '$time' 
                  AND table_number = '$table' 
                  AND status = 'Reserved'";
                  
    $check_res = $conn->query($check_sql);
    
    if($check_res->num_rows > 0) {
        // --- JIKA PENUH (SET SWEETALERT ERROR) ---
        $swal_icon = "error";
        $swal_title = "Table Unavailable!";
        $swal_text = "Meja No. $table sudah ter-booking pada tanggal $date jam $time. Silakan pilih meja lain.";
    } else {
        // --- JIKA KOSONG (AVAILABLE) -> LANJUT SIMPAN ---
        $code = "M-" . rand(10000, 99999);
        
        $sql_insert = "INSERT INTO reservations (booking_code, customer_name, reservation_date, reservation_time, guests, table_number, status) 
                       VALUES ('$code', '$name', '$date', '$time', '$guests', '$table', 'Reserved')";
                       
        if($conn->query($sql_insert)) {
            // --- SUKSES (SET SWEETALERT SUCCESS) ---
            $swal_icon = "success";
            $swal_title = "Reservation Added";
            $swal_text = "Manual reservation has been successfully saved.";
        } else {
            $swal_icon = "error";
            $swal_title = "System Error";
            $swal_text = "Failed to add reservation.";
        }
    }
}

// --- LOGIKA 3: AUTO-CANCEL (TELAT 30 MENIT) ---
$date_now = date('Y-m-d');
$time_now = date('H:i:s');

$conn->query("UPDATE reservations SET status = 'Cancelled' WHERE status = 'Reserved' AND reservation_date = '$date_now' AND ADDTIME(reservation_time, '00:30:00') < '$time_now'");
$conn->query("UPDATE reservations SET status = 'Cancelled' WHERE status = 'Reserved' AND reservation_date < '$date_now'");
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <title>Booking Queue - Admin</title>
    <style>
        .btn-check { background-color: #27ae60; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px; }
        .btn-check:hover { background-color: #219150; }
        .booking-item { display: flex; justify-content: space-between; align-items: center; background: #f9f9f9; padding: 8px; border-radius: 5px; margin-bottom: 5px; border-left: 3px solid var(--primary-orange); }
        
        /* --- STYLE MODAL POPUP --- */
        .modal-overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 1000;
            justify-content: center; align-items: center;
        }
        .modal-content {
            background: #fff; padding: 30px; border-radius: 10px; width: 400px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3); position: relative;
            animation: slideDown 0.3s ease-out;
        }
        @keyframes slideDown { from { transform: translateY(-50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        
        .modal-header { font-size: 20px; font-weight: bold; margin-bottom: 20px; color: #333; }
        .close-btn { position: absolute; top: 15px; right: 20px; font-size: 24px; cursor: pointer; color: #888; }
        
        /* Form Styles inside Modal */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-size: 13px; color: #666; font-weight: 600; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; outline: none; }
        .btn-submit { width: 100%; padding: 12px; background: var(--primary-orange); color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; margin-top: 10px; }
        .btn-submit:hover { background: #e67e22; }
        
        .hidden-option { display: none; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="top-header"><div>Admin Powered By <br><span>DineSmart</span></div></div>
        
        <div class="container">
            <h2 class="page-title">Booking Queue (Weekly)</h2>
            
            <div class="queue-grid">
                <?php
                $target_days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                $times = ['11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'];

                foreach($target_days as $day_name) {
                    $is_today = (date('l') == $day_name);
                    
                    if($is_today) {
                        $date_db = date('Y-m-d');
                        $display_date = date('d M Y');
                        $active_class = "active-day";
                        $badge_html = "<i class='fas fa-circle live-indicator'></i> <span class='today-badge'>TODAY</span>";
                    } else {
                        $date_db = date('Y-m-d', strtotime("next " . $day_name));
                        $display_date = date('d M', strtotime($date_db));
                        $active_class = "";
                        $badge_html = "";
                    }

                    echo "<div class='queue-col $active_class'>";
                    echo "<div class='queue-header'>$day_name $badge_html <br><span style='font-size:12px; font-weight:normal; color:#888'>$display_date</span></div>";
                    
                    foreach($times as $time_slot) {
                        $sql = "SELECT * FROM reservations WHERE reservation_date = '$date_db' AND reservation_time LIKE '$time_slot%' AND status = 'Reserved'";
                        $result = $conn->query($sql);

                        echo "<div class='queue-slot' style='display:block;'>"; 
                        echo "<div class='slot-time' style='margin-bottom:5px; font-weight:bold;'>$time_slot</div>";
                        
                        if($result->num_rows > 0) {
                            while($data = $result->fetch_assoc()) {
                                ?>
                                <div class="booking-item">
                                    <div class="slot-info">
                                        <b><?php echo htmlspecialchars($data['customer_name']); ?></b><br>
                                        <small><i class='fas fa-user'></i> <?php echo $data['guests']; ?> &nbsp; <i class='fas fa-table'></i> <?php echo $data['table_number']; ?></small>
                                    </div>
                                    <form method="POST">
                                        <input type="hidden" name="reservation_id" value="<?php echo $data['id']; ?>">
                                        <button type="submit" name="mark_completed" class="btn-check" title="Mark as Completed"><i class="fas fa-check"></i> Done</button>
                                    </form>
                                </div>
                                <?php
                            }
                        } else {
                             echo "<div class='slot-info' style='color:#ccc; font-size:12px; padding:5px;'>No Reservation</div>";
                        }
                        echo "</div>"; 
                    }

                    echo "<div style='padding:10px; border-top:1px solid #eee;'>
                            <button class='slot-btn reserve' style='width:100%' onclick='openModal(\"$date_db\")'>+ Reserve Table</button>
                          </div>";

                    echo "</div>"; 
                }
                ?>
            </div>
        </div>
    </div>

    <div id="reserveModal" class="modal-overlay">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <div class="modal-header">Add Walk-in Reservation</div>
            
            <form method="POST">
                <input type="hidden" name="manual_reserve" value="1">
                
                <div class="form-group">
                    <label>Customer Name</label>
                    <input type="text" name="name" placeholder="Guest Name" required>
                </div>

                <div class="form-group" style="display:flex; gap:10px;">
                    <div style="flex:1">
                        <label>Date</label>
                        <input type="date" name="date" id="modalDate" required>
                    </div>
                    <div style="flex:1">
                        <label>Time</label>
                        <select name="time" required>
                            <?php foreach($times as $t) echo "<option value='$t'>$t</option>"; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="display:flex; gap:10px;">
                    <div style="flex:1">
                        <label>Guests</label>
                        <select name="guests" id="guestSelect" onchange="updateTableOptions()">
                            <?php for($i=1;$i<=10;$i++) echo "<option value='$i'>$i Pax</option>"; ?>
                        </select>
                    </div>
                    <div style="flex:1">
                        <label>Table No</label>
                        <select name="table_no" id="tableSelect">
                            <option value="1" class="large-table">Table 1 (8pax)</option>
                            <option value="2" class="small-table">Table 2 (4pax)</option>
                            <option value="3" class="small-table">Table 3 (4pax)</option>
                            <option value="4" class="small-table">Table 4 (2pax)</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Confirm Reservation</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(dateStr) {
            document.getElementById('reserveModal').style.display = 'flex';
            document.getElementById('modalDate').value = dateStr;
            updateTableOptions();
        }

        function closeModal() {
            document.getElementById('reserveModal').style.display = 'none';
        }

        window.onclick = function(event) {
            var modal = document.getElementById('reserveModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        function updateTableOptions() {
            var guests = parseInt(document.getElementById('guestSelect').value);
            var smallTables = document.querySelectorAll('.small-table');
            var largeTables = document.querySelectorAll('.large-table');
            var select = document.getElementById('tableSelect');

            select.value = ""; 

            if (guests >= 5) {
                largeTables.forEach(opt => opt.style.display = 'block');
                smallTables.forEach(opt => opt.style.display = 'none');
                if(largeTables.length > 0) select.value = largeTables[0].value;
            } else {
                largeTables.forEach(opt => opt.style.display = 'none');
                smallTables.forEach(opt => opt.style.display = 'block');
                if(smallTables.length > 0) select.value = smallTables[0].value;
            }
        }

        // --- TRIGGER SWEETALERT JIKA ADA PESAN DARI PHP ---
        <?php if(!empty($swal_icon)): ?>
            Swal.fire({
                icon: '<?php echo $swal_icon; ?>',
                title: '<?php echo $swal_title; ?>',
                text: '<?php echo $swal_text; ?>',
                confirmButtonColor: '#F2994A',
                confirmButtonText: 'OK'
            }).then((result) => {
                // Refresh halaman bersih (menghapus POST data) agar tidak resubmit saat reload
                if (result.isConfirmed || result.isDismissed) {
                    window.location = 'booking_queue.php';
                }
            });
        <?php endif; ?>
    </script>

</body>
</html>