<?php
include 'config.php';

// Cek Session
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: user_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

// --- LOGIC 1: UPDATE BIODATA ---
if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);

    $update_sql = "UPDATE customers SET name='$name', email='$email', mobile_no='$mobile' WHERE id='$user_id'";
    if ($conn->query($update_sql)) {
        $_SESSION['user'] = $name;
        $msg = "<p style='color:green; font-size:14px;'>Data successfully updated!</p>";
    } else {
        $msg = "<p style='color:red; font-size:14px;'>Error updating data.</p>";
    }
}

// --- LOGIC 2: BATALKAN RESERVASI ---
if (isset($_POST['cancel_booking'])) {
    $res_id = $_POST['res_id'];
    $cancel_sql = "UPDATE reservations SET status='Cancelled' WHERE id='$res_id' AND customer_id='$user_id'";
    if($conn->query($cancel_sql)){
        echo "<script>alert('Reservation has been cancelled.'); window.location='profile.php';</script>";
    }
}

// Ambil Data User Terbaru
$user_sql = "SELECT * FROM customers WHERE id='$user_id'";
$user_data = $conn->query($user_sql)->fetch_assoc();

// Ambil Data History Reservasi
$history_sql = "SELECT * FROM reservations WHERE customer_id='$user_id' ORDER BY reservation_date DESC";
$history_res = $conn->query($history_sql);
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

    <div class="history-card" style="flex:2.5;"> <h3 class="profile-title">Reservation History</h3>
        
        <?php if ($history_res->num_rows > 0): ?>
        <table class="history-table">
            <thead>
                <tr>
                    <th>Booking Code</th>
                    <th>Date & Time</th>
                    <th>Table</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $history_res->fetch_assoc()): ?>
                <tr>
                    <td style="font-weight:bold; color:var(--primary-orange);">
                        #<?php echo !empty($row['booking_code']) ? $row['booking_code'] : $row['id']; ?>
                    </td>

                    <td>
                        <?php echo date('d M Y', strtotime($row['reservation_date'])); ?><br>
                        <small style="color:#888"><?php echo date('H:i', strtotime($row['reservation_time'])); ?></small>
                    </td>

                    <td>
                        No. <?php echo $row['table_number']; ?> <br>
                        <small style="color:#888"><?php echo $row['guests']; ?> Pax</small>
                    </td>

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

                    <td>
                        <?php 
                        $res_date = $row['reservation_date'];
                        $today = date('Y-m-d');
                        
                        if($status == 'Reserved' && $res_date >= $today): 
                        ?>
                            <a href="reschedule.php?id=<?php echo $row['id']; ?>" class="btn-action btn-reschedule" title="Reschedule Booking">
                                <i class="fas fa-calendar-alt"></i>
                            </a>

                            <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure want to cancel this booking?');">
                                <input type="hidden" name="res_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="cancel_booking" class="btn-action btn-cancel" title="Cancel Booking">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>

                        <?php else: ?>
                            <span style="color:#ccc; font-size:12px;">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="color:#888; margin-top:10px;">You haven't made any reservations yet.</p>
            <a href="reservation.php" class="btn-orange" style="margin-top:20px; display:inline-block; width:auto; font-size:12px;">Book Now</a>
        <?php endif; ?>
    </div>

</div>

<?php include 'footer.php'; ?>