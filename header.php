<?php 
// Cek status session sebelum memulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RestoNest</title>
    <link rel="stylesheet" href="user_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="user-header">
        <div class="header-logo">
            <i class="fas fa-cloud-meatball"></i> RestoNest
        </div>
        <nav class="header-nav">
            <a href="index.php">HOME</a>
            <a href="menu.php">MENU</a>
            <a href="reservation.php">RESERVATION</a>
            <a href="contact.php">CONTACT US</a>
            
            <?php if(isset($_SESSION['user'])): ?>
                <a href="profile.php" class="user-icon" title="My Profile">
                    <i class="fas fa-user-circle"></i> 
                    <span style="font-size:14px; margin-left:5px; font-weight:normal;">Hi, <?php echo $_SESSION['user']; ?></span>
                </a>
                <a href="logout.php" style="font-size:14px; color:#aaa; margin-left:10px;"><i class="fas fa-sign-out-alt"></i></a>
            <?php else: ?>
                <a href="user_login.php" class="user-icon" title="Login"><i class="fas fa-user"></i></a>
            <?php endif; ?>
        </nav>
    </header>
    <div class="main-container">
        <div class="main-overlay">