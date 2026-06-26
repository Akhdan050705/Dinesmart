<?php
include 'header.php';

// Logic PHP untuk Form Feedback (Tetap sama)
$name_val = '';
$email_val = '';
if(isset($_SESSION['user_id'])) {
    include 'config.php'; 
    $uid = $_SESSION['user_id'];
    $u_query = $conn->query("SELECT name, email FROM customers WHERE id='$uid'");
    if($u_query->num_rows > 0) {
        $u_data = $u_query->fetch_assoc();
        $name_val = $u_data['name'];
        $email_val = $u_data['email'];
    }
}

if(isset($_POST['send_feedback'])) {
    include 'config.php';
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $rating = $_POST['rating']; 
    $sql = "INSERT INTO feedbacks (customer_name, email, content, rating, status) VALUES ('$name', '$email', '$message', '$rating', 'Open')";
    if($conn->query($sql)) { echo "<script>window.location='contact_success.php';</script>"; exit(); }
}
?>

<style>
    /* --- Form Rating Styles (Tetap) --- */
    .rating-box { display: flex; flex-direction: row-reverse; justify-content: flex-end; margin-bottom: 20px; }
    .rating-box input { display: none; }
    .rating-box label { font-size: 30px; color: #ddd; cursor: pointer; margin-right: 5px; transition: color 0.2s; }
    .rating-box input:checked ~ label, .rating-box label:hover, .rating-box label:hover ~ label { color: #F2994A; }

    /* --- FLOATING CHAT BUTTON --- */
    .chat-btn {
        position: fixed; bottom: 30px; right: 30px;
        background: linear-gradient(135deg, #F2994A, #F2C94C); /* Gradient Oranye */
        color: white;
        width: 60px; height: 60px; border-radius: 50%;
        display: flex; justify-content: center; align-items: center;
        font-size: 28px; cursor: pointer; 
        box-shadow: 0 4px 15px rgba(242, 153, 74, 0.4);
        z-index: 9999; transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .chat-btn:hover { transform: scale(1.1) rotate(10deg); }

    /* --- CHAT BOX CONTAINER --- */
    .chat-box {
        display: none; /* Default Hidden */
        position: fixed; bottom: 100px; right: 30px;
        width: 360px; height: 500px; /* Lebih tinggi sedikit */
        background: #fff; border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        z-index: 9999; flex-direction: column; overflow: hidden;
        border: 1px solid #f0f0f0;
        animation: slideIn 0.3s ease-out;
    }
    @keyframes slideIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    /* Header Chat */
    .chat-header {
        background: linear-gradient(135deg, #2c3e50, #4ca1af); /* Warna gelap elegan */
        color: #fff; padding: 15px 20px; font-weight: 600; 
        display: flex; justify-content: space-between; align-items: center;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .chat-status { font-size: 11px; opacity: 0.8; font-weight: normal; display: block; }

    /* Area Pesan */
    .chat-content {
        flex: 1; padding: 15px; 
        overflow-y: auto; 
        background-color: #f7f9fc; /* Latar abu-abu sangat muda */
        display: flex; flex-direction: column; gap: 12px;
    }

    /* Input Area */
    .chat-input-area {
        padding: 15px; border-top: 1px solid #eee; display: flex; background: #fff; align-items: center; gap: 10px;
    }
    .chat-input-area input {
        flex: 1; padding: 12px 15px; border: 1px solid #ddd; border-radius: 25px; outline: none; background: #f9f9f9; font-size: 14px;
    }
    .chat-input-area input:focus { border-color: #F2994A; background: #fff; }
    .chat-input-area button {
        background: #F2994A; color: #fff; border: none; width: 40px; height: 40px; border-radius: 50%; 
        cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; box-shadow: 0 2px 5px rgba(242, 153, 74, 0.3);
    }
    .chat-input-area button:hover { background: #e08e43; transform: scale(1.05); }

    /* --- BUBBLE CHAT STYLES --- */
    .message { display: flex; width: 100%; }
    
    /* Posisi User (Kanan) */
    .chat-me { justify-content: flex-end; }
    .chat-me .msg-bubble {
        background: #F2994A; /* Warna Oranye Brand */
        color: #fff;
        border-radius: 15px 15px 0 15px; /* Lancip di kanan bawah */
        box-shadow: 0 2px 5px rgba(242, 153, 74, 0.2);
    }

    /* Posisi Admin (Kiri) */
    .chat-admin { justify-content: flex-start; }
    .chat-admin .msg-bubble {
        background: #fff; /* Warna Putih Bersih */
        color: #333;
        border-radius: 15px 15px 15px 0; /* Lancip di kiri bawah */
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }

    /* Isi Bubble */
    .msg-bubble {
        max-width: 75%; padding: 10px 14px; font-size: 13px; line-height: 1.5; position: relative; word-wrap: break-word;
    }
    
    /* Nama & Waktu Kecil */
    .msg-info { display: block; font-size: 10px; margin-bottom: 2px; opacity: 0.7; }
    .chat-me .msg-info { text-align: right; color: #ffe0b2; } /* Warna text info user agak terang */
    .chat-admin .msg-info { text-align: left; color: #888; }
</style>

<div class="auth-wrapper">
    <div class="auth-card contact-card">
        <h2 class="auth-title">CONTACT US</h2>
        <p class="auth-subtitle">Send Valuable Feedback To Us</p>
        <form method="POST" class="auth-form">
            <div style="display: flex; gap: 20px;">
                <input type="text" name="name" placeholder="Name" value="<?php echo $name_val; ?>" required style="flex:1;">
                <input type="email" name="email" placeholder="Email" value="<?php echo $email_val; ?>" required style="flex:1;">
            </div>
            <div style="text-align: left; margin-bottom: 5px; color: #555; font-weight: 500;">Rate Your Experience:</div>
            <div class="rating-box">
                <input type="radio" name="rating" id="star5" value="5" required><label for="star5"><i class="fas fa-star"></i></label>
                <input type="radio" name="rating" id="star4" value="4"><label for="star4"><i class="fas fa-star"></i></label>
                <input type="radio" name="rating" id="star3" value="3"><label for="star3"><i class="fas fa-star"></i></label>
                <input type="radio" name="rating" id="star2" value="2"><label for="star2"><i class="fas fa-star"></i></label>
                <input type="radio" name="rating" id="star1" value="1"><label for="star1"><i class="fas fa-star"></i></label>
            </div>
            <textarea name="message" placeholder="Message" required></textarea>
            <div style="text-align: left;">
                <button type="submit" name="send_feedback" class="btn-orange" style="width: 150px;">Send</button>
            </div>
        </form>
    </div>
</div>

<div class="chat-btn" onclick="toggleChat()">
    <i class="fas fa-comment-dots"></i>
</div>

<div class="chat-box" id="chatBox">
    <div class="chat-header">
        <div>
            <div><i class="fas fa-robot"></i> Live Support</div>
            <span class="chat-status">Online • Reply in minutes</span>
        </div>
        <span onclick="toggleChat()" style="cursor:pointer; font-size:20px;">&times;</span>
    </div>
    
    <div class="chat-content" id="chatContent">
        </div>

    <div class="chat-input-area">
        <input type="text" id="chatInput" placeholder="Type a message...">
        <button onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function toggleChat() {
        var chat = document.getElementById("chatBox");
        if (chat.style.display === "none" || chat.style.display === "") {
            chat.style.display = "flex";
            loadMessages();
            // Scroll ke bawah saat pertama buka
            setTimeout(scrollToBottom, 200);
            
            // Mulai auto refresh
            if (!window.chatInterval) {
                window.chatInterval = setInterval(loadMessages, 3000);
            }
        } else {
            chat.style.display = "none";
            // Stop interval kalau tutup biar hemat resource
            if (window.chatInterval) {
                clearInterval(window.chatInterval);
                window.chatInterval = null;
            }
        }
    }

    function scrollToBottom() {
        var chatDiv = document.getElementById("chatContent");
        chatDiv.scrollTop = chatDiv.scrollHeight;
    }

    function sendMessage() {
        var msg = $("#chatInput").val();
        if(msg.trim() == "") return;

        $.post("chat_endpoint.php", {
            action: 'send_message',
            message: msg
        }, function(response) {
            $("#chatInput").val(""); 
            loadMessages(); 
            setTimeout(scrollToBottom, 200); // Scroll setelah kirim
        });
    }

    function loadMessages() {
        $.post("chat_endpoint.php", {
            action: 'fetch_messages'
        }, function(data) {
            if(data.trim() !== "") {
                var chatDiv = $("#chatContent");
                // Cek apakah user sedang scroll di atas? Jika iya jangan auto scroll
                var isScrolledToBottom = chatDiv[0].scrollHeight - chatDiv[0].clientHeight <= chatDiv[0].scrollTop + 10;
                
                chatDiv.html(data);
                
                // Hanya auto scroll jika user memang ada di bawah
                if(isScrolledToBottom) {
                    scrollToBottom();
                }
            } else {
                 $("#chatContent").html('<div style="text-align:center; color:#ccc; margin-top:50%; font-size:12px;">Start chatting with Admin...</div>');
            }
        });
    }

    $("#chatInput").keypress(function(e) {
        if(e.which == 13) sendMessage();
    });
</script>

<?php include 'footer.php'; ?>