<?php
include 'config.php';
include 'header.php';

$booking_code = "";

// Proses Insert Data hanya jika tombol ditekan
if(isset($_POST['confirm_booking']) && isset($_SESSION['temp_booking'])) {
    
    // Ambil Data Tahap 1
    $data = $_SESSION['temp_booking'];
    $name = $data['name'];
    $phone = $data['phone']; // Jika mau disimpan ke DB, pastikan tabel reservations ada kolom phone
    $date = $data['date'];
    $time = $data['time'];
    $people = $data['people'];
    
    // Ambil Data Tahap 2
    $table_no = $_POST['table_no'];
    
    // Ambil ID customer jika login, jika tidak NULL
    $cust_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NULL';

    // Generate Booking Code (5 Digit Acak)
    $booking_code = rand(10000, 99999);

    // Insert ke Database
    // Pastikan struktur tabel reservations Anda mendukung kolom-kolom ini
    // Jika kolom phone belum ada, Anda bisa abaikan atau ALTER TABLE dulu
    $sql = "INSERT INTO reservations (customer_name, customer_id, reservation_date, reservation_time, guests, table_number, status) 
            VALUES ('$name', $cust_id, '$date', '$time', '$people', '$table_no', 'Reserved')";
            
    if($conn->query($sql)) {
        // Berhasil disimpan, Hapus session temp
        unset($_SESSION['temp_booking']);
    } else {
        echo "Error: " . $conn->error;
        exit();
    }

} else {
    // Jika user menembak url langsung tanpa proses, kembalikan ke home
    if(empty($booking_code)) {
        echo "<script>window.location='index.php';</script>";
        exit();
    }
}
?>

<div class="auth-wrapper">
    <div class="auth-card" style="width:600px; padding: 60px 40px;">
        
        <h2 class="auth-title" style="border-bottom: 2px solid #ddd; margin-bottom:30px;">T H A N K Y O U</h2>
        
        <h3 style="color:var(--primary-orange); font-size: 24px; margin-bottom:10px;">Your booking is confirmed!</h3>
        <p style="color:#555;">Your reservation code</p>
        
        <div class="success-code"><?php echo $booking_code; ?></div>
        
        <p style="color:#888; font-size:14px; margin-bottom:40px;">
            Thank you for choosing us, and we look<br>forward to welcoming you soon.
        </p>

        <a href="index.php" class="btn-orange" style="text-decoration:none; display:inline-block; width:100%;">Back to Home</a>
    </div>
</div>

<?php include 'footer.php'; ?>