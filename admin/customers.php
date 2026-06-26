<?php 
include '../config.php'; 

// --- LOGIKA HAPUS DATA ---
if(isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    
    // Query Delete
    $del_sql = "DELETE FROM customers WHERE id = '$id'";
    
    if($conn->query($del_sql)) {
        // Redirect agar URL kembali bersih
        echo "<script>alert('Customer deleted successfully!'); window.location='customers.php';</script>";
    } else {
        echo "<script>alert('Error deleting record: " . $conn->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .status-badge { padding: 5px 10px; border-radius: 5px; font-weight: 600; font-size: 12px; }
        .status-active { color: #27ae60; background-color: #eafaf1; }
        .status-inactive { color: #f39c12; background-color: #fef5e7; }
        .status-deleted { color: #c0392b; background-color: #f9e7e7; }
    </style>
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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM customers ORDER BY date_joined DESC";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                
                                $status_class = '';
                                if($row['status'] == 'Active') { $status_class = 'status-active'; } 
                                elseif($row['status'] == 'Inactive') { $status_class = 'status-inactive'; } 
                                else { $status_class = 'status-deleted'; }
                                
                                $date_formatted = date('d/m/Y', strtotime($row['date_joined']));
                        ?>
                            <tr>
                                <td><b><?php echo htmlspecialchars($row['name']); ?></b></td>
                                <td><?php echo htmlspecialchars($row['mobile_no']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo $date_formatted; ?></td>
                                <td>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="customers.php?delete_id=<?php echo $row['id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this customer permanently?')" 
                                       style="text-decoration:none;">
                                        <i class="fas fa-trash" style="color:red; cursor:pointer;" title="Delete Customer"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php 
                            } 
                        } else {
                            echo "<tr><td colspan='6' style='text-align:center; padding:20px; color:#888;'>No customers found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>