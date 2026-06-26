<?php
include 'header.php'; // Session start ada di sini
include 'config.php';

// 1. Cek ID Reservasi
if(!isset($_GET['id'])) {
    header("Location: profile.php");
    exit();
}

$res_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// 2. Ambil Data Lama dari Database
// Kita pastikan data milik user yang sedang login (Security)
$sql = "SELECT * FROM reservations WHERE id='$res_id' AND customer_id='$user_id'";
$result = $conn->query($sql);

if($result->num_rows == 0) {
    echo "<script>alert('Reservation not found!'); window.location='profile.php';</script>";
    exit();
}

$old_data = $result->fetch_assoc();

// 3. Proses Lanjut ke Pemilihan Meja
if(isset($_POST['go_to_table'])) {
    // Simpan data perubahan sementara di Session
    $_SESSION['reschedule_data'] = [
        'id' => $res_id, // ID Reservasi yg mau diupdate
        'date' => $_POST['date'],
        'time' => $_POST['time'],
        'people' => $_POST['people'],
        'name' => $_POST['name'],  // Nama & HP sekedar info
        'phone' => $_POST['phone']
    ];
    
    // Arahkan ke pemilihan meja
    echo "<script>window.location='reschedule_table.php';</script>";
    exit();
}
?>

<div class="auth-wrapper">
    <div class="auth-card" style="width: 600px;">
        <h2 class="auth-title">RESCHEDULE</h2>
        <p class="auth-subtitle">Update Your Booking Details</p>

        <div style="text-align:center; margin-bottom:20px; color:var(--primary-orange); font-weight:bold;">
            Booking ID: #<?php echo $old_data['booking_code'] ? $old_data['booking_code'] : $old_data['id']; ?>
        </div>

        <form method="POST" class="auth-form">
            <input type="text" name="name" value="<?php echo htmlspecialchars($old_data['customer_name']); ?>" required>
            <input type="text" name="phone" placeholder="Phone Number" value="" required> <div class="reservation-form-grid">
                
                <div>
                    <label style="font-size:12px; font-weight:600; color:#888; margin-left:5px;">New Date</label>
                    <input type="date" name="date" required min="<?php echo date('Y-m-d'); ?>" 
                           value="<?php echo $old_data['reservation_date']; ?>">
                </div>
                
                <div>
                    <label style="font-size:12px; font-weight:600; color:#888; margin-left:5px;">New Time</label>
                    <select name="time" required class="custom-select">
                        <option value="" disabled>Select Time</option>
                        <?php 
                        $old_time = date('H:i', strtotime($old_data['reservation_time'])); // Format 12:00
                        for($i=11; $i<=20; $i++) {
                            $time_str = sprintf("%02d:00", $i);
                            // Cek jika jam sama dengan data lama, tambahkan 'selected'
                            $selected = ($time_str == $old_time) ? 'selected' : '';
                            echo "<option value='$time_str' $selected>$time_str</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="full-width">
                    <label style="font-size:12px; font-weight:600; color:#888; margin-left:5px;">Total People</label>
                    <select name="people" required class="custom-select">
                        <option value="" disabled>How many people?</option>
                        <?php 
                        $old_guest = $old_data['guests'];
                        for($p=1; $p<=10; $p++): 
                            $selected = ($p == $old_guest) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $p; ?>" <?php echo $selected; ?>><?php echo $p; ?> Person<?php echo ($p > 1) ? 's' : ''; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <button type="submit" name="go_to_table" class="btn-orange" style="margin-top:10px;">NEXT: SELECT TABLE</button>
            <a href="profile.php" style="display:block; text-align:center; margin-top:15px; color:#888; text-decoration:none; font-size:14px;">Cancel Reschedule</a>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>