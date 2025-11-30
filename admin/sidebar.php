<div class="sidebar">
    <div class="brand-logo"><i class="fas fa-home"></i></div>
    <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-th-large"></i></a>
    <a href="menu_input.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'menu_input.php' ? 'active' : ''; ?>"><i class="fas fa-clipboard-list"></i></a>
    <a href="booking_queue.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'booking_queue.php' ? 'active' : ''; ?>"><i class="fas fa-hamburger"></i></a> <a href="customers.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'customers.php' ? 'active' : ''; ?>"><i class="fas fa-user"></i></a>
    <a href="login.php"><i class="fas fa-sign-out-alt"></i></a>
</div>