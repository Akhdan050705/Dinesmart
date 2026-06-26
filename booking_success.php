<?php
include 'config.php';
include 'header.php';

// Pastikan Timezone sesuai
date_default_timezone_set('Asia/Jakarta');

$booking_code = "";

// Proses Insert Data hanya jika tombol ditekan
if(isset($_POST['confirm_booking']) && isset($_SESSION['temp_booking'])) {
    
    // Ambil Data Tahap 1
    $data = $_SESSION['temp_booking'];
    $name = $data['name'];
    $phone = $data['phone']; 
    $date = $data['date'];
    $time = $data['time'];
    $people = $data['people'];
    
    // Ambil Data Tahap 2
    $table_no = $_POST['table_no'];
    $cust_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NULL';

    // --- [LOGIKA] CEK KETERSEDIAAN SEBELUM SIMPAN ---
    $check_sql = "SELECT id FROM reservations 
                  WHERE reservation_date = '$date' 
                  AND reservation_time = '$time' 
                  AND table_number = '$table_no' 
                  AND status = 'Reserved'";
    
    $check_res = $conn->query($check_sql);

    // JIKA PENUH -> TAMPILKAN SWEETALERT
    if($check_res->num_rows > 0) {
        ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops... Table Occupied!',
                    text: 'Sorry, Table No. <?php echo $table_no; ?> has just been booked by another customer. Please choose another table or time.',
                    confirmButtonColor: '#F2994A',
                    confirmButtonText: 'Choose Another Table'
                }).then((result) => {
                    // Redirect kembali ke halaman reservasi
                    window.location = 'reservation.php';
                });
            });
        </script>
        <?php
        // Hentikan eksekusi script ke bawah (jangan tampilkan halaman sukses)
        exit();
    }

    // --- JIKA KOSONG (AMAN), LANJUT SIMPAN ---
    $booking_code = rand(10000, 99999);

    $sql = "INSERT INTO reservations (booking_code, customer_name, customer_id, reservation_date, reservation_time, guests, table_number, status) 
            VALUES ('$booking_code', '$name', $cust_id, '$date', '$time', '$people', '$table_no', 'Reserved')";
            
    if($conn->query($sql)) {
        unset($_SESSION['temp_booking']);
    } else {
        echo "Error: " . $conn->error;
        exit();
    }

} else {
    // Jika akses langsung tanpa booking
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