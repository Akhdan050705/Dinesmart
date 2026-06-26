<?php
include 'config.php';
session_start();

$action = isset($_POST['action']) ? $_POST['action'] : '';

// 1. KIRIM PESAN (User -> Admin)
if ($action == 'send_message') {
    $msg = mysqli_real_escape_string($conn, $_POST['message']);
    $uid = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; 
    
    // Jika guest (0), bisa pakai session ID PHP atau IP address untuk identifikasi sementara
    // Untuk demo ini kita asumsikan user sudah login
    if($uid > 0) {
        $sql = "INSERT INTO chat_messages (sender_type, user_id, message) VALUES ('User', '$uid', '$msg')";
        $conn->query($sql);
    }
}

// 2. KIRIM BALASAN (Admin -> Specific User)
if ($action == 'reply_message') {
    $msg = mysqli_real_escape_string($conn, $_POST['message']);
    $target_uid = $_POST['user_id']; // ID User yang dibalas

    $sql = "INSERT INTO chat_messages (sender_type, user_id, message) VALUES ('Admin', '$target_uid', '$msg')";
    $conn->query($sql);
}

// 3. AMBIL PESAN (User Side / Admin Side Specific Chat)
if ($action == 'fetch_messages') {
    // Jika ada parameter user_id (dari admin), pakai itu. Jika tidak, pakai session user sendiri.
    if(isset($_POST['user_id'])) {
        $uid = $_POST['user_id'];
    } else {
        $uid = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    }

    $sql = "SELECT * FROM chat_messages WHERE user_id = '$uid' ORDER BY created_at ASC";
    $result = $conn->query($sql);
    
    while ($row = $result->fetch_assoc()) {
        // Tampilan CSS Bubble
        $is_me = ($row['sender_type'] == 'User');
        // Jika request dari Admin, logikanya terbalik (User = Kiri, Admin = Kanan)
        if(isset($_POST['is_admin_view'])) {
            $class = ($row['sender_type'] == 'Admin') ? 'chat-me' : 'chat-other';
            $name  = ($row['sender_type'] == 'Admin') ? 'You' : 'User';
        } else {
            // User view
            $class = ($row['sender_type'] == 'User') ? 'chat-me' : 'chat-other';
            $name  = ($row['sender_type'] == 'User') ? 'You' : 'Admin';
        }
        
        $time  = date('H:i', strtotime($row['created_at']));
        
        echo "
        <div class='message $class'>
            <div class='msg-bubble'>
                <div style='font-size:10px; opacity:0.7; margin-bottom:3px;'>$name • $time</div>
                {$row['message']}
            </div>
        </div>";
    }
}

// 4. AMBIL DAFTAR USER (Admin Only)
if ($action == 'fetch_chat_users') {
    // Query yang diperbarui: Menghitung pesan unread dari User -> Admin
    $sql = "SELECT m.user_id, c.name, MAX(m.created_at) as last_chat,
            (SELECT COUNT(*) FROM chat_messages cm WHERE cm.user_id = m.user_id AND cm.sender_type = 'User' AND cm.is_read = 0) as unread
            FROM chat_messages m 
            JOIN customers c ON m.user_id = c.id 
            GROUP BY m.user_id 
            ORDER BY last_chat DESC";
            
    $result = $conn->query($sql);
    
    while($row = $result->fetch_assoc()) {
        $uid = $row['user_id'];
        $name = htmlspecialchars($row['name']);
        $unread = $row['unread'];
        
        // Logic Bubble Notifikasi
        $badge_html = "";
        if($unread > 0) {
            $badge_html = "<div class='unread-badge'>$unread</div>";
        }
        
        echo "
        <div class='user-list-item' onclick='selectUser($uid, \"$name\")' id='user_$uid'>
            <div class='user-avatar'><i class='fas fa-user'></i></div>
            <div class='user-info'>
                <div class='u-name'>$name</div>
                <div class='u-status'>Click to chat</div>
            </div>
            $badge_html
        </div>";
    }
}
if ($action == 'mark_read') {
    $uid = $_POST['user_id'];
    $sql = "UPDATE chat_messages SET is_read = 1 WHERE user_id = '$uid' AND sender_type = 'User'";
    $conn->query($sql);
}
?>