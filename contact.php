<?php
include 'header.php';

// Pre-fill data jika user sudah login
$name_val = '';
$email_val = '';

if(isset($_SESSION['user_id'])) {
    include 'config.php'; // Pastikan koneksi db ada
    $uid = $_SESSION['user_id'];
    // Ambil email dari database customer
    $u_query = $conn->query("SELECT name, email FROM customers WHERE id='$uid'");
    if($u_query->num_rows > 0) {
        $u_data = $u_query->fetch_assoc();
        $name_val = $u_data['name'];
        $email_val = $u_data['email'];
    }
}

// Proses Kirim Feedback
if(isset($_POST['send_feedback'])) {
    include 'config.php';
    
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    $sql = "INSERT INTO feedbacks (customer_name, email, content, status) 
            VALUES ('$name', '$email', '$message', 'Open')";
            
    if($conn->query($sql)) {
        echo "<script>window.location='contact_success.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>

<div class="auth-wrapper">
    <div class="auth-card contact-card">
        <h2 class="auth-title">CONTACT US</h2>
        <p class="auth-subtitle">Send Valueable Feedback To Us</p>

        <form method="POST" class="auth-form">
            
            <div style="display: flex; gap: 20px;">
                <input type="text" name="name" placeholder="Name" value="<?php echo $name_val; ?>" required style="flex:1;">
                <input type="email" name="email" placeholder="Email" value="<?php echo $email_val; ?>" required style="flex:1;">
            </div>

            <textarea name="message" placeholder="Message" required></textarea>

            <div style="text-align: left;">
                <button type="submit" name="send_feedback" class="btn-orange" style="width: 150px;">Send</button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>