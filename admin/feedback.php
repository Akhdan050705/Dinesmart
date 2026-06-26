<?php 
include '../config.php'; 
session_start();

// Proteksi Halaman Admin
if(!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// --- LOGIKA 1: HAPUS FEEDBACK ---
if(isset($_POST['delete_feedback'])) {
    $id = $_POST['fb_id'];
    $conn->query("DELETE FROM feedbacks WHERE id='$id'");
}

// --- LOGIKA 2: UBAH STATUS JADI CLOSED ---
if(isset($_POST['mark_closed'])) {
    $id = $_POST['fb_id'];
    $conn->query("UPDATE feedbacks SET status='Closed' WHERE id='$id'");
}

// Ambil Semua Data Feedback (Urut dari terbaru)
$sql = "SELECT * FROM feedbacks ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Customer Feedbacks - Admin</title>
    <style>
        /* Style Khusus Halaman Feedback */
        .feedback-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .fb-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 5px solid #eee;
            transition: 0.2s;
            position: relative;
        }
        
        /* Warna border kiri berdasarkan rating */
        .fb-card.good-rating { border-left-color: #27ae60; } /* Hijau jika bintang 4-5 */
        .fb-card.bad-rating { border-left-color: #e74c3c; } /* Merah jika bintang 1-2 */
        .fb-card.mid-rating { border-left-color: #f1c40f; } /* Kuning jika bintang 3 */

        .fb-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .fb-user-info h4 { margin: 0; font-size: 16px; color: #333; }
        .fb-user-info span { font-size: 12px; color: #888; }
        .fb-date { font-size: 12px; color: #aaa; }

        .fb-content { font-size: 14px; color: #555; line-height: 1.6; margin-bottom: 15px; }

        .fb-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .star-display { color: #F2994A; font-size: 14px; }
        
        .action-btns button {
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 5px;
            color: white;
            transition: 0.2s;
        }
        
        .btn-del { background: #e74c3c; }
        .btn-del:hover { background: #c0392b; }
        
        .btn-mark { background: #3498db; }
        .btn-mark:hover { background: #2980b9; }
        
        /* Status Badge */
        .status-badge {
            font-size: 10px; padding: 3px 8px; border-radius: 10px; font-weight: bold; text-transform: uppercase;
        }
        .st-open { background: #e3f2fd; color: #3498db; }
        .st-closed { background: #eee; color: #888; }

    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="top-header">
            <div>Admin Powered By <br><span>DineSmart</span></div>
        </div>

        <div class="container">
            <h2 class="page-title">Customer Feedbacks</h2>
            
            <div class="feedback-list">
                <?php if($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        
                        <?php 
                            // Tentukan Warna Border berdasarkan Rating
                            $rating_class = "mid-rating";
                            if($row['rating'] >= 4) $rating_class = "good-rating";
                            if($row['rating'] <= 2) $rating_class = "bad-rating";
                        ?>

                        <div class="fb-card <?php echo $rating_class; ?>">
                            
                            <div class="fb-header">
                                <div class="fb-user-info">
                                    <h4><?php echo htmlspecialchars($row['customer_name']); ?></h4>
                                    <span><?php echo htmlspecialchars($row['email']); ?></span>
                                </div>
                                <div style="text-align: right;">
                                    <div class="fb-date">
                                        <i class="far fa-clock"></i> <?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?>
                                    </div>
                                    <div style="margin-top:5px;">
                                        <span class="status-badge <?php echo ($row['status']=='Open') ? 'st-open' : 'st-closed'; ?>">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="fb-content">
                                "<?php echo nl2br(htmlspecialchars($row['content'])); ?>"
                            </div>

                            <div class="fb-footer">
                                <div class="star-display">
                                    <?php 
                                    // Loop Bintang
                                    for($i=1; $i<=5; $i++) {
                                        if($i <= $row['rating']) echo '<i class="fas fa-star"></i>';
                                        else echo '<i class="far fa-star" style="color:#ddd;"></i>';
                                    }
                                    ?>
                                    <span style="color:#888; font-size:12px; margin-left:5px;">(<?php echo $row['rating']; ?>/5)</span>
                                </div>

                                <div class="action-btns">
                                    
                                    <?php if($row['status'] == 'Open'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="fb_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="mark_closed" class="btn-mark" title="Mark as Read/Closed">
                                            <i class="fas fa-check"></i> Mark Read
                                        </button>
                                    </form>
                                    <?php endif; ?>

                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this feedback?');">
                                        <input type="hidden" name="fb_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete_feedback" class="btn-del" title="Delete Feedback">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </div>

                        </div>

                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="text-align:center; padding:50px; color:#999;">
                        <i class="fas fa-comment-slash" style="font-size:40px; margin-bottom:10px;"></i>
                        <p>No feedbacks received yet.</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

</body>
</html>