<?php
include 'config.php';
// Header sudah ada session_start(), jadi include saja
// Namun kita perlu cek apakah user login SEBELUM render HTML
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: user_login.php");
    exit();
}

// 1. Logic Update Biodata
$msg = "";
$user_id = $_SESSION['user_id'];

if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);

    $update_sql = "UPDATE customers SET name='$name', email='$email', mobile_no='$mobile' WHERE id='$user_id'";
    
    if ($conn->query($update_sql)) {
        $_SESSION['user'] = $name; // Update nama di session juga
        $msg = "<p style='color:green'>Data successfully updated!</p>";
    } else {
        $msg = "<p style='color:red'>Error updating data.</p>";
    }
}

// 2. Ambil Data User Terbaru
$user_sql = "SELECT * FROM customers WHERE id='$user_id'";
$user_data = $conn->query($user_sql)->fetch_assoc();

// 3. Ambil Data History Reservasi
// Kita asumsikan saat booking nanti, customer_id disimpan di tabel reservations
$history_sql = "SELECT * FROM reservations WHERE customer_id='$user_id' ORDER BY reservation_date DESC";
$history_res = $conn->query($history_sql);

// Baru include header setelah logic di atas (karena header ada output HTML)
// Note: Karena header.php saya sebelumnya ada session_start(), 
// pastikan tidak double session_start.
// Solusi: Edit header.php agar session_start() hanya dipanggil jika belum aktif, 
// ATAU hapus session_start() di file ini dan biarkan header.php yg handle.
// Supaya aman, kita include header di bawah tapi logic redirect di atas tetap jalan.
?>

<?php include 'header.php'; ?> 

<div class="profile-container">
    
    <div class="profile-card">
        <h3 class="profile-title">My Profile</h3>
        <?php echo $msg; ?>
        
        <form method="POST" class="profile-form">
            <label>Full Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
            
            <label>Email Address</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
            
            <label>Phone Number</label>
            <input type="text" name="mobile" value="<?php echo htmlspecialchars($user_data['mobile_no']); ?>" required>
            
            <button type="submit" name="update_profile" class="btn-orange" style="padding:10px;">Update Profile</button>
        </form>
    </div>

    <div class="history-card">
        <h3 class="profile-title">Reservation History</h3>
        
        <?php if ($history_res->num_rows > 0): ?>
        <table class="history-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Guests</th>
                    <th>Table No</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $history_res->fetch_assoc()): ?>
                <tr>
                    <td><?php echo date('d M Y', strtotime($row['reservation_date'])); ?></td>
                    <td><?php echo date('H:i', strtotime($row['reservation_time'])); ?></td>
                    <td><?php echo $row['guests']; ?> Pax</td>
                    <td><?php echo $row['table_number'] ? $row['table_number'] : '-'; ?></td>
                    <td>
                        <?php 
                        $status = $row['status'];
                        $badgeClass = '';
                        if($status == 'Reserved') $badgeClass = 'badge-reserved';
                        elseif($status == 'Completed') $badgeClass = 'badge-completed';
                        else $badgeClass = 'badge-cancelled';
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>"><?php echo $status; ?></span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="color:#888; margin-top:10px;">You haven't made any reservations yet.</p>
            <a href="#" class="btn-orange" style="margin-top:20px; display:inline-block; width:auto; font-size:12px;">Book Now</a>
        <?php endif; ?>
    </div>

</div>

<?php include 'footer.php'; ?>