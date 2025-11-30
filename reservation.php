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
    <div class="auth-card" style="width: 600px;"> <h2 class="auth-title">RESERVATION</h2>
        <p class="auth-subtitle">Let's Start Your Dinner With Us</p>

        <form method="POST" class="auth-form">
            <input type="text" name="name" placeholder="Full Name" value="<?php echo $name_val; ?>" required>
            <input type="text" name="phone" placeholder="Phone Number" value="<?php echo $phone_val; ?>" required>

            <div class="reservation-form-grid">
                <div class="form-group">
                    <input type="date" name="date" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="form-group">
                    <select name="time" required>
                        <option value="" disabled selected>Time</option>
                        <?php 
                        for($i=11; $i<=21; $i++) {
                            echo "<option value='{$i}:00'>{$i}:00</option>";
                            echo "<option value='{$i}:30'>{$i}:30</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <select name="people" required>
                        <option value="" disabled selected>People</option>
                        <?php for($p=1; $p<=10; $p++): ?>
                            <option value="<?php echo $p; ?>"><?php echo $p; ?> Person</option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <button type="submit" name="find_table" class="btn-orange">FIND TABLE</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>