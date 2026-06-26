<?php
session_start();
if(!isset($_SESSION['admin'])) header("Location: index.php");
include '../config.php';

// --- LOGIC 0: CEK & BUAT DATA STATUS JIKA BELUM ADA ---
// Ini mencegah error jika tabel kosong
$check_exist = $conn->query("SELECT * FROM system_settings WHERE setting_key = 'store_status'");
if($check_exist->num_rows == 0) {
    $conn->query("INSERT INTO system_settings (setting_key, setting_value) VALUES ('store_status', 'open')");
}

// --- LOGIC 1: HANDLE TOGGLE CLICK (PERBAIKAN UTAMA DISINI) ---
// Kita cek 'save_toggle' (input hidden), bukan checkbox-nya.
if(isset($_POST['save_toggle'])) {
    
    // 1. Ambil status 'real' saat ini dari Database
    $curr_res = $conn->query("SELECT setting_value FROM system_settings WHERE setting_key = 'store_status'");
    $curr_status = $curr_res->fetch_assoc()['setting_value'];

    // 2. Balik statusnya (Jika Open jadi Closed, sebaliknya)
    $new_status = ($curr_status == 'open') ? 'closed' : 'open';
    
    // 3. Update Database
    $update_sql = "UPDATE system_settings SET setting_value = '$new_status' WHERE setting_key = 'store_status'";
    $conn->query($update_sql);
    
    // 4. Redirect pakai Javascript (Solusi ampuh hilangkan popup Resubmission)
    echo "<script>window.location.href='dashboard.php';</script>";
    exit();
}

// --- AMBIL STATUS TERBARU UNTUK TAMPILAN ---
$status_res = $conn->query("SELECT setting_value FROM system_settings WHERE setting_key = 'store_status'");
$store_status = $status_res->fetch_assoc()['setting_value'];


// --- LOGIC LAINNYA (Chart, Total, dll) ---
$total_capacity = 9;
$date_today = date('Y-m-d');
$res_sql = "SELECT COUNT(DISTINCT table_number) as occupied FROM reservations 
            WHERE reservation_date = '$date_today' AND status = 'Reserved'";
$res_result = $conn->query($res_sql);
$occupied_tables = $res_result->fetch_assoc()['occupied'];

$available_tables = $total_capacity - $occupied_tables;
if($available_tables < 0) $available_tables = 0;

$cust_sql = "SELECT COUNT(*) as total_active FROM customers WHERE status = 'Active'";
$cust_result = $conn->query($cust_sql);
$total_customers = $cust_result->fetch_assoc()['total_active'];

// Grafik Harian
$chart_labels = [];
$chart_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date_check = date('Y-m-d', strtotime("-$i days"));
    $sql_chart = "SELECT COUNT(*) as total_booking FROM reservations WHERE reservation_date = '$date_check'";
    $res_chart = $conn->query($sql_chart);
    $row_chart = $res_chart->fetch_assoc();
    $chart_labels[] = date('d M', strtotime($date_check)); 
    $chart_data[] = $row_chart['total_booking'] ? $row_chart['total_booking'] : 0;
}
$json_labels = json_encode($chart_labels);
$json_data = json_encode($chart_data);

