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
            <h2 class="page-title">Customer Information</h2>
            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Mobile No</th>
                            <th>Email Address</th>
                            <th>Date Joined</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM customers";
                        $result = $conn->query($sql);
                        // Jika DB kosong, tampilkan dummy
                        ?>
                        <tr>
                            <td><b>Robert</b></td>
                            <td>8053667426</td>
                            <td>RobertDowney@gmail.com</td>
                            <td>25/12/2022</td>
                            <td class="status-active">Active</td>
                            <td><i class="fas fa-trash" style="color:red"></i></td>
                        </tr>
                         <tr>
                            <td><b>Buzz</b></td>
                            <td>8053667426</td>
                            <td>buzz@gmail.com</td>
                            <td>23/11/2022</td>
                            <td class="status-closed">Deleted</td>
                            <td><i class="fas fa-trash" style="color:red"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>