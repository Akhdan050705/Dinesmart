<?php include '../config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="top-header"><div>Admin Powered By <br><span>DineSmart</span></div></div>
        <div class="container">
            <h2 class="page-title">Customer Feedbacks</h2>
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
</body>
</html>