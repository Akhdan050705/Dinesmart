<?php
session_start(); // Wajib paling atas
include 'config.php';

// Logika Login
if (isset($_POST['login_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Cek database
    $sql = "SELECT * FROM customers WHERE name='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // 1. Simpan Data Session
        $_SESSION['user'] = $row['name'];
        $_SESSION['user_id'] = $row['id'];
        
        // 2. Redirect ke Index
        header("Location: index.php");
        exit(); // Wajib pakai exit setelah header
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<?php include 'header.php'; ?> 

<div class="auth-wrapper">
    <div class="auth-card">
        <h2 class="auth-title">LOGIN</h2>
        <p class="auth-subtitle">Let's Start Your Dinner With Us</p>
        
        <?php if(isset($error)) echo "<p style='color:red; margin-bottom:10px; background:#ffe6e6; padding:10px; border-radius:5px;'>$error</p>"; ?>

        <form method="POST" class="auth-form">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login_user" class="btn-orange">LOGIN</button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="user_signup.php">Sign Up</a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>