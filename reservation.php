<?php
include 'header.php'; // Session start ada di header

// Jika user belum login, biarkan kosong, atau paksa login (opsional)
$name_val = isset($_SESSION['user']) ? $_SESSION['user'] : '';
$phone_val = ''; 

// Cek apakah ada data user lengkap di DB (untuk mengisi no hp otomatis)
if(isset($_SESSION['user_id'])){
    include 'config.php';
    $uid = $_SESSION['user_id'];
    $u_query = $conn->query("SELECT mobile_no FROM customers WHERE id='$uid'");
    if($u_query->num_rows > 0) {
        $phone_val = $u_query->fetch_assoc()['mobile_no'];
    }
}

// Proses jika tombol diklik
if(isset($_POST['find_table'])) {
    // Simpan data ke session sementara
    $_SESSION['temp_booking'] = [
        'name' => $_POST['name'],
        'phone' => $_POST['phone'],
        'date' => $_POST['date'],
        'time' => $_POST['time'],
        'people' => $_POST['people']
    ];
    // Arahkan ke pemilihan meja
    echo "<script>window.location='select_table.php';</script>";
    exit();
}
?>

<div class="auth-wrapper">
    <div class="auth-card" style="width: 600px;">
        <h2 class="auth-title">RESERVATION</h2>
        <p class="auth-subtitle">Let's Start Your Dinner With Us</p>

        <form method="POST" class="auth-form">
            <input type="text" name="name" placeholder="Full Name" value="<?php echo $name_val; ?>" required>
            <input type="text" name="phone" placeholder="Phone Number" value="<?php echo $phone_val; ?>" required>

            <div class="reservation-form-grid">
                
                <div>
                    <label style="font-size:12px; font-weight:600; color:#888; margin-left:5px;">Date</label>
                    <input type="date" name="date" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div>
                    <label style="font-size:12px; font-weight:600; color:#888; margin-left:5px;">Time</label>
                    <select name="time" required class="custom-select">
                        <option value="" disabled selected>Select Time</option>
                        <?php 
                        // REVISI: Loop dari jam 11 sampai 20, step 1 jam
                        for($i=11; $i<=20; $i++) {
                            // Format 2 digit (misal 9 jadi 09, tapi karena mulai 11 aman)
                            $time_str = sprintf("%02d:00", $i);
                            echo "<option value='$time_str'>$time_str</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="full-width">
                    <label style="font-size:12px; font-weight:600; color:#888; margin-left:5px;">Total People</label>
                    <select name="people" required class="custom-select">
                        <option value="" disabled selected>How many people?</option>
                        <?php for($p=1; $p<=10; $p++): ?>
                            <option value="<?php echo $p; ?>"><?php echo $p; ?> Person<?php echo ($p > 1) ? 's' : ''; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <button type="submit" name="find_table" class="btn-orange" style="margin-top:10px;">FIND TABLE</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>