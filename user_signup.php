<?php
include 'config.php';

if(isset($_POST['signup_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); 
    // Catatan: Sebaiknya password di-hash menggunakan password_hash($password, PASSWORD_DEFAULT)

    // Cek apakah username atau email sudah ada
    $check = $conn->query("SELECT * FROM customers WHERE name='$username' OR email='$email'");
    if($check->num_rows > 0) {
        $error = "Username atau Email sudah terdaftar!";
    } else {
        // Insert data customer baru (Status default 'Active')
        $sql = "INSERT INTO customers (name, mobile_no, email, password, status) 
                VALUES ('$username', '$phone', '$email', '$password', 'Active')";
        
        if($conn->query($sql)) {
            echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location='user_login.php';</script>";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}

include 'header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card" style="margin-top: 30px;"> <h2 class="auth-title">SIGN UP</h2>
        <p class="auth-subtitle">Let's Start Your Dinner With Us</p>

        <?php if(isset($error)) echo "<p style='color:red; margin-bottom:10px;'>$error</p>"; ?>

        <form method="POST" class="auth-form">
            <input type="text" name="username" placeholder="Username" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="signup_user" class="btn-orange">SIGN UP</button>
        </form>

        <div class="auth-footer">
            Already have an account ? <a href="user_login.php">Login</a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>