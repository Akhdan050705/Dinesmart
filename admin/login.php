<?php
session_start();
include '../config.php';

$error = "";

if(isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query hanya cek username dulu
    $sql = "SELECT * FROM admins WHERE username='$username'"; 
    $result = $conn->query($sql);
    
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Cek Password: 
        // 1. Jika password di database sama persis dengan input (Plain text)
        // 2. ATAU jika password ter-hash (Jika nanti Anda pakai password_hash)
        if($password == $row['password'] || password_verify($password, $row['password'])) {
            $_SESSION['admin'] = $username;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-image"></div>
        <div class="auth-form">
            <h1>Welcome Back!</h1>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login" class="btn" style="width:100%">LOGIN</button>
            </form>
            
            <?php if($error): ?>
                <p style="color:red; margin-top: 10px; text-align:center;"><?php echo $error; ?></p>
            <?php endif; ?>

            <p style="margin-top:20px">Don't have an account? <a href="signup.php" style="color:var(--primary)">SignUp</a></p>
        </div>
    </div>
</body>
</html>