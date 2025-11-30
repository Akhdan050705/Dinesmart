<?php
session_start();
if(!isset($_SESSION['admin'])) header("Location: login.php");
include '../config.php';

// 1. Ambil Total Reservasi
$res_query = $conn->query("SELECT COUNT(*) as total FROM reservations");
$total_reservations = $res_query->fetch_assoc()['total'];

// 2. Ambil Total Balance (Income) dari tabel Orders
$bal_query = $conn->query("SELECT SUM(total_amount) as total_income FROM orders WHERE payment_status='Paid'");
$row_bal = $bal_query->fetch_assoc();
$total_balance = $row_bal['total_income'] ? $row_bal['total_income'] : 0; // Jika null jadikan 0

// 3. Ambil Data Grafik Daily Selling (7 Hari Terakhir)
$chart_labels = [];
$chart_data = [];

// Loop 7 hari ke belakang
for ($i = 6; $i >= 0; $i--) {
    $date_check = date('Y-m-d', strtotime("-$i days"));
    $sql_chart = "SELECT SUM(total_amount) as daily_total FROM orders WHERE order_date = '$date_check'";
    $res_chart = $conn->query($sql_chart);
    $row_chart = $res_chart->fetch_assoc();
    
    $chart_labels[] = date('d M', strtotime($date_check)); // Label tgl (misal: 20 Nov)
    $chart_data[] = $row_chart['daily_total'] ? $row_chart['daily_total'] : 0;
}

// Konversi PHP Array ke JSON untuk JavaScript
$json_labels = json_encode($chart_labels);
$json_data = json_encode($chart_data);
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="top-header">
            <div>Admin Powered By <br><span>DineSmart</span></div>
        </div>

        <div class="container">
            <div class="dashboard-grid">
                
                <div class="card">
                    <h3>Total Reservation</h3>
                    <h1 style="text-align:center; margin-top:20px; font-size: 48px; color:var(--primary);">
                        <?php echo $total_reservations; ?>
                    </h1>
                    <p style="text-align:center; color:#888;">Bookings so far</p>
                    <div style="width: 150px; margin: 10px auto;">
                        <canvas id="reservationChart"></canvas>
                    </div>
                </div>

                <div class="card">
                    <h3>Total Balance</h3>
                    <h1 style="color: green; margin: 20px 0;">Rp. <?php echo number_format($total_balance, 0, ',', '.'); ?></h1>
                    
                    <div style="display:flex; align-items:center; margin-bottom:15px;">
                        <div style="background:#000; color:#fff; padding:10px; border-radius:50%; margin-right:10px;"><i class="fas fa-chart-bar"></i></div>
                        <div>
                            <p>Total Income</p>
                            <b>Real-time from DB</b>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center;">
                        <div style="background:#F2994A; color:#fff; padding:10px; border-radius:50%; margin-right:10px;"><i class="fas fa-wallet"></i></div>
                        <div>
                            <p>Total Expense</p>
                            <b>$ 0 (Static)</b>
                        </div>
                    </div>
                </div>

                <div class="card" style="grid-column: 1 / -1;">
                    <h3>Daily Selling (Last 7 Days)</h3>
                    <canvas id="sellingChart" height="80"></canvas>
                </div>

                <div class="card" style="grid-column: 1 / -1; margin-top: 20px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                        <h3>Recent Feedbacks</h3>
                        <a href="feedbacks.php" style="color:var(--primary); text-decoration:none; font-size:14px;">View All</a>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Message</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Ambil 5 feedback terbaru
                            $fb_sql = "SELECT * FROM feedbacks ORDER BY created_at DESC LIMIT 5";
                            $fb_res = $conn->query($fb_sql);

                            if($fb_res->num_rows > 0) {
                                while($fb = $fb_res->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td><b>" . htmlspecialchars($fb['customer_name']) . "</b></td>";
                                    echo "<td>" . htmlspecialchars($fb['email']) . "</td>";
                                    // Potong pesan jika terlalu panjang
                                    echo "<td>" . substr(htmlspecialchars($fb['content']), 0, 50) . "...</td>";
                                    echo "<td>" . date('d M H:i', strtotime($fb['created_at'])) . "</td>";
                                    
                                    // Warna status
                                    $status_color = ($fb['status'] == 'Open') ? 'green' : 'red';
                                    echo "<td style='color:$status_color; font-weight:bold;'>" . $fb['status'] . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' style='text-align:center; color:#888;'>No feedbacks yet.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Reservation Chart (Visual Only)
        new Chart(document.getElementById('reservationChart'), {
            type: 'doughnut',
            data: {
                labels: ['On Spot', 'App'],
                datasets: [{
                    data: [30, 70], // Anda bisa membuat query sql terpisah untuk memisahkan ini jika mau
                    backgroundColor: ['#ccc', '#F2994A']
                }]
            },
            options: { cutout: '70%', plugins: { legend: { display: false } } }
        });

        // Selling Line Chart (REAL DATA)
        const labels = <?php echo $json_labels; ?>;
        const salesData = <?php echo $json_data; ?>;

        new Chart(document.getElementById('sellingChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Income (Rp)',
                    data: salesData,
                    borderColor: '#F2994A',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(242, 153, 74, 0.1)'
                }]
            },
            options: { 
                scales: { y: { beginAtZero: true } }, 
                plugins: { legend: { display: false } } 
            }
        });
    </script>
</body>
</html>