// Menu Populer
$top_menu_sql = "SELECT * FROM menu_items ORDER BY views DESC LIMIT 4";
$top_menu_res = $conn->query($top_menu_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .bottom-section { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 20px; }
        .menu-list-item { display: flex; align-items: center; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .menu-list-item:last-child { border-bottom: none; margin-bottom: 0; }
        .menu-thumb { width: 50px; height: 50px; border-radius: 8px; object-fit: cover; margin-right: 15px; }
        .view-badge { background: #fff3cd; color: #856404; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: bold; }

        /* TOGGLE SWITCH STYLE */
        .status-card {
            background: <?php echo ($store_status == 'open') ? '#e8f5e9' : '#ffebee'; ?>;
            border: 1px solid <?php echo ($store_status == 'open') ? '#c8e6c9' : '#ffcdd2'; ?>;
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 20px; border-radius: 10px; margin-bottom: 20px;
            transition: 0.3s;
        }
        .toggle-switch {
            position: relative; display: inline-block; width: 60px; height: 34px;
        }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .slider {
            position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
            background-color: #ccc; transition: .4s; border-radius: 34px;
        }
        .slider:before {
            position: absolute; content: ""; height: 26px; width: 26px; left: 4px; bottom: 4px;
            background-color: white; transition: .4s; border-radius: 50%;
        }
        input:checked + .slider { background-color: #27ae60; }
        input:checked + .slider:before { transform: translateX(26px); }
        .status-text { font-weight: bold; font-size: 18px; color: #333; }
        .status-sub { font-size: 12px; color: #666; }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="top-header">
            <div>Admin Powered By <br><span>DineSmart</span></div>
        </div>

        <div class="container">
            
            <div class="status-card">
                <div>
                    <div class="status-text">Store Status: <span style="color:<?php echo ($store_status == 'open') ? 'green' : 'red'; ?>"><?php echo strtoupper($store_status); ?></span></div>
                    <div class="status-sub">
                        <?php echo ($store_status == 'open') ? 'Restaurant is visible to customers' : 'Restaurant is closed (Popup active)'; ?>
                    </div>
                </div>
                
                <form method="POST">
                    <input type="hidden" name="save_toggle" value="1">
                    
                    <label class="toggle-switch">
                        <input type="checkbox" onchange="this.form.submit()" <?php echo ($store_status == 'open') ? 'checked' : ''; ?>>
                        <span class="slider"></span>
                    </label>
                </form>
            </div>

            <div class="dashboard-grid">
                <div class="card">
                    <h3>Table Occupancy (Today)</h3>
                    <div style="display: flex; align-items: center; justify-content: space-around; margin-top: 20px;">
                        <div style="width: 140px; height: 140px;">
                            <canvas id="occupancyChart"></canvas>
                        </div>
                        <div style="text-align: left;">
                            <h1 style="font-size: 40px; color: var(--primary); margin: 0;">
                                <?php echo $occupied_tables; ?><span style="font-size: 20px; color:#ccc">/<?php echo $total_capacity; ?></span>
                            </h1>
                            <p style="color: #888; font-size: 14px;">Tables Booked</p>
                            <div style="margin-top: 10px; font-size: 12px;">
                                <div><i class="fas fa-circle" style="color: #F2994A;"></i> Occupied</div>
                                <div><i class="fas fa-circle" style="color: #eee;"></i> Available</div>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="customers.php" style="text-decoration: none; color: inherit;">
                    <div class="card" style="cursor: pointer; transition: transform 0.2s;">
                        <h3>Total Active Customers</h3>
                        <div style="display: flex; align-items: center; height: 100%; padding-bottom: 30px;">
                            <div style="background: rgba(242, 153, 74, 0.1); padding: 20px; border-radius: 50%; margin-right: 20px;">
                                <i class="fas fa-users" style="font-size: 40px; color: var(--primary);"></i>
                            </div>
                            <div>
                                <h1 style="font-size: 48px; margin: 0; color: #333;"><?php echo number_format($total_customers); ?></h1>
                                <p style="color: #27ae60; font-weight: bold;"><i class="fas fa-check-circle"></i> Verified Accounts</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="bottom-section">
                <div class="card">
                    <h3>Daily Booking (Last 7 Days)</h3>
                    <div style="height: 300px; padding-top: 20px;">
                        <canvas id="bookingChart"></canvas>
                    </div>
                </div>

                <div class="card">
                    <h3>Most Discovered Eats</h3>
                    <p style="color:#888; font-size:12px; margin-bottom:20px;">Most viewed menu by users</p>
                    <?php if($top_menu_res->num_rows > 0): ?>
                        <?php while($menu = $top_menu_res->fetch_assoc()): ?>
                            <div class="menu-list-item">
                                <img src="<?php echo $menu['image_path']; ?>" class="menu-thumb">
                                <div style="flex:1;">
                                    <div style="font-weight:600; font-size:14px;"><?php echo $menu['name']; ?></div>
                                    <div style="font-size:12px; color:#888;">IDR <?php echo number_format($menu['price']); ?></div>
                                </div>
                                <div class="view-badge">
                                    <i class="fas fa-eye"></i> <?php echo $menu['views']; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color:#ccc; text-align:center;">No data yet.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <script>
        new Chart(document.getElementById('occupancyChart'), {
            type: 'doughnut',
            data: {
                labels: ['Occupied', 'Available'],
                datasets: [{
                    data: [<?php echo $occupied_tables; ?>, <?php echo $available_tables; ?>],
                    backgroundColor: ['#F2994A', '#eee'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: { cutout: '75%', plugins: { legend: { display: false } } }
        });

        const labels = <?php echo $json_labels; ?>;
        const bookingData = <?php echo $json_data; ?>;
        new Chart(document.getElementById('bookingChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Reservations',
                    data: bookingData,
                    borderColor: '#F2994A',
                    backgroundColor: 'rgba(242, 153, 74, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#F2994A',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: { 
                responsive: true, maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } }, x: { grid: { display: false } } }, 
                plugins: { legend: { display: false } } 
            }
        });
    </script>
</body>
</html